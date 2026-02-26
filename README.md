# Preventivo Commercialisti

**Applicazione web per la gestione dei preventivi degli studi commercialistici**

[![Licenza: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)
[![PHP](https://img.shields.io/badge/PHP-8.1%2B-purple.svg)](https://www.php.net/)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-blueviolet.svg)](https://getbootstrap.com/)

## Descrizione

**Preventivo Commercialisti** è un'applicazione web open source (GPL v3) sviluppata per studi commercialistici italiani. Consente di:

- Gestire il listino tariffe basato sugli **Onorari Consigliati ANC**
- Creare preventivi dettagliati per i clienti
- Calcolare automaticamente tariffe cedolini, sconti doppi e IVA
- Esportare i preventivi in **PDF** e **ODS** (LibreOffice)
- Gestire l'anagrafica dello studio, dei professionisti e dei clienti
- Creare pacchetti di servizi personalizzati

## Requisiti

- **Linux** (Ubuntu 20.04+ o Debian 11+)
- **Apache** 2.4+ con `mod_rewrite`
- **PHP** 8.1+ con estensioni: `pdo_mysql`, `mbstring`, `gd`, `zip`, `xml`
- **MariaDB** 10.5+ (o MySQL 8.0+)
- **Composer** 2.x

## Installazione

### 1. Clona il repository

```bash
git clone https://github.com/dado70/preventivo-commercialisti.git
cd preventivo-commercialisti
```

### 2. Installa le dipendenze PHP

```bash
composer install --no-dev --optimize-autoloader
```

### 3. Configura il database

```bash
cd database
chmod +x install.sh
./install.sh
```

Oppure manualmente:

```sql
CREATE DATABASE preventivo_comm CHARACTER SET utf8mb4;
CREATE USER 'preventivo_user'@'localhost' IDENTIFIED BY 'TUA_PASSWORD';
GRANT ALL PRIVILEGES ON preventivo_comm.* TO 'preventivo_user'@'localhost';

mysql -u root -p preventivo_comm < database/schema.sql
mysql -u root -p preventivo_comm < database/seed_tariffe.sql
```

### 4. Configura l'applicazione

Copia e modifica il file di configurazione:

```bash
cp config/config.php config/config.local.php
```

Modifica `config/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'preventivo_comm');
define('DB_USER', 'preventivo_user');
define('DB_PASS', 'TUA_PASSWORD_SICURA');
define('BASE_URL', 'http://tuodominio.it/preventivo-commercialisti/public');
```

Per il recupero password via email, configura le variabili `MAIL_*`.

### 5. Configura Apache

Crea un VirtualHost o configura il DocumentRoot su `public/`:

```apache
<VirtualHost *:80>
    ServerName preventivo.tuodominio.it
    DocumentRoot /var/www/preventivo-commercialisti/public

    <Directory /var/www/preventivo-commercialisti/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Abilita mod_rewrite:
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### 6. Permessi

```bash
chmod -R 755 /var/www/preventivo-commercialisti
chmod -R 775 /var/www/preventivo-commercialisti/public/uploads
chmod -R 775 /var/www/preventivo-commercialisti/logs
chown -R www-data:www-data /var/www/preventivo-commercialisti
```

## Primo Accesso

- **URL**: `http://tuodominio.it/preventivo-commercialisti/public/`
- **Email**: `dado70@gmail.com`
- **Password**: `password`

> ⚠️ **CAMBIA IMMEDIATAMENTE LA PASSWORD** dopo il primo accesso!

## Struttura del Progetto

```
preventivo-commercialisti/
├── public/               # Web root (DocumentRoot Apache)
│   ├── index.php         # Entry point
│   ├── .htaccess         # Rewrite rules
│   └── assets/           # CSS, JS, immagini
├── src/
│   ├── Core/             # Database, Auth, Router, Session, View
│   ├── Controllers/      # Un controller per modulo
│   ├── Models/           # Modelli dati
│   └── Views/            # Template PHP con Bootstrap 5
├── database/
│   ├── schema.sql        # Schema DB completo
│   ├── seed_tariffe.sql  # Tariffe ANC precaricate
│   └── install.sh        # Script installazione
├── config/
│   └── config.php        # Configurazione applicazione
├── logs/                 # Log errori (auto-creata)
├── composer.json
├── LICENSE               # GPL v3
└── README.md
```

## Dipendenze

| Pacchetto | Versione | Uso |
|-----------|----------|-----|
| [mpdf/mpdf](https://mpdf.github.io/) | ^8.2 | Generazione PDF |
| [phpoffice/phpspreadsheet](https://phpspreadsheet.readthedocs.io/) | ^2.1 | Generazione ODS |
| [phpmailer/phpmailer](https://github.com/PHPMailer/PHPMailer) | ^6.9 | Invio email recupero password |

## Funzionalità

### Tariffe
- Listino completo basato su onorari ANC (oltre 120 voci)
- Suddiviso in sezioni: A.1 Contabilità, A.2 Fiscale, A.3 Consulenza, A.4 ETS, B Adempimenti, C.1 Personale
- Tipi: fisso, minimo (da €), a preventivo, formula, tabella cedolini
- Modificabile dall'amministratore

### Pacchetti
- Aggregati di più tariffe con possibilità di prezzo fisso
- Aggiunta rapida di un intero pacchetto al preventivo

### Preventivi
- Builder interattivo con ricerca tariffe per sezione
- Calcolo automatico cedolini (tabella 1-60 dipendenti, 12/13/14 mensilità)
- Doppio sconto in cascata (sconto1 + sconto2)
- IVA 22% separata con riepilogo dettagliato
- Export PDF e ODS
- Stati: bozza, inviato, accettato, rifiutato, scaduto

### Anagrafica Studio
- Dati completi per il mandato professionale
- Upload logo per intestazione documenti

### Utenti e Sicurezza
- Multi-utente con ruoli (admin / operatore)
- Recupero password GDPR-compliant (token con scadenza 1h)
- Protezione CSRF sul login
- Password con requisiti minimi di sicurezza

## Licenza

**Preventivo Commercialisti** è software libero distribuito sotto i termini della
[GNU General Public License versione 3](https://www.gnu.org/licenses/gpl-3.0.html).

```
Copyright (C) 2024 Alessandro Scapuzzi <dado70@gmail.com>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.
```

## Autore

**Alessandro Scapuzzi**
Email: dado70@gmail.com
GitHub: https://github.com/dado70

## Contribuire

Pull request benvenute! Per modifiche importanti, apri prima una issue per discutere cosa vorresti cambiare.

## Roadmap

- [ ] Modulo Dolibarr/CRM
- [ ] Template Joomla
- [ ] Firma digitale mandato professionale
- [ ] Invio preventivo via email al cliente
- [ ] Dashboard analytics avanzata
- [ ] API REST
