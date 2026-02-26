<?php
/**
 * Preventivo Commercialisti - Model Professionista
 * Copyright (C) 2024 Alessandro Scapuzzi <dado70@gmail.com>
 * Licenza: GPL v3
 */

namespace App\Models;

use App\Core\Database;

class Professionista
{
    private static function db(): Database
    {
        return Database::getInstance();
    }

    public static function getAll(bool $soloAttivi = true): array
    {
        $where = $soloAttivi ? 'WHERE attivo = 1' : '';
        return self::db()->fetchAll("SELECT * FROM professionisti $where ORDER BY cognome, nome");
    }

    public static function findById(int $id): array|false
    {
        return self::db()->fetchOne('SELECT * FROM professionisti WHERE id = ?', [$id]);
    }

    public static function create(array $data): int|string
    {
        return self::db()->insert(
            'INSERT INTO professionisti (nome, cognome, titolo, qualifica, codice_fiscale, partita_iva,
             email, pec, telefono, ordine_professionale, n_iscrizione_ordine, sez_registro,
             provincia_ordine, note, attivo) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,1)',
            [
                $data['nome'],
                $data['cognome'],
                $data['titolo'] ?? null,
                $data['qualifica'] ?? null,
                $data['codice_fiscale'] ?? null,
                $data['partita_iva'] ?? null,
                $data['email'] ?? null,
                $data['pec'] ?? null,
                $data['telefono'] ?? null,
                $data['ordine_professionale'] ?? null,
                $data['n_iscrizione_ordine'] ?? null,
                $data['sez_registro'] ?? null,
                $data['provincia_ordine'] ?? null,
                $data['note'] ?? null,
            ]
        );
    }

    public static function update(int $id, array $data): int
    {
        return self::db()->execute(
            'UPDATE professionisti SET nome=?, cognome=?, titolo=?, qualifica=?, codice_fiscale=?, partita_iva=?,
             email=?, pec=?, telefono=?, ordine_professionale=?, n_iscrizione_ordine=?, sez_registro=?,
             provincia_ordine=?, note=?, attivo=? WHERE id=?',
            [
                $data['nome'],
                $data['cognome'],
                $data['titolo'] ?? null,
                $data['qualifica'] ?? null,
                $data['codice_fiscale'] ?? null,
                $data['partita_iva'] ?? null,
                $data['email'] ?? null,
                $data['pec'] ?? null,
                $data['telefono'] ?? null,
                $data['ordine_professionale'] ?? null,
                $data['n_iscrizione_ordine'] ?? null,
                $data['sez_registro'] ?? null,
                $data['provincia_ordine'] ?? null,
                $data['note'] ?? null,
                $data['attivo'] ?? 1,
                $id
            ]
        );
    }

    public static function delete(int $id): void
    {
        self::db()->execute('UPDATE professionisti SET attivo=0 WHERE id=?', [$id]);
    }
}
