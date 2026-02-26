<?php
/**
 * Preventivo Commercialisti - Entry Point (flat deployment)
 * Copyright (C) 2024 Alessandro Scapuzzi <dado70@gmail.com>
 * Licenza: GPL v3
 */

// Se esiste l'installer e config non è configurato, reindirizza all'installer
if (file_exists(__DIR__ . '/installer.php') && (
    !file_exists(__DIR__ . '/config/config.php') ||
    str_contains(file_get_contents(__DIR__ . '/config/config.php'), 'CAMBIA_QUESTA_PASSWORD')
)) {
    header('Location: installer.php');
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/config.php';

use App\Core\Session;
use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\TariffeController;
use App\Controllers\PacchettiController;
use App\Controllers\StudioController;
use App\Controllers\ProfessionistiController;
use App\Controllers\ClientiController;
use App\Controllers\PreventiviController;
use App\Controllers\UtentiController;
use App\Controllers\ExportController;

Session::start();

$router = new Router();

// Auth
$router->get('/auth/login',                  [AuthController::class, 'loginForm']);
$router->post('/auth/login',                 [AuthController::class, 'login']);
$router->get('/auth/logout',                 [AuthController::class, 'logout']);
$router->get('/auth/forgot-password',        [AuthController::class, 'forgotForm']);
$router->post('/auth/forgot-password',       [AuthController::class, 'forgotSend']);
$router->get('/auth/reset-password',         [AuthController::class, 'resetForm']);
$router->post('/auth/reset-password',        [AuthController::class, 'resetSave']);
$router->get('/auth/change-password',        [AuthController::class, 'changeForm']);
$router->post('/auth/change-password',       [AuthController::class, 'changeSave']);

// Dashboard
$router->get('/',                            [DashboardController::class, 'index']);
$router->get('/dashboard',                   [DashboardController::class, 'index']);

// Tariffe
$router->get('/tariffe',                     [TariffeController::class, 'index']);
$router->get('/tariffe/create',              [TariffeController::class, 'create']);
$router->post('/tariffe/create',             [TariffeController::class, 'store']);
$router->get('/tariffe/{id}/edit',           [TariffeController::class, 'edit']);
$router->post('/tariffe/{id}/edit',          [TariffeController::class, 'update']);
$router->post('/tariffe/{id}/delete',        [TariffeController::class, 'delete']);

// Pacchetti
$router->get('/pacchetti',                   [PacchettiController::class, 'index']);
$router->get('/pacchetti/create',            [PacchettiController::class, 'create']);
$router->post('/pacchetti/create',           [PacchettiController::class, 'store']);
$router->get('/pacchetti/{id}/edit',         [PacchettiController::class, 'edit']);
$router->post('/pacchetti/{id}/edit',        [PacchettiController::class, 'update']);
$router->post('/pacchetti/{id}/delete',      [PacchettiController::class, 'delete']);

// Studio
$router->get('/studio',                      [StudioController::class, 'index']);
$router->post('/studio',                     [StudioController::class, 'save']);

// Professionisti
$router->get('/professionisti',              [ProfessionistiController::class, 'index']);
$router->get('/professionisti/create',       [ProfessionistiController::class, 'create']);
$router->post('/professionisti/create',      [ProfessionistiController::class, 'store']);
$router->get('/professionisti/{id}/edit',    [ProfessionistiController::class, 'edit']);
$router->post('/professionisti/{id}/edit',   [ProfessionistiController::class, 'update']);
$router->post('/professionisti/{id}/delete', [ProfessionistiController::class, 'delete']);

// Clienti
$router->get('/clienti',                     [ClientiController::class, 'index']);
$router->get('/clienti/create',              [ClientiController::class, 'create']);
$router->post('/clienti/create',             [ClientiController::class, 'store']);
$router->get('/clienti/{id}/edit',           [ClientiController::class, 'edit']);
$router->post('/clienti/{id}/edit',          [ClientiController::class, 'update']);
$router->post('/clienti/{id}/delete',        [ClientiController::class, 'delete']);
$router->get('/clienti/search',              [ClientiController::class, 'search']);

// Preventivi
$router->get('/preventivi',                  [PreventiviController::class, 'index']);
$router->get('/preventivi/create',           [PreventiviController::class, 'create']);
$router->post('/preventivi/create',          [PreventiviController::class, 'store']);
$router->get('/preventivi/{id}',             [PreventiviController::class, 'show']);
$router->get('/preventivi/{id}/edit',        [PreventiviController::class, 'edit']);
$router->post('/preventivi/{id}/edit',       [PreventiviController::class, 'update']);
$router->post('/preventivi/{id}/delete',     [PreventiviController::class, 'delete']);
$router->post('/preventivi/{id}/stato',      [PreventiviController::class, 'updateStato']);

// Export
$router->get('/preventivi/{id}/pdf',         [ExportController::class, 'pdf']);
$router->get('/preventivi/{id}/ods',         [ExportController::class, 'ods']);

// Utenti
$router->get('/utenti',                      [UtentiController::class, 'index']);
$router->get('/utenti/create',               [UtentiController::class, 'create']);
$router->post('/utenti/create',              [UtentiController::class, 'store']);
$router->get('/utenti/{id}/edit',            [UtentiController::class, 'edit']);
$router->post('/utenti/{id}/edit',           [UtentiController::class, 'update']);
$router->post('/utenti/{id}/delete',         [UtentiController::class, 'delete']);

// API Ajax
$router->get('/api/tariffe',                 [TariffeController::class, 'apiList']);
$router->get('/api/cedolini-calcolo',        [TariffeController::class, 'apiCedolini']);
$router->get('/api/pacchetti',               [PacchettiController::class, 'apiList']);

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
