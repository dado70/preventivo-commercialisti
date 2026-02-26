<?php
/**
 * Preventivo Commercialisti - Controller Tariffe
 * Copyright (C) 2024 Alessandro Scapuzzi <dado70@gmail.com>
 * Licenza: GPL v3
 */

namespace App\Controllers;

use App\Core\Auth;
use App\Core\View;
use App\Core\Session;
use App\Models\Tariffa;

class TariffeController
{
    /**
     * Elenco tariffe raggruppate per sezione (tutti gli utenti autenticati).
     */
    public function index(): void
    {
        Auth::requireLogin();

        $tariffe = Tariffa::getAll();

        // Raggruppa le tariffe per sezione
        $perSezione = [];
        foreach ($tariffe as $t) {
            $perSezione[$t['sezione']][] = $t;
        }
        ksort($perSezione);

        View::render('tariffe/index', [
            'pageTitle'  => 'Tariffe',
            'tariffe'    => $tariffe,
            'perSezione' => $perSezione,
        ]);
    }

    /**
     * Form per la creazione di una nuova tariffa (solo admin).
     */
    public function create(): void
    {
        Auth::requireAdmin();

        View::render('tariffe/form', [
            'pageTitle' => 'Nuova Tariffa',
            'tariffa'   => [],
        ]);
    }

    /**
     * Salva una nuova tariffa nel database (solo admin).
     */
    public function store(): void
    {
        Auth::requireAdmin();

        $errore = $this->validaPost($_POST);
        if ($errore !== null) {
            Session::flash('error', $errore);
            header('Location: ' . BASE_URL . '/tariffe/create');
            exit;
        }

        Tariffa::create($_POST);

        Session::flash('success', 'Tariffa creata con successo.');
        header('Location: ' . BASE_URL . '/tariffe');
        exit;
    }

    /**
     * Form di modifica di una tariffa esistente (solo admin).
     *
     * @param array $params Parametri di route, deve contenere 'id'.
     */
    public function edit(array $params): void
    {
        Auth::requireAdmin();

        $id      = (int)($params['id'] ?? 0);
        $tariffa = Tariffa::findById($id);

        if (!$tariffa) {
            Session::flash('error', 'Tariffa non trovata.');
            header('Location: ' . BASE_URL . '/tariffe');
            exit;
        }

        View::render('tariffe/form', [
            'pageTitle' => 'Modifica Tariffa',
            'tariffa'   => $tariffa,
        ]);
    }

    /**
     * Aggiorna una tariffa esistente (solo admin).
     *
     * @param array $params Parametri di route, deve contenere 'id'.
     */
    public function update(array $params): void
    {
        Auth::requireAdmin();

        $id = (int)($params['id'] ?? 0);

        $errore = $this->validaPost($_POST);
        if ($errore !== null) {
            Session::flash('error', $errore);
            header('Location: ' . BASE_URL . '/tariffe/' . $id . '/edit');
            exit;
        }

        Tariffa::update($id, $_POST);

        Session::flash('success', 'Tariffa aggiornata con successo.');
        header('Location: ' . BASE_URL . '/tariffe');
        exit;
    }

    /**
     * Elimina una tariffa (solo admin).
     *
     * @param array $params Parametri di route, deve contenere 'id'.
     */
    public function delete(array $params): void
    {
        Auth::requireAdmin();

        $id = (int)($params['id'] ?? 0);

        Tariffa::delete($id);

        Session::flash('success', 'Tariffa eliminata.');
        header('Location: ' . BASE_URL . '/tariffe');
        exit;
    }

    /**
     * API JSON: restituisce tutte le tariffe attive raggruppate per sezione.
     * Endpoint usato dal frontend per popolare il selettore tariffe nel preventivo.
     */
    public function apiList(): void
    {
        Auth::requireLogin();

        header('Content-Type: application/json; charset=utf-8');

        $tariffe = Tariffa::getAll(true); // solo attive

        // Raggruppa per sezione
        $perSezione = [];
        foreach ($tariffe as $t) {
            $perSezione[$t['sezione']][] = $t;
        }
        ksort($perSezione);

        echo json_encode([
            'success' => true,
            'data'    => $perSezione,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * API JSON: calcola il costo annuale per i cedolini paga.
     *
     * Parametri GET:
     *   n     = numero di cedolini mensili
     *   mesi  = numero di mensilità (12, 13 o 14; default 12)
     */
    public function apiCedolini(): void
    {
        Auth::requireLogin();

        header('Content-Type: application/json; charset=utf-8');

        $n    = max(1, (int)($_GET['n'] ?? 1));
        $mesi = (int)($_GET['mesi'] ?? 12);

        // Clamp tra 12 e 14 mensilità
        $mesi = max(12, min(14, $mesi));

        $importo = Tariffa::calcolaCedolini($n, $mesi);

        echo json_encode([
            'success'  => true,
            'n'        => $n,
            'mesi'     => $mesi,
            'importo'  => $importo,
            'importo_formattato' => number_format($importo, 2, ',', '.'),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    // -------------------------------------------------------------------------
    // Metodi privati di supporto
    // -------------------------------------------------------------------------

    /**
     * Valida i campi obbligatori del form tariffa.
     * Restituisce un messaggio di errore oppure null se tutto è valido.
     */
    private function validaPost(array $post): ?string
    {
        $codice     = trim($post['codice'] ?? '');
        $descrizione = trim($post['descrizione'] ?? '');
        $sezione    = trim($post['sezione'] ?? '');
        $tipo       = trim($post['tipo'] ?? '');
        $frequenza  = trim($post['frequenza'] ?? '');

        if ($codice === '') {
            return 'Il campo Codice è obbligatorio.';
        }

        if ($descrizione === '') {
            return 'Il campo Descrizione è obbligatorio.';
        }

        if ($sezione === '') {
            return 'Il campo Sezione è obbligatorio.';
        }

        if ($tipo === '') {
            return 'Il campo Tipo è obbligatorio.';
        }

        if ($frequenza === '') {
            return 'Il campo Frequenza è obbligatorio.';
        }

        return null;
    }
}
