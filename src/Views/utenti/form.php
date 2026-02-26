<?php
use App\Core\View;

$isNew  = empty($utente['id']);
$title  = $isNew ? 'Nuovo Utente' : 'Modifica: ' . ($utente['nome'] ?? '') . ' ' . ($utente['cognome'] ?? '');
$action = $isNew ? View::url('utenti/store') : View::url('utenti/' . (int)($utente['id']) . '/update');

$v = function (string $key) use ($utente): string {
    return View::e($utente[$key] ?? '');
};
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="bi bi-<?= $isNew ? 'person-plus' : 'person-gear' ?> me-2 text-primary"></i>
        <?= View::e($title) ?>
    </h1>
    <a href="<?= View::url('utenti') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Torna agli Utenti
    </a>
</div>

<div class="card shadow-sm" style="max-width: 680px;">
    <div class="card-body">

        <form action="<?= $action ?>" method="POST" novalidate>
            <?php if (!$isNew): ?>
            <input type="hidden" name="_method" value="PUT">
            <?php endif; ?>

            <div class="row g-3">

                <!-- Nome -->
                <div class="col-md-6">
                    <label for="nome" class="form-label">Nome <span class="text-danger">*</span></label>
                    <input type="text" id="nome" name="nome" class="form-control"
                           value="<?= $v('nome') ?>" required maxlength="100"
                           placeholder="Nome">
                </div>

                <!-- Cognome -->
                <div class="col-md-6">
                    <label for="cognome" class="form-label">Cognome <span class="text-danger">*</span></label>
                    <input type="text" id="cognome" name="cognome" class="form-control"
                           value="<?= $v('cognome') ?>" required maxlength="100"
                           placeholder="Cognome">
                </div>

                <!-- Email -->
                <div class="col-12">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" id="email" name="email" class="form-control"
                               value="<?= $v('email') ?>" required maxlength="100"
                               placeholder="utente@studio.it">
                    </div>
                </div>

                <!-- Ruolo -->
                <div class="col-md-6">
                    <label for="ruolo" class="form-label">Ruolo <span class="text-danger">*</span></label>
                    <select id="ruolo" name="ruolo" class="form-select" required>
                        <option value="">-- Seleziona --</option>
                        <option value="admin"
                            <?= ($utente['ruolo'] ?? '') === 'admin' ? 'selected' : '' ?>>
                            <i class="bi bi-shield-fill"></i> Admin
                        </option>
                        <option value="operatore"
                            <?= ($utente['ruolo'] ?? '') === 'operatore' ? 'selected' : '' ?>>
                            Operatore
                        </option>
                    </select>
                    <div class="form-text">
                        <strong>Admin</strong>: accesso completo.
                        <strong>Operatore</strong>: accesso limitato (no configurazione).
                    </div>
                </div>

                <!-- Attivo -->
                <div class="col-md-6 d-flex align-items-end pb-1">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch"
                               id="attivo" name="attivo" value="1"
                               <?= ($isNew || !empty($utente['attivo'])) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="attivo">
                            Utente attivo (pu&ograve; accedere al sistema)
                        </label>
                    </div>
                </div>

                <!-- Separatore password -->
                <div class="col-12">
                    <hr class="my-1">
                    <h6 class="text-muted small text-uppercase">
                        <i class="bi bi-key me-1"></i>Password
                    </h6>
                </div>

                <?php if (!$isNew): ?>
                <div class="col-12">
                    <div class="alert alert-info py-2 mb-0 small">
                        <i class="bi bi-info-circle me-2"></i>
                        Lascia vuoto il campo password per non modificarla.
                    </div>
                </div>
                <?php endif; ?>

                <!-- Password -->
                <div class="col-md-6">
                    <label for="password" class="form-label">
                        Password
                        <?php if ($isNew): ?>
                        <span class="text-danger">*</span>
                        <?php endif; ?>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" id="password" name="password" class="form-control"
                               <?= $isNew ? 'required' : '' ?>
                               minlength="8"
                               autocomplete="new-password"
                               placeholder="<?= $isNew ? 'Min. 8 caratteri' : 'Lascia vuoto per non cambiare' ?>">
                        <button class="btn btn-outline-secondary" type="button" id="btnTogglePwd"
                                title="Mostra/Nascondi password">
                            <i class="bi bi-eye" id="iconPwd"></i>
                        </button>
                    </div>
                </div>

                <!-- Conferma Password -->
                <div class="col-md-6">
                    <label for="password_confirm" class="form-label">
                        Conferma Password
                        <?php if ($isNew): ?>
                        <span class="text-danger">*</span>
                        <?php endif; ?>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" id="password_confirm" name="password_confirm" class="form-control"
                               <?= $isNew ? 'required' : '' ?>
                               autocomplete="new-password"
                               placeholder="Ripeti la password">
                    </div>
                    <div id="pwdMismatch" class="invalid-feedback" style="display:none;">
                        Le password non coincidono.
                    </div>
                </div>

            </div><!-- /.row -->

            <hr class="my-4">

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4" id="btnSalva">
                    <i class="bi bi-floppy me-1"></i>Salva
                </button>
                <a href="<?= View::url('utenti') ?>" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-x-lg me-1"></i>Annulla
                </a>
            </div>

        </form>
    </div>
</div>

<script>
(function () {
    // Toggle visibilità password
    const btnToggle = document.getElementById('btnTogglePwd');
    const inputPwd  = document.getElementById('password');
    const iconPwd   = document.getElementById('iconPwd');

    btnToggle.addEventListener('click', function () {
        const isText = inputPwd.type === 'text';
        inputPwd.type = isText ? 'password' : 'text';
        iconPwd.className = isText ? 'bi bi-eye' : 'bi bi-eye-slash';
    });

    // Verifica corrispondenza password
    const inputConfirm = document.getElementById('password_confirm');
    const pwdMismatch  = document.getElementById('pwdMismatch');
    const btnSalva     = document.getElementById('btnSalva');

    function checkPasswords() {
        const pwd     = inputPwd.value;
        const confirm = inputConfirm.value;

        if (confirm && pwd !== confirm) {
            inputConfirm.classList.add('is-invalid');
            pwdMismatch.style.display = 'block';
        } else {
            inputConfirm.classList.remove('is-invalid');
            pwdMismatch.style.display = 'none';
        }
    }

    inputPwd.addEventListener('input', checkPasswords);
    inputConfirm.addEventListener('input', checkPasswords);

    document.querySelector('form').addEventListener('submit', function (e) {
        const pwd     = inputPwd.value;
        const confirm = inputConfirm.value;
        if (pwd && pwd !== confirm) {
            e.preventDefault();
            inputConfirm.classList.add('is-invalid');
            pwdMismatch.style.display = 'block';
            inputConfirm.focus();
        }
    });
})();
</script>
