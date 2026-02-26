<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 &mdash; Pagina non trovata</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
        }
        .error-code {
            font-size: 7rem;
            font-weight: 900;
            line-height: 1;
            background: linear-gradient(135deg, #0d6efd, #6610f2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .error-icon {
            font-size: 4rem;
            color: #adb5bd;
        }
        .card-error {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 1rem 3rem rgba(0,0,0,.1);
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-sm-8 col-md-6 col-lg-5">
            <div class="card card-error text-center p-5">
                <div class="card-body">

                    <!-- Icona -->
                    <div class="mb-3">
                        <i class="bi bi-compass error-icon"></i>
                    </div>

                    <!-- Codice 404 -->
                    <div class="error-code mb-3">404</div>

                    <!-- Titolo -->
                    <h1 class="h3 fw-bold text-dark mb-2">Pagina non trovata</h1>

                    <!-- Sottotitolo -->
                    <p class="text-muted mb-4">
                        La pagina che stai cercando non esiste o &egrave; stata spostata.
                    </p>

                    <!-- Pulsanti -->
                    <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
                        <a href="<?php echo defined('BASE_URL') ? BASE_URL . '/dashboard' : '/dashboard'; ?>"
                           class="btn btn-primary px-4">
                            <i class="bi bi-speedometer2 me-2"></i>Torna alla Dashboard
                        </a>
                        <button type="button" class="btn btn-outline-secondary px-4"
                                onclick="history.back()">
                            <i class="bi bi-arrow-left me-2"></i>Indietro
                        </button>
                    </div>

                </div>

                <div class="card-footer bg-transparent border-0 text-muted small pt-0">
                    Se il problema persiste, contatta l'amministratore di sistema.
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
