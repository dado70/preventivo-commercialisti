<?php
/**
 * Preventivo Commercialisti - Auth (GDPR-compliant)
 * Copyright (C) 2024 Alessandro Scapuzzi <dado70@gmail.com>
 * Licenza: GPL v3
 */

namespace App\Core;

use App\Models\Utente;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;

class Auth
{
    private static ?array $currentUser = null;

    /**
     * Tenta il login. Restituisce true se riuscito.
     */
    public static function login(string $email, string $password): bool
    {
        $utente = Utente::findByEmail($email);

        if (!$utente || !$utente['attivo']) {
            // Ritardo per prevenire timing attack
            password_verify('dummy', '$2y$12$invalidhashtopreventtimingattacks');
            return false;
        }

        if (!password_verify($password, $utente['password_hash'])) {
            return false;
        }

        // Regenera ID sessione per prevenire session fixation
        Session::regenerate();

        Session::set('user_id', $utente['id']);
        Session::set('user_email', $utente['email']);
        Session::set('user_nome', $utente['nome'] . ' ' . $utente['cognome']);
        Session::set('user_ruolo', $utente['ruolo']);
        Session::set('login_time', time());

        // Aggiorna ultimo accesso
        Utente::updateLastAccess($utente['id']);

        self::$currentUser = $utente;
        return true;
    }

    public static function logout(): void
    {
        Session::destroy();
        self::$currentUser = null;
    }

    public static function isLoggedIn(): bool
    {
        if (!Session::has('user_id')) return false;

        // Controlla scadenza sessione
        $loginTime = Session::get('login_time', 0);
        if (time() - $loginTime > SESSION_LIFETIME) {
            self::logout();
            return false;
        }

        return true;
    }

    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            Session::flash('error', 'Devi effettuare il login per accedere.');
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    public static function requireAdmin(): void
    {
        self::requireLogin();
        if (Session::get('user_ruolo') !== 'admin') {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
    }

    public static function isAdmin(): bool
    {
        return Session::get('user_ruolo') === 'admin';
    }

    public static function userId(): ?int
    {
        return Session::get('user_id');
    }

    public static function userName(): string
    {
        return Session::get('user_nome', '');
    }

    public static function userRole(): string
    {
        return Session::get('user_ruolo', '');
    }

    /**
     * Invia email con link di recupero password (GDPR: token scade in 1h).
     */
    public static function requestPasswordReset(string $email): bool
    {
        $utente = Utente::findByEmail($email);
        if (!$utente || !$utente['attivo']) {
            // Non rivelare se l'email esiste
            return true;
        }

        $token = bin2hex(random_bytes(32));
        $scadenza = date('Y-m-d H:i:s', time() + TOKEN_RESET_LIFETIME);

        Utente::setResetToken($utente['id'], $token, $scadenza);

        $link = BASE_URL . '/auth/reset-password?token=' . $token;

        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = MAIL_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = MAIL_USERNAME;
            $mail->Password   = MAIL_PASSWORD;
            $mail->SMTPSecure = MAIL_ENCRYPTION;
            $mail->Port       = MAIL_PORT;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
            $mail->addAddress($email, $utente['nome'] . ' ' . $utente['cognome']);

            $mail->Subject = APP_NAME . ' - Recupero password';
            $mail->isHTML(true);
            $mail->Body = self::getResetEmailHtml($utente['nome'], $link);
            $mail->AltBody = "Clicca sul seguente link per reimpostare la tua password (valido 1 ora):\n$link\n\nSe non hai richiesto il recupero password, ignora questa email.";

            $mail->send();
        } catch (MailException $e) {
            error_log('Errore invio email reset: ' . $e->getMessage());
            // Non bloccare l'utente per errori mail in debug
        }

        return true;
    }

    /**
     * Verifica il token e imposta la nuova password.
     */
    public static function resetPassword(string $token, string $newPassword): bool
    {
        $utente = Utente::findByResetToken($token);

        if (!$utente) return false;
        if (strtotime($utente['token_scadenza']) < time()) {
            Utente::clearResetToken($utente['id']);
            return false;
        }

        Utente::updatePassword($utente['id'], password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]));
        Utente::clearResetToken($utente['id']);

        return true;
    }

    private static function getResetEmailHtml(string $nome, string $link): string
    {
        return <<<HTML
        <!DOCTYPE html>
        <html><body style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;padding:20px;">
        <h2 style="color:#0d6efd;">Recupero Password - Preventivo Commercialisti</h2>
        <p>Ciao <strong>{$nome}</strong>,</p>
        <p>Hai richiesto il recupero della password. Clicca sul pulsante sottostante per impostarne una nuova.</p>
        <p style="text-align:center;margin:30px 0;">
            <a href="{$link}" style="background:#0d6efd;color:#fff;padding:12px 28px;text-decoration:none;border-radius:5px;font-size:16px;">
                Reimposta Password
            </a>
        </p>
        <p style="color:#666;font-size:13px;">Il link è valido per <strong>1 ora</strong>. Se non hai richiesto il recupero password, ignora questa email.</p>
        <hr style="border:none;border-top:1px solid #eee;margin:20px 0;">
        <p style="color:#999;font-size:12px;">Preventivo Commercialisti &mdash; Alessandro Scapuzzi</p>
        </body></html>
        HTML;
    }
}
