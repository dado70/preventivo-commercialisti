<?php
/**
 * Preventivo Commercialisti - Web Installer
 * Copyright (C) 2024 Alessandro Scapuzzi <dado70@gmail.com>
 * Licenza: GPL v3
 *
 * Istruzioni:
 *  1. Carica l'intero pacchetto via FTP nella root del tuo hosting
 *  2. Apri http://tuodominio.it/installer.php nel browser
 *  3. Segui i passaggi guidati
 *  4. Al termine il file si auto-elimina per sicurezza
 */

declare(strict_types=1);
session_start();

define('INSTALLER_VERSION', '1.0.0');
define('MIN_PHP', '8.1.0');
define('DB_SCHEMA',  __DIR__ . '/database/schema.sql');
define('DB_SEED',    __DIR__ . '/database/seed_tariffe.sql');
define('CONFIG_FILE', __DIR__ . '/config/config.php');

// ── Sicurezza: blocca se già installato ──────────────────────────────────────
if (file_exists(CONFIG_FILE) && !isset($_GET['force'])) {
    $cfg = file_get_contents(CONFIG_FILE);
    if (strpos($cfg, 'CAMBIA_QUESTA_PASSWORD') === false) {
        die('<h2>Applicazione già installata.</h2><p>Elimina <code>installer.php</code> dal server per sicurezza. <a href="index.php">Vai all\'applicazione</a></p>');
    }
}

$step  = (int)($_POST['step'] ?? $_GET['step'] ?? 1);
$error = '';
$data  = $_SESSION['installer_data'] ?? [];

