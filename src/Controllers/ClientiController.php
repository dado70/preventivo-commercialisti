<?php
/**
 * Preventivo Commercialisti - Controller Clienti
 * Copyright (C) 2024 Alessandro Scapuzzi <dado70@gmail.com>
 * Licenza: GPL v3
 */

namespace App\Controllers;

use App\Core\Auth;
use App\Core\View;
use App\Core\Session;
use App\Models\Cliente;
use App\Models\Professionista;

class ClientiController
{
    /**
     * Elenco di tutti i clienti (tutti gli utenti autenticati).
     */
    public function index(): void
    {
        Auth::requireLogin();

        $clienti = Cliente::getAll();

        View::render('clienti/index', [
            'pageTitle' => 'Clienti',
            'clienti'   => $clienti,
        ]);
    }

    /**
     * Form per la creazione di un nuovo cliente (tutti gli utenti autenticati).
     */
    public function create(): void
    {
        Auth::requireLogin();

        $professionisti = Professionista::getAll();

        View::render('clienti/form', [
            'pageTitle'      => 'Nuovo Cliente',
            'cliente'        => [],
            'professionisti' => $professionisti,
        ]);
    }

    /**
     * Salva un nuovo cliente nel database (tutti gli utenti autenticati).
     */
    public function store(): void
    {
        Auth::requireLogin();

        $ragioneSociale = trim($_POST['ragione_sociale'] ?? '');

        if ($ragioneSociale === '') {
            Session::flash('error', 'Il campo Ragione Sociale è obbligatorio.');
            header('Location: ' . BASE_URL . '/clienti/create');
            exit;
        }

        Cliente::create($_POST);

        Session::flash('success', 'Cliente creato con successo.');
        header('Location: ' . BASE_URL . '/clienti');
        exit;
    }

    /**
     * Form di modifica di un cliente esistente (tutti gli utenti autenticati).
     *
     * @param array $params Parametri di route, deve contenere 'id'.
     */
    public function edit(array $params): void
    {
        Auth::requireLogin();

        $id      = (int)($params['id'] ?? 0);
        $cliente = Cliente::findById($id);

        if (!$cliente) {
            Session::flash('error', 'Cliente non trovato.');
            header('Location: ' . BASE_URL . '/clienti');
            exit;
        }

        $professionisti = Professionista::getAll();

        View::render('clienti/form', [
            'pageTitle'      => 'Modifica Cliente',
            'cliente'        => $cliente,
            'professionisti' => $professionisti,
        ]);
    }

    /**
     * Aggiorna un cliente esistente nel database (tutti gli utenti autenticati).
     *
     * @param array $params Parametri di route, deve contenere 'id'.
     */
    public function update(array $params): void
    {
        Auth::requireLogin();

        $id             = (int)($params['id'] ?? 0);
        $ragioneSociale = trim($_POST['ragione_sociale'] ?? '');

        if ($ragioneSociale === '') {
            Session::flash('error', 'Il campo Ragione Sociale è obbligatorio.');
            header('Location: ' . BASE_URL . '/clienti/' . $id . '/edit');
            exit;
        }

        Cliente::update($id, $_POST);

        Session::flash('success', 'Cliente aggiornato con successo.');
        header('Location: ' . BASE_URL . '/clienti');
        exit;
    }

    /**
     * Disattiva un cliente (soft-delete) (tutti gli utenti autenticati).
     *
     * @param array $params Parametri di route, deve contenere 'id'.
     */
    public function delete(array $params): void
    {
        Auth::requireLogin();

        $id = (int)($params['id'] ?? 0);

        Cliente::delete($id);

        Session::flash('success', 'Cliente disattivato.');
        header('Location: ' . BASE_URL . '/clienti');
        exit;
    }

    /**
     * API JSON: ricerca clienti per ragione sociale (autocomplete).
     * Parametro GET: q = stringa di ricerca.
     */
    public function search(): void
    {
        Auth::requireLogin();

        header('Content-Type: application/json; charset=utf-8');

        $term    = trim($_GET['q'] ?? '');
        $clienti = Cliente::search($term);

        echo json_encode($clienti, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}
