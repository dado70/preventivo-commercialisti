-- ============================================================
-- Preventivo Commercialisti - Seed Tariffe
-- Basato sugli "Onorari Consigliati" ANC
-- Copyright (C) 2024 Alessandro Scapuzzi <dado70@gmail.com>
-- ============================================================

USE `preventivo_comm`;

-- ============================================================
-- SEZIONE A.1 - CONTABILITÀ
-- ============================================================
INSERT INTO `tariffe` (`codice`,`sezione`,`categoria`,`descrizione`,`tipo`,`importo_min`,`importo_max`,`frequenza`,`unita`,`note`,`ordine`) VALUES

('IMP-SEMPL','A.1','Contabilità - Impianto','Impianto contabilità Semplificata','fisso',125.00,125.00,'una_tantum',NULL,NULL,10),
('IMP-ORD','A.1','Contabilità - Impianto','Impianto contabilità Ordinaria e professionisti','fisso',210.00,210.00,'una_tantum',NULL,NULL,20),
('IMP-MIG','A.1','Contabilità - Impianto','Migrazione da altri applicativi','aprev',NULL,NULL,'una_tantum',NULL,NULL,30),

('CONT-SEMPL-F','A.1','Contabilità Semplificata','Contabilità Semplificata - Fisso minimo mensile','fisso',70.00,70.00,'mensile',NULL,'Più € 2,20 per singola registrazione',40),
('CONT-SEMPL-R','A.1','Contabilità Semplificata','Contabilità Semplificata - Costo per singola registrazione','fisso',2.20,2.20,'mensile','a registrazione',NULL,50),
('CONT-ORD-F','A.1','Contabilità Ordinaria','Contabilità Ordinaria - Fisso minimo mensile','fisso',100.00,100.00,'mensile',NULL,'Più € 3,40 per singola registrazione',60),
('CONT-ORD-R','A.1','Contabilità Ordinaria','Contabilità Ordinaria - Costo per singola registrazione','fisso',3.40,3.40,'mensile','a registrazione',NULL,70),

('SITUAZ','A.1','Contabilità','Situazioni periodiche infrannuali straordinarie (situazione contabile)','minimo',120.00,NULL,'a_prestazione',NULL,NULL,80),

('CHIVAM','A.1','Chiusure IVA','Chiusure IVA Mensili','fisso',50.00,50.00,'mensile','a chiusura',NULL,90),
('CHIVAT','A.1','Chiusure IVA','Chiusure IVA Trimestrali','fisso',72.00,72.00,'trimestrale','a chiusura',NULL,100),

-- Dichiarazione IVA (scaglioni per volume d''affari - gestiti come note)
('DIVA-1','A.1','Dichiarazione IVA','Dichiarazione IVA - Volume d''affari fino a 75.000 €','fisso',376.00,376.00,'annuale',NULL,NULL,110),
('DIVA-2','A.1','Dichiarazione IVA','Dichiarazione IVA - Volume d''affari da 75.001 a 150.000 €','fisso',469.00,469.00,'annuale',NULL,NULL,120),
('DIVA-3','A.1','Dichiarazione IVA','Dichiarazione IVA - Volume d''affari da 150.001 a 300.000 €','fisso',602.00,602.00,'annuale',NULL,NULL,130),
('DIVA-4','A.1','Dichiarazione IVA','Dichiarazione IVA - Volume d''affari da 300.001 a 500.000 €','fisso',753.00,753.00,'annuale',NULL,NULL,140),
('DIVA-5','A.1','Dichiarazione IVA','Dichiarazione IVA - Volume d''affari da 500.001 a 750.000 €','fisso',1050.00,1050.00,'annuale',NULL,NULL,150),
('DIVA-6','A.1','Dichiarazione IVA','Dichiarazione IVA - Volume d''affari oltre 750.000 €','fisso',1876.00,1876.00,'annuale',NULL,NULL,160);

-- ============================================================
-- SEZIONE A.2 - FISCALE - DICHIARAZIONI
-- ============================================================
INSERT INTO `tariffe` (`codice`,`sezione`,`categoria`,`descrizione`,`tipo`,`importo_min`,`importo_max`,`frequenza`,`unita`,`note`,`ordine`) VALUES

('REDD-PF','A.2','Fiscale - Dichiarazioni','Modello Redditi Persone Fisiche (ex 740) senza P.IVA','minimo',130.00,NULL,'annuale',NULL,NULL,200),
('REDD-730','A.2','Fiscale - Dichiarazioni','Redazione modello 730','minimo',120.00,NULL,'annuale',NULL,NULL,210),

-- Redditi PF con P.IVA e Società di Persone
('REDD-PF-IVA-1','A.2','Fiscale - Dichiarazioni','Redditi PF titolari P.IVA / Società di Persone - Ricavi fino a 75.000 €','fisso',602.00,602.00,'annuale',NULL,NULL,220),
('REDD-PF-IVA-2','A.2','Fiscale - Dichiarazioni','Redditi PF titolari P.IVA / Società di Persone - Ricavi 75.001 - 150.000 €','fisso',811.00,811.00,'annuale',NULL,NULL,230),
('REDD-PF-IVA-3','A.2','Fiscale - Dichiarazioni','Redditi PF titolari P.IVA / Società di Persone - Ricavi 150.001 - 300.000 €','fisso',1082.00,1082.00,'annuale',NULL,NULL,240),
('REDD-PF-IVA-4','A.2','Fiscale - Dichiarazioni','Redditi PF titolari P.IVA / Società di Persone - Ricavi 300.001 - 500.000 €','fisso',1351.00,1351.00,'annuale',NULL,NULL,250),
('REDD-PF-IVA-5','A.2','Fiscale - Dichiarazioni','Redditi PF titolari P.IVA / Società di Persone - Ricavi 500.001 - 750.000 €','fisso',1622.00,1622.00,'annuale',NULL,NULL,260),
('REDD-PF-IVA-6','A.2','Fiscale - Dichiarazioni','Redditi PF titolari P.IVA / Società di Persone - Ricavi oltre 750.000 €','fisso',2026.00,2026.00,'annuale',NULL,NULL,270),

