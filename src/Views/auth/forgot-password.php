<?php
use App\Core\View;
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recupero Password &mdash; <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

<main class="flex-grow-1 d-flex align-items-center justify-content-center py-5">
    <div class="w-100" style="max-width: 420px; padding: 0 1rem;">

        <div class="text-center mb-4">
            <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                 style="width: 64px; height: 64px;">
                <i class="bi bi-key fs-3"></i>
            </div>
            <h1 class="h4 fw-bold text-dark"><?= APP_NAME ?></h1>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">

                <h2 class="h5 fw-semibold mb-1 text-center">Recupero Password</h2>
                <p class="text-muted small text-center mb-4">
                    Inserisci la tua email, riceverai le istruzioni per reimpostare la password.
                </p>

                <?php if (!empty($flash_info)): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle me-2"></i><?= View::e($flash_info) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <?php if (!empty($flash_error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i><?= View::e($flash_error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <form method="POST" action="<?= View::url('auth/forgot-password') ?>" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= View::e($csrf_token ?? '') ?>">

                    <div class="mb-4">
                        <label for="email" class="form-label fw-medium">Indirizzo email</label>
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

                    <div class="d-grid">
                        <button type="submit" class="btn btn-warning btn-lg fw-semibold">
                            <i class="bi bi-send me-2"></i>Invia istruzioni
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
</body>
</html>
