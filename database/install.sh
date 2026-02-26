#!/bin/bash
# ============================================================
# Preventivo Commercialisti - Script di installazione DB
# Copyright (C) 2024 Alessandro Scapuzzi <dado70@gmail.com>
# ============================================================

echo "=== Preventivo Commercialisti - Installazione Database ==="
echo ""

# Chiedi credenziali MySQL root
read -p "Host MySQL [localhost]: " DB_HOST
DB_HOST=${DB_HOST:-localhost}

read -p "Utente MySQL root [root]: " DB_ROOT
DB_ROOT=${DB_ROOT:-root}

read -s -p "Password MySQL root: " DB_ROOT_PASS
echo ""

read -p "Nome database da creare [preventivo_comm]: " DB_NAME
DB_NAME=${DB_NAME:-preventivo_comm}

read -p "Utente DB da creare [preventivo_user]: " DB_USER
DB_USER=${DB_USER:-preventivo_user}

read -s -p "Password per l'utente DB: " DB_PASS
echo ""
echo ""

# Crea database, utente e permessi
mysql -h "$DB_HOST" -u "$DB_ROOT" -p"$DB_ROOT_PASS" <<EOF
CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '$DB_USER'@'$DB_HOST' IDENTIFIED BY '$DB_PASS';
GRANT ALL PRIVILEGES ON \`$DB_NAME\`.* TO '$DB_USER'@'$DB_HOST';
FLUSH PRIVILEGES;
EOF

if [ $? -ne 0 ]; then
    echo "ERRORE: impossibile creare il database. Controlla le credenziali."
    exit 1
fi

echo "Database e utente creati con successo."

# Esegui schema
echo "Esecuzione schema.sql..."
mysql -h "$DB_HOST" -u "$DB_ROOT" -p"$DB_ROOT_PASS" "$DB_NAME" < "$(dirname "$0")/schema.sql"

# Esegui seed
echo "Caricamento tariffe (seed_tariffe.sql)..."
mysql -h "$DB_HOST" -u "$DB_ROOT" -p"$DB_ROOT_PASS" "$DB_NAME" < "$(dirname "$0")/seed_tariffe.sql"

echo ""
echo "=== Installazione completata! ==="
echo ""
echo "Aggiorna il file config/config.php con:"
echo "  DB_HOST: $DB_HOST"
echo "  DB_NAME: $DB_NAME"
echo "  DB_USER: $DB_USER"
echo "  DB_PASS: [la password che hai inserito]"
echo ""
echo "Credenziali iniziali:"
echo "  Email:    dado70@gmail.com"
echo "  Password: password"
echo ""
echo "  !!! CAMBIA LA PASSWORD SUBITO DOPO IL PRIMO ACCESSO !!!"