-- Redditi Società di Capitali
('REDD-SC-1','A.2','Fiscale - Dichiarazioni','Redditi Soc. Capitali/Assoc./Consorzi - Ricavi fino a 150.000 €','fisso',899.00,899.00,'annuale',NULL,NULL,280),
('REDD-SC-2','A.2','Fiscale - Dichiarazioni','Redditi Soc. Capitali/Assoc./Consorzi - Ricavi fino a 300.000 €','fisso',1127.00,1127.00,'annuale',NULL,NULL,290),
('REDD-SC-3','A.2','Fiscale - Dichiarazioni','Redditi Soc. Capitali/Assoc./Consorzi - Ricavi 300.001 - 500.000 €','fisso',1688.00,1688.00,'annuale',NULL,NULL,300),
('REDD-SC-4','A.2','Fiscale - Dichiarazioni','Redditi Soc. Capitali/Assoc./Consorzi - Ricavi 500.001 - 1.500.000 €','fisso',2254.00,2254.00,'annuale',NULL,NULL,310),
('REDD-SC-5','A.2','Fiscale - Dichiarazioni','Redditi Soc. Capitali/Assoc./Consorzi - Ricavi 1.500.001 - 3.000.000 €','fisso',2815.00,2815.00,'annuale',NULL,NULL,320),
('REDD-SC-6','A.2','Fiscale - Dichiarazioni','Redditi Soc. Capitali/Assoc./Consorzi - Ricavi 3.000.001 - 5.000.000 €','fisso',3378.00,3378.00,'annuale',NULL,NULL,330),
('REDD-SC-7','A.2','Fiscale - Dichiarazioni','Redditi Soc. Capitali/Assoc./Consorzi - Ricavi 5.000.001 - 7.500.000 €','fisso',3943.00,3943.00,'annuale',NULL,NULL,340),
('REDD-SC-8','A.2','Fiscale - Dichiarazioni','Redditi Soc. Capitali/Assoc./Consorzi - Ricavi oltre 7.500.000 €','aprev',NULL,NULL,'annuale',NULL,NULL,350),

-- Bilancio Tab.1 - Attività
('BIL-TAB1-1','A.2','Fiscale - Bilancio','Bilancio societario - Tab.1 Attività fino a 130.000 €','fisso',569.00,569.00,'annuale',NULL,'Importo Tab.1 - Ammontare attività/perdite',360),
('BIL-TAB1-2','A.2','Fiscale - Bilancio','Bilancio societario - Tab.1 Attività 130.001 - 500.000 €','fisso',1001.00,1001.00,'annuale',NULL,'Importo Tab.1 - Ammontare attività/perdite',370),
('BIL-TAB1-3','A.2','Fiscale - Bilancio','Bilancio societario - Tab.1 Attività 500.001 - 1.300.000 €','fisso',1427.00,1427.00,'annuale',NULL,'Importo Tab.1 - Ammontare attività/perdite',380),
('BIL-TAB1-4','A.2','Fiscale - Bilancio','Bilancio societario - Tab.1 Attività 1.300.001 - 2.600.000 €','fisso',2139.00,2139.00,'annuale',NULL,'Importo Tab.1 - Ammontare attività/perdite',390),
('BIL-TAB1-5','A.2','Fiscale - Bilancio','Bilancio societario - Tab.1 Attività 2.600.001 - 5.750.000 €','fisso',2853.00,2853.00,'annuale',NULL,'Importo Tab.1 - Ammontare attività/perdite',400),
('BIL-TAB1-6','A.2','Fiscale - Bilancio','Bilancio societario - Tab.1 Attività oltre 5.750.000 €','aprev',NULL,NULL,'annuale',NULL,'Importo Tab.1 - Ammontare attività/perdite',410),

-- Bilancio Tab.2 - Componenti positivi
('BIL-TAB2-1','A.2','Fiscale - Bilancio','Bilancio societario - Tab.2 Componenti positivi fino a 150.000 €','fisso',713.00,713.00,'annuale',NULL,'Importo Tab.2 - Componenti positivi di reddito',420),
('BIL-TAB2-2','A.2','Fiscale - Bilancio','Bilancio societario - Tab.2 Componenti positivi fino a 300.000 €','fisso',784.00,784.00,'annuale',NULL,'Importo Tab.2 - Componenti positivi di reddito',430),
('BIL-TAB2-3','A.2','Fiscale - Bilancio','Bilancio societario - Tab.2 Componenti positivi 300.001 - 500.000 €','fisso',1001.00,1001.00,'annuale',NULL,'Importo Tab.2 - Componenti positivi di reddito',440),
('BIL-TAB2-4','A.2','Fiscale - Bilancio','Bilancio societario - Tab.2 Componenti positivi 500.001 - 1.500.000 €','fisso',1142.00,1142.00,'annuale',NULL,'Importo Tab.2 - Componenti positivi di reddito',450),
('BIL-TAB2-5','A.2','Fiscale - Bilancio','Bilancio societario - Tab.2 Componenti positivi 1.500.001 - 3.000.000 €','fisso',1427.00,1427.00,'annuale',NULL,'Importo Tab.2 - Componenti positivi di reddito',460),
('BIL-TAB2-6','A.2','Fiscale - Bilancio','Bilancio societario - Tab.2 Componenti positivi 3.000.001 - 5.000.000 €','fisso',2139.00,2139.00,'annuale',NULL,'Importo Tab.2 - Componenti positivi di reddito',470),
('BIL-TAB2-7','A.2','Fiscale - Bilancio','Bilancio societario - Tab.2 Componenti positivi 5.000.001 - 7.500.000 €','fisso',2853.00,2853.00,'annuale',NULL,'Importo Tab.2 - Componenti positivi di reddito',480),
('BIL-TAB2-8','A.2','Fiscale - Bilancio','Bilancio societario - Tab.2 Componenti positivi oltre 7.500.000 €','aprev',NULL,NULL,'annuale',NULL,'Importo Tab.2 - Componenti positivi di reddito',490),

-- Regimi minimi e forfettari
('FORF-REDD','A.2','Regimi Minimi/Forfettari','Dichiarazione redditi regime minimo/forfettario','minimo',480.00,NULL,'annuale',NULL,NULL,500),
('FORF-CONS','A.2','Regimi Minimi/Forfettari','Consulenza regime minimo/forfettario','minimo',690.00,NULL,'annuale',NULL,NULL,510),
('FORF-CORR','A.2','Regimi Minimi/Forfettari','Recupero corrispettivi telematici da sito ADE','fisso',300.00,300.00,'annuale',NULL,NULL,520),
('FORF-SITU','A.2','Regimi Minimi/Forfettari','Redazione situazione contabile periodica/infrannuale','fisso',77.00,77.00,'a_prestazione',NULL,NULL,530),

-- IMU
('IMUDIC','A.2','IMU','Dichiarazione IMU','minimo',60.00,NULL,'annuale',NULL,NULL,540),
('IMUVER','A.2','IMU','Calcolo e predisposizione versamento IMU','minimo',45.00,NULL,'a_prestazione',NULL,NULL,550),
('IMUAC','A.2','IMU','Calcolo e predisposizione versamento acconti d''imposta','minimo',70.00,NULL,'a_prestazione',NULL,NULL,560),

