<?php
use App\Core\Auth;
use App\Core\Session;
use App\Core\View;
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= View::e($pageTitle ?? APP_NAME) ?> &mdash; <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= View::asset('css/app.css') ?>">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="<?= View::url('dashboard') ?>">
      <i class="bi bi-calculator me-2"></i><?= APP_NAME ?>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/dashboard') || $_SERVER['REQUEST_URI'] === '/' ? 'active' : '' ?>" href="<?= View::url('dashboard') ?>">
            <i class="bi bi-speedometer2 me-1"></i>Dashboard
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/preventivi') ? 'active' : '' ?>" href="<?= View::url('preventivi') ?>">
            <i class="bi bi-file-text me-1"></i>Preventivi
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/clienti') ? 'active' : '' ?>" href="<?= View::url('clienti') ?>">
            <i class="bi bi-people me-1"></i>Clienti
          </a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?= str_contains($_SERVER['REQUEST_URI'], '/tariffe') || str_contains($_SERVER['REQUEST_URI'], '/pacchetti') ? 'active' : '' ?>" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-list-ul me-1"></i>Listino
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?= View::url('tariffe') ?>"><i class="bi bi-tag me-2"></i>Tariffe</a></li>
            <li><a class="dropdown-item" href="<?= View::url('pacchetti') ?>"><i class="bi bi-box me-2"></i>Pacchetti</a></li>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?= str_contains($_SERVER['REQUEST_URI'], '/studio') || str_contains($_SERVER['REQUEST_URI'], '/professionisti') ? 'active' : '' ?>" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-building me-1"></i>Studio
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?= View::url('studio') ?>"><i class="bi bi-building me-2"></i>Anagrafica Studio</a></li>
            <li><a class="dropdown-item" href="<?= View::url('professionisti') ?>"><i class="bi bi-person-badge me-2"></i>Professionisti</a></li>
          </ul>
        </li>
        <?php if (Auth::isAdmin()): ?>
        <li class="nav-item">
          <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/utenti') ? 'active' : '' ?>" href="<?= View::url('utenti') ?>">
            <i class="bi bi-people-fill me-1"></i>Utenti
          </a>
        </li>
        <?php endif; ?>
      </ul>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle me-1"></i><?= View::e(Auth::userName()) ?>
            <span class="badge bg-light text-primary ms-1 small"><?= View::e(Auth::userRole()) ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="<?= View::url('auth/change-password') ?>"><i class="bi bi-key me-2"></i>Cambia Password</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="<?= View::url('auth/logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container-fluid py-4 px-4">

  <?php if (Session::hasFlash('success')): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i><?= View::e(Session::flash('success')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php endif; ?>

  <?php if (Session::hasFlash('error')): ?>
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle me-2"></i><?= View::e(Session::flash('error')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php endif; ?>

  <?php if (Session::hasFlash('info')): ?>
  <div class="alert alert-info alert-dismissible fade show" role="alert">
    <i class="bi bi-info-circle me-2"></i><?= View::e(Session::flash('info')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php endif; ?>

  <?= $content ?>
</div>

<footer class="text-center text-muted small py-3 border-top bg-white mt-4">
  <?= APP_NAME ?> v<?= APP_VERSION ?> &mdash; &copy; <?= date('Y') ?> <?= APP_AUTHOR ?> &mdash;
  <a href="https://www.gnu.org/licenses/gpl-3.0.html" class="text-muted" target="_blank">Licenza GPL v3</a>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= View::asset('js/app.js') ?>"></script>
</body>
</html>
