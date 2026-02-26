<?php
/**
 * Preventivo Commercialisti - Configurazione
 * Copyright (C) 2024 Alessandro Scapuzzi <dado70@gmail.com>
 * Licenza: GPL v3
 */

define('APP_NAME', 'Preventivo Commercialisti');
define('APP_VERSION', '1.0.0');
define('APP_AUTHOR', 'Alessandro Scapuzzi');
define('APP_EMAIL', 'dado70@gmail.com');
define('APP_LICENSE', 'GPL-3.0');

// --- Database ---
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'preventivo_comm');
define('DB_USER', 'preventivo_user');
define('DB_PASS', 'CAMBIA_QUESTA_PASSWORD');
define('DB_CHARSET', 'utf8mb4');

// --- Percorsi ---
define('BASE_PATH', dirname(__DIR__));
define('PUBLIC_PATH', BASE_PATH . '/public');
define('SRC_PATH', BASE_PATH . '/src');
define('TEMPLATES_PATH', BASE_PATH . '/templates');
define('UPLOADS_PATH', PUBLIC_PATH . '/uploads');
define('LOGS_PATH', BASE_PATH . '/logs');

// --- URL base (senza slash finale) ---
define('BASE_URL', 'http://localhost/preventivo-commercialisti/public');

// --- Sessione ---
define('SESSION_NAME', 'prev_comm_session');
define('SESSION_LIFETIME', 7200); // 2 ore

// --- Email (per recupero password) ---
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'dado70@gmail.com');
define('MAIL_PASSWORD', 'CAMBIA_APP_PASSWORD');
define('MAIL_FROM', 'dado70@gmail.com');
define('MAIL_FROM_NAME', 'Preventivo Commercialisti');
define('MAIL_ENCRYPTION', 'tls');

// --- IVA ---
define('ALIQUOTA_IVA', 22.0);

// --- Token reset password ---
define('TOKEN_RESET_LIFETIME', 3600); // 1 ora (GDPR)

// --- Timezone ---
date_default_timezone_set('Europe/Rome');

// --- Modalità debug (false in produzione) ---
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
    ini_set('log_errors', 1);
    ini_set('error_log', LOGS_PATH . '/error.log');
}