-- Altri adempimenti fiscali
('INVIODICH','A.2','Altri Adempimenti Fiscali','Mero invio telematico dichiarazioni fiscali predisposte dal contribuente','fisso',70.00,70.00,'a_prestazione',NULL,NULL,570),
('VISTOCRED','A.2','Altri Adempimenti Fiscali','Certificazione credito IVA con visto di conformità (contabilità interna)','minimo',160.00,450.00,'annuale',NULL,NULL,580),
('ACCIMPOST','A.2','Altri Adempimenti Fiscali','Calcolo e predisposizione versamento acconti d''imposta','minimo',70.00,NULL,'a_prestazione',NULL,NULL,590),
('RIMBRIRAP','A.2','Altri Adempimenti Fiscali','Predisposizione istanza di rimborso IRAP','fisso',100.00,100.00,'a_prestazione',NULL,NULL,600),
('RIMBIRAP2','A.2','Altri Adempimenti Fiscali','Predisposizione istanza di rimborso IRES/IRPEF per mancata deduzione IRAP','minimo',130.00,NULL,'a_prestazione',NULL,NULL,610),
('LIPE','A.2','Altri Adempimenti Fiscali','Comunicazione liquidazioni periodiche IVA (LIPE)','fisso',80.00,80.00,'trimestrale',NULL,NULL,620),
('SPESOMETRO','A.2','Altri Adempimenti Fiscali','Comunicazioni dati fatture (spesometro/esterometro) a comunicazione','minimo',80.00,NULL,'a_prestazione','a comunicazione',NULL,630),
('INTRA','A.2','Altri Adempimenti Fiscali','Comunicazione e presentazione modello INTRA','minimo',80.00,NULL,'a_prestazione',NULL,NULL,640),
('BOLLOFE','A.2','Altri Adempimenti Fiscali','Calcolo pagamento bollo su fatture elettroniche','minimo',80.00,NULL,'trimestrale',NULL,NULL,650);

-- ============================================================
-- SEZIONE A.3 - CONSULENZA SPECIFICA
-- ============================================================
INSERT INTO `tariffe` (`codice`,`sezione`,`categoria`,`descrizione`,`tipo`,`importo_min`,`importo_max`,`frequenza`,`unita`,`note`,`ordine`) VALUES
('CONS-VAC','A.3','Consulenza','Assistenza e consulenza a vacazione (1 sessione = 1 ora)','minimo',115.00,NULL,'a_prestazione','a sessione',NULL,700),
('CONS-CONT','A.3','Consulenza','Assistenza e consulenza continuativa materia contabile/fiscale/societaria','aprev',NULL,NULL,'mensile',NULL,NULL,710);

-- ============================================================
-- SEZIONE A.4 - ENTI TERZO SETTORE
-- ============================================================
INSERT INTO `tariffe` (`codice`,`sezione`,`categoria`,`descrizione`,`tipo`,`importo_min`,`importo_max`,`frequenza`,`unita`,`note`,`ordine`) VALUES
('EAS','A.4','Enti Terzo Settore','Modello EAS','minimo',80.00,NULL,'annuale',NULL,NULL,800),
('CINQ-ISCR','A.4','Enti Terzo Settore','Iscrizione elenchi 5 per mille','minimo',80.00,NULL,'annuale',NULL,NULL,810),
('CINQ-REND','A.4','Enti Terzo Settore','Rendicontazione 5 per mille','minimo',120.00,NULL,'annuale',NULL,NULL,820),
('RUNTS','A.4','Enti Terzo Settore','Pratiche portale RUNTS','minimo',80.00,NULL,'a_prestazione',NULL,NULL,830),
('RNASD','A.4','Enti Terzo Settore','Pratiche portale RNASD','minimo',80.00,NULL,'a_prestazione',NULL,NULL,840),
('AFFILIAZ','A.4','Enti Terzo Settore','Pratiche affiliazioni/iscrizioni a enti nazionali','minimo',80.00,NULL,'a_prestazione',NULL,NULL,850),
('STATUTO-ETS','A.4','Enti Terzo Settore','Predisposizione statuto ente sportivo o terzo settore','minimo',80.00,NULL,'una_tantum',NULL,NULL,860),
('CONS-ETS','A.4','Enti Terzo Settore','Assistenza e consulenza ETS a vacazione (1 ora)','minimo',115.00,NULL,'a_prestazione','a sessione',NULL,870);

-- ============================================================
-- SEZIONE B - ALTRI ADEMPIMENTI
-- ============================================================
INSERT INTO `tariffe` (`codice`,`sezione`,`categoria`,`descrizione`,`tipo`,`importo_min`,`importo_max`,`frequenza`,`unita`,`note`,`ordine`) VALUES
('VERSA','B','Pagamenti','Predisposizione e/o pagamento modello F24 telematico','fisso',22.00,22.00,'a_prestazione',NULL,NULL,900),
('SPESE','B','Pagamenti','Servizio pagamento con bonifici bancari, MAV, bollettini postali','fisso',5.50,5.50,'a_prestazione','a pagamento',NULL,910),

-- Pratiche CCIAA
('PIVAAPERT','B','CCIAA / Registro Imprese','Formalità per apertura/chiusura partita IVA','minimo',100.00,NULL,'a_prestazione',NULL,NULL,920),
('CCIAA-PRAT','B','CCIAA / Registro Imprese','Formalità pratica CCIAA/RI','minimo',110.00,NULL,'a_prestazione',NULL,NULL,930),
('PIVAVAR','B','CCIAA / Registro Imprese','Formalità per variazioni IVA','minimo',50.00,NULL,'a_prestazione',NULL,NULL,940),
('SCIA','B','CCIAA / Registro Imprese','Pratiche SCIA/SUAP/Comune','minimo',120.00,NULL,'a_prestazione',NULL,NULL,950),
('ACCESSO','B','CCIAA / Registro Imprese','Brevi accessi presso uffici o enti anche telematicamente','minimo',20.00,NULL,'a_prestazione',NULL,NULL,960),
('COMVARIE','B','CCIAA / Registro Imprese','Altre comunicazioni, istanze, esposti, memorie, risposte a questionari','minimo',70.00,NULL,'a_prestazione',NULL,NULL,970),
('VISURE','B','CCIAA / Registro Imprese','Richiesta Visure o altri uffici/enti (a documento)','fisso',40.00,40.00,'a_prestazione','a documento',NULL,980),
('CERTIFIC','B','CCIAA / Registro Imprese','Richiesta Certificati','fisso',50.00,50.00,'a_prestazione',NULL,NULL,990),

