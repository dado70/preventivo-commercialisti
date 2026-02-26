<?php
/**
 * Preventivo Commercialisti - Model Pacchetto
 * Copyright (C) 2024 Alessandro Scapuzzi <dado70@gmail.com>
 * Licenza: GPL v3
 */

namespace App\Models;

use App\Core\Database;

class Pacchetto
{
    private static function db(): Database
    {
        return Database::getInstance();
    }

    public static function getAll(bool $soloAttivi = true): array
    {
        $where = $soloAttivi ? 'WHERE attivo = 1' : '';
        return self::db()->fetchAll("SELECT * FROM pacchetti $where ORDER BY nome");
    }

    public static function findById(int $id): array|false
    {
        return self::db()->fetchOne('SELECT * FROM pacchetti WHERE id = ?', [$id]);
    }

    public static function getTariffe(int $pacchettoId): array
    {
        return self::db()->fetchAll(
            'SELECT pt.*, t.codice, t.descrizione, t.importo_min, t.importo_max, t.tipo, t.frequenza, t.unita
             FROM pacchetto_tariffe pt JOIN tariffe t ON pt.tariffa_id = t.id
             WHERE pt.pacchetto_id = ? ORDER BY t.ordine',
            [$pacchettoId]
        );
    }

    public static function create(array $data): int|string
    {
        return self::db()->insert(
            'INSERT INTO pacchetti (nome, descrizione, prezzo_fisso, attivo) VALUES (?,?,?,1)',
            [$data['nome'], $data['descrizione'] ?? null, $data['prezzo_fisso'] ?: null]
        );
    }

    public static function update(int $id, array $data): void
    {
        self::db()->execute(
            'UPDATE pacchetti SET nome=?, descrizione=?, prezzo_fisso=?, attivo=? WHERE id=?',
            [$data['nome'], $data['descrizione'] ?? null, $data['prezzo_fisso'] ?: null, $data['attivo'] ?? 1, $id]
        );
    }

    public static function syncTariffe(int $pacchettoId, array $tariffe): void
    {
        self::db()->execute('DELETE FROM pacchetto_tariffe WHERE pacchetto_id = ?', [$pacchettoId]);
        foreach ($tariffe as $t) {
            self::db()->insert(
                'INSERT INTO pacchetto_tariffe (pacchetto_id, tariffa_id, quantita, note) VALUES (?,?,?,?)',
                [$pacchettoId, $t['tariffa_id'], $t['quantita'] ?? 1, $t['note'] ?? null]
            );
        }
    }

    public static function delete(int $id): void
    {
        self::db()->execute('DELETE FROM pacchetto_tariffe WHERE pacchetto_id=?', [$id]);
        self::db()->execute('DELETE FROM pacchetti WHERE id=?', [$id]);
    }
}
