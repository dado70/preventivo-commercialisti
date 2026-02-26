<?php
/**
 * Preventivo Commercialisti - Controller Preventivi
 * Copyright (C) 2024 Alessandro Scapuzzi <dado70@gmail.com>
 * Licenza: GPL v3
 */

namespace App\Controllers;

use App\Core\Auth;
use App\Core\View;
use App\Core\Session;
use App\Models\Preventivo;
use App\Models\Cliente;
use App\Models\Professionista;
use App\Models\Tariffa;
use App\Models\Pacchetto;

class PreventiviController
{
    public function index(array $params = []): void
    {
        Auth::requireLogin();
        $filtri = [
            'stato' => $_GET['stato'] ?? '',
            'anno'  => $_GET['anno'] ?? date('Y'),
            'cliente_id' => (int)($_GET['cliente_id'] ?? 0),
        ];
        $preventivi = Preventivo::getAll($filtri);
        $clienti    = Cliente::getAll();
        View::render('preventivi/index', [
            'pageTitle'  => 'Preventivi',
            'preventivi' => $preventivi,
            'clienti'    => $clienti,
            'filtri'     => $filtri,
        ]);
    }

    public function create(array $params = []): void
    {
        Auth::requireLogin();
        $clienti        = Cliente::getAll();
        $professionisti = Professionista::getAll();
        $tariffeGruppi  = Tariffa::getBySections();
        $pacchetti      = Pacchetto::getAll();
        $cedoliniScagl  = Tariffa::getCedoliniScaglioni();

        View::render('preventivi/form', [
            'pageTitle'      => 'Nuovo Preventivo',
            'preventivo'     => [],
            'voci'           => [],
            'clienti'        => $clienti,
            'professionisti' => $professionisti,
            'tariffeGruppi'  => $tariffeGruppi,
            'pacchetti'      => $pacchetti,
            'cedoliniScagl'  => $cedoliniScagl,
            'isNew'          => true,
        ]);
    }

    public function store(array $params = []): void
    {
        Auth::requireLogin();

        $errore = $this->validaPost($_POST);
        if ($errore) {
            Session::flash('error', $errore);
            header('Location: ' . BASE_URL . '/preventivi/create');
            exit;
        }

        $data = $_POST;
        $data['numero']       = Preventivo::generaNumero();
        $data['utente_id']    = Auth::userId();
        $data['anno_riferimento'] = date('Y', strtotime($data['data_preventivo']));

        // Ricalcola totali
        $voci = $this->parseVoci($_POST);
        $imponibile = array_sum(array_column($voci, 'importo_riga'));
        $totali = Preventivo::calcolaTotali(
            $imponibile,
            (float)($data['sconto1'] ?? 0),
            (float)($data['sconto2'] ?? 0),
            (float)($data['iva_perc'] ?? ALIQUOTA_IVA)
        );
        $data = array_merge($data, $totali);

        $db = \App\Core\Database::getInstance();
        $db->beginTransaction();
        try {
            $id = Preventivo::create($data);
            foreach ($voci as $i => $voce) {
                $voce['ordine'] = $i;
                Preventivo::addVoce((int)$id, $voce);
            }
            $db->commit();
        } catch (\Exception $e) {
            $db->rollback();
            Session::flash('error', 'Errore nel salvataggio: ' . $e->getMessage());
            header('Location: ' . BASE_URL . '/preventivi/create');
            exit;
        }

        Session::flash('success', 'Preventivo ' . $data['numero'] . ' creato con successo.');
        header('Location: ' . BASE_URL . '/preventivi/' . $id);
        exit;
    }

    public function show(array $params): void
    {
        Auth::requireLogin();
        $preventivo = Preventivo::findById((int)$params['id']);
        if (!$preventivo) {
            Session::flash('error', 'Preventivo non trovato.');
            header('Location: ' . BASE_URL . '/preventivi');
            exit;
        }
        $voci = Preventivo::getVoci((int)$params['id']);
        View::render('preventivi/show', [
            'pageTitle'  => 'Preventivo ' . $preventivo['numero'],
            'preventivo' => $preventivo,
            'voci'       => $voci,
        ]);
    }

    public function edit(array $params): void
    {
        Auth::requireLogin();
        $preventivo = Preventivo::findById((int)$params['id']);
        if (!$preventivo) {
            Session::flash('error', 'Preventivo non trovato.');
            header('Location: ' . BASE_URL . '/preventivi');
            exit;
        }
        $voci           = Preventivo::getVoci((int)$params['id']);
        $clienti        = Cliente::getAll();
        $professionisti = Professionista::getAll();
        $tariffeGruppi  = Tariffa::getBySections();
        $pacchetti      = Pacchetto::getAll();
        $cedoliniScagl  = Tariffa::getCedoliniScaglioni();

        View::render('preventivi/form', [
            'pageTitle'      => 'Modifica Preventivo ' . $preventivo['numero'],
            'preventivo'     => $preventivo,
            'voci'           => $voci,
            'clienti'        => $clienti,
            'professionisti' => $professionisti,
            'tariffeGruppi'  => $tariffeGruppi,
            'pacchetti'      => $pacchetti,
            'cedoliniScagl'  => $cedoliniScagl,
            'isNew'          => false,
        ]);
    }

