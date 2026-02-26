<?php
/**
 * Preventivo Commercialisti - Model Cliente
 * Copyright (C) 2024 Alessandro Scapuzzi <dado70@gmail.com>
 * Licenza: GPL v3
 */

namespace App\Models;

use App\Core\Database;

class Cliente
{
    private static function db(): Database
    {
        return Database::getInstance();
    }

    public static function getAll(bool $soloAttivi = true): array
    {
        $where = $soloAttivi ? 'WHERE c.attivo = 1' : '';
        return self::db()->fetchAll(
            "SELECT c.*, CONCAT(p.titolo,' ',p.nome,' ',p.cognome) AS professionista_nome
             FROM clienti c
             LEFT JOIN professionisti p ON c.professionista_id = p.id
             $where
             ORDER BY c.ragione_sociale"
        );
    }

    public static function findById(int $id): array|false
    {
        return self::db()->fetchOne(
            'SELECT c.*, CONCAT(IFNULL(p.titolo,"")," ",IFNULL(p.nome,"")," ",IFNULL(p.cognome,"")) AS professionista_nome
             FROM clienti c LEFT JOIN professionisti p ON c.professionista_id = p.id WHERE c.id = ?',
            [$id]
        );
    }

    public static function search(string $term): array
    {
        $like = '%' . $term . '%';
        return self::db()->fetchAll(
            'SELECT id, ragione_sociale, partita_iva, codice_fiscale FROM clienti WHERE attivo=1
             AND (ragione_sociale LIKE ? OR partita_iva LIKE ? OR codice_fiscale LIKE ?)
             ORDER BY ragione_sociale LIMIT 20',
            [$like, $like, $like]
        );
    }

    public static function create(array $data): int|string
    {
        return self::db()->insert(
            'INSERT INTO clienti (ragione_sociale, nome_referente, forma_giuridica, partita_iva, codice_fiscale,
             indirizzo, cap, citta, provincia, telefono, email, pec, sdi_codice, tipo_contabilita,
             regime_fiscale, settore_attivita, codice_ateco, professionista_id, sconto1, sconto2, note, attivo)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,1)',
            [
                $data['ragione_sociale'],
                $data['nome_referente'] ?? null,
                $data['forma_giuridica'] ?? null,
                $data['partita_iva'] ?? null,
                $data['codice_fiscale'] ?? null,
                $data['indirizzo'] ?? null,
                $data['cap'] ?? null,
                $data['citta'] ?? null,
                $data['provincia'] ?? null,
                $data['telefono'] ?? null,
                $data['email'] ?? null,
                $data['pec'] ?? null,
                $data['sdi_codice'] ?? null,
                $data['tipo_contabilita'] ?? 'semplificata',
                $data['regime_fiscale'] ?? null,
                $data['settore_attivita'] ?? null,
                $data['codice_ateco'] ?? null,
                $data['professionista_id'] ?: null,
                (float)($data['sconto1'] ?? 0),
                (float)($data['sconto2'] ?? 0),
                $data['note'] ?? null,
            ]
        );
    }

    public static function update(int $id, array $data): int
    {
        return self::db()->execute(
            'UPDATE clienti SET ragione_sociale=?, nome_referente=?, forma_giuridica=?, partita_iva=?, codice_fiscale=?,
             indirizzo=?, cap=?, citta=?, provincia=?, telefono=?, email=?, pec=?, sdi_codice=?, tipo_contabilita=?,
             regime_fiscale=?, settore_attivita=?, codice_ateco=?, professionista_id=?, sconto1=?, sconto2=?, note=?, attivo=?
             WHERE id=?',
            [
                $data['ragione_sociale'],
                $data['nome_referente'] ?? null,
                $data['forma_giuridica'] ?? null,
                $data['partita_iva'] ?? null,
                $data['codice_fiscale'] ?? null,
                $data['indirizzo'] ?? null,
                $data['cap'] ?? null,
                $data['citta'] ?? null,
                $data['provincia'] ?? null,
                $data['telefono'] ?? null,
                $data['email'] ?? null,
                $data['pec'] ?? null,
                $data['sdi_codice'] ?? null,
                $data['tipo_contabilita'] ?? 'semplificata',
                $data['regime_fiscale'] ?? null,
                $data['settore_attivita'] ?? null,
                $data['codice_ateco'] ?? null,
                $data['professionista_id'] ?: null,
                (float)($data['sconto1'] ?? 0),
                (float)($data['sconto2'] ?? 0),
                $data['note'] ?? null,
                $data['attivo'] ?? 1,
                $id
            ]
        );
    }

    public static function delete(int $id): void
    {
        self::db()->execute('UPDATE clienti SET attivo=0 WHERE id=?', [$id]);
    }
}