-- IVS INPS
('ISCIVS','B','IVS INPS','Iscrizione o cancellazione IVS (Art/Com INPS)','fisso',35.00,35.00,'a_prestazione',NULL,NULL,1000),
('VAIIVS','B','IVS INPS','Variazione IVS (Art/Com INPS)','minimo',30.00,NULL,'a_prestazione',NULL,NULL,1010),
('RIDIVS','B','IVS INPS','Pratica di riduzione contributiva','minimo',60.00,NULL,'a_prestazione',NULL,NULL,1020),
('IVS-TRIM','B','IVS INPS','Gestione trimestrale contributi IVS','fisso',18.00,18.00,'trimestrale',NULL,NULL,1030),

-- Organi societari
('NOA','B','Organi Societari','Nomina/rinnovo organo amministrativo','minimo',440.00,NULL,'a_prestazione',NULL,NULL,1040),
('NOAPOT','B','Organi Societari','Nomina organo amministrativo comitato esecutivo e poteri','minimo',510.00,NULL,'a_prestazione',NULL,NULL,1050),
('STATUTO-SOC','B','Organi Societari','Adeguamenti statuti delle società di capitali','minimo',620.00,NULL,'a_prestazione',NULL,NULL,1060),

-- Vidimazione libri
('VIDLIB','B','Vidimazione Libri','Libro a fogli mobili (per ogni 100 pagine)','fisso',22.00,22.00,'a_prestazione','per 100 pg',NULL,1070),
('VIDACC','B','Vidimazione Libri','Accesso presso CCIAA/Notaio per vidimazione libri','fisso',50.00,50.00,'a_prestazione',NULL,NULL,1080),
('VIDIMA','B','Vidimazione Libri','Vidimazione libro contabile/societario (a libro)','fisso',65.00,65.00,'a_prestazione','a libro',NULL,1090),
('DIRITT','B','Vidimazione Libri','Diritti di Segreteria per Vidimazione libri (per ogni libro)','fisso',25.00,25.00,'a_prestazione','a libro','Oltre a diritti di legge anticipati e marche da bollo (€ 16,00 a libro)',1100),

-- Dichiarazioni telematiche varie
('BOLLVIRT','B','Dichiarazioni Varie','Dichiarazione telematica consuntiva imposta di bollo virtuale','fisso',150.00,150.00,'annuale',NULL,NULL,1110),
('LETINT-F','B','Lettere di Intento','Assistenza e invio comunicazione lettere di intento - Fisso a invio','fisso',100.00,100.00,'a_prestazione','a invio',NULL,1120),
('LETINT-U','B','Lettere di Intento','Assistenza e invio comunicazione lettere di intento - Per singola comunicazione','fisso',6.50,6.50,'a_prestazione','a comunicazione',NULL,1130),
('TESSSAN-A','B','Tessera Sanitaria','Comunicazione Tessera Sanitaria (dati automatizzati) - Fisso a invio','fisso',85.00,85.00,'annuale','a invio',NULL,1140),
('TESSSAN-M','B','Tessera Sanitaria','Comunicazione Tessera Sanitaria (dati manuali) - Fisso a invio','fisso',190.00,190.00,'annuale','a invio',NULL,1150),
('TESSSAN-R','B','Tessera Sanitaria','Comunicazione Tessera Sanitaria (dati manuali) - Per riga manuale','fisso',0.40,0.40,'a_prestazione','a riga',NULL,1160),
('VISTODICH','B','Dichiarazioni Varie','Rilascio visto di conformità su dichiarazione','aprev',NULL,NULL,'a_prestazione',NULL,NULL,1170),
('ISA','B','Dichiarazioni Varie','Compilazione questionari ISA','minimo',135.00,NULL,'annuale',NULL,NULL,1180),
('ISTAT-A','B','Dichiarazioni Varie','Assistenza per questionari ISTAT e indagini statistiche','minimo',70.00,NULL,'a_prestazione',NULL,NULL,1190),

-- Avvisi/Cartelle/Ruoli
('AVVTEL','B','Avvisi e Cartelle','Predisposizione fascicolo e controllo avviso anche telematico','fisso',30.00,30.00,'a_prestazione',NULL,NULL,1200),
('ANADEB','B','Avvisi e Cartelle','Analisi delle somme dovute per avviso, cartella o altra richiesta','minimo',115.00,NULL,'a_prestazione',NULL,NULL,1210),
('RATE3','B','Avvisi e Cartelle','Redazione e presentazione piano di rateizzazione somme dovute','fisso',60.00,60.00,'a_prestazione',NULL,NULL,1220),
('RATE3-G','B','Avvisi e Cartelle','Gestione rate successive alla prima','fisso',24.00,24.00,'a_prestazione','a rata',NULL,1230),
('CIVIS','B','Avvisi e Cartelle','Presentazione istanza di rettifica CIVIS','fisso',90.00,90.00,'a_prestazione',NULL,NULL,1240),
('RAVV','B','Avvisi e Cartelle','Consulenza e predisposizione ravvedimento operoso','fisso',29.50,29.50,'a_prestazione',NULL,NULL,1250),
('ADEVER','B','Avvisi e Cartelle','Verifica carichi affidati ADE - definizione cartelle e/o rottamazione','minimo',60.00,NULL,'a_prestazione',NULL,NULL,1260),
('SGRAVI','B','Avvisi e Cartelle','Predisposizione e deposito istanza di sgravio cartelle/avvisi','minimo',100.00,NULL,'a_prestazione',NULL,NULL,1270),
('ROTT','B','Avvisi e Cartelle','Predisposizione e deposito istanza rottamazione/definizione agevolata','minimo',100.00,NULL,'a_prestazione',NULL,NULL,1280),
('PAGAM','B','Avvisi e Cartelle','Pagamento cartella e avvisi','fisso',60.00,60.00,'a_prestazione',NULL,NULL,1290),

-- Locazioni
('YLOCST','B','Locazioni','Registrazione/rinnovo contratti locazione uso strumentale (a contratto)','minimo',185.00,NULL,'a_prestazione','a contratto',NULL,1300),
('LOCTEP','B','Locazioni','Registrazione/rinnovo contratti locazione uso privato (a contratto)','minimo',150.00,NULL,'a_prestazione','a contratto',NULL,1310),
('REGPAG','B','Locazioni','Pagamento telematico imposta di registro (a contratto)','fisso',15.00,15.00,'a_prestazione','a contratto',NULL,1320),
('CREDFISC','B','Crediti Fiscali','Assistenza verifica periodica crediti fiscali (es. sconto in fattura)','fisso',530.00,530.00,'annuale',NULL,NULL,1330),

