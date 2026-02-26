<?php
/**
 * Preventivo Commercialisti - Model Utente
 * Copyright (C) 2024 Alessandro Scapuzzi <dado70@gmail.com>
 * Licenza: GPL v3
 */

namespace App\Models;

use App\Core\Database;

class Utente
{
    private static function db(): Database
    {
        return Database::getInstance();
    }

    public static function findById(int $id): array|false
    {
        return self::db()->fetchOne('SELECT * FROM utenti WHERE id = ?', [$id]);
    }

    public static function findByEmail(string $email): array|false
    {
        return self::db()->fetchOne('SELECT * FROM utenti WHERE email = ?', [$email]);
    }

    public static function findByResetToken(string $token): array|false
    {
        return self::db()->fetchOne(
            'SELECT * FROM utenti WHERE token_reset = ? AND token_scadenza > NOW() AND attivo = 1',
            [$token]
        );
    }

    public static function getAll(): array
    {
        return self::db()->fetchAll('SELECT id, nome, cognome, email, ruolo, attivo, ultimo_accesso, created_at FROM utenti ORDER BY cognome, nome');
    }

    public static function create(array $data): int|string
    {
        return self::db()->insert(
            'INSERT INTO utenti (nome, cognome, email, password_hash, ruolo, attivo) VALUES (?,?,?,?,?,1)',
            [$data['nome'], $data['cognome'], $data['email'],
             password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]),
             $data['ruolo']]
        );
    }

    public static function update(int $id, array $data): int
    {
        $fields = ['nome = ?', 'cognome = ?', 'email = ?', 'ruolo = ?', 'attivo = ?'];
        $params = [$data['nome'], $data['cognome'], $data['email'], $data['ruolo'], $data['attivo'] ?? 1, $id];

        if (!empty($data['password'])) {
            $fields[] = 'password_hash = ?';
            $params = [$data['nome'], $data['cognome'], $data['email'], $data['ruolo'], $data['attivo'] ?? 1,
                       password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]), $id];
        }

        return self::db()->execute(
            'UPDATE utenti SET ' . implode(', ', $fields) . ' WHERE id = ?',
            $params
        );
    }

    public static function changePassword(int $id, string $newPassword): void
    {
        self::db()->execute(
            'UPDATE utenti SET password_hash = ? WHERE id = ?',
            [password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]), $id]
        );
    }

    public static function updateLastAccess(int $id): void
    {
        self::db()->execute('UPDATE utenti SET ultimo_accesso = NOW() WHERE id = ?', [$id]);
    }

    public static function setResetToken(int $id, string $token, string $scadenza): void
    {
        self::db()->execute(
            'UPDATE utenti SET token_reset = ?, token_scadenza = ? WHERE id = ?',
            [$token, $scadenza, $id]
        );
    }

    public static function clearResetToken(int $id): void
    {
        self::db()->execute(
            'UPDATE utenti SET token_reset = NULL, token_scadenza = NULL WHERE id = ?',
            [$id]
        );
    }

    public static function updatePassword(int $id, string $hash): void
    {
        self::db()->execute('UPDATE utenti SET password_hash = ? WHERE id = ?', [$hash, $id]);
    }

    public static function delete(int $id): void
    {
        self::db()->execute('DELETE FROM utenti WHERE id = ?', [$id]);
    }
}
