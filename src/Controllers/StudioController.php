<?php
/**
 * Preventivo Commercialisti - Controller Studio
 * Copyright (C) 2024 Alessandro Scapuzzi <dado70@gmail.com>
 * Licenza: GPL v3
 */

namespace App\Controllers;

use App\Core\Auth;
use App\Core\View;
use App\Core\Session;
use App\Models\Studio;

class StudioController
{
    /**
     * Mostra l'anagrafica dello studio (solo admin).
     */
    public function index(): void
    {
        Auth::requireAdmin();

        $studio = Studio::get();

        View::render('studio/index', [
            'pageTitle' => 'Anagrafica Studio',
            'studio'    => $studio,
        ]);
    }

    /**
     * Salva i dati dell'anagrafica studio, gestendo anche l'upload del logo (solo admin).
     *
     * - Valida che la ragione sociale non sia vuota.
     * - Se viene caricato un file logo, ne valida il MIME type e lo salva in UPLOADS_PATH.
     * - Chiama Studio::saveLogo() per registrare il percorso relativo del logo.
     * - Chiama Studio::save() per persistere tutti gli altri dati del form.
     */
    public function save(): void
    {
        Auth::requireAdmin();

        // Validazione campo obbligatorio
        $ragioneSociale = trim($_POST['ragione_sociale'] ?? '');

        if ($ragioneSociale === '') {
            Session::flash('error', 'Il campo Ragione Sociale è obbligatorio.');
            header('Location: ' . BASE_URL . '/studio');
            exit;
        }

        // Gestione upload logo
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['logo'];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                Session::flash('error', 'Errore durante il caricamento del logo (codice: ' . $file['error'] . ').');
                header('Location: ' . BASE_URL . '/studio');
                exit;
            }

            // Validazione MIME type tramite finfo (non ci si fida di $_FILES['type'])
            $finfo    = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($file['tmp_name']);

            $mimeConsentiti = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/gif'  => 'gif',
            ];

            if (!array_key_exists($mimeType, $mimeConsentiti)) {
                Session::flash('error', 'Formato logo non valido. Sono accettati solo JPEG, PNG e GIF.');
                header('Location: ' . BASE_URL . '/studio');
                exit;
            }

            $estensione    = $mimeConsentiti[$mimeType];
            $nomeFile      = 'logo_studio.' . $estensione;
            $percorsoAssoluto = rtrim(UPLOADS_PATH, '/') . '/' . $nomeFile;
            $percorsoRelativo = 'uploads/' . $nomeFile;

            if (!move_uploaded_file($file['tmp_name'], $percorsoAssoluto)) {
                Session::flash('error', 'Impossibile salvare il file del logo. Verificare i permessi della cartella uploads.');
                header('Location: ' . BASE_URL . '/studio');
                exit;
            }

            Studio::saveLogo($percorsoRelativo);
        }

        // Salvataggio dati studio
        Studio::save($_POST);

        Session::flash('success', 'Dati studio salvati.');
        header('Location: ' . BASE_URL . '/studio');
        exit;
    }
}