-- Bonus Edilizia
('VISTOED-S','B','Bonus Edilizia','Visto di conformità Bonus Edilizia - pratiche con valore < 200k (3%-4% min. 600 €)','minimo',600.00,NULL,'a_prestazione',NULL,'3%-4% del valore pratica, minimo € 600,00',1340),
('VISTOED-L','B','Bonus Edilizia','Visto di conformità Bonus Edilizia - pratiche con valore > 200k (1%-4%)','minimo',NULL,NULL,'a_prestazione',NULL,'1%-4% del valore pratica',1350),
('CESCRED','B','Bonus Edilizia','Invio comunicazione opzione cessione credito/sconto in fattura','minimo',180.00,NULL,'a_prestazione',NULL,NULL,1360),
('ANNCOM','B','Bonus Edilizia','Annullamento comunicazione cessione credito','fisso',60.00,60.00,'a_prestazione',NULL,NULL,1370),
('GESTCRED','B','Bonus Edilizia','Gestione crediti da bonus edilizia su piattaforma AdE','minimo',100.00,NULL,'annuale',NULL,NULL,1380),
('GESTBANCHE','B','Bonus Edilizia','Gestione documentazione piattaforme banche (Deloitte, KPMG, ecc.)','minimo',300.00,1500.00,'a_prestazione','a pratica',NULL,1390),

-- PEC ed Email
('PECRICHIESTA','B','PEC e Email','Richiesta o rinnovo casella email o PEC','fisso',40.00,40.00,'annuale',NULL,NULL,1400),
('PECLITE','B','PEC e Email','Casella PEC LITE @peceasy.it','fisso',14.00,14.00,'annuale',NULL,NULL,1410),
('PECPRO','B','PEC e Email','Casella PEC PRO @peceasy.it','fisso',40.00,40.00,'annuale',NULL,NULL,1420),
('PECCFG','B','PEC e Email','Configurazione casella PEC su richiesta del cliente','fisso',65.00,65.00,'una_tantum',NULL,NULL,1430),
('PECRI','B','PEC e Email','Comunicazione indirizzo PEC al Registro Imprese','fisso',25.00,25.00,'a_prestazione',NULL,NULL,1440),
('PECGEST','B','PEC e Email','Canone mensile gestione casella PEC','minimo',45.00,NULL,'mensile',NULL,NULL,1450),

-- Smart Card CNS
('CNS-CARTA','B','Smart Card CNS','Rilascio SmartCard Aruba formato carta credito + certificato firma CNS','fisso',130.00,130.00,'una_tantum',NULL,NULL,1460),
('CNS-USB','B','Smart Card CNS','Rilascio SmartCard Aruba USB + certificato firma CNS','fisso',180.00,180.00,'una_tantum',NULL,NULL,1470),
('CNS-CUS','B','Smart Card CNS','Servizio annuo di custodia SmartCard','fisso',50.00,50.00,'annuale',NULL,NULL,1480),
('SPID','B','Smart Card CNS','Assistenza rilascio/rinnovo/recupero password SPID','fisso',75.00,75.00,'a_prestazione',NULL,NULL,1490),

-- Fatture elettroniche
('FTHUB','B','Fatture Elettroniche','Canone mensile scarico Fatture Elettroniche da hub diverso da doceasy','fisso',15.00,15.00,'mensile',NULL,NULL,1500),
('FTHUB2','B','Fatture Elettroniche','Verifica mensile fatture con il sito dell''Agenzia delle Entrate','fisso',25.00,25.00,'mensile',NULL,NULL,1510),

-- Hosting e software contabile
('HSRVCFG','B','Hosting e Software','Configurazione una-tantum server e PC cliente (primo PC)','fisso',250.00,250.00,'una_tantum',NULL,'+ € 85,00 per ogni posto di lavoro aggiuntivo',1520),
('HSRVADD','B','Hosting e Software','Configurazione posto di lavoro aggiuntivo','fisso',85.00,85.00,'una_tantum','a postazione',NULL,1530),
('GISRANOCCHI','B','Hosting e Software','Accesso e utilizzo sistema contabile Ranocchi GIS su server studio','fisso',85.00,85.00,'mensile',NULL,NULL,1540),
('VPN-PC','B','Hosting e Software','Canone noleggio PC con VPN e collegamento remoto dedicato','fisso',42.00,42.00,'mensile',NULL,NULL,1550),
('VPN-CLI','B','Hosting e Software','VPN e collegamento remoto dedicato con PC fornito dal cliente','fisso',15.00,15.00,'mensile',NULL,NULL,1560),

-- Domiciliazione
('DOMILEG','B','Domiciliazione','Servizio di domiciliazione della sede legale','fisso',150.00,150.00,'trimestrale',NULL,NULL,1570),
('DOMIPEC','B','Domiciliazione','Servizio di domiciliazione sede legale con gestione PEC','fisso',250.00,250.00,'trimestrale',NULL,NULL,1580),

-- Lavoro Occasionale (sezione B)
('LOCASPRIMA','B','Lavoro Occasionale','Comunicazione preventiva "Lavoro Occasionale" - Prima comunicazione','fisso',45.00,45.00,'a_prestazione',NULL,NULL,1590),
('LOCASNEXT','B','Lavoro Occasionale','Comunicazione preventiva "Lavoro Occasionale" - Singola dopo la prima','fisso',28.00,28.00,'a_prestazione',NULL,NULL,1600),
('LOCASP3','B','Lavoro Occasionale','Pack 3 comunicazioni "Lavoro Occasionale"','fisso',90.00,90.00,'a_prestazione','pack 3',NULL,1610),
('LOCASP5','B','Lavoro Occasionale','Pack 5 comunicazioni "Lavoro Occasionale"','fisso',145.00,145.00,'a_prestazione','pack 5',NULL,1620),
('OCCASI-B','B','Lavoro Occasionale','Predisposizione contratto di lavoro parasubordinato','fisso',55.00,55.00,'a_prestazione','a contratto',NULL,1630);

-- ============================================================
-- SEZIONE C.1 - CEDOLINI (tabella scaglioni - tariffa speciale)
-- ============================================================
INSERT INTO `tariffe` (`codice`,`sezione`,`categoria`,`descrizione`,`tipo`,`importo_min`,`importo_max`,`frequenza`,`unita`,`note`,`ordine`) VALUES
('CEDOLINI','C.1','Elaborazione Cedolini','Elaborazione cedolini paga mensili (tabella scaglioni 1-60+)','tabella_cedolini',80.00,NULL,'mensile','per mese','13^ mensilità: 100% onorario dicembre. 14^ mensilità: 100% onorario giugno. Edilizia: +10%-30%',1700);

