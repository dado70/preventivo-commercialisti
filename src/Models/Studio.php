<?php
/**
 * Preventivo Commercialisti - Model Studio
 * Copyright (C) 2024 Alessandro Scapuzzi <dado70@gmail.com>
 * Licenza: GPL v3
 */

namespace App\Models;

use App\Core\Database;

class Studio
{
    private static function db(): Database
    {
        return Database::getInstance();
    }

    public static function get(): array
    {
        return self::db()->fetchOne('SELECT * FROM studio LIMIT 1') ?: [];
    }

    public static function save(array $data): void
    {
        $existing = self::db()->fetchOne('SELECT id FROM studio LIMIT 1');
        if ($existing) {
            self::db()->execute(
                'UPDATE studio SET ragione_sociale=?, forma_giuridica=?, partita_iva=?, codice_fiscale=?,
                 indirizzo=?, cap=?, citta=?, provincia=?, telefono=?, fax=?, email=?, pec=?,
                 sito_web=?, iban=?, banca=?, ordine_professionale=?, n_iscrizione_ordine=?,
                 sez_registro=?, note=? WHERE id=?',
                [
                    $data['ragione_sociale'],
                    $data['forma_giuridica'] ?? null,
                    $data['partita_iva'] ?? null,
                    $data['codice_fiscale'] ?? null,
                    $data['indirizzo'] ?? null,
                    $data['cap'] ?? null,
                    $data['citta'] ?? null,
                    $data['provincia'] ?? null,
                    $data['telefono'] ?? null,
                    $data['fax'] ?? null,
                    $data['email'] ?? null,
                    $data['pec'] ?? null,
                    $data['sito_web'] ?? null,
                    $data['iban'] ?? null,
                    $data['banca'] ?? null,
                    $data['ordine_professionale'] ?? null,
                    $data['n_iscrizione_ordine'] ?? null,
                    $data['sez_registro'] ?? null,
                    $data['note'] ?? null,
                    $existing['id'],
                ]
            );
        } else {
            self::db()->insert(
                'INSERT INTO studio (ragione_sociale, forma_giuridica, partita_iva, codice_fiscale,
                 indirizzo, cap, citta, provincia, telefono, fax, email, pec,
                 sito_web, iban, banca, ordine_professionale, n_iscrizione_ordine, sez_registro, note)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
                [
                    $data['ragione_sociale'],
                    $data['forma_giuridica'] ?? null,
                    $data['partita_iva'] ?? null,
                    $data['codice_fiscale'] ?? null,
                    $data['indirizzo'] ?? null,
                    $data['cap'] ?? null,
                    $data['citta'] ?? null,
                    $data['provincia'] ?? null,
                    $data['telefono'] ?? null,
                    $data['fax'] ?? null,
                    $data['email'] ?? null,
                    $data['pec'] ?? null,
                    $data['sito_web'] ?? null,
                    $data['iban'] ?? null,
                    $data['banca'] ?? null,
                    $data['ordine_professionale'] ?? null,
                    $data['n_iscrizione_ordine'] ?? null,
                    $data['sez_registro'] ?? null,
                    $data['note'] ?? null,
                ]
            );
        }
    }

    public static function saveLogo(string $path): void
    {
        self::db()->execute("UPDATE impostazioni SET valore=? WHERE chiave='logo_studio'", [$path]);
    }
}
