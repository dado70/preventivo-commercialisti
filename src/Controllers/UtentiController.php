<?php
/**
 * Preventivo Commercialisti - Controller Utenti
 * Copyright (C) 2024 Alessandro Scapuzzi <dado70@gmail.com>
 * Licenza: GPL v3
 */

namespace App\Controllers;

use App\Core\Auth;
use App\Core\View;
use App\Core\Session;
use App\Models\Utente;

class UtentiController
{
    /**
     * Elenco di tutti gli utenti (solo admin).
     */
    public function index(): void
    {
        Auth::requireAdmin();

        $utenti = Utente::getAll();

        View::render('utenti/index', [
            'pageTitle' => 'Utenti',
            'utenti'    => $utenti,
        ]);
    }

    /**
     * Form per la creazione di un nuovo utente (solo admin).
     */
    public function create(): void
    {
        Auth::requireAdmin();

        View::render('utenti/form', [
            'pageTitle' => 'Nuovo Utente',
            'utente'    => [],
        ]);
    }

    /**
     * Salva un nuovo utente nel database (solo admin).
     *
     * Validazioni:
     * - nome, cognome, email e password obbligatori
     * - email non duplicata
     * - password: min 8 caratteri, almeno 1 maiuscola, almeno 1 numero
     */
    public function store(): void
    {
        Auth::requireAdmin();

        $nome     = trim($_POST['nome'] ?? '');
        $cognome  = trim($_POST['cognome'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validazione campi obbligatori
        if ($nome === '') {
            Session::flash('error', 'Il campo Nome è obbligatorio.');
            header('Location: ' . BASE_URL . '/utenti/create');
            exit;
        }

        if ($cognome === '') {
            Session::flash('error', 'Il campo Cognome è obbligatorio.');
            header('Location: ' . BASE_URL . '/utenti/create');
            exit;
        }

        if ($email === '') {
            Session::flash('error', 'Il campo Email è obbligatorio.');
            header('Location: ' . BASE_URL . '/utenti/create');
            exit;
        }

        if ($password === '') {
            Session::flash('error', 'Il campo Password è obbligatorio.');
            header('Location: ' . BASE_URL . '/utenti/create');
            exit;
        }

        // Verifica email non duplicata
        if (Utente::findByEmail($email)) {
            Session::flash('error', 'L\'indirizzo email è già associato a un altro utente.');
            header('Location: ' . BASE_URL . '/utenti/create');
            exit;
        }

        // Validazione requisiti password
        $errorePassword = $this->validaPassword($password);
        if ($errorePassword !== null) {
            Session::flash('error', $errorePassword);
            header('Location: ' . BASE_URL . '/utenti/create');
            exit;
        }

        Utente::create($_POST);

        Session::flash('success', 'Utente creato con successo.');
        header('Location: ' . BASE_URL . '/utenti');
        exit;
    }

    /**
     * Form di modifica di un utente esistente (solo admin).
     *
     * Impedisce la modifica se l'utente da modificare è l'unico amministratore
     * attivo e coincide con l'utente attualmente loggato.
     *
     * @param array $params Parametri di route, deve contenere 'id'.
     */
    public function edit(array $params): void
    {
        Auth::requireAdmin();

        $id     = (int)($params['id'] ?? 0);
        $utente = Utente::findById($id);

        if (!$utente) {
            Session::flash('error', 'Utente non trovato.');
            header('Location: ' . BASE_URL . '/utenti');
            exit;
        }

        // Protezione: non permettere la modifica dell'utente loggato se è l'unico admin
        if ($id === Auth::userId() && $this->isUnicoAdmin($id)) {
            Session::flash('error', 'Non puoi modificare il tuo account mentre sei l\'unico amministratore del sistema.');
            header('Location: ' . BASE_URL . '/utenti');
            exit;
        }

        View::render('utenti/form', [
            'pageTitle' => 'Modifica Utente',
            'utente'    => $utente,
        ]);
    }

    /**
     * Aggiorna un utente esistente nel database (solo admin).
     * Se il campo password è compilato, viene validato prima di essere aggiornato.
     *
     * @param array $params Parametri di route, deve contenere 'id'.
     */
    public function update(array $params): void
    {
        Auth::requireAdmin();

        $id       = (int)($params['id'] ?? 0);
        $password = $_POST['password'] ?? '';

        // Validazione password (solo se fornita)
        if ($password !== '') {
            $errorePassword = $this->validaPassword($password);
            if ($errorePassword !== null) {
                Session::flash('error', $errorePassword);
                header('Location: ' . BASE_URL . '/utenti/' . $id . '/edit');
                exit;
            }
        }

        Utente::update($id, $_POST);

        Session::flash('success', 'Utente aggiornato con successo.');
        header('Location: ' . BASE_URL . '/utenti');
        exit;
    }

    /**
     * Elimina un utente (solo admin).
     *
     * Impedisce la cancellazione di se stessi.
     *
     * @param array $params Parametri di route, deve contenere 'id'.
     */
    public function delete(array $params): void
    {
        Auth::requireAdmin();

        $id = (int)($params['id'] ?? 0);

        // Protezione: non permettere la cancellazione del proprio account
        if ($id === Auth::userId()) {
            Session::flash('error', 'Non puoi eliminare il tuo stesso account.');
            header('Location: ' . BASE_URL . '/utenti');
            exit;
        }

        Utente::delete($id);

        Session::flash('success', 'Utente eliminato con successo.');
        header('Location: ' . BASE_URL . '/utenti');
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

    /**
     * Verifica se un dato utente è l'unico amministratore attivo nel sistema.
     *
     * @param int $userId ID dell'utente da verificare.
     * @return bool True se è l'unico admin attivo, false altrimenti.
     */
    private function isUnicoAdmin(int $userId): bool
    {
        $tuttiGliAdmin = Utente::getAllAdmin(); // restituisce array di admin attivi

        // Se non esiste il metodo getAllAdmin, fallback sicuro: non bloccare
        if (!is_array($tuttiGliAdmin)) {
            return false;
        }

        $adminAttivi = array_filter($tuttiGliAdmin, static function (array $u): bool {
            return (bool)($u['attivo'] ?? true);
        });

        if (count($adminAttivi) !== 1) {
            return false;
        }

        $unicoAdmin = reset($adminAttivi);

        return (int)($unicoAdmin['id'] ?? 0) === $userId;
    }
}