-- ============================================================
-- TABELLA SCAGLIONI CEDOLINI
-- ============================================================
INSERT INTO `cedolini_scaglioni` (`n_cedolini`, `importo`) VALUES
(1,80.00),(2,121.00),(3,158.00),(4,194.00),(5,239.00),
(6,273.00),(7,309.00),(8,350.00),(9,386.00),(10,418.00),
(11,458.00),(12,491.00),(13,525.00),(14,555.00),(15,585.00),
(16,621.00),(17,653.00),(18,681.00),(19,710.00),(20,740.00),
(21,769.00),(22,798.00),(23,823.00),(24,855.00),(25,882.00),
(26,908.00),(27,935.00),(28,963.00),(29,992.00),(30,1018.00),
(31,1038.00),(32,1064.00),(33,1090.00),(34,1116.00),(35,1136.00),
(36,1159.00),(37,1181.00),(38,1208.00),(39,1231.00),(40,1255.00),
(41,1275.00),(42,1295.00),(43,1316.00),(44,1342.00),(45,1363.00),
(46,1388.00),(47,1409.00),(48,1430.00),(49,1448.00),(50,1474.00),
(51,1496.00),(52,1516.00),(53,1536.00),(54,1556.00),(55,1575.00),
(56,1597.00),(57,1618.00),(58,1638.00),(59,1660.00),(60,1678.00);