// ── Gestione POST ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $step = (int)($_POST['step'] ?? 1);

    if ($step === 2) {
        // Salva config DB e testa connessione
        $data['db_host']   = trim($_POST['db_host'] ?? 'localhost');
        $data['db_port']   = trim($_POST['db_port'] ?? '3306');
        $data['db_name']   = trim($_POST['db_name'] ?? '');
        $data['db_user']   = trim($_POST['db_user'] ?? '');
        $data['db_pass']   = $_POST['db_pass'] ?? '';
        $data['db_prefix'] = trim($_POST['db_prefix'] ?? '');
        $_SESSION['installer_data'] = $data;

        try {
            $dsn = "mysql:host={$data['db_host']};port={$data['db_port']};charset=utf8mb4";
            $pdo = new PDO($dsn, $data['db_user'], $data['db_pass'],
                          [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            // Crea il database se non esiste
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$data['db_name']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `{$data['db_name']}`");
            $step = 3;
        } catch (PDOException $e) {
            $error = 'Connessione al database fallita: ' . $e->getMessage();
            $step  = 2;
        }

    } elseif ($step === 3) {
        // Salva dati admin e app
        $data['admin_nome']    = trim($_POST['admin_nome'] ?? '');
        $data['admin_cognome'] = trim($_POST['admin_cognome'] ?? '');
        $data['admin_email']   = trim($_POST['admin_email'] ?? '');
        $data['admin_pass']    = $_POST['admin_pass'] ?? '';
        $data['admin_pass2']   = $_POST['admin_pass2'] ?? '';
        $data['base_url']      = rtrim(trim($_POST['base_url'] ?? ''), '/');
        $data['mail_host']     = trim($_POST['mail_host'] ?? '');
        $data['mail_port']     = trim($_POST['mail_port'] ?? '587');
        $data['mail_user']     = trim($_POST['mail_user'] ?? '');
        $data['mail_pass']     = $_POST['mail_pass'] ?? '';
        $data['mail_from']     = trim($_POST['mail_from'] ?? $data['admin_email']);
        $_SESSION['installer_data'] = $data;

        if (empty($data['admin_nome']) || empty($data['admin_cognome']) || empty($data['admin_email'])) {
            $error = 'Nome, cognome ed email amministratore sono obbligatori.';
            $step  = 3;
        } elseif (strlen($data['admin_pass']) < 8) {
            $error = 'La password deve essere di almeno 8 caratteri.';
            $step  = 3;
        } elseif (!preg_match('/[A-Z]/', $data['admin_pass'])) {
            $error = 'La password deve contenere almeno una lettera maiuscola.';
            $step  = 3;
        } elseif (!preg_match('/[0-9]/', $data['admin_pass'])) {
            $error = 'La password deve contenere almeno un numero.';
            $step  = 3;
        } elseif ($data['admin_pass'] !== $data['admin_pass2']) {
            $error = 'Le password non coincidono.';
            $step  = 3;
        } elseif (empty($data['base_url'])) {
            $error = 'L\'URL base dell\'applicazione è obbligatorio.';
            $step  = 3;
        } else {
            $step = 4; // Vai all'installazione
        }

    } elseif ($step === 4) {
        // INSTALLAZIONE EFFETTIVA
        $data = $_SESSION['installer_data'];
        try {
            // Connessione al DB
            $dsn = "mysql:host={$data['db_host']};port={$data['db_port']};dbname={$data['db_name']};charset=utf8mb4";
            $pdo = new PDO($dsn, $data['db_user'], $data['db_pass'],
                          [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

            // Esegui schema.sql
            installer_exec_sql($pdo, DB_SCHEMA);

            // Esegui seed_tariffe.sql (senza l'utente demo)
            installer_exec_sql($pdo, DB_SEED, skipUserInsert: true);

            // Crea utente admin
            $hash = password_hash($data['admin_pass'], PASSWORD_BCRYPT, ['cost' => 12]);
            $stmt = $pdo->prepare(
                'INSERT INTO utenti (nome, cognome, email, password_hash, ruolo, attivo)
                 VALUES (?,?,?,?,\'admin\',1)
                 ON DUPLICATE KEY UPDATE password_hash=VALUES(password_hash), ruolo=\'admin\', attivo=1'
            );
            $stmt->execute([$data['admin_nome'], $data['admin_cognome'], $data['admin_email'], $hash]);

            // Scrivi config.php
            installer_write_config($data);

            // Auto-elimina installer
            @unlink(__FILE__);

            $_SESSION['installer_data'] = [];
            session_destroy();
            $step = 5;

        } catch (Throwable $e) {
            $error = 'Errore durante l\'installazione: ' . $e->getMessage();
            $step  = 4;
        }
    }
}

// ── Funzioni helper ───────────────────────────────────────────────────────────

function installer_exec_sql(PDO $pdo, string $file, bool $skipUserInsert = false): void
{
    if (!file_exists($file)) {
        throw new RuntimeException("File SQL non trovato: $file");
    }
    $sql = file_get_contents($file);

    // Rimuovi commenti
    $sql = preg_replace('/--[^\n]*\n/', "\n", $sql);
    $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);

    // Rimuovi USE e CREATE DATABASE (gestiti dall'installer)
    $sql = preg_replace('/^\s*(USE|CREATE DATABASE)[^\;]+;/mi', '', $sql);

    // Salta INSERT utenti se richiesto (l'installer crea il suo utente admin)
    if ($skipUserInsert) {
        $sql = preg_replace('/INSERT\s+INTO\s+`?utenti`?[^;]+;/si', '', $sql);
    }

    // Esegui statement per statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    foreach ($statements as $stmt) {
        if (!empty($stmt)) {
            $pdo->exec($stmt);
        }
    }
}

function installer_write_config(array $d): void
{
    $basePath    = addslashes(__DIR__);
    $publicPath  = addslashes(__DIR__);
    $baseUrl     = addslashes($d['base_url']);
    $dbHost      = addslashes($d['db_host']);
    $dbPort      = addslashes($d['db_port']);
    $dbName      = addslashes($d['db_name']);
    $dbUser      = addslashes($d['db_user']);
    $dbPass      = addslashes($d['db_pass']);
    $mailHost    = addslashes($d['mail_host'] ?? '');
    $mailPort    = (int)($d['mail_port'] ?? 587);
    $mailUser    = addslashes($d['mail_user'] ?? '');
    $mailPass    = addslashes($d['mail_pass'] ?? '');
    $mailFrom    = addslashes($d['mail_from'] ?? $d['admin_email']);
    $adminEmail  = addslashes($d['admin_email']);
    $generated   = date('Y-m-d H:i:s');

    $config = <<<PHP
<?php
/**
 * Preventivo Commercialisti - Configurazione
 * Generato dall'installer il: {$generated}
 * Copyright (C) 2024 Alessandro Scapuzzi <dado70@gmail.com>
 * Licenza: GPL v3
 */

define('APP_NAME', 'Preventivo Commercialisti');
define('APP_VERSION', '1.0.0');
define('APP_AUTHOR', 'Alessandro Scapuzzi');
define('APP_EMAIL', 'dado70@gmail.com');
define('APP_LICENSE', 'GPL-3.0');

// --- Database ---
define('DB_HOST',    '{$dbHost}');
define('DB_PORT',    '{$dbPort}');
define('DB_NAME',    '{$dbName}');
define('DB_USER',    '{$dbUser}');
define('DB_PASS',    '{$dbPass}');
define('DB_CHARSET', 'utf8mb4');

// --- Percorsi ---
define('BASE_PATH',     '{$basePath}');
define('PUBLIC_PATH',   '{$publicPath}');
define('SRC_PATH',      BASE_PATH . '/src');
define('TEMPLATES_PATH',BASE_PATH . '/templates');
define('UPLOADS_PATH',  BASE_PATH . '/uploads');
define('LOGS_PATH',     BASE_PATH . '/logs');

// --- URL base (senza slash finale) ---
define('BASE_URL', '{$baseUrl}');

// --- Sessione ---
define('SESSION_NAME',     'prev_comm_session');
define('SESSION_LIFETIME', 7200);

// --- Email ---
define('MAIL_HOST',      '{$mailHost}');
define('MAIL_PORT',       {$mailPort});
define('MAIL_USERNAME',  '{$mailUser}');
define('MAIL_PASSWORD',  '{$mailPass}');
define('MAIL_FROM',      '{$mailFrom}');
define('MAIL_FROM_NAME', 'Preventivo Commercialisti');
define('MAIL_ENCRYPTION','tls');

// --- IVA ---
define('ALIQUOTA_IVA', 22.0);

// --- Token reset password ---
define('TOKEN_RESET_LIFETIME', 3600);

// --- Timezone ---
date_default_timezone_set('Europe/Rome');

// --- Debug (impostare false in produzione) ---
define('DEBUG_MODE', false);

if (DEBUG_MODE) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
    ini_set('log_errors', 1);
    ini_set('error_log', LOGS_PATH . '/error.log');
}
PHP;

    if (!is_dir(dirname(CONFIG_FILE))) {
        mkdir(dirname(CONFIG_FILE), 0755, true);
    }
    file_put_contents(CONFIG_FILE, $config);
    chmod(CONFIG_FILE, 0644);
}

function installer_check_requirements(): array
{
    $checks = [];

    // PHP version
    $checks[] = [
        'label'  => 'PHP ' . MIN_PHP . '+',
        'ok'     => version_compare(PHP_VERSION, MIN_PHP, '>='),
        'detail' => 'Versione attuale: ' . PHP_VERSION,
    ];

    // Estensioni
    foreach (['pdo', 'pdo_mysql', 'mbstring', 'json', 'openssl', 'fileinfo'] as $ext) {
        $checks[] = [
            'label'  => "Estensione PHP: $ext",
            'ok'     => extension_loaded($ext),
            'detail' => extension_loaded($ext) ? 'Disponibile' : 'MANCANTE - richiesta',
        ];
    }

    // Permessi scrittura
    foreach ([
        'config/'   => __DIR__ . '/config',
        'logs/'     => __DIR__ . '/logs',
        'uploads/'  => __DIR__ . '/uploads',
    ] as $label => $path) {
        if (!is_dir($path)) @mkdir($path, 0755, true);
        $checks[] = [
            'label'  => "Scrittura su $label",
            'ok'     => is_writable($path),
            'detail' => is_writable($path) ? 'OK' : 'PERMESSO NEGATO - usa chmod 755',
        ];
    }

    // File SQL presenti
    foreach (['database/schema.sql' => DB_SCHEMA, 'database/seed_tariffe.sql' => DB_SEED] as $lbl => $path) {
        $checks[] = [
            'label'  => "File: $lbl",
            'ok'     => file_exists($path),
            'detail' => file_exists($path) ? 'Trovato' : 'MANCANTE',
        ];
    }

    return $checks;
}

function installer_base_url_guess(): string
{
    $proto  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script = dirname($_SERVER['SCRIPT_NAME'] ?? '/');
    $script = ($script === '/') ? '' : $script;
    return $proto . '://' . $host . $script;
}

$checks     = installer_check_requirements();
$allOk      = array_reduce($checks, fn($c, $i) => $c && $i['ok'], true);
$guessedUrl = installer_base_url_guess();

?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Installer – Preventivo Commercialisti</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
body { background: linear-gradient(135deg,#0d6efd18,#0d6efd08); min-height:100vh; }
.installer-card { max-width: 720px; margin: 40px auto; }
.step-bar .step { flex:1; text-align:center; padding:8px 0; font-size:.82rem; border-bottom:3px solid #dee2e6; color:#6c757d; }
.step-bar .step.active { border-color:#0d6efd; color:#0d6efd; font-weight:600; }
.step-bar .step.done   { border-color:#198754; color:#198754; }
.req-item { display:flex; align-items:center; gap:10px; padding:6px 0; border-bottom:1px solid #f0f0f0; font-size:.9rem; }
.req-item:last-child { border:none; }
</style>
</head>
<body>
<div class="installer-card px-3">

  <div class="text-center my-4">
    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width:56px;height:56px;">
      <i class="bi bi-calculator fs-4"></i>
    </div>
    <h1 class="h4 fw-bold">Preventivo Commercialisti</h1>
    <p class="text-muted small">Installer v<?= INSTALLER_VERSION ?></p>
  </div>

  <!-- Step bar -->
  <div class="d-flex step-bar mb-4">
    <?php
    $steps = ['Requisiti','Database','Amministratore','Installazione','Completato'];
    foreach ($steps as $i => $lbl):
        $n = $i + 1;
        $cls = $n < $step ? 'done' : ($n === $step ? 'active' : '');
    ?>
    <div class="step <?= $cls ?>">
      <?php if ($n < $step): ?><i class="bi bi-check-circle-fill text-success"></i><?php else: ?><span class="fw-bold"><?= $n ?></span><?php endif; ?>
      <br><?= $lbl ?>
    </div>
    <?php endforeach; ?>
  </div>

  <?php if ($error): ?>
  <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <div class="card shadow-sm">
  <div class="card-body p-4">

  <?php if ($step === 1): ?>
  <!-- ═══════════════════════════════════════ STEP 1: REQUISITI -->
  <h5 class="fw-bold mb-3"><i class="bi bi-clipboard-check me-2"></i>Verifica requisiti di sistema</h5>
  <div class="mb-4">
    <?php foreach ($checks as $c): ?>
    <div class="req-item">
      <i class="bi bi-<?= $c['ok'] ? 'check-circle-fill text-success' : 'x-circle-fill text-danger' ?> fs-5"></i>
      <div>
        <span class="fw-semibold"><?= htmlspecialchars($c['label']) ?></span>
        <span class="text-muted ms-2 small"><?= htmlspecialchars($c['detail']) ?></span>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <?php if ($allOk): ?>
    <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Tutti i requisiti sono soddisfatti. Puoi procedere.</div>
    <form method="POST">
      <input type="hidden" name="step" value="2">
      <button class="btn btn-primary w-100">Avanti: Configurazione Database <i class="bi bi-arrow-right ms-1"></i></button>
    </form>
  <?php else: ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>Correggi i problemi segnalati in rosso prima di procedere.</div>
    <a href="installer.php" class="btn btn-secondary w-100"><i class="bi bi-arrow-clockwise me-1"></i>Ricontrolla</a>
  <?php endif; ?>

  <?php elseif ($step === 2): ?>
  <!-- ═══════════════════════════════════════ STEP 2: DATABASE -->
  <h5 class="fw-bold mb-3"><i class="bi bi-database me-2"></i>Configurazione Database</h5>
  <p class="text-muted small mb-3">Inserisci le credenziali del database MySQL/MariaDB. Il database verrà creato automaticamente se non esiste.</p>
  <form method="POST">
    <input type="hidden" name="step" value="2">
    <div class="row g-3">
      <div class="col-md-8">
        <label class="form-label fw-semibold">Host Database <span class="text-danger">*</span></label>
        <input type="text" name="db_host" class="form-control" value="<?= htmlspecialchars($data['db_host'] ?? 'localhost') ?>" required placeholder="localhost">
        <div class="form-text">Di solito "localhost" sui server condivisi</div>
      </div>
      <div class="col-md-4">
        <label class="form-label fw-semibold">Porta</label>
        <input type="number" name="db_port" class="form-control" value="<?= htmlspecialchars($data['db_port'] ?? '3306') ?>" placeholder="3306">
      </div>
      <div class="col-12">
        <label class="form-label fw-semibold">Nome Database <span class="text-danger">*</span></label>
        <input type="text" name="db_name" class="form-control" value="<?= htmlspecialchars($data['db_name'] ?? '') ?>" required placeholder="preventivo_comm">
        <div class="form-text">Il database sarà creato automaticamente se non esiste (l'utente deve avere i permessi)</div>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold">Utente Database <span class="text-danger">*</span></label>
        <input type="text" name="db_user" class="form-control" value="<?= htmlspecialchars($data['db_user'] ?? '') ?>" required placeholder="utente_db">
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold">Password Database</label>
        <div class="input-group">
          <input type="password" name="db_pass" id="db_pass" class="form-control" value="<?= htmlspecialchars($data['db_pass'] ?? '') ?>" placeholder="password database">
          <button type="button" class="btn btn-outline-secondary" onclick="togglePw('db_pass',this)"><i class="bi bi-eye"></i></button>
        </div>
      </div>
    </div>
    <div class="d-flex gap-2 mt-4">
      <a href="installer.php?step=1" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Indietro</a>
      <button class="btn btn-primary flex-grow-1">Testa connessione e prosegui <i class="bi bi-arrow-right ms-1"></i></button>
    </div>
  </form>

  <?php elseif ($step === 3): ?>
  <!-- ═══════════════════════════════════════ STEP 3: ADMIN + APP -->
  <h5 class="fw-bold mb-3"><i class="bi bi-person-gear me-2"></i>Account amministratore e configurazione</h5>
  <form method="POST">
    <input type="hidden" name="step" value="3">

    <h6 class="text-primary fw-semibold mt-2 mb-3 border-bottom pb-1">Account Amministratore</h6>
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label fw-semibold">Nome <span class="text-danger">*</span></label>
        <input type="text" name="admin_nome" class="form-control" value="<?= htmlspecialchars($data['admin_nome'] ?? '') ?>" required>
      </div>
      <div class="col-md-4">
        <label class="form-label fw-semibold">Cognome <span class="text-danger">*</span></label>
        <input type="text" name="admin_cognome" class="form-control" value="<?= htmlspecialchars($data['admin_cognome'] ?? '') ?>" required>
      </div>
      <div class="col-md-4">
        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
        <input type="email" name="admin_email" class="form-control" value="<?= htmlspecialchars($data['admin_email'] ?? '') ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
        <div class="input-group">
          <input type="password" name="admin_pass" id="adm_pass" class="form-control" required minlength="8">
          <button type="button" class="btn btn-outline-secondary" onclick="togglePw('adm_pass',this)"><i class="bi bi-eye"></i></button>
        </div>
        <div class="form-text">Min. 8 caratteri, 1 maiuscola, 1 numero</div>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold">Conferma Password <span class="text-danger">*</span></label>
        <div class="input-group">
          <input type="password" name="admin_pass2" id="adm_pass2" class="form-control" required>
          <button type="button" class="btn btn-outline-secondary" onclick="togglePw('adm_pass2',this)"><i class="bi bi-eye"></i></button>
        </div>
      </div>
    </div>

    <h6 class="text-primary fw-semibold mt-4 mb-3 border-bottom pb-1">Configurazione Applicazione</h6>
    <div class="mb-3">
      <label class="form-label fw-semibold">URL Base dell'applicazione <span class="text-danger">*</span></label>
      <input type="url" name="base_url" class="form-control" value="<?= htmlspecialchars($data['base_url'] ?? $guessedUrl) ?>" required>
      <div class="form-text">URL senza slash finale — es: <code>https://tuodominio.it</code> oppure <code>https://tuodominio.it/preventivo</code></div>
    </div>

    <h6 class="text-primary fw-semibold mt-4 mb-3 border-bottom pb-1">
      Email (per recupero password) <span class="badge bg-secondary fw-normal">Opzionale</span>
    </h6>
    <div class="row g-3">
      <div class="col-md-8">
        <label class="form-label">Server SMTP</label>
        <input type="text" name="mail_host" class="form-control" value="<?= htmlspecialchars($data['mail_host'] ?? '') ?>" placeholder="smtp.gmail.com">
      </div>
      <div class="col-md-4">
        <label class="form-label">Porta SMTP</label>
        <input type="number" name="mail_port" class="form-control" value="<?= htmlspecialchars($data['mail_port'] ?? '587') ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Utente SMTP</label>
        <input type="text" name="mail_user" class="form-control" value="<?= htmlspecialchars($data['mail_user'] ?? '') ?>" placeholder="user@gmail.com">
      </div>
      <div class="col-md-6">
        <label class="form-label">Password SMTP</label>
        <input type="password" name="mail_pass" class="form-control" value="">
      </div>
    </div>

    <div class="d-flex gap-2 mt-4">
      <a href="installer.php?step=2" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Indietro</a>
      <button class="btn btn-primary flex-grow-1">Avanti: Riepilogo <i class="bi bi-arrow-right ms-1"></i></button>
    </div>
  </form>

  <?php elseif ($step === 4): ?>
  <!-- ═══════════════════════════════════════ STEP 4: CONFERMA E INSTALLA -->
  <h5 class="fw-bold mb-3"><i class="bi bi-rocket-takeoff me-2"></i>Riepilogo – Pronto per l'installazione</h5>

  <table class="table table-sm table-bordered mb-4">
    <tbody>
      <tr><th class="table-light" style="width:40%">Database Host</th><td><?= htmlspecialchars($data['db_host'] . ':' . ($data['db_port'] ?? '3306')) ?></td></tr>
      <tr><th class="table-light">Nome Database</th><td><?= htmlspecialchars($data['db_name']) ?></td></tr>
      <tr><th class="table-light">Utente Database</th><td><?= htmlspecialchars($data['db_user']) ?></td></tr>
      <tr><th class="table-light">Amministratore</th><td><?= htmlspecialchars($data['admin_nome'] . ' ' . $data['admin_cognome']) ?> &lt;<?= htmlspecialchars($data['admin_email']) ?>&gt;</td></tr>
      <tr><th class="table-light">URL Applicazione</th><td><?= htmlspecialchars($data['base_url']) ?></td></tr>
      <tr><th class="table-light">Server Email</th><td><?= $data['mail_host'] ? htmlspecialchars($data['mail_host']) : '<span class="text-muted">Non configurato</span>' ?></td></tr>
    </tbody>
  </table>

  <div class="alert alert-warning">
    <i class="bi bi-shield-lock me-2"></i>
    <strong>Attenzione:</strong> verrà creato il database, importate le tariffe ANC e l'installer si auto-eliminerà al termine.
  </div>

  <form method="POST">
    <input type="hidden" name="step" value="4">
    <div class="d-flex gap-2">
      <a href="installer.php?step=3" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Indietro</a>
      <button class="btn btn-success flex-grow-1 fw-bold">
        <i class="bi bi-rocket-takeoff me-2"></i>Installa Preventivo Commercialisti
      </button>
    </div>
  </form>

  <?php elseif ($step === 5): ?>
  <!-- ═══════════════════════════════════════ STEP 5: COMPLETATO -->
  <div class="text-center py-3">
    <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:72px;height:72px;">
      <i class="bi bi-check-lg" style="font-size:2rem;"></i>
    </div>
    <h4 class="fw-bold text-success mb-2">Installazione completata!</h4>
    <p class="text-muted mb-4">L'applicazione è pronta. Il file <code>installer.php</code> è stato eliminato automaticamente.</p>

    <div class="alert alert-info text-start">
      <strong><i class="bi bi-person-circle me-2"></i>Credenziali di accesso:</strong><br>
      <span class="text-muted">Email:</span> <strong><?= htmlspecialchars($data['admin_email'] ?? '') ?></strong><br>
      <span class="text-muted">Password:</span> quella che hai impostato
    </div>

    <a href="index.php" class="btn btn-primary btn-lg w-100">
      <i class="bi bi-box-arrow-in-right me-2"></i>Accedi all'applicazione
    </a>
  </div>
  <?php endif; ?>

  </div><!-- /card-body -->
  </div><!-- /card -->

  <p class="text-center text-muted small mt-3">
    Preventivo Commercialisti &mdash; <a href="https://www.gnu.org/licenses/gpl-3.0.html" target="_blank" class="text-muted">GPL v3</a>
    &mdash; &copy; 2024 Alessandro Scapuzzi
  </p>
</div>

<script>
function togglePw(id, btn) {
    const el = document.getElementById(id);
    if (!el) return;
    el.type = el.type === 'password' ? 'text' : 'password';
    btn.innerHTML = el.type === 'password' ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
}
</script>
</body>
</html>
