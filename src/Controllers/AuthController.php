<?php
/**
 * Preventivo Commercialisti - Controller Auth
 * Copyright (C) 2024 Alessandro Scapuzzi <dado70@gmail.com>
 * Licenza: GPL v3
 */

namespace App\Controllers;

use App\Core\Auth;
use App\Core\View;
use App\Core\Session;
use App\Models\Utente;

class AuthController
{
    /**
     * Mostra il form di login.
     * Se l'utente è già autenticato lo reindirizza alla dashboard.
     * Genera un nuovo token CSRF e lo salva in sessione.
     */
    public function loginForm(): void
    {
        if (Auth::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $csrfToken = bin2hex(random_bytes(32));
        Session::set('csrf_token', $csrfToken);

        View::render('auth/login', ['csrfToken' => $csrfToken], false);
    }

    /**
     * Gestisce il POST del form di login.
     * Verifica il token CSRF, autentica l'utente e reindirizza.
     */
    public function login(): void
    {
        // Verifica CSRF
        if (
            empty($_POST['csrf_token']) ||
            $_POST['csrf_token'] !== Session::get('csrf_token')
        ) {
            Session::flash('error', 'Richiesta non valida. Riprova.');
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (Auth::login($email, $password)) {
            // Invalida il token CSRF dopo il login riuscito
            Session::delete('csrf_token');
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        Session::flash('error', 'Credenziali non valide oppure account disabilitato.');
        header('Location: ' . BASE_URL . '/auth/login');
        exit;
    }

    /**
     * Effettua il logout e reindirizza alla pagina di login.
     */
    public function logout(): void
    {
        Auth::logout();
        header('Location: ' . BASE_URL . '/auth/login');
        exit;
    }

    /**
     * Mostra il form per richiedere il recupero password.
     */
    public function forgotForm(): void
    {
        View::render('auth/forgot-password', [], false);
    }

    /**
     * Gestisce il POST del form di recupero password.
     * Invia le istruzioni all'indirizzo email indicato (se esiste).
     */
    public function forgotSend(): void
    {
        $email = trim($_POST['email'] ?? '');

        if ($email !== '') {
            Auth::requestPasswordReset($email);
        }

        // Messaggio generico: non rivela se l'email è registrata (GDPR)
        Session::flash('info', "Se l'indirizzo email è registrato, riceverai a breve le istruzioni per reimpostare la password.");
        header('Location: ' . BASE_URL . '/auth/forgot-password');
        exit;
    }

    /**
     * Mostra il form di reset password, leggendo il token dall'URL.
     */
    public function resetForm(): void
    {
        $token = trim($_GET['token'] ?? '');

        View::render('auth/reset-password', ['token' => $token], false);
    }

    /**
     * Gestisce il POST del form di reset password.
     * Valida i requisiti della nuova password e chiama Auth::resetPassword().
     */
    public function resetSave(): void
    {
        $token           = trim($_POST['token'] ?? '');
        $password        = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        // Validazione: le due password devono corrispondere
        if ($password !== $passwordConfirm) {
            Session::flash('error', 'Le password non corrispondono.');
            header('Location: ' . BASE_URL . '/auth/reset-password?token=' . urlencode($token));
            exit;
        }

        // Validazione requisiti password
        $errore = $this->validaPassword($password);
        if ($errore !== null) {
            Session::flash('error', $errore);
            header('Location: ' . BASE_URL . '/auth/reset-password?token=' . urlencode($token));
            exit;
        }

        if (Auth::resetPassword($token, $password)) {
            Session::flash('success', 'Password reimpostata con successo. Puoi ora effettuare il login.');
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        Session::flash('error', 'Il link di recupero non è valido o è scaduto. Richiedi un nuovo link.');
        header('Location: ' . BASE_URL . '/auth/forgot-password');
        exit;
    }

    /**
     * Mostra il form per cambiare la password (utente loggato).
     */
    public function changeForm(): void
    {
        Auth::requireLogin();

        View::render('auth/change-password', ['pageTitle' => 'Cambia Password']);
    }

    /**
     * Gestisce il POST del form di cambio password (utente loggato).
     * Verifica la vecchia password e valida i requisiti della nuova.
     */
    public function changeSave(): void
    {
        Auth::requireLogin();

        $vecchiaPassword = $_POST['vecchia_password'] ?? '';
        $nuovaPassword   = $_POST['nuova_password'] ?? '';
        $confermaPassword = $_POST['conferma_password'] ?? '';

        // Verifica la vecchia password
        $utente = Utente::findById(Auth::userId());
        if (!$utente || !password_verify($vecchiaPassword, $utente['password_hash'])) {
            Session::flash('error', 'La password attuale non è corretta.');
            header('Location: ' . BASE_URL . '/auth/change-password');
            exit;
        }

        // Validazione: le due nuove password devono corrispondere
        if ($nuovaPassword !== $confermaPassword) {
            Session::flash('error', 'Le nuove password non corrispondono.');
            header('Location: ' . BASE_URL . '/auth/change-password');
            exit;
        }

        // Validazione requisiti nuova password
        $errore = $this->validaPassword($nuovaPassword);
        if ($errore !== null) {
            Session::flash('error', $errore);
            header('Location: ' . BASE_URL . '/auth/change-password');
            exit;
        }

        Utente::changePassword(Auth::userId(), $nuovaPassword);

        Session::flash('success', 'Password aggiornata con successo.');
        header('Location: ' . BASE_URL . '/auth/change-password');
        exit;
    }

    // -------------------------------------------------------------------------
    // Metodi privati di supporto
    // -------------------------------------------------------------------------

    /**
     * Valida i requisiti minimi della password.
     * Restituisce un messaggio di errore oppure null se la password è valida.
     *
     * Requisiti: almeno 8 caratteri, una lettera maiuscola, un numero.
     */
    private function validaPassword(string $password): ?string
    {
        if (strlen($password) < 8) {
            return 'La password deve essere di almeno 8 caratteri.';
        }

        if (!preg_match('/[A-Z]/', $password)) {
            return 'La password deve contenere almeno una lettera maiuscola.';
        }

        if (!preg_match('/[0-9]/', $password)) {
            return 'La password deve contenere almeno un numero.';
        }

        return null;
    }
}