-- ============================================================
-- SEZIONE C.1 - ALTRE PRESTAZIONI PERSONALE
-- ============================================================
INSERT INTO `tariffe` (`codice`,`sezione`,`categoria`,`descrizione`,`tipo`,`importo_min`,`importo_max`,`frequenza`,`unita`,`note`,`ordine`) VALUES
('IMP-PAGHE','C.1','Cedolini - Impianto','Primo impianto archivi meccanografici anagrafici azienda/dipendenti','minimo',180.00,NULL,'una_tantum',NULL,NULL,1710),
('VERSA-C','C.1','Cedolini - Pagamenti','Pagamento modello F24 telematico','fisso',22.00,22.00,'a_prestazione',NULL,NULL,1720),
('SPESE-C','C.1','Cedolini - Pagamenti','Pagamenti con bonifici bancari, MAV, bollettini postali','fisso',6.00,6.00,'a_prestazione','a pagamento',NULL,1730),
('CHIUS-C','C.1','Cedolini - Immatricolazione','Pratiche di immatricolazione (INPS, INAIL, ASL)','minimo',135.00,NULL,'a_prestazione','ad ente',NULL,1740),
('VARIAZ','C.1','Cedolini - Variazioni','Pratiche di variazione immatricolazione o chiusura posizioni','fisso',75.00,75.00,'a_prestazione',NULL,NULL,1750),
('PATINA','C.1','Cedolini - INAIL','Variazione PAT INAIL','fisso',60.00,60.00,'a_prestazione',NULL,NULL,1760),
('ASSUNZIONE','C.1','Cedolini - Assunzioni','Assunzioni nuovi dipendenti o chiamate','fisso',6.00,6.00,'a_prestazione',NULL,NULL,1770),
('ASSUEE','C.1','Cedolini - Assunzioni','Assunzione extracomunitari','fisso',115.00,115.00,'a_prestazione',NULL,NULL,1780),
('APPRENT','C.1','Cedolini - Assunzioni','Assunzione apprendistato professionalizzante','minimo',200.00,NULL,'a_prestazione',NULL,NULL,1790),
('PFORMAPP','C.1','Cedolini - Assunzioni','Piano formativo annuale per apprendista professionalizzante','minimo',260.00,380.00,'annuale',NULL,NULL,1800),
('LIQUI','C.1','Cedolini - Liquidazioni','Calcolo liquidazione dipendente compreso TFR','fisso',70.00,70.00,'a_prestazione',NULL,NULL,1810),
('CALTFR','C.1','Cedolini - Liquidazioni','Calcolo anticipazioni e acconti TFR','fisso',30.00,30.00,'a_prestazione',NULL,NULL,1820),
('UNILAV','C.1','Cedolini - Comunicazioni','Predisposizione e trasmissione modelli UniLav','fisso',16.50,16.50,'a_prestazione',NULL,NULL,1830),
('VARDAT','C.1','Cedolini - Comunicazioni','Predisposizione e trasmissione modelli VarDatori','fisso',16.50,16.50,'a_prestazione','a nominativo',NULL,1840),
('UNIINT','C.1','Cedolini - Comunicazioni','Predisposizione e trasmissione modelli UniIntermittente','fisso',16.50,16.50,'a_prestazione','a nominativo',NULL,1850),
('PARTIM','C.1','Cedolini - Contratti','Predisposizione contratto di lavoro part-time','fisso',45.00,45.00,'a_prestazione',NULL,NULL,1860),
('TRASFO','C.1','Cedolini - Contratti','Trasformazione rapporto part-time a full-time','fisso',45.00,45.00,'a_prestazione',NULL,NULL,1870),
('LIVELL','C.1','Cedolini - Contratti','Pratica aumento di retribuzione o passaggio di livello','fisso',45.00,45.00,'a_prestazione',NULL,NULL,1880),
('TIROCINIO','C.1','Cedolini - Tirocini','Instaurazione tirocini formativi convenzione e progetto','minimo',390.00,640.00,'a_prestazione',NULL,NULL,1890),
('EMENS','C.1','Cedolini - Modelli','Modelli Uniemens (fisso + a nominativo)','formula',40.00,NULL,'mensile',NULL,'€ 40 fisso + € 4 a nominativo',1900),
('PROPA','C.1','Cedolini - Contabilità','Prospetto mensile paghe e contributi per contabilità','fisso',20.00,20.00,'mensile',NULL,NULL,1910),
('LUL','C.1','Cedolini - LUL','Conservazione sostitutiva Libro Unico del Lavoro','formula',10.00,NULL,'mensile',NULL,'€ 10 fisso al mese + € 2,50 a cedolino',1920),
('FONDIS','C.1','Cedolini - Fondi','Assistenza iscrizione dipendente al fondo (una tantum)','fisso',37.00,37.00,'una_tantum',NULL,NULL,1930),
('FONDAZ','C.1','Cedolini - Fondi','Fisso mensile per azienda con dipendenti iscritti a fondi','fisso',30.00,30.00,'mensile',NULL,NULL,1940),
('FONDO','C.1','Cedolini - Fondi','Per ogni dipendente iscritto + per ogni fondo gestito','fisso',20.00,20.00,'mensile','a dipendente/fondo','€ 20,00 a dipendente + € 20,00 a fondo',1950),
('OCCASI-C','C.1','Cedolini - Contratti','Predisposizione contratto lavoro parasubordinato','fisso',65.00,65.00,'a_prestazione','a contratto',NULL,1960),
('ATTSER','C.1','Cedolini - Varie','Predisposizione attestato di servizio','fisso',30.00,30.00,'a_prestazione',NULL,NULL,1970),
('ASFAM','C.1','Cedolini - Varie','Documentazione assegni nucleo familiare','fisso',38.00,38.00,'a_prestazione',NULL,NULL,1980),
('PIGNO','C.1','Cedolini - Varie','Dichiarazione terzo pignorato','minimo',50.00,NULL,'a_prestazione',NULL,NULL,1990),
('CERTRET','C.1','Cedolini - Varie','Certificazioni retribuzioni per dipendenti','fisso',50.00,50.00,'a_prestazione',NULL,NULL,2000),
('DISCI','C.1','Cedolini - Disciplinare','Pratica di contestazione disciplinare e predisposizione documentazione','minimo',60.00,NULL,'a_prestazione',NULL,NULL,2010),
('LICENZ','C.1','Cedolini - Disciplinare','Pratica di licenziamento e predisposizione lettere','minimo',85.00,NULL,'a_prestazione',NULL,NULL,2020),
('REGAZ','C.1','Cedolini - Varie','Predisposizione regolamento aziendale','minimo',250.00,NULL,'una_tantum',NULL,NULL,2030),
('APCANT','C.1','Cedolini - Cantieri','Apertura cantiere edile','fisso',100.00,100.00,'a_prestazione',NULL,NULL,2040),
('PRCANT','C.1','Cedolini - Cantieri','Proroga apertura cantiere edile','fisso',45.00,45.00,'a_prestazione',NULL,NULL,2050),
('CONRET','C.1','Cedolini - Conteggi','Conteggi per verifica differenze su retribuzioni, oneri sociali e TFR','minimo',50.00,NULL,'a_prestazione',NULL,NULL,2060),
('PREPRO','C.1','Cedolini - Conteggi','Predisposizione prospetti costi o budget personale a lavoratore','minimo',10.00,NULL,'a_prestazione','a lavoratore',NULL,2070),
('INFDIS','C.1','Cedolini - Disabili','Predisposizione e trasmissione nota informativa disabili','fisso',115.00,115.00,'annuale',NULL,NULL,2080),
('COLLDIS','C.1','Cedolini - Disabili','Assistenza per convenzioni collocamento mirato disabili','minimo',160.00,NULL,'a_prestazione',NULL,NULL,2090),
('VERIFI','C.1','Cedolini - Ispezioni','Assistenza per verifiche ispettive','minimo',275.00,NULL,'a_prestazione',NULL,NULL,2100),
('VIDEOS','C.1','Cedolini - Autorizzazioni','Pratica autorizzazione Ispettorato del Lavoro per videosorveglianza','minimo',200.00,NULL,'a_prestazione',NULL,NULL,2110),
('DURC','C.1','Cedolini - DURC','Richiesta D.U.R.C. online','fisso',25.00,25.00,'a_prestazione',NULL,NULL,2120),
('CON730','C.1','Cedolini - Conguagli','Conguagli 730','fisso',46.00,46.00,'a_prestazione','a dipendente',NULL,2130),
('TFR-ANN','C.1','Cedolini - TFR','Conteggi annuali T.F.R.','minimo',65.00,195.00,'annuale',NULL,NULL,2140),
('MOD770','C.1','Cedolini - Modelli','Modello 770 (fisso)','fisso',98.00,98.00,'annuale',NULL,NULL,2150),
('MOD770ORD','C.1','Cedolini - Modelli','Modello 770 Ordinario','minimo',130.00,230.00,'annuale',NULL,NULL,2160),
('CERTU','C.1','Cedolini - CU','Certificazione Unica - fisso','fisso',60.00,60.00,'annuale',NULL,NULL,2170),
('CERTI','C.1','Cedolini - CU','Certificazione Unica - per nominativo dipendente','fisso',42.00,42.00,'annuale','a nominativo',NULL,2180),
('CERTI-PROF','C.1','Cedolini - CU','Certificazione Unica - per nominativo professionista/collaboratore occasionale','fisso',20.00,20.00,'annuale','a nominativo',NULL,2190),
('INAIL-AUT','C.1','Cedolini - INAIL','Denuncia salari e autoliquidazione INAIL - solo titolare/soci (senza dip.)','fisso',62.00,62.00,'annuale',NULL,NULL,2200),
('INAIL-1','C.1','Cedolini - INAIL','Denuncia salari e autoliquidazione INAIL - 1 dipendente','fisso',72.00,72.00,'annuale',NULL,NULL,2210),
('INAIL-2','C.1','Cedolini - INAIL','Denuncia salari e autoliquidazione INAIL - 2 dipendenti','fisso',86.00,86.00,'annuale',NULL,NULL,2220),
('INAIL-3','C.1','Cedolini - INAIL','Denuncia salari e autoliquidazione INAIL - 3 dipendenti','fisso',95.00,95.00,'annuale',NULL,NULL,2230),
('INAIL-N','C.1','Cedolini - INAIL','Denuncia salari INAIL oltre 3 dip. (€ 95 + € 4 per ogni dip. in più, max € 200)','formula',95.00,200.00,'annuale',NULL,'€ 95,00 + € 4,00 per ogni dipendente oltre 3 (max € 200,00)',2240),
('INFOR','C.1','Cedolini - Infortuni','Denunce infortuni','fisso',65.00,65.00,'a_prestazione',NULL,NULL,2250),
('DNA-INAIL','C.1','Cedolini - INAIL','DNA all''INAIL di soci/collaboratori','fisso',16.00,16.00,'a_prestazione',NULL,NULL,2260),
('CIGO','C.1','Cedolini - CIG','Cassa integrazione ordinaria','minimo',70.00,NULL,'a_prestazione',NULL,NULL,2270),
('CIGS','C.1','Cedolini - CIG','Cassa integrazione straordinaria','fisso',640.00,640.00,'a_prestazione',NULL,NULL,2280),
('DILAZ','C.1','Cedolini - Dilazioni','Domanda di dilazione amministrativa','minimo',30.00,NULL,'a_prestazione',NULL,NULL,2290),
('REGOLA','C.1','Cedolini - Regolarizzazioni','Assistenza per regolarizzazione posizioni contributive e assicurative','minimo',100.00,NULL,'a_prestazione',NULL,NULL,2300),
('AUDIO','C.1','Cedolini - Autorizzazioni','Assistenza per istanze autorizzazione impianti audiovisivi','minimo',150.00,NULL,'a_prestazione',NULL,NULL,2310),
('ISTAT-C','C.1','Cedolini - ISTAT','Assistenza per questionari ISTAT e indagini statistiche','minimo',70.00,NULL,'a_prestazione',NULL,NULL,2320),

