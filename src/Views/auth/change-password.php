<?php
use App\Core\View;
?>
<div class="row justify-content-center">
    <div class="col-12" style="max-width: 500px;">

        <div class="d-flex align-items-center mb-4">
            <a href="<?= View::url('dashboard') ?>" class="btn btn-sm btn-outline-secondary me-3">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h1 class="h4 fw-bold mb-0">
                <i class="bi bi-key me-2 text-primary"></i>Cambia Password
            </h1>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">

                <form method="POST" action="<?= View::url('auth/change-password') ?>" novalidate id="changePasswordForm">
                    <input type="hidden" name="csrf_token" value="<?= View::e($csrf_token ?? '') ?>">

                    <div class="mb-3">
                        <label for="password_corrente" class="form-label fw-medium">Password corrente</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input
                                type="password"
                                class="form-control"
                                id="password_corrente"
                                name="password_corrente"
                                placeholder="La tua password attuale"
                                required
                                autofocus
                                autocomplete="current-password"
                            >
                            <button
                                class="btn btn-outline-secondary toggle-pw"
                                type="button"
                                data-target="password_corrente"
                                title="Mostra/nascondi"
                            ><i class="bi bi-eye"></i></button>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="mb-3">
                        <label for="password_nuova" class="form-label fw-medium">Nuova password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input
                                type="password"
                                class="form-control"
                                id="password_nuova"
                                name="password_nuova"
                                placeholder="Minimo 8 caratteri"
                                minlength="8"
                                required
                                autocomplete="new-password"
                            >
                            <button
                                class="btn btn-outline-secondary toggle-pw"
                                type="button"
                                data-target="password_nuova"
                                title="Mostra/nascondi"
                            ><i class="bi bi-eye"></i></button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password_conferma" class="form-label fw-medium">Conferma nuova password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input
                                type="password"
                                class="form-control"
                                id="password_conferma"
                                name="password_conferma"
                                placeholder="Ripeti la nuova password"
                                minlength="8"
                                required
                                autocomplete="new-password"
                            >
                        </div>
                        <div id="matchError" class="text-danger small mt-1 d-none">
                            <i class="bi bi-exclamation-circle me-1"></i>Le password non coincidono.
                        </div>
                    </div>

                    <div class="card bg-light border-0 mb-4 p-3">
                        <p class="small fw-semibold text-muted mb-2">
                            <i class="bi bi-shield-check me-1"></i>Requisiti password:
                        </p>
                        <ul class="small text-muted mb-0 ps-3">
                            <li id="req-length">Almeno <strong>8 caratteri</strong></li>
                            <li id="req-upper">Almeno <strong>1 lettera maiuscola</strong></li>
                            <li id="req-number">Almeno <strong>1 numero</strong></li>
                        </ul>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-check-lg me-2"></i>Salva nuova password
                        </button>
                        <a href="<?= View::url('dashboard') ?>" class="btn btn-outline-secondary">
                            Annulla
                        </a>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>

<script>
(function () {
    // Toggle password visibility
    document.querySelectorAll('.toggle-pw').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const targetId = this.dataset.target;
            const input    = document.getElementById(targetId);
            const icon     = this.querySelector('i');
            if (!input) return;
            const isPassword = input.type === 'password';
            input.type  = isPassword ? 'text' : 'password';
            icon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    });

    // Live requirement indicators
    const nuova    = document.getElementById('password_nuova');
    const reqLength = document.getElementById('req-length');
    const reqUpper  = document.getElementById('req-upper');
    const reqNumber = document.getElementById('req-number');

    function setReq(el, ok) {
        if (!el) return;
        el.style.color = ok ? '#198754' : '';
        el.style.fontWeight = ok ? 'bold' : '';
    }

    if (nuova) {
        nuova.addEventListener('input', function () {
            const v = this.value;
            setReq(reqLength, v.length >= 8);
            setReq(reqUpper,  /[A-Z]/.test(v));
            setReq(reqNumber, /[0-9]/.test(v));
        });
    }

    // Confirm match validation
    const form     = document.getElementById('changePasswordForm');
    const conferma = document.getElementById('password_conferma');
    const matchErr = document.getElementById('matchError');

    if (form) {
        form.addEventListener('submit', function (e) {
            if (nuova && conferma && nuova.value !== conferma.value) {
                e.preventDefault();
                conferma.classList.add('is-invalid');
                if (matchErr) matchErr.classList.remove('d-none');
            }
        });
    }

    if (conferma) {
        conferma.addEventListener('input', function () {
            if (nuova && nuova.value === this.value) {
                this.classList.remove('is-invalid');
                if (matchErr) matchErr.classList.add('d-none');
            }
        });
    }
})();
</script>
