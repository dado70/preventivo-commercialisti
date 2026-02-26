<?php
/**
 * Preventivo Commercialisti - Controller Pacchetti
 * Copyright (C) 2024 Alessandro Scapuzzi <dado70@gmail.com>
 * Licenza: GPL v3
 */

namespace App\Controllers;

use App\Core\Auth;
use App\Core\View;
use App\Core\Session;
use App\Models\Pacchetto;
use App\Models\Tariffa;

class PacchettiController
{
    /**
     * Elenco di tutti i pacchetti con le relative tariffe associate
     * (tutti gli utenti autenticati).
     */
    public function index(): void
    {
        Auth::requireLogin();

        $pacchetti = Pacchetto::getAll(false);

        // Arricchisce ogni pacchetto con le proprie tariffe
        foreach ($pacchetti as &$p) {
            $p['tariffe'] = Pacchetto::getTariffe($p['id']);
        }
        unset($p);

        View::render('pacchetti/index', [
            'pageTitle' => 'Pacchetti',
            'pacchetti' => $pacchetti,
        ]);
    }

    /**
     * Form per la creazione di un nuovo pacchetto (solo admin).
     */
    public function create(): void
    {
        Auth::requireAdmin();

        $tariffe = Tariffa::getAll(true);

        View::render('pacchetti/form', [
            'pageTitle'   => 'Nuovo Pacchetto',
            'pacchetto'   => [],
            'tariffe'     => $tariffe,
            'tariffe_sel' => [],
        ]);
    }

    /**
     * Salva un nuovo pacchetto e le tariffe associate (solo admin).
     */
    public function store(): void
    {
        Auth::requireAdmin();

        $nome = trim($_POST['nome'] ?? '');

        if ($nome === '') {
            Session::flash('error', 'Il campo Nome è obbligatorio.');
            header('Location: ' . BASE_URL . '/pacchetti/create');
            exit;
        }

        $pacchetto = Pacchetto::create($_POST);
        $id        = $pacchetto['id'] ?? ($pacchetto instanceof \stdClass ? $pacchetto->id : null);

        // Sincronizza le tariffe selezionate
        $tariffeIds = $this->estraiTariffeIds($_POST['tariffe'] ?? []);
        Pacchetto::syncTariffe($id, $tariffeIds);

        Session::flash('success', 'Pacchetto creato con successo.');
        header('Location: ' . BASE_URL . '/pacchetti');
        exit;
    }

    /**
     * Form di modifica di un pacchetto esistente (solo admin).
     *
     * @param array $params Parametri di route, deve contenere 'id'.
     */
    public function edit(array $params): void
    {
        Auth::requireAdmin();

        $id        = (int)($params['id'] ?? 0);
        $pacchetto = Pacchetto::findById($id);

        if (!$pacchetto) {
            Session::flash('error', 'Pacchetto non trovato.');
            header('Location: ' . BASE_URL . '/pacchetti');
            exit;
        }

        $tariffe     = Tariffa::getAll(true);
        $tariffe_sel = Pacchetto::getTariffe($id);

        View::render('pacchetti/form', [
            'pageTitle'   => 'Modifica Pacchetto',
            'pacchetto'   => $pacchetto,
            'tariffe'     => $tariffe,
            'tariffe_sel' => $tariffe_sel,
        ]);
    }

    /**
     * Aggiorna un pacchetto esistente e sincronizza le tariffe (solo admin).
     *
     * @param array $params Parametri di route, deve contenere 'id'.
     */
    public function update(array $params): void
    {
        Auth::requireAdmin();

        $id   = (int)($params['id'] ?? 0);
        $nome = trim($_POST['nome'] ?? '');

        if ($nome === '') {
            Session::flash('error', 'Il campo Nome è obbligatorio.');
            header('Location: ' . BASE_URL . '/pacchetti/' . $id . '/edit');
            exit;
        }

        Pacchetto::update($id, $_POST);

        // Costruisce l'array di ID tariffa da $_POST['tariffe'] e sincronizza
        $tariffeIds = $this->estraiTariffeIds($_POST['tariffe'] ?? []);
        Pacchetto::syncTariffe($id, $tariffeIds);

        Session::flash('success', 'Pacchetto aggiornato con successo.');
        header('Location: ' . BASE_URL . '/pacchetti');
        exit;
    }

    /**
     * Elimina un pacchetto (solo admin).
     *
     * @param array $params Parametri di route, deve contenere 'id'.
     */
    public function delete(array $params): void
    {
        Auth::requireAdmin();

        $id = (int)($params['id'] ?? 0);

        Pacchetto::delete($id);

        Session::flash('success', 'Pacchetto eliminato.');
        header('Location: ' . BASE_URL . '/pacchetti');
        exit;
    }

    /**
     * API JSON: restituisce tutti i pacchetti attivi con le rispettive tariffe.
     * Endpoint usato dal frontend per popolare il selettore pacchetti nel preventivo.
     */
    public function apiList(): void
    {
        Auth::requireLogin();

        header('Content-Type: application/json; charset=utf-8');

        $pacchetti = Pacchetto::getAll(true); // solo attivi

        foreach ($pacchetti as &$p) {
            $p['tariffe'] = Pacchetto::getTariffe($p['id']);
        }
        unset($p);

        echo json_encode([
            'success' => true,
            'data'    => $pacchetti,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    // -------------------------------------------------------------------------
    // Metodi privati di supporto
    // -------------------------------------------------------------------------

    /**
     * Estrae un array di interi (ID tariffa) da $_POST['tariffe'].
     * Accetta sia un array associativo (tariffa_id => valore) sia un array
     * sequenziale di ID già pronti.
     *
     * @param mixed $raw Valore grezzo proveniente da $_POST['tariffe'].
     * @return int[]
     */
    private function estraiTariffeIds(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }

        $ids = [];
        foreach ($raw as $key => $val) {
            // Se la chiave è l'ID e il valore è un flag (es. checkbox HTML)
            if (is_numeric($key)) {
                $ids[] = (int)$key;
            } elseif (is_numeric($val)) {
                // Se il valore stesso è l'ID
                $ids[] = (int)$val;
            }
        }

        return array_values(array_unique($ids));
    }
}
