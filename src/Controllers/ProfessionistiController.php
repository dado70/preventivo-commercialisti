<?php
/**
 * Preventivo Commercialisti - Controller Professionisti
 * Copyright (C) 2024 Alessandro Scapuzzi <dado70@gmail.com>
 * Licenza: GPL v3
 */

namespace App\Controllers;

use App\Core\Auth;
use App\Core\View;
use App\Core\Session;
use App\Models\Professionista;

class ProfessionistiController
{
    /**
     * Elenco di tutti i professionisti (tutti gli utenti autenticati).
     */
    public function index(): void
    {
        Auth::requireLogin();

        $prof = Professionista::getAll(false);

        View::render('professionisti/index', [
            'pageTitle' => 'Professionisti',
            'prof'      => $prof,
        ]);
    }

    /**
     * Form per la creazione di un nuovo professionista (solo admin).
     */
    public function create(): void
    {
        Auth::requireAdmin();

        View::render('professionisti/form', [
            'pageTitle' => 'Nuovo Professionista',
            'prof'      => [],
        ]);
    }

    /**
     * Salva un nuovo professionista nel database (solo admin).
     */
    public function store(): void
    {
        Auth::requireAdmin();

        $nome    = trim($_POST['nome'] ?? '');
        $cognome = trim($_POST['cognome'] ?? '');

        if ($nome === '') {
            Session::flash('error', 'Il campo Nome è obbligatorio.');
            header('Location: ' . BASE_URL . '/professionisti/create');
            exit;
        }

        if ($cognome === '') {
            Session::flash('error', 'Il campo Cognome è obbligatorio.');
            header('Location: ' . BASE_URL . '/professionisti/create');
            exit;
        }

        Professionista::create($_POST);

        Session::flash('success', 'Professionista creato con successo.');
        header('Location: ' . BASE_URL . '/professionisti');
        exit;
    }

    /**
     * Form di modifica di un professionista esistente (solo admin).
     *
     * @param array $params Parametri di route, deve contenere 'id'.
     */
    public function edit(array $params): void
    {
        Auth::requireAdmin();

        $id   = (int)($params['id'] ?? 0);
        $prof = Professionista::findById($id);

        if (!$prof) {
            Session::flash('error', 'Professionista non trovato.');
            header('Location: ' . BASE_URL . '/professionisti');
            exit;
        }

        View::render('professionisti/form', [
            'pageTitle' => 'Modifica Professionista',
            'prof'      => $prof,
        ]);
    }

    /**
     * Aggiorna un professionista esistente nel database (solo admin).
     *
     * @param array $params Parametri di route, deve contenere 'id'.
     */
    public function update(array $params): void
    {
        Auth::requireAdmin();

        $id      = (int)($params['id'] ?? 0);
        $nome    = trim($_POST['nome'] ?? '');
        $cognome = trim($_POST['cognome'] ?? '');

        if ($nome === '') {
            Session::flash('error', 'Il campo Nome è obbligatorio.');
            header('Location: ' . BASE_URL . '/professionisti/' . $id . '/edit');
            exit;
        }

        if ($cognome === '') {
            Session::flash('error', 'Il campo Cognome è obbligatorio.');
            header('Location: ' . BASE_URL . '/professionisti/' . $id . '/edit');
            exit;
        }

        Professionista::update($id, $_POST);

        Session::flash('success', 'Professionista aggiornato con successo.');
        header('Location: ' . BASE_URL . '/professionisti');
        exit;
    }

    /**
     * Disattiva un professionista (soft-delete) (solo admin).
     *
     * @param array $params Parametri di route, deve contenere 'id'.
     */
    public function delete(array $params): void
    {
        Auth::requireAdmin();

        $id = (int)($params['id'] ?? 0);

        Professionista::delete($id);

        Session::flash('success', 'Professionista disattivato.');
        header('Location: ' . BASE_URL . '/professionisti');
        exit;
    }
}
