<?php
/**
 * Preventivo Commercialisti - Model Preventivo
 * Copyright (C) 2024 Alessandro Scapuzzi <dado70@gmail.com>
 * Licenza: GPL v3
 */

namespace App\Models;

use App\Core\Database;

class Preventivo
{
    private static function db(): Database
    {
        return Database::getInstance();
    }

    public static function getAll(array $filtri = []): array
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($filtri['cliente_id'])) {
            $where[] = 'p.cliente_id = ?';
            $params[] = $filtri['cliente_id'];
        }
        if (!empty($filtri['stato'])) {
            $where[] = 'p.stato = ?';
            $params[] = $filtri['stato'];
        }
        if (!empty($filtri['anno'])) {
            $where[] = 'p.anno_riferimento = ?';
            $params[] = $filtri['anno'];
        }

        $whereStr = implode(' AND ', $where);
        return self::db()->fetchAll(
            "SELECT p.*, c.ragione_sociale AS cliente_nome,
                    CONCAT(IFNULL(pr.titolo,''),' ',IFNULL(pr.nome,''),' ',IFNULL(pr.cognome,'')) AS professionista_nome
             FROM preventivi p
             JOIN clienti c ON p.cliente_id = c.id
             LEFT JOIN professionisti pr ON p.professionista_id = pr.id
             WHERE $whereStr
             ORDER BY p.data_preventivo DESC, p.id DESC",
            $params
        );
    }

    public static function findById(int $id): array|false
    {
        return self::db()->fetchOne(
            'SELECT p.*, c.ragione_sociale AS cliente_nome, c.partita_iva AS cliente_piva,
                    c.codice_fiscale AS cliente_cf, c.indirizzo AS cliente_indirizzo,
                    c.cap AS cliente_cap, c.citta AS cliente_citta, c.provincia AS cliente_prov,
                    c.pec AS cliente_pec,
                    CONCAT(IFNULL(pr.titolo,"")," ",IFNULL(pr.nome,"")," ",IFNULL(pr.cognome,"")) AS professionista_nome
             FROM preventivi p
             JOIN clienti c ON p.cliente_id = c.id
             LEFT JOIN professionisti pr ON p.professionista_id = pr.id
             WHERE p.id = ?',
            [$id]
        );
    }

    public static function getVoci(int $preventivoId): array
    {
        return self::db()->fetchAll(
            'SELECT * FROM preventivo_voci WHERE preventivo_id = ? ORDER BY ordine, id',
            [$preventivoId]
        );
    }

    public static function generaNumero(): string
    {
        $db = self::db();
        $anno = date('Y');
        $prefisso = $db->fetchOne("SELECT valore FROM impostazioni WHERE chiave='numero_preventivo_prefisso'")['valore'] ?? 'PREV';

        $ultimo = $db->fetchOne(
            'SELECT MAX(CAST(SUBSTRING_INDEX(numero, "/", -1) AS UNSIGNED)) AS max_num FROM preventivi WHERE anno_riferimento = ?',
            [$anno]
        );
        $progressivo = (int)($ultimo['max_num'] ?? 0) + 1;

        return $prefisso . '/' . $anno . '/' . str_pad($progressivo, 4, '0', STR_PAD_LEFT);
    }

    public static function create(array $data): int|string
    {
        return self::db()->insert(
            'INSERT INTO preventivi (numero, cliente_id, professionista_id, data_preventivo, data_scadenza, anno_riferimento,
             titolo, note_interne, note_cliente, sconto1, sconto2, imponibile, importo_sconto, imponibile_scontato,
             iva_perc, importo_iva, totale, stato, utente_id)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
            [
                $data['numero'],
                $data['cliente_id'],
                $data['professionista_id'] ?: null,
                $data['data_preventivo'],
                $data['data_scadenza'] ?: null,
                $data['anno_riferimento'],
                $data['titolo'] ?? null,
                $data['note_interne'] ?? null,
                $data['note_cliente'] ?? null,
                (float)($data['sconto1'] ?? 0),
                (float)($data['sconto2'] ?? 0),
                (float)($data['imponibile'] ?? 0),
                (float)($data['importo_sconto'] ?? 0),
                (float)($data['imponibile_scontato'] ?? 0),
                (float)($data['iva_perc'] ?? ALIQUOTA_IVA),
                (float)($data['importo_iva'] ?? 0),
                (float)($data['totale'] ?? 0),
                $data['stato'] ?? 'bozza',
                $data['utente_id'] ?? null,
            ]
        );
    }

    public static function updateTotali(int $id, array $totali): void
    {
        self::db()->execute(
            'UPDATE preventivi SET imponibile=?, importo_sconto=?, imponibile_scontato=?, importo_iva=?, totale=?,
             sconto1=?, sconto2=? WHERE id=?',
            [
                $totali['imponibile'],
                $totali['importo_sconto'],
                $totali['imponibile_scontato'],
                $totali['importo_iva'],
                $totali['totale'],
                $totali['sconto1'],
                $totali['sconto2'],
                $id,
            ]
        );
    }

    public static function updateStato(int $id, string $stato): void
    {
        self::db()->execute('UPDATE preventivi SET stato=? WHERE id=?', [$stato, $id]);
    }

    public static function update(int $id, array $data): void
    {
        self::db()->execute(
            'UPDATE preventivi SET cliente_id=?, professionista_id=?, data_preventivo=?, data_scadenza=?,
             anno_riferimento=?, titolo=?, note_interne=?, note_cliente=?, sconto1=?, sconto2=?,
             imponibile=?, importo_sconto=?, imponibile_scontato=?, iva_perc=?, importo_iva=?, totale=?, stato=?
             WHERE id=?',
            [
                $data['cliente_id'],
                $data['professionista_id'] ?: null,
                $data['data_preventivo'],
                $data['data_scadenza'] ?: null,
                $data['anno_riferimento'],
                $data['titolo'] ?? null,
                $data['note_interne'] ?? null,
                $data['note_cliente'] ?? null,
                (float)($data['sconto1'] ?? 0),
                (float)($data['sconto2'] ?? 0),
                (float)($data['imponibile'] ?? 0),
                (float)($data['importo_sconto'] ?? 0),
                (float)($data['imponibile_scontato'] ?? 0),
                (float)($data['iva_perc'] ?? ALIQUOTA_IVA),
                (float)($data['importo_iva'] ?? 0),
                (float)($data['totale'] ?? 0),
                $data['stato'] ?? 'bozza',
                $id
            ]
        );
    }

    public static function addVoce(int $preventivoId, array $voce): int|string
    {
        return self::db()->insert(
            'INSERT INTO preventivo_voci (preventivo_id, tipo_voce, tariffa_id, pacchetto_id, codice, descrizione,
             frequenza, mesi, quantita, importo_unitario, importo_riga, ordine, note)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)',
            [
                $preventivoId,
                $voce['tipo_voce'] ?? 'tariffa',
                $voce['tariffa_id'] ?: null,
                $voce['pacchetto_id'] ?: null,
                $voce['codice'] ?? null,
                $voce['descrizione'],
                $voce['frequenza'] ?? 'a_prestazione',
                (int)($voce['mesi'] ?? 12),
                (float)($voce['quantita'] ?? 1),
                (float)$voce['importo_unitario'],
                (float)$voce['importo_riga'],
                (int)($voce['ordine'] ?? 0),
                $voce['note'] ?? null,
            ]
        );
    }

    public static function deleteVoci(int $preventivoId): void
    {
        self::db()->execute('DELETE FROM preventivo_voci WHERE preventivo_id = ?', [$preventivoId]);
    }

    public static function delete(int $id): void
    {
        self::db()->execute('DELETE FROM preventivo_voci WHERE preventivo_id = ?', [$id]);
        self::db()->execute('DELETE FROM preventivi WHERE id = ?', [$id]);
    }

    /**
     * Calcola i totali del preventivo applicando sconto1 e sconto2 in cascata + IVA.
     */
    public static function calcolaTotali(float $imponibile, float $sconto1, float $sconto2, float $ivaPerc = ALIQUOTA_IVA): array
    {
        $dopoSconto1 = $imponibile * (1 - $sconto1 / 100);
        $dopoSconto2 = $dopoSconto1 * (1 - $sconto2 / 100);

        $importoSconto = $imponibile - $dopoSconto2;
        $importoIva    = $dopoSconto2 * ($ivaPerc / 100);
        $totale        = $dopoSconto2 + $importoIva;

        return [
            'imponibile'          => round($imponibile, 2),
            'importo_sconto'      => round($importoSconto, 2),
            'imponibile_scontato' => round($dopoSconto2, 2),
            'importo_iva'         => round($importoIva, 2),
            'totale'              => round($totale, 2),
            'sconto1'             => $sconto1,
            'sconto2'             => $sconto2,
        ];
    }

    public static function getStats(): array
    {
        return self::db()->fetchOne(
            "SELECT
                COUNT(*) AS totale,
                SUM(CASE WHEN stato='bozza' THEN 1 ELSE 0 END) AS bozze,
                SUM(CASE WHEN stato='inviato' THEN 1 ELSE 0 END) AS inviati,
                SUM(CASE WHEN stato='accettato' THEN 1 ELSE 0 END) AS accettati,
                SUM(CASE WHEN stato='accettato' THEN totale ELSE 0 END) AS valore_accettati
             FROM preventivi WHERE YEAR(data_preventivo) = YEAR(CURDATE())"
        );
    }
}