-- Lavoro domestico (Colf/Badanti)
('ASSCO','C.1','Lavoro Domestico','Assunzioni e variazioni del rapporto di lavoro (Colf/Badanti)','fisso',40.00,40.00,'a_prestazione',NULL,NULL,2330),
('ASSCO-EE','C.1','Lavoro Domestico','Maggiorazione assunzione extracomunitari (Colf/Badanti)','fisso',25.00,25.00,'a_prestazione',NULL,NULL,2340),
('ASSOSP','C.1','Lavoro Domestico','Comunicazione ospitalità Questura','fisso',30.00,30.00,'a_prestazione',NULL,NULL,2350),
('CESCO','C.1','Lavoro Domestico','Cessazioni e liquidazioni TFR (Colf/Badanti)','fisso',70.00,70.00,'a_prestazione',NULL,NULL,2360),
('BUSCO','C.1','Lavoro Domestico','Elaborazione dati retribuzioni mensili (busta paga Colf/Badanti)','fisso',12.00,12.00,'mensile',NULL,NULL,2370),
('COLF','C.1','Lavoro Domestico','Gestione contributi trimestrali INPS e Certificazione Unica (Colf)','fisso',39.00,39.00,'trimestrale',NULL,NULL,2380),
('COLF1','C.1','Lavoro Domestico','Conteggi straordinari su retribuzioni e liquidazioni TFR (Colf)','minimo',50.00,NULL,'a_prestazione',NULL,NULL,2390),
('CUCOLF','C.1','Lavoro Domestico','Certificazione Unica senza gestione contributi (Colf)','minimo',40.00,NULL,'annuale',NULL,NULL,2400),

-- Pratiche PRESTO (ex voucher)
('OCC1C','C.1','Pratiche PRESTO','PRESTO - Prima comunicazione','fisso',60.00,60.00,'a_prestazione',NULL,NULL,2410),
('OCC1','C.1','Pratiche PRESTO','PRESTO - Singola comunicazione dopo la prima','fisso',40.00,40.00,'a_prestazione',NULL,NULL,2420),
('OCC3','C.1','Pratiche PRESTO','PRESTO - Pack 3 comunicazioni','fisso',130.00,130.00,'a_prestazione','pack 3',NULL,2430),
('OCC5','C.1','Pratiche PRESTO','PRESTO - Pack 5 comunicazioni','fisso',190.00,190.00,'a_prestazione','pack 5',NULL,2440),

-- Rilevazione Presenze
('RPFSUP','C.1','Rilevazione Presenze','Attivazione piattaforma Fluida e prima formazione','aprev',NULL,NULL,'una_tantum',NULL,NULL,2450),
('RPFESS','C.1','Rilevazione Presenze','Rilevazione presenze "Fluida" Essential (fisso + a nominativo oltre 5)','formula',10.75,NULL,'mensile',NULL,'€ 10,75 fisse al mese + € 1,30 per nominativo oltre 5',2460),
('RPFSTD','C.1','Rilevazione Presenze','Rilevazione presenze "Fluida" Standard','formula',19.25,NULL,'mensile',NULL,'€ 19,25 fisse al mese + € 3,00 per nominativo oltre 5',2470),
('RPFPLU','C.1','Rilevazione Presenze','Rilevazione presenze "Fluida" Plus','formula',23.50,NULL,'mensile',NULL,'€ 23,50 fisse al mese + € 3,85 per nominativo oltre 5',2480),
('DOCKER-5','C.1','Rilevazione Presenze','Profiler Cloud Docker - Micro (fino a 5 dip.) - Attivazione','fisso',69.00,69.00,'una_tantum',NULL,NULL,2490),
('DOCKER-5M','C.1','Rilevazione Presenze','Profiler Cloud Docker - Micro (fino a 5 dip.) - Canone mensile','fisso',16.00,16.00,'mensile',NULL,NULL,2500),
('DOCKER-10','C.1','Rilevazione Presenze','Profiler Cloud Docker - Micro impresa (6-10 dip.) - Attivazione','fisso',99.00,99.00,'una_tantum',NULL,NULL,2510),
('DOCKER-10M','C.1','Rilevazione Presenze','Profiler Cloud Docker - Micro impresa (6-10 dip.) - Canone mensile','fisso',2.70,2.70,'mensile','a dipendente',NULL,2520),
('DOCKER-20','C.1','Rilevazione Presenze','Profiler Cloud Docker - Piccola impresa (11-20 dip.) - Attivazione','fisso',119.00,119.00,'una_tantum',NULL,NULL,2530),
('DOCKER-20M','C.1','Rilevazione Presenze','Profiler Cloud Docker - Piccola impresa (11-20 dip.) - Canone mensile','fisso',2.30,2.30,'mensile','a dipendente',NULL,2540),
('DOCKER-50','C.1','Rilevazione Presenze','Profiler Cloud Docker - Media impresa (21-50 dip.) - Attivazione','fisso',139.00,139.00,'una_tantum',NULL,NULL,2550),
('DOCKER-50M','C.1','Rilevazione Presenze','Profiler Cloud Docker - Media impresa (21-50 dip.) - Canone mensile','fisso',1.80,1.80,'mensile','a dipendente',NULL,2560),
('DOCKER-100','C.1','Rilevazione Presenze','Profiler Cloud Docker - Grande impresa (51-100 dip.) - Attivazione','fisso',199.00,199.00,'una_tantum',NULL,NULL,2570),
('DOCKER-100M','C.1','Rilevazione Presenze','Profiler Cloud Docker - Grande impresa (51-100 dip.) - Canone mensile','fisso',1.70,1.70,'mensile','a dipendente',NULL,2580),
('DOCKER-CLOUD','C.1','Rilevazione Presenze','Profiler Cloud Docker - Spazio cloud aggiuntivo 5GB','fisso',0.40,0.40,'mensile','per 5GB',NULL,2590);

-- ============================================================
-- UTENTE ADMIN INIZIALE (password: Admin@2024 - VA CAMBIATA)
-- hash bcrypt di "Admin@2024"
-- ============================================================
INSERT INTO `utenti` (`nome`, `cognome`, `email`, `password_hash`, `ruolo`) VALUES
('Alessandro', 'Scapuzzi', 'dado70@gmail.com',
 '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'admin');
-- NOTA: la password iniziale è "password" - CAMBIARLA SUBITO dopo il primo accesso!