    public function update(array $params): void
    {
        Auth::requireLogin();
        $id = (int)$params['id'];

        $errore = $this->validaPost($_POST);
        if ($errore) {
            Session::flash('error', $errore);
            header('Location: ' . BASE_URL . '/preventivi/' . $id . '/edit');
            exit;
        }

        $data = $_POST;
        $data['anno_riferimento'] = date('Y', strtotime($data['data_preventivo']));

        $voci = $this->parseVoci($_POST);
        $imponibile = array_sum(array_column($voci, 'importo_riga'));
        $totali = Preventivo::calcolaTotali(
            $imponibile,
            (float)($data['sconto1'] ?? 0),
            (float)($data['sconto2'] ?? 0),
            (float)($data['iva_perc'] ?? ALIQUOTA_IVA)
        );
        $data = array_merge($data, $totali);

        $db = \App\Core\Database::getInstance();
        $db->beginTransaction();
        try {
            Preventivo::update($id, $data);
            Preventivo::deleteVoci($id);
            foreach ($voci as $i => $voce) {
                $voce['ordine'] = $i;
                Preventivo::addVoce($id, $voce);
            }
            $db->commit();
        } catch (\Exception $e) {
            $db->rollback();
            Session::flash('error', 'Errore nel salvataggio: ' . $e->getMessage());
            header('Location: ' . BASE_URL . '/preventivi/' . $id . '/edit');
            exit;
        }

        Session::flash('success', 'Preventivo aggiornato con successo.');
        header('Location: ' . BASE_URL . '/preventivi/' . $id);
        exit;
    }

    public function delete(array $params): void
    {
        Auth::requireLogin();
        Preventivo::delete((int)$params['id']);
        Session::flash('success', 'Preventivo eliminato.');
        header('Location: ' . BASE_URL . '/preventivi');
        exit;
    }

    public function updateStato(array $params): void
    {
        Auth::requireLogin();
        $stati = ['bozza', 'inviato', 'accettato', 'rifiutato', 'scaduto'];
        $stato = $_POST['stato'] ?? '';
        if (in_array($stato, $stati)) {
            Preventivo::updateStato((int)$params['id'], $stato);
        }
        header('Location: ' . BASE_URL . '/preventivi/' . $params['id']);
        exit;
    }

    // -------------------------------------------------------
    // HELPER PRIVATI
    // -------------------------------------------------------

    private function validaPost(array $data): ?string
    {
        if (empty($data['cliente_id'])) return 'Seleziona un cliente.';
        if (empty($data['data_preventivo'])) return 'La data preventivo è obbligatoria.';
        return null;
    }

    /**
     * Legge le voci dal POST (array serializzato come voci[0][campo]=valore).
     */
    private function parseVoci(array $post): array
    {
        $voci = [];
        $rawVoci = $post['voci'] ?? [];
        if (!is_array($rawVoci)) return [];

        foreach ($rawVoci as $v) {
            if (empty($v['descrizione'])) continue;
            $importoUnitario = (float)str_replace(',', '.', $v['importo_unitario'] ?? 0);
            $quantita        = (float)str_replace(',', '.', $v['quantita'] ?? 1);
            $mesi            = (int)($v['mesi'] ?? 12);

            // Calcolo importo riga
            $importoRiga = $importoUnitario * $quantita;
            // Per servizi mensili con moltiplicatore di mesi
            if (($v['frequenza'] ?? '') === 'mensile' && $mesi > 0) {
                $importoRiga = $importoUnitario * $mesi;
            }

            $voci[] = [
                'tipo_voce'        => $v['tipo_voce'] ?? 'tariffa',
                'tariffa_id'       => $v['tariffa_id'] ?: null,
                'pacchetto_id'     => $v['pacchetto_id'] ?: null,
                'codice'           => $v['codice'] ?? null,
                'descrizione'      => $v['descrizione'],
                'frequenza'        => $v['frequenza'] ?? 'a_prestazione',
                'mesi'             => $mesi,
                'quantita'         => $quantita,
                'importo_unitario' => $importoUnitario,
                'importo_riga'     => round($importoRiga, 2),
                'note'             => $v['note'] ?? null,
            ];
        }
        return $voci;
    }
}
