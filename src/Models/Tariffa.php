<?php
/**
 * Preventivo Commercialisti - Model Tariffa
 * Copyright (C) 2024 Alessandro Scapuzzi <dado70@gmail.com>
 * Licenza: GPL v3
 */

namespace App\Models;

use App\Core\Database;

class Tariffa
{
    private static function db(): Database
    {
        return Database::getInstance();
    }

    public static function getAll(bool $soloAttive = false): array
    {
        $where = $soloAttive ? 'WHERE attivo = 1' : '';
        return self::db()->fetchAll("SELECT * FROM tariffe $where ORDER BY ordine, sezione, codice");
    }

    public static function getBySections(): array
    {
        $tariffe = self::getAll(true);
        $grouped = [];
        foreach ($tariffe as $t) {
            $grouped[$t['sezione']][$t['categoria']][] = $t;
        }
        return $grouped;
    }

    public static function findById(int $id): array|false
    {
        return self::db()->fetchOne('SELECT * FROM tariffe WHERE id = ?', [$id]);
    }

    public static function findByCodice(string $codice): array|false
    {
        return self::db()->fetchOne('SELECT * FROM tariffe WHERE codice = ?', [$codice]);
    }

    public static function getSections(): array
    {
        return self::db()->fetchAll('SELECT DISTINCT sezione, categoria FROM tariffe WHERE attivo=1 ORDER BY ordine, sezione');
    }

    public static function create(array $data): int|string
    {
        return self::db()->insert(
            'INSERT INTO tariffe (codice, sezione, categoria, descrizione, tipo, importo_min, importo_max, frequenza, unita, note, attivo, ordine)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?)',
            [
                strtoupper(trim($data['codice'])),
                $data['sezione'],
                $data['categoria'],
                $data['descrizione'],
                $data['tipo'],
                $data['importo_min'] ?: null,
                $data['importo_max'] ?: null,
                $data['frequenza'],
                $data['unita'] ?: null,
                $data['note'] ?: null,
                $data['attivo'] ?? 1,
                $data['ordine'] ?? 0,
            ]
        );
    }

    public static function update(int $id, array $data): int
    {
        return self::db()->execute(
            'UPDATE tariffe SET codice=?, sezione=?, categoria=?, descrizione=?, tipo=?, importo_min=?, importo_max=?,
             frequenza=?, unita=?, note=?, attivo=?, ordine=? WHERE id=?',
            [
                strtoupper(trim($data['codice'])),
                $data['sezione'],
                $data['categoria'],
                $data['descrizione'],
                $data['tipo'],
                $data['importo_min'] ?: null,
                $data['importo_max'] ?: null,
                $data['frequenza'],
                $data['unita'] ?: null,
                $data['note'] ?: null,
                $data['attivo'] ?? 1,
                $data['ordine'] ?? 0,
                $id
            ]
        );
    }

    public static function delete(int $id): int
    {
        return self::db()->execute('DELETE FROM tariffe WHERE id = ?', [$id]);
    }

    public static function getCedoliniScaglioni(): array
    {
        return self::db()->fetchAll('SELECT * FROM cedolini_scaglioni ORDER BY n_cedolini');
    }

    /**
     * Calcola il costo cedolini per n cedolini e n mensilità (12/13/14).
     */
    public static function calcolaCedolini(int $nCedolini, int $mensilita = 12): float
    {
        // Scaglioni disponibili
        $scaglioni = self::db()->fetchAll('SELECT * FROM cedolini_scaglioni ORDER BY n_cedolini');
        $mappa = [];
        foreach ($scaglioni as $s) {
            $mappa[$s['n_cedolini']] = (float)$s['importo'];
        }

        // Prendi la tariffa dello scaglione corretto (al valore massimo disponibile)
        $nCedolini = max(1, $nCedolini);
        $keys = array_keys($mappa);
        $importoMensile = $mappa[max($keys)]; // default: massimo
        foreach ($keys as $k) {
            if ($nCedolini <= $k) {
                $importoMensile = $mappa[$k];
                break;
            }
        }

        // Calcolo annuale
        $totale = $importoMensile * 12; // 12 mensilità base

        if ($mensilita >= 13) {
            $totale += $importoMensile; // 13^ = 100% onorario dicembre
        }
        if ($mensilita >= 14) {
            $totale += $importoMensile; // 14^ = 100% onorario giugno
        }

        return $totale;
    }
}
