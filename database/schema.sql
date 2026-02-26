-- ============================================================
-- Preventivo Commercialisti - Schema Database
-- Copyright (C) 2024 Alessandro Scapuzzi <dado70@gmail.com>
-- Licenza: GPL v3
-- ============================================================

CREATE DATABASE IF NOT EXISTS `preventivo_comm`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `preventivo_comm`;

-- ============================================================
-- UTENTI
-- ============================================================
CREATE TABLE IF NOT EXISTS `utenti` (
    `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nome`            VARCHAR(100) NOT NULL,
    `cognome`         VARCHAR(100) NOT NULL,
    `email`           VARCHAR(200) NOT NULL UNIQUE,
    `password_hash`   VARCHAR(255) NOT NULL,
    `ruolo`           ENUM('admin','operatore') NOT NULL DEFAULT 'operatore',
    `token_reset`     VARCHAR(64) NULL,
    `token_scadenza`  DATETIME NULL,
    `attivo`          TINYINT(1) NOT NULL DEFAULT 1,
    `ultimo_accesso`  DATETIME NULL,
    `created_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- STUDIO
-- ============================================================
CREATE TABLE IF NOT EXISTS `studio` (
    `id`                    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `ragione_sociale`       VARCHAR(200) NOT NULL,
    `forma_giuridica`       VARCHAR(100) DEFAULT NULL,
    `partita_iva`           VARCHAR(20) DEFAULT NULL,
    `codice_fiscale`        VARCHAR(20) DEFAULT NULL,
    `indirizzo`             VARCHAR(250) DEFAULT NULL,
    `cap`                   VARCHAR(10) DEFAULT NULL,
    `citta`                 VARCHAR(100) DEFAULT NULL,
    `provincia`             VARCHAR(5) DEFAULT NULL,
    `telefono`              VARCHAR(30) DEFAULT NULL,
    `fax`                   VARCHAR(30) DEFAULT NULL,
    `email`                 VARCHAR(200) DEFAULT NULL,
    `pec`                   VARCHAR(200) DEFAULT NULL,
    `sito_web`              VARCHAR(200) DEFAULT NULL,
    `iban`                  VARCHAR(34) DEFAULT NULL,
    `banca`                 VARCHAR(150) DEFAULT NULL,
    `ordine_professionale`  VARCHAR(200) DEFAULT NULL,
    `n_iscrizione_ordine`   VARCHAR(50) DEFAULT NULL,
    `sez_registro`          VARCHAR(100) DEFAULT NULL,
    `logo_path`             VARCHAR(300) DEFAULT NULL,
    `note`                  TEXT DEFAULT NULL,
    `created_at`            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- PROFESSIONISTI DELLO STUDIO
-- ============================================================
CREATE TABLE IF NOT EXISTS `professionisti` (
    `id`                    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nome`                  VARCHAR(100) NOT NULL,
    `cognome`               VARCHAR(100) NOT NULL,
    `titolo`                VARCHAR(50) DEFAULT NULL COMMENT 'Es: Dott., Dott.ssa, Rag.',
    `qualifica`             VARCHAR(150) DEFAULT NULL COMMENT 'Es: Commercialista, Consulente del Lavoro',
    `codice_fiscale`        VARCHAR(20) DEFAULT NULL,
    `partita_iva`           VARCHAR(20) DEFAULT NULL,
    `email`                 VARCHAR(200) DEFAULT NULL,
    `pec`                   VARCHAR(200) DEFAULT NULL,
    `telefono`              VARCHAR(30) DEFAULT NULL,
    `ordine_professionale`  VARCHAR(200) DEFAULT NULL,
    `n_iscrizione_ordine`   VARCHAR(50) DEFAULT NULL,
    `sez_registro`          VARCHAR(100) DEFAULT NULL,
    `provincia_ordine`      VARCHAR(100) DEFAULT NULL,
    `firma_path`            VARCHAR(300) DEFAULT NULL,
    `attivo`                TINYINT(1) NOT NULL DEFAULT 1,
    `note`                  TEXT DEFAULT NULL,
    `created_at`            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- CLIENTI
-- ============================================================
CREATE TABLE IF NOT EXISTS `clienti` (
    `id`                    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `ragione_sociale`       VARCHAR(200) NOT NULL,
    `nome_referente`        VARCHAR(200) DEFAULT NULL,
    `forma_giuridica`       VARCHAR(100) DEFAULT NULL,
    `partita_iva`           VARCHAR(20) DEFAULT NULL,
    `codice_fiscale`        VARCHAR(20) DEFAULT NULL,
    `indirizzo`             VARCHAR(250) DEFAULT NULL,
    `cap`                   VARCHAR(10) DEFAULT NULL,
    `citta`                 VARCHAR(100) DEFAULT NULL,
    `provincia`             VARCHAR(5) DEFAULT NULL,
    `telefono`              VARCHAR(30) DEFAULT NULL,
    `email`                 VARCHAR(200) DEFAULT NULL,
    `pec`                   VARCHAR(200) DEFAULT NULL,
    `sdi_codice`            VARCHAR(10) DEFAULT NULL COMMENT 'Codice SDI per fatturazione elettronica',
    `tipo_contabilita`      ENUM('semplificata','ordinaria','forfettaria','minima','nessuna') DEFAULT 'semplificata',
    `regime_fiscale`        VARCHAR(100) DEFAULT NULL,
    `settore_attivita`      VARCHAR(200) DEFAULT NULL,
    `codice_ateco`          VARCHAR(20) DEFAULT NULL,
    `professionista_id`     INT UNSIGNED DEFAULT NULL COMMENT 'Professionista responsabile',
    `sconto1`               DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    `sconto2`               DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    `note`                  TEXT DEFAULT NULL,
    `attivo`                TINYINT(1) NOT NULL DEFAULT 1,
    `created_at`            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`professionista_id`) REFERENCES `professionisti`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TARIFFE
-- ============================================================
CREATE TABLE IF NOT EXISTS `tariffe` (
    `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `codice`        VARCHAR(20) NOT NULL UNIQUE,
    `sezione`       VARCHAR(10) NOT NULL COMMENT 'A.1, A.2, A.3, B, C.1, ecc.',
    `categoria`     VARCHAR(100) NOT NULL COMMENT 'Descrizione sezione',
    `descrizione`   TEXT NOT NULL,
    `tipo`          ENUM('fisso','minimo','scaglione','formula','aprev','tabella_cedolini') NOT NULL DEFAULT 'fisso',
    `importo_min`   DECIMAL(10,2) DEFAULT NULL,
    `importo_max`   DECIMAL(10,2) DEFAULT NULL,
    `frequenza`     ENUM('una_tantum','mensile','trimestrale','semestrale','annuale','a_prestazione') NOT NULL DEFAULT 'a_prestazione',
    `unita`         VARCHAR(50) DEFAULT NULL COMMENT 'Es: a chiusura, a nominativo, a libro',
    `note`          TEXT DEFAULT NULL,
    `attivo`        TINYINT(1) NOT NULL DEFAULT 1,
    `ordine`        INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELLA SCAGLIONI CEDOLINI (C.1)
-- ============================================================
CREATE TABLE IF NOT EXISTS `cedolini_scaglioni` (
    `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `n_cedolini`    INT UNSIGNED NOT NULL UNIQUE,
    `importo`       DECIMAL(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- PACCHETTI
-- ============================================================
CREATE TABLE IF NOT EXISTS `pacchetti` (
    `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nome`          VARCHAR(200) NOT NULL,
    `descrizione`   TEXT DEFAULT NULL,
    `prezzo_fisso`  DECIMAL(10,2) DEFAULT NULL COMMENT 'Se NULL: somma delle tariffe incluse',
    `attivo`        TINYINT(1) NOT NULL DEFAULT 1,
    `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `pacchetto_tariffe` (
    `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `pacchetto_id`  INT UNSIGNED NOT NULL,
    `tariffa_id`    INT UNSIGNED NOT NULL,
    `quantita`      DECIMAL(8,2) NOT NULL DEFAULT 1,
    `note`          VARCHAR(200) DEFAULT NULL,
    FOREIGN KEY (`pacchetto_id`) REFERENCES `pacchetti`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`tariffa_id`) REFERENCES `tariffe`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `uq_pacco_tariffa` (`pacchetto_id`, `tariffa_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- PREVENTIVI
-- ============================================================
CREATE TABLE IF NOT EXISTS `preventivi` (
    `id`                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `numero`            VARCHAR(30) NOT NULL UNIQUE,
    `cliente_id`        INT UNSIGNED NOT NULL,
    `professionista_id` INT UNSIGNED DEFAULT NULL,
    `data_preventivo`   DATE NOT NULL,
    `data_scadenza`     DATE DEFAULT NULL,
    `anno_riferimento`  YEAR NOT NULL,
    `titolo`            VARCHAR(300) DEFAULT NULL,
    `note_interne`      TEXT DEFAULT NULL,
    `note_cliente`      TEXT DEFAULT NULL,
    `sconto1`           DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    `sconto2`           DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    `imponibile`        DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `importo_sconto`    DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `imponibile_scontato` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `iva_perc`          DECIMAL(5,2) NOT NULL DEFAULT 22.00,
    `importo_iva`       DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `totale`            DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `stato`             ENUM('bozza','inviato','accettato','rifiutato','scaduto') NOT NULL DEFAULT 'bozza',
    `utente_id`         INT UNSIGNED DEFAULT NULL,
    `created_at`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`cliente_id`) REFERENCES `clienti`(`id`) ON DELETE RESTRICT,
    FOREIGN KEY (`professionista_id`) REFERENCES `professionisti`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`utente_id`) REFERENCES `utenti`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- VOCI PREVENTIVO
-- ============================================================
CREATE TABLE IF NOT EXISTS `preventivo_voci` (
    `id`                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `preventivo_id`     INT UNSIGNED NOT NULL,
    `tipo_voce`         ENUM('tariffa','pacchetto','manuale') NOT NULL DEFAULT 'tariffa',
    `tariffa_id`        INT UNSIGNED DEFAULT NULL,
    `pacchetto_id`      INT UNSIGNED DEFAULT NULL,
    `codice`            VARCHAR(20) DEFAULT NULL,
    `descrizione`       TEXT NOT NULL,
    `frequenza`         ENUM('una_tantum','mensile','trimestrale','semestrale','annuale','a_prestazione') DEFAULT 'a_prestazione',
    `mesi`              INT UNSIGNED DEFAULT 12 COMMENT 'Numero di mensilità (per cedolini: 12/13/14)',
    `quantita`          DECIMAL(8,2) NOT NULL DEFAULT 1,
    `importo_unitario`  DECIMAL(10,2) NOT NULL,
    `importo_riga`      DECIMAL(12,2) NOT NULL,
    `ordine`            INT UNSIGNED NOT NULL DEFAULT 0,
    `note`              TEXT DEFAULT NULL,
    FOREIGN KEY (`preventivo_id`) REFERENCES `preventivi`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`tariffa_id`) REFERENCES `tariffe`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`pacchetto_id`) REFERENCES `pacchetti`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- IMPOSTAZIONI GENERALI
-- ============================================================
CREATE TABLE IF NOT EXISTS `impostazioni` (
    `chiave`    VARCHAR(100) PRIMARY KEY,
    `valore`    TEXT DEFAULT NULL,
    `tipo`      ENUM('text','number','boolean','json') NOT NULL DEFAULT 'text',
    `nota`      VARCHAR(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `impostazioni` (`chiave`, `valore`, `tipo`, `nota`) VALUES
('iva_default', '22', 'number', 'Aliquota IVA predefinita (%)'),
('numero_preventivo_prefisso', 'PREV', 'text', 'Prefisso numero preventivo'),
('numero_preventivo_anno', YEAR(CURDATE()), 'text', 'Anno corrente per numerazione'),
('numero_preventivo_progressivo', '0', 'number', 'Ultimo numero progressivo'),
('validita_preventivo_giorni', '30', 'number', 'Giorni di validità preventivo'),
('logo_studio', '', 'text', 'Path logo studio'),
('tema_colore', '#0d6efd', 'text', 'Colore principale Bootstrap');
