<?php
use App\Core\View;
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Accedi &mdash; <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0d6efd11 0%, #0d6efd22 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body class="bg-light d-flex flex-column min-vh-100">

<main class="flex-grow-1 d-flex align-items-center justify-content-center py-5">
    <div class="w-100" style="max-width: 400px; padding: 0 1rem;">

        <div class="text-center mb-4">
            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                 style="width: 64px; height: 64px;">
                <i class="bi bi-calculator fs-3"></i>
            </div>
            <h1 class="h4 fw-bold text-dark"><?= APP_NAME ?></h1>
            <p class="text-muted small">Gestione preventivi per studi commercialisti</p>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">

                <h2 class="h5 fw-semibold mb-4 text-center">Accedi al tuo account</h2>

                <?php if (!empty($flash_error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i><?= View::e($flash_error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <?php if (!empty($flash_info)): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle me-2"></i><?= View::e($flash_info) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <form method="POST" action="<?= View::url('auth/login') ?>" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= View::e($csrf_token ?? '') ?>">

                    <div class="mb-3">
                        <label for="email" class="form-label fw-medium">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input
                                type="email"
                                class="form-control"
                                id="email"
                                name="email"
                                value="<?= View::e($email ?? '') ?>"
                                placeholder="nome@studio.it"
                                required
                                autofocus
                                autocomplete="email"
                            >
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-medium">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input
                                type="password"
                                class="form-control"
                                id="password"
                                name="password"
                                placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                                required
                                autocomplete="current-password"
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

                    <div class="mb-3 d-flex align-items-center justify-content-between">
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="remember"
                                name="remember"
                                value="1"
                            >
                            <label class="form-check-label text-muted small" for="remember">
                                Ricordami
                            </label>
                        </div>
                        <a href="<?= View::url('auth/forgot-password') ?>" class="small text-primary text-decoration-none">
                            Password dimenticata?
                        </a>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Accedi
                        </button>
                    </div>
                </form>

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
})();
</script>
</body>
</html>
