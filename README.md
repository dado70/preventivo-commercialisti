# Preventivo Commercialisti

**Applicazione web per la gestione dei preventivi degli studi commercialistici**

[![Licenza: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)
[![PHP](https://img.shields.io/badge/PHP-8.1%2B-purple.svg)](https://www.php.net/)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-blueviolet.svg)](https://getbootstrap.com/)
[![MariaDB](https://img.shields.io/badge/MariaDB-10.5%2B-blue.svg)](https://mariadb.org/)

## Descrizione

**Preventivo Commercialisti** è un'applicazione web open source (GPL v3) sviluppata per studi commercialistici italiani. Consente di:

- Gestire il listino tariffe basato sugli **Onorari Consigliati ANC** (oltre 120 voci precaricate)
- Creare preventivi dettagliati per i clienti con builder interattivo
- Calcolare automaticamente tariffe cedolini, sconti doppi in cascata e IVA
- Esportare i preventivi in **PDF** e **ODS** (LibreOffice)
- Gestire l'anagrafica dello studio, dei professionisti e dei clienti
- Creare pacchetti di servizi personalizzati aggregando più tariffe
- Multi-utenza con ruoli (admin / operatore) e recupero password GDPR-compliant

---

## Installazione rapida (server condiviso / shared hosting)

### 1. Scarica il pacchetto

Scarica il file ZIP dalla [pagina Release](https://github.com/dado70/preventivo-commercialisti/releases) oppure clona il repository:

```bash
git clone https://github.com/dado70/preventivo-commercialisti.git
```

### 2. Carica via FTP

Carica **tutto il contenuto** della cartella nella root del tuo dominio (o in una sottocartella, es. `/preventivo/`) tramite FTP/SFTP.

### 3. Avvia il web installer

Apri nel browser:

```
http://tuodominio.it/installer.php
```

Il wizard guida in **5 step automatici**:

| Step | Descrizione |
|------|-------------|
| 1 | Verifica requisiti (PHP, estensioni, permessi) |
| 2 | Configurazione database (crea il DB se non esiste) |
| 3 | Account amministratore + URL applicazione + email SMTP |
| 4 | Riepilogo e conferma |
| 5 | Installazione automatica → `installer.php` si auto-elimina |

> ⚠️ Il file `installer.php` viene eliminato automaticamente al termine. Cartelle sensibili (`src/`, `config/`, `vendor/`, `database/`) sono protette dall'accesso diretto tramite `.htaccess`.

---

## Installazione su server locale (LAMP)

### Requisiti

- **Linux** (Ubuntu 20.04+ / Debian 11+)
- **Apache** 2.4+ con `mod_rewrite`
- **PHP** 8.1+ con estensioni: `pdo_mysql`, `mbstring`, `gd`, `zip`, `xml`, `openssl`, `fileinfo`
- **MariaDB** 10.5+ (o MySQL 8.0+)
- **Composer** 2.x

### Setup

```bash
# 1. Clona il repository
git clone https://github.com/dado70/preventivo-commercialisti.git
cd preventivo-commercialisti

# 2. Installa le dipendenze PHP
composer install --no-dev --optimize-autoloader

# 3. Installa il database
cd database && chmod +x install.sh && ./install.sh

# 4. Oppure manualmente
mysql -u root -p preventivo_comm < database/schema.sql
mysql -u root -p preventivo_comm < database/seed_tariffe.sql
```

### Configurazione Apache (VirtualHost)

```apache
<VirtualHost *:80>
    ServerName preventivo.localhost
    DocumentRoot /var/www/html/preventivo-commercialisti

    <Directory /var/www/html/preventivo-commercialisti>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

```bash
sudo a2ensite preventivo-comm.conf
sudo a2enmod rewrite
sudo systemctl reload apache2
```

### Configura `config/config.php`

```php
define('BASE_URL', 'http://preventivo.localhost');
define('DB_HOST',  'localhost');
define('DB_NAME',  'preventivo_comm');
define('DB_USER',  'preventivo_user');
define('DB_PASS',  'tua_password');
```

---

## Struttura del progetto

```
preventivo-commercialisti/
├── installer.php         # Web installer (si auto-elimina dopo l'uso)
├── index.php             # Entry point applicazione
├── .htaccess             # Rewrite rules + protezione cartelle sensibili
├── assets/               # CSS, JS, immagini pubblici
├── uploads/              # Logo studio e allegati (scrivibile)
├── src/
│   ├── Core/             # Database (PDO), Auth, Router, Session, View
│   ├── Controllers/      # Un controller per modulo
│   ├── Models/           # Modelli dati
│   └── Views/            # Template PHP con Bootstrap 5
├── config/
│   └── config.php        # Configurazione (generata dall'installer)
├── database/
│   ├── schema.sql        # Schema DB completo
│   ├── seed_tariffe.sql  # 120+ tariffe ANC precaricate
│   └── install.sh        # Script installazione da riga di comando
├── vendor/               # Dipendenze Composer (incluse nel pacchetto)
├── logs/                 # Log errori applicazione
├── composer.json
├── composer.lock
└── LICENSE               # GPL v3
```

---

## Dipendenze

| Pacchetto | Versione | Uso |
|-----------|----------|-----|
| [mpdf/mpdf](https://mpdf.github.io/) | ^8.2 | Generazione PDF |
| [phpoffice/phpspreadsheet](https://phpspreadsheet.readthedocs.io/) | ^2.1 | Generazione ODS (LibreOffice) |
| [phpmailer/phpmailer](https://github.com/PHPMailer/PHPMailer) | ^6.9 | Invio email recupero password |

---

## Funzionalità

### Tariffe ANC precaricate (120+ voci)
Suddivise nelle sezioni ufficiali:
- **A.1** Contabilità (semplificata, ordinaria, IVA)
- **A.2** Fiscale (dichiarazioni redditi, bilanci, IMU, adempimenti)
- **A.3** Consulenza specifica
- **A.4** Enti del Terzo Settore
- **B** Altri adempimenti (CCIAA, vidimazioni, PEC, hosting, domiciliazione)
- **C.1** Amministrazione del personale (cedolini, INPS, INAIL, contratti)

### Preventivi
- Builder interattivo con ricerca e selezione tariffe per sezione
- Aggiunta rapida di **pacchetti** (aggregati di tariffe)
- Calcolo automatico **cedolini** (tabella scaglioni 1-60 dipendenti, 12/13/14 mensilità)
- **Doppio sconto in cascata** (sconto1% → sconto2%)
- **IVA 22%** separata con riepilogo dettagliato
- Esportazione **PDF** e **ODS**
- Stati: bozza, inviato, accettato, rifiutato, scaduto

### Anagrafica
- **Studio**: dati completi per il mandato professionale, upload logo
- **Professionisti**: elenco con iscrizione albo e ordine professionale
- **Clienti**: anagrafica completa con tipo contabilità, regime fiscale, sconti predefiniti

### Sicurezza
- Multi-utenza con ruoli **admin** e **operatore**
- Recupero password **GDPR-compliant** (token con scadenza 1 ora)
- Protezione **CSRF** sul login
- Password con requisiti minimi di sicurezza (8 char, maiuscola, numero)
- Cartelle sensibili protette via `.htaccess`

---

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
Email: [dado70@gmail.com](mailto:dado70@gmail.com)
GitHub: [https://github.com/dado70](https://github.com/dado70)

---

## Roadmap

- [ ] Modulo per Dolibarr (CRM/ERP)
- [ ] Template/modulo per Joomla
- [ ] Firma digitale mandato professionale
- [ ] Invio preventivo via email al cliente
- [ ] Dashboard analytics avanzata
- [ ] API REST
