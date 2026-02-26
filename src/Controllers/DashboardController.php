<?php
/**
 * Preventivo Commercialisti - Controller Dashboard
 * Copyright (C) 2024 Alessandro Scapuzzi <dado70@gmail.com>
 * Licenza: GPL v3
 */

namespace App\Controllers;

use App\Core\Auth;
use App\Core\View;
use App\Models\Preventivo;
use App\Models\Studio;

class DashboardController
{
    /**
     * Pagina principale della dashboard.
     *
     * Carica le statistiche dell'anno corrente, gli ultimi 10 preventivi
     * e i dati dello studio, quindi renderizza la vista con il layout principale.
     */
    public function index(): void
    {
        Auth::requireLogin();

        // Statistiche aggregate (totale, bozze, inviati, accettati, valore_accettati)
        $stats = Preventivo::getStats();

        // Ultimi 10 preventivi ordinati per data discendente
        $recenti = array_slice(Preventivo::getAll(), 0, 10);

        // Dati dello studio (intestazione, logo, ecc.)
        $studio = Studio::get();

        View::render('dashboard/index', [
            'pageTitle' => 'Dashboard',
            'stats'     => $stats,
            'recenti'   => $recenti,
            'studio'    => $studio,
        ]);
    }
}
