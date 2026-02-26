<?php
use App\Core\View;
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Imposta nuova password &mdash; <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

<main class="flex-grow-1 d-flex align-items-center justify-content-center py-5">
    <div class="w-100" style="max-width: 440px; padding: 0 1rem;">

        <div class="text-center mb-4">
            <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                 style="width: 64px; height: 64px;">
                <i class="bi bi-shield-lock fs-3"></i>
            </div>
            <h1 class="h4 fw-bold text-dark"><?= APP_NAME ?></h1>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">

                <h2 class="h5 fw-semibold mb-1 text-center">Imposta nuova password</h2>
                <p class="text-muted small text-center mb-4">
                    Scegli una password sicura per il tuo account.
                </p>

                <?php if (!empty($flash_error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i><?= View::e($flash_error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <form method="POST" action="<?= View::url('auth/reset-password') ?>" novalidate id="resetForm">
                    <input type="hidden" name="csrf_token" value="<?= View::e($csrf_token ?? '') ?>">
                    <input type="hidden" name="token" value="<?= View::e($token ?? '') ?>">

                    <div class="mb-3">
                        <label for="password" class="form-label fw-medium">Nuova password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input
                                type="password"
                                class="form-control"
                                id="password"
                                name="password"
                                placeholder="Minimo 8 caratteri"
                                minlength="8"
                                required
                                autofocus
                                autocomplete="new-password"
                            >
                            <button
                                class="btn btn-outline-secondary"
                                type="button"
                                id="togglePassword"
                                title="Mostra/nascondi password"
                            >
                                <i class="bi bi-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirm" class="form-label fw-medium">Conferma password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input
                                type="password"
                                class="form-control"
                                id="password_confirm"
                                name="password_confirm"
                                placeholder="Ripeti la nuova password"
                                minlength="8"
                                required
                                autocomplete="new-password"
                            >
                        </div>
                        <div id="matchError" class="invalid-feedback d-none">
                            Le password non coincidono.
                        </div>
                    </div>

                    <div class="card bg-light border-0 mb-4 p-3">
                        <p class="small fw-semibold text-muted mb-2">
                            <i class="bi bi-info-circle me-1"></i>Requisiti password:
                        </p>
                        <ul class="small text-muted mb-0 ps-3">
                            <li id="req-length">Almeno <strong>8 caratteri</strong></li>
                            <li id="req-upper">Almeno <strong>1 lettera maiuscola</strong></li>
                            <li id="req-number">Almeno <strong>1 numero</strong></li>
                        </ul>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success btn-lg fw-semibold">
                            <i class="bi bi-check-lg me-2"></i>Salva nuova password
                        </button>
                    </div>
                </form>

                <hr class="my-4">

                <div class="text-center">
                    <a href="<?= View::url('auth/login') ?>" class="text-decoration-none text-muted small">
                        <i class="bi bi-arrow-left me-1"></i>Torna al login
                    </a>
                </div>

            </div>
        </div>

    </div>
</main>

<footer class="text-center text-muted py-3 border-top bg-white small">
    <?= APP_NAME ?> v<?= APP_VERSION ?> &mdash;
    <a href="https://www.gnu.org/licenses/gpl-3.0.html" class="text-muted text-decoration-none" target="_blank">GPL v3</a>
    &mdash; <?= APP_AUTHOR ?>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    // Toggle visibility
    const btn  = document.getElementById('togglePassword');
    const inp  = document.getElementById('password');
    const icon = document.getElementById('togglePasswordIcon');
    if (btn) {
        btn.addEventListener('click', function () {
            const isPassword = inp.type === 'password';
            inp.type = isPassword ? 'text' : 'password';
            icon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    }

    // Live requirement indicators
    const reqLength = document.getElementById('req-length');
    const reqUpper  = document.getElementById('req-upper');
    const reqNumber = document.getElementById('req-number');

    function updateReqs(val) {
        toggle(reqLength, val.length >= 8);
        toggle(reqUpper,  /[A-Z]/.test(val));
        toggle(reqNumber, /[0-9]/.test(val));
    }

    function toggle(el, ok) {
        if (!el) return;
        el.style.color    = ok ? '#198754' : '';
        el.style.fontWeight = ok ? 'bold' : '';
    }

    if (inp) {
        inp.addEventListener('input', function () { updateReqs(this.value); });
    }

    // Confirm match validation
    const form    = document.getElementById('resetForm');
    const confirm = document.getElementById('password_confirm');
    const matchErr = document.getElementById('matchError');

    if (form) {
        form.addEventListener('submit', function (e) {
            if (inp.value !== confirm.value) {
                e.preventDefault();
                confirm.classList.add('is-invalid');
                if (matchErr) matchErr.classList.remove('d-none');
            }
        });
    }

    if (confirm) {
        confirm.addEventListener('input', function () {
            if (inp.value === this.value) {
                this.classList.remove('is-invalid');
                if (matchErr) matchErr.classList.add('d-none');
            }
        });
    }
})();
</script>
</body>
</html>
