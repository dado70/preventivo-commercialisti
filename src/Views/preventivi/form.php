<?php
use App\Core\Auth;
use App\Core\View;

$isNew  = $isNew ?? true;
$p      = $preventivo ?? [];
$action = $isNew
    ? View::url('preventivi/store')
    : View::url('preventivi/' . (int)($p['id']) . '/update');

$oggi      = date('Y-m-d');
$annoCorr  = (int)date('Y');
$freqOpts  = [
    'una_tantum'    => 'Una Tantum',
    'mensile'       => 'Mensile',
    'trimestrale'   => 'Trimestrale',
    'semestrale'    => 'Semestrale',
    'annuale'       => 'Annuale',
    'a_prestazione' => 'A Prestazione',
];
$statiOpts = [
    'bozza'    => 'Bozza',
    'inviato'  => 'Inviato',
    'accettato'=> 'Accettato',
    'rifiutato'=> 'Rifiutato',
    'scaduto'  => 'Scaduto',
];
$ivaOpts   = [0, 4, 5, 10, 22];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="bi bi-<?= $isNew ? 'file-plus' : 'pencil-square' ?> me-2 text-primary"></i>
        <?= $isNew ? 'Nuovo Preventivo' : 'Modifica Preventivo ' . View::e($p['numero'] ?? '') ?>
    </h1>
    <a href="<?= View::url('preventivi') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Torna ai Preventivi
    </a>
</div>

<form action="<?= $action ?>" method="POST" novalidate id="form-preventivo">
    <?php if (!$isNew): ?>
    <input type="hidden" name="_method" value="PUT">
    <?php endif; ?>

    <div class="row g-4">

        <!-- ====================================================
             COLONNA SINISTRA (8 colonne)
             ==================================================== -->
        <div class="col-lg-8">

            <!-- Card Dati Preventivo -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light fw-semibold">
                    <i class="bi bi-file-text me-2 text-primary"></i>Dati Preventivo
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        <!-- Cliente -->
                        <div class="col-md-6">
                            <label for="cliente_id" class="form-label">
                                Cliente <span class="text-danger">*</span>
                            </label>
                            <input list="clienti-list" id="cliente_search"
                                   class="form-control" placeholder="Cerca cliente..."
                                   autocomplete="off"
                                   value="<?php
                                       foreach ($clienti ?? [] as $c) {
                                           if ((int)$c['id'] === (int)($p['cliente_id'] ?? 0)) {
                                               echo View::e($c['ragione_sociale']);
                                               break;
                                           }
                                       }
                                   ?>">
                            <datalist id="clienti-list">
                                <?php foreach ($clienti ?? [] as $c): ?>
                                <option
                                    data-id="<?= (int)$c['id'] ?>"
                                    value="<?= View::e($c['ragione_sociale']) ?>">
                                    <?= View::e($c['ragione_sociale']) ?>
                                </option>
                                <?php endforeach; ?>
                            </datalist>
                            <input type="hidden" id="cliente_id" name="cliente_id"
                                   value="<?= (int)($p['cliente_id'] ?? 0) ?>" required>
                            <div class="invalid-feedback">Seleziona un cliente.</div>
                        </div>

                        <!-- Professionista -->
                        <div class="col-md-6">
                            <label for="professionista_id" class="form-label">Professionista</label>
                            <select id="professionista_id" name="professionista_id" class="form-select">
                                <option value="">-- Nessuno --</option>
                                <?php foreach ($professionisti ?? [] as $pr): ?>
                                <option value="<?= (int)$pr['id'] ?>"
                                    <?= (int)($p['professionista_id'] ?? 0) === (int)$pr['id'] ? 'selected' : '' ?>>
                                    <?= View::e(trim(($pr['titolo'] ?? '') . ' ' . ($pr['nome'] ?? '') . ' ' . ($pr['cognome'] ?? ''))) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Data preventivo -->
                        <div class="col-md-3">
                            <label for="data_preventivo" class="form-label">
                                Data <span class="text-danger">*</span>
                            </label>
                            <input type="date" id="data_preventivo" name="data_preventivo"
                                   class="form-control"
                                   value="<?= View::e($p['data_preventivo'] ?? $oggi) ?>"
                                   required>
                        </div>

                        <!-- Data scadenza -->
                        <div class="col-md-3">
                            <label for="data_scadenza" class="form-label">Scadenza</label>
                            <input type="date" id="data_scadenza" name="data_scadenza"
                                   class="form-control"
                                   value="<?= View::e($p['data_scadenza'] ?? '') ?>">
                        </div>

                        <!-- Anno riferimento -->
                        <div class="col-md-3">
                            <label for="anno_riferimento" class="form-label">Anno Rif.</label>
                            <select id="anno_riferimento" name="anno_riferimento" class="form-select">
                                <?php for ($a = $annoCorr - 2; $a <= $annoCorr + 2; $a++): ?>
                                <option value="<?= $a ?>"
                                    <?= (int)($p['anno_riferimento'] ?? $annoCorr) === $a ? 'selected' : '' ?>>
                                    <?= $a ?>
                                </option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <!-- Titolo -->
                        <div class="col-md-9">
                            <label for="titolo" class="form-label">Oggetto / Titolo</label>
                            <input type="text" id="titolo" name="titolo" class="form-control"
                                   value="<?= View::e($p['titolo'] ?? '') ?>"
                                   maxlength="200"
                                   placeholder="Descrizione breve del preventivo">
                        </div>

                    </div>
                </div>
            </div><!-- /card dati preventivo -->

            <!-- Card Voci del Preventivo -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light fw-semibold d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-list-check me-2 text-primary"></i>Voci del Preventivo</span>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-primary"
                                data-bs-toggle="modal" data-bs-target="#modal-tariffe">
                            <i class="bi bi-plus-lg me-1"></i>Aggiungi Servizio
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary"
                                data-bs-toggle="modal" data-bs-target="#modal-pacchetti">
                            <i class="bi bi-box me-1"></i>Da Pacchetto
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                id="btn-voce-manuale">
                            <i class="bi bi-pencil me-1"></i>Voce Manuale
                        </button>
                    </div>
                </div>
                <div class="card-body p-2">

                    <!-- Lista voci dinamica -->
                    <div id="voce-lista">
                        <div id="voce-empty" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            Nessuna voce aggiunta. Usa i pulsanti sopra per aggiungere servizi.
                        </div>
                    </div>

                    <!-- Sezione speciale cedolini -->
                    <div class="card border-info mt-3">
                        <div class="card-header bg-info bg-opacity-10 text-info-emphasis fw-semibold small py-2">
                            <i class="bi bi-people me-1"></i>Aggiunta rapida Cedolini
                        </div>
                        <div class="card-body py-2">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label small mb-1">N. Cedolini</label>
                                    <input type="number" id="ced-numero" class="form-control form-control-sm"
                                           min="1" max="999" placeholder="es. 5">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small mb-1">Mensilit&agrave;</label>
                                    <select id="ced-mensil" class="form-select form-select-sm">
                                        <option value="12">12 mensilit&agrave;</option>
                                        <option value="13">13 mensilit&agrave;</option>
                                        <option value="14">14 mensilit&agrave;</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small mb-1">Importo calcolato</label>
                                    <input type="text" id="ced-importo" class="form-control form-control-sm bg-light"
                                           readonly placeholder="—">
                                </div>
                                <div class="col-md-3">
                                    <button type="button" id="btn-ced-calcola"
                                            class="btn btn-sm btn-info w-100">
                                        <i class="bi bi-calculator me-1"></i>Calcola e Aggiungi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div><!-- /card voci -->

        </div><!-- /col-lg-8 -->

        <!-- ====================================================
             COLONNA DESTRA (4 colonne)
             ==================================================== -->
        <div class="col-lg-4">

            <!-- Card Riepilogo e Sconti -->
            <div class="card shadow-sm sticky-top" style="top: 1rem;">
                <div class="card-header bg-primary text-white fw-semibold">
                    <i class="bi bi-calculator me-2"></i>Riepilogo e Sconti
                </div>
                <div class="card-body">

                    <!-- Sconti -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label for="sconto1" class="form-label small">Sconto 1 (%)</label>
                            <div class="input-group input-group-sm">
                                <input type="number" id="sconto1" name="sconto1"
                                       class="form-control" min="0" max="100" step="0.5"
                                       value="<?= View::e($p['sconto1'] ?? '0') ?>">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <label for="sconto2" class="form-label small">Sconto 2 (%) <span class="text-muted">aggiuntivo</span></label>
                            <div class="input-group input-group-sm">
                                <input type="number" id="sconto2" name="sconto2"
                                       class="form-control" min="0" max="100" step="0.5"
                                       value="<?= View::e($p['sconto2'] ?? '0') ?>">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>

                    <!-- IVA -->
                    <div class="mb-3">
                        <label for="iva_perc" class="form-label small">Aliquota IVA</label>
                        <select id="iva_perc" name="iva_perc" class="form-select form-select-sm">
                            <?php foreach ($ivaOpts as $iva): ?>
                            <option value="<?= $iva ?>"
                                <?= (int)($p['iva_perc'] ?? ALIQUOTA_IVA) === $iva ? 'selected' : '' ?>>
                                <?= $iva ?>%
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Tabella riepilogo calcolata in real-time -->
                    <table class="table table-sm table-borderless mb-3">
                        <tbody>
                            <tr>
                                <td class="text-muted small">Imponibile lordo</td>
                                <td class="text-end fw-semibold" id="riepilogo-lordo">&euro; 0,00</td>
                            </tr>
                            <tr id="row-sc1" class="text-danger small" style="display:none">
                                <td>Sconto 1 (<span id="lbl-sc1">0</span>%)</td>
                                <td class="text-end">- <span id="riepilogo-sc1">0,00</span></td>
                            </tr>
                            <tr id="row-sc2" class="text-danger small" style="display:none">
                                <td>Sconto 2 (<span id="lbl-sc2">0</span>%)</td>
                                <td class="text-end">- <span id="riepilogo-sc2">0,00</span></td>
                            </tr>
                            <tr class="border-top">
                                <td class="text-muted small">Imponibile netto</td>
                                <td class="text-end fw-semibold" id="riepilogo-netto">&euro; 0,00</td>
                            </tr>
                            <tr>
                                <td class="text-muted small">IVA (<span id="lbl-iva"><?= (int)($p['iva_perc'] ?? ALIQUOTA_IVA) ?></span>%)</td>
                                <td class="text-end" id="riepilogo-iva">&euro; 0,00</td>
                            </tr>
                            <tr class="border-top table-primary">
                                <td class="fw-bold">TOTALE</td>
                                <td class="text-end fw-bold fs-5" id="riepilogo-totale">&euro; 0,00</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Stato -->
                    <div class="mb-3">
                        <label for="stato" class="form-label small">Stato</label>
                        <select id="stato" name="stato" class="form-select form-select-sm">
                            <?php foreach ($statiOpts as $k => $lbl): ?>
                            <option value="<?= $k ?>"
                                <?= ($p['stato'] ?? 'bozza') === $k ? 'selected' : '' ?>>
                                <?= $lbl ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Note interne -->
                    <div class="mb-3">
                        <label for="note_interne" class="form-label small">
                            Note interne <span class="text-muted">(non stampate)</span>
                        </label>
                        <textarea id="note_interne" name="note_interne" class="form-control form-control-sm"
                                  rows="3"><?= View::e($p['note_interne'] ?? '') ?></textarea>
                    </div>

                    <!-- Note per cliente -->
                    <div class="mb-3">
                        <label for="note_cliente" class="form-label small">
                            Note per cliente <span class="text-muted">(stampate)</span>
                        </label>
                        <textarea id="note_cliente" name="note_cliente" class="form-control form-control-sm"
                                  rows="3"><?= View::e($p['note_cliente'] ?? '') ?></textarea>
                    </div>

                    <!-- Bottone salva -->
                    <div class="d-grid mt-3">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-floppy me-2"></i>Salva Preventivo
                        </button>
                    </div>

                </div>
            </div><!-- /card riepilogo -->

        </div><!-- /col-lg-4 -->

    </div><!-- /row principale -->

    <!-- Hidden inputs per le voci serializzate (popolate da JS al submit) -->
    <div id="voci-hidden-container"></div>

</form>

<!-- ==============================================================
     TEMPLATE voce (nascosto, clonato da JS)
     ============================================================== -->
<template id="tmpl-voce">
    <div class="voce-item card mb-2 border-start border-4 border-primary">
        <div class="card-body p-2">
            <div class="row g-2 align-items-start">

                <!-- Codice -->
                <div class="col-auto">
                    <label class="form-label small mb-1">Codice</label>
                    <input type="text" class="form-control form-control-sm font-monospace voce-codice"
                           style="width:90px" readonly>
                </div>

                <!-- Descrizione -->
                <div class="col">
                    <label class="form-label small mb-1">Descrizione</label>
                    <textarea class="form-control form-control-sm voce-descrizione"
                              rows="2" required></textarea>
                </div>

                <!-- Frequenza -->
                <div class="col-md-2">
                    <label class="form-label small mb-1">Frequenza</label>
                    <select class="form-select form-select-sm voce-frequenza">
                        <option value="una_tantum">Una Tantum</option>
                        <option value="mensile">Mensile</option>
                        <option value="trimestrale">Trimestrale</option>
                        <option value="semestrale">Semestrale</option>
                        <option value="annuale">Annuale</option>
                        <option value="a_prestazione" selected>A Prestazione</option>
                    </select>
                </div>

                <!-- Mesi (solo per mensile) -->
                <div class="col-auto voce-mesi-wrap" style="display:none">
                    <label class="form-label small mb-1">Mesi</label>
                    <input type="number" class="form-control form-control-sm voce-mesi"
                           style="width:60px" min="1" max="14" value="12">
                </div>

                <!-- Mensilita (solo cedolini) -->
                <div class="col-auto voce-mensil-wrap" style="display:none">
                    <label class="form-label small mb-1">Mensilit&agrave;</label>
                    <select class="form-select form-select-sm voce-mensil">
                        <option value="12">12</option>
                        <option value="13">13</option>
                        <option value="14">14</option>
                    </select>
                </div>

                <!-- Quantita -->
                <div class="col-auto">
                    <label class="form-label small mb-1">Qta</label>
                    <input type="number" class="form-control form-control-sm voce-quantita"
                           style="width:65px" min="0" step="1" value="1">
                </div>

                <!-- Importo unitario -->
                <div class="col-md-2">
                    <label class="form-label small mb-1">Importo Unit. (&euro;)</label>
                    <input type="number" class="form-control form-control-sm voce-importo-unitario"
                           min="0" step="0.01" value="0.00">
                </div>

                <!-- Importo riga -->
                <div class="col-auto">
                    <label class="form-label small mb-1">Totale riga</label>
                    <input type="text" class="form-control form-control-sm bg-light text-end voce-importo-riga fw-semibold"
                           style="width:100px" readonly value="€ 0,00">
                </div>

                <!-- Pulsante rimuovi -->
                <div class="col-auto d-flex align-items-end pb-1">
                    <button type="button" class="btn btn-sm btn-danger btn-rimuovi-voce" title="Rimuovi voce">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <!-- Note voce -->
                <div class="col-12 mt-1">
                    <input type="text" class="form-control form-control-sm voce-note"
                           placeholder="Note voce (opzionale)">
                </div>

                <!-- Hidden fields -->
                <input type="hidden" class="voce-tariffa-id">
                <input type="hidden" class="voce-tipo-voce" value="tariffa">
                <input type="hidden" class="voce-pacchetto-id">

            </div>
        </div>
    </div>
</template>

<!-- ==============================================================
     MODAL Selezione Tariffa
     ============================================================== -->
<div class="modal fade" id="modal-tariffe" tabindex="-1" aria-labelledby="modal-tariffe-label" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modal-tariffe-label">
                    <i class="bi bi-tags me-2"></i>Seleziona Servizio
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Ricerca tariffe -->
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" id="ricerca-tariffe" class="form-control"
                               placeholder="Cerca per codice, categoria, descrizione...">
                    </div>
                </div>

                <!-- Accordion sezioni -->
                <div class="accordion" id="accordion-tariffe">
                    <?php
                    $sezioneIdx = 0;
                    foreach ($tariffeGruppi ?? [] as $sezione => $categorie):
                        $sezioneId = 'sez-' . preg_replace('/[^a-z0-9]/i', '-', strtolower($sezione));
                        $sezioneIdx++;
                    ?>
                    <div class="accordion-item tariffa-sezione-block">
                        <h2 class="accordion-header">
                            <button class="accordion-button <?= $sezioneIdx > 1 ? 'collapsed' : '' ?>"
                                    type="button" data-bs-toggle="collapse"
                                    data-bs-target="#<?= $sezioneId ?>"
                                    aria-expanded="<?= $sezioneIdx === 1 ? 'true' : 'false' ?>"
                                    aria-controls="<?= $sezioneId ?>">
                                <strong><?= View::e($sezione) ?></strong>
                            </button>
                        </h2>
                        <div id="<?= $sezioneId ?>"
                             class="accordion-collapse collapse <?= $sezioneIdx === 1 ? 'show' : '' ?>"
                             data-bs-parent="#accordion-tariffe">
                            <div class="accordion-body p-0">
                                <?php foreach ($categorie as $categoria => $tariffe): ?>
                                <div class="tariffa-cat-block">
                                    <div class="bg-light px-3 py-2 border-bottom">
                                        <small class="text-muted fw-semibold text-uppercase"><?= View::e($categoria) ?></small>
                                    </div>
                                    <ul class="list-group list-group-flush">
                                        <?php foreach ($tariffe as $t):
                                            $importoDisp = '';
                                            if (($t['tipo'] ?? '') === 'aprev') {
                                                $importoDisp = 'A preventivo';
                                            } elseif (isset($t['importo_min'])) {
                                                $importoDisp = 'da ' . View::euro($t['importo_min']);
                                                if (!empty($t['importo_max'])) {
                                                    $importoDisp .= ' a ' . View::euro($t['importo_max']);
                                                }
                                            }
                                        ?>
                                        <li class="list-group-item list-group-item-action tariffa-riga py-2"
                                            data-search="<?= strtolower(View::e(($t['codice'] ?? '') . ' ' . ($t['categoria'] ?? '') . ' ' . ($t['descrizione'] ?? ''))) ?>"
                                            data-id="<?= (int)($t['id'] ?? 0) ?>"
                                            data-codice="<?= View::e($t['codice'] ?? '') ?>"
                                            data-descrizione="<?= View::e($t['descrizione'] ?? '') ?>"
                                            data-frequenza="<?= View::e($t['frequenza'] ?? 'a_prestazione') ?>"
                                            data-importo="<?= number_format((float)($t['importo_min'] ?? 0), 2, '.', '') ?>"
                                            data-tipo="<?= View::e($t['tipo'] ?? 'fisso') ?>"
                                            style="cursor:pointer">
                                            <div class="d-flex align-items-center gap-3">
                                                <span class="badge bg-secondary font-monospace"><?= View::e($t['codice'] ?? '') ?></span>
                                                <span class="flex-grow-1"><?= View::e($t['descrizione'] ?? '') ?></span>
                                                <span class="text-muted small me-3"><?= $importoDisp ?></span>
                                                <span class="badge bg-light text-dark border">
                                                    <?= View::e($freqOpts[$t['frequenza'] ?? ''] ?? ($t['frequenza'] ?? '')) ?>
                                                </span>
                                                <button type="button" class="btn btn-sm btn-primary btn-seleziona-tariffa">
                                                    <i class="bi bi-plus-lg"></i>
                                                </button>
                                            </div>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>

<!-- ==============================================================
     MODAL Selezione Pacchetto
     ============================================================== -->
<div class="modal fade" id="modal-pacchetti" tabindex="-1" aria-labelledby="modal-pacchetti-label" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modal-pacchetti-label">
                    <i class="bi bi-box me-2"></i>Seleziona Pacchetto
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <?php if (empty($pacchetti)): ?>
                <div class="text-center text-muted py-4">
                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                    Nessun pacchetto disponibile.
                </div>
                <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($pacchetti as $pk): ?>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header fw-semibold">
                                <?= View::e($pk['nome'] ?? '') ?>
                                <?php if (!empty($pk['importo_totale'])): ?>
                                <span class="float-end badge bg-primary"><?= View::euro($pk['importo_totale']) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body p-2">
                                <?php if (!empty($pk['descrizione'])): ?>
                                <p class="small text-muted mb-2"><?= View::e($pk['descrizione']) ?></p>
                                <?php endif; ?>
                                <ul class="list-unstyled small mb-0">
                                    <?php foreach ($pk['tariffe'] ?? [] as $pt): ?>
                                    <li class="py-1 border-bottom">
                                        <i class="bi bi-check2 text-success me-1"></i>
                                        <span class="font-monospace text-muted"><?= View::e($pt['codice'] ?? '') ?></span>
                                        <?= View::e($pt['descrizione'] ?? '') ?>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <div class="card-footer text-end p-2">
                                <button type="button" class="btn btn-sm btn-primary btn-seleziona-pacchetto"
                                        data-pacchetto='<?= json_encode($pk, JSON_HEX_APOS | JSON_HEX_QUOT) ?>'>
                                    <i class="bi bi-plus-lg me-1"></i>Aggiungi tutte le voci
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>

<!-- ==============================================================
     JAVASCRIPT
     ============================================================== -->
<script>
(function () {
    'use strict';

    // ----------------------------------------------------------------
    // Riferimenti DOM
    // ----------------------------------------------------------------
    const voceLista        = document.getElementById('voce-lista');
    const voceEmpty        = document.getElementById('voce-empty');
    const tmplVoce         = document.getElementById('tmpl-voce');
    const formPreventivo   = document.getElementById('form-preventivo');
    const vociHidden       = document.getElementById('voci-hidden-container');
    const inputSconto1     = document.getElementById('sconto1');
    const inputSconto2     = document.getElementById('sconto2');
    const selectIva        = document.getElementById('iva_perc');
    const lblIva           = document.getElementById('lbl-iva');
    const ricercaTariffe   = document.getElementById('ricerca-tariffe');
    const modalTariffe     = document.getElementById('modal-tariffe');
    const modalPacchetti   = document.getElementById('modal-pacchetti');
    const clienteSearch    = document.getElementById('cliente_search');
    const clienteIdInput   = document.getElementById('cliente_id');
    const clientiList      = document.getElementById('clienti-list');

    // ----------------------------------------------------------------
    // Utilità formattazione euro
    // ----------------------------------------------------------------
    function fmt(n) {
        return '€ ' + Number(n).toLocaleString('it-IT', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    function parseNum(s) {
        return parseFloat(('' + s).replace(',', '.')) || 0;
    }

    // ----------------------------------------------------------------
    // Aggiorna visibilità del messaggio "vuoto"
    // ----------------------------------------------------------------
    function aggiornaEmpty() {
        const haVoci = voceLista.querySelectorAll('.voce-item').length > 0;
        voceEmpty.style.display = haVoci ? 'none' : 'block';
    }

    // ----------------------------------------------------------------
    // Calcola e aggiorna importo riga su una voce
    // ----------------------------------------------------------------
    function calcolaRiga(voceEl) {
        const freq  = voceEl.querySelector('.voce-frequenza').value;
        const mesi  = parseNum(voceEl.querySelector('.voce-mesi').value) || 1;
        const qta   = parseNum(voceEl.querySelector('.voce-quantita').value) || 1;
        const unit  = parseNum(voceEl.querySelector('.voce-importo-unitario').value);

        let riga;
        if (freq === 'mensile') {
            riga = unit * mesi;
        } else {
            riga = unit * qta;
        }
        riga = Math.round(riga * 100) / 100;
        voceEl.querySelector('.voce-importo-riga').value = fmt(riga);
        return riga;
    }

    // ----------------------------------------------------------------
    // Aggiorna totali nel riepilogo
    // ----------------------------------------------------------------
    function aggiornaTotali() {
        let lordo = 0;
        vocelista.querySelectorAll('.voce-item').forEach(function (v) {
            lordo += calcolaRiga(v);
        });

        const sc1  = parseNum(inputSconto1.value);
        const sc2  = parseNum(inputSconto2.value);
        const iva  = parseNum(selectIva.value);

        const importoSc1 = Math.round(lordo * sc1 / 100 * 100) / 100;
        const dopoSc1    = lordo - importoSc1;
        const importoSc2 = Math.round(dopoSc1 * sc2 / 100 * 100) / 100;
        const netto      = dopoSc1 - importoSc2;
        const importoIva = Math.round(netto * iva / 100 * 100) / 100;
        const totale     = netto + importoIva;

        document.getElementById('riepilogo-lordo').textContent  = fmt(lordo);
        document.getElementById('riepilogo-netto').textContent  = fmt(netto);
        document.getElementById('riepilogo-iva').textContent    = fmt(importoIva);
        document.getElementById('riepilogo-totale').textContent = fmt(totale);
        document.getElementById('lbl-iva').textContent          = iva;

        // Sconto 1
        const rowSc1 = document.getElementById('row-sc1');
        if (sc1 > 0) {
            document.getElementById('lbl-sc1').textContent      = sc1;
            document.getElementById('riepilogo-sc1').textContent = fmt(importoSc1);
            rowSc1.style.display = '';
        } else {
            rowSc1.style.display = 'none';
        }
        // Sconto 2
        const rowSc2 = document.getElementById('row-sc2');
        if (sc2 > 0) {
            document.getElementById('lbl-sc2').textContent      = sc2;
            document.getElementById('riepilogo-sc2').textContent = fmt(importoSc2);
            rowSc2.style.display = '';
        } else {
            rowSc2.style.display = 'none';
        }
    }

    // Alias corretto (typo fix: vocesList -> vocesList)
    const vocesList = voceLista; // entrambi puntano a voce-lista

    // ----------------------------------------------------------------
    // aggiungiVoce(dati): clona il template e popola
    // ----------------------------------------------------------------
    function aggiungiVoce(dati) {
        const clone = tmplVoce.content.cloneNode(true);
        const el    = clone.querySelector('.voce-item');

        el.querySelector('.voce-codice').value            = dati.codice || '';
        el.querySelector('.voce-descrizione').value       = dati.descrizione || '';
        el.querySelector('.voce-quantita').value          = dati.quantita || 1;
        el.querySelector('.voce-importo-unitario').value  = dati.importo_unitario || '0.00';
        el.querySelector('.voce-note').value              = dati.note || '';
        el.querySelector('.voce-tariffa-id').value        = dati.tariffa_id || '';
        el.querySelector('.voce-tipo-voce').value         = dati.tipo_voce || 'tariffa';
        el.querySelector('.voce-pacchetto-id').value      = dati.pacchetto_id || '';
        el.querySelector('.voce-mesi').value              = dati.mesi || 12;

        // Frequenza
        const selFreq = el.querySelector('.voce-frequenza');
        if (dati.frequenza) {
            selFreq.value = dati.frequenza;
        }
        // Mensilità cedolini
        const selMensil = el.querySelector('.voce-mensil');
        if (dati.mensil) {
            selMensil.value = dati.mensil;
        }

        // Visibilità mesi/mensilità
        aggiornaWrap(el);

        // Event listeners
        attaccaListenerVoce(el);

        voceLista.insertBefore(el, voceEmpty);
        aggiornaEmpty();
        calcolaRiga(el);
        aggiornaTotali();

        return el;
    }

    // ----------------------------------------------------------------
    // Mostra/nasconde i campi opzionali in base alla frequenza
    // ----------------------------------------------------------------
    function aggiornaWrap(voceEl) {
        const freq      = voceEl.querySelector('.voce-frequenza').value;
        const mesiWrap  = voceEl.querySelector('.voce-mesi-wrap');
        const mensilWrap= voceEl.querySelector('.voce-mensil-wrap');

        mesiWrap.style.display   = freq === 'mensile' ? '' : 'none';
        mensilWrap.style.display = freq === 'mensile' ? '' : 'none';
    }

    // ----------------------------------------------------------------
    // Attacca listener ai campi di una voce
    // ----------------------------------------------------------------
    function attaccaListenerVoce(voceEl) {
        voceEl.querySelector('.voce-frequenza').addEventListener('change', function () {
            aggiornaWrap(voceEl);
            calcolaRiga(voceEl);
            aggiornaTotali();
        });
        ['voce-quantita', 'voce-importo-unitario', 'voce-mesi'].forEach(function (cls) {
            voceEl.querySelector('.' + cls).addEventListener('input', function () {
                calcolaRiga(voceEl);
                aggiornaTotali();
            });
        });
        voceEl.querySelector('.voce-mensil').addEventListener('change', function () {
            calcolaRiga(voceEl);
            aggiornaTotali();
        });
        voceEl.querySelector('.btn-rimuovi-voce').addEventListener('click', function () {
            voceEl.remove();
            aggiornaEmpty();
            aggiornaTotali();
        });
    }

    // ----------------------------------------------------------------
    // Client-side search nei clienti tramite datalist
    // ----------------------------------------------------------------
    clienteSearch.addEventListener('input', function () {
        const val = this.value.trim().toLowerCase();
        const opts = clientiList.querySelectorAll('option');
        let found = null;
        opts.forEach(function (o) {
            if (o.value.toLowerCase() === val) {
                found = o;
            }
        });
        if (found) {
            clienteIdInput.value = found.dataset.id;
        } else if (!val) {
            clienteIdInput.value = '';
        }
    });

    // ----------------------------------------------------------------
    // Ricerca tariffe nel modale
    // ----------------------------------------------------------------
    ricercaTariffe.addEventListener('input', function () {
        const q = this.value.toLowerCase().trim();
        document.querySelectorAll('.tariffa-riga').forEach(function (row) {
            const match = !q || row.dataset.search.includes(q);
            row.style.display = match ? '' : 'none';
        });
        // Apri tutti gli accordion se c'è testo di ricerca
        if (q) {
            modalTariffe.querySelectorAll('.accordion-collapse').forEach(function (col) {
                if (!col.classList.contains('show')) {
                    col.classList.add('show');
                }
            });
        }
    });

    // ----------------------------------------------------------------
    // Selezione tariffa dal modale
    // ----------------------------------------------------------------
    document.querySelectorAll('.btn-seleziona-tariffa').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const row = btn.closest('.tariffa-riga');
            const dati = {
                tariffa_id:       row.dataset.id,
                codice:           row.dataset.codice,
                descrizione:      row.dataset.descrizione,
                frequenza:        row.dataset.frequenza,
                importo_unitario: row.dataset.importo,
                tipo_voce:        'tariffa',
                quantita:         1,
            };
            aggiungiVoce(dati);
            bootstrap.Modal.getInstance(modalTariffe).hide();
        });
    });

    // Click sull'intera riga tariffa (non sul bottone)
    document.querySelectorAll('.tariffa-riga').forEach(function (row) {
        row.addEventListener('click', function (e) {
            if (e.target.closest('.btn-seleziona-tariffa')) return;
            const dati = {
                tariffa_id:       row.dataset.id,
                codice:           row.dataset.codice,
                descrizione:      row.dataset.descrizione,
                frequenza:        row.dataset.frequenza,
                importo_unitario: row.dataset.importo,
                tipo_voce:        'tariffa',
                quantita:         1,
            };
            aggiungiVoce(dati);
            bootstrap.Modal.getInstance(modalTariffe).hide();
        });
    });

    // ----------------------------------------------------------------
    // Selezione pacchetto dal modale
    // ----------------------------------------------------------------
    document.querySelectorAll('.btn-seleziona-pacchetto').forEach(function (btn) {
        btn.addEventListener('click', function () {
            let pk;
            try { pk = JSON.parse(btn.dataset.pacchetto); } catch(e) { return; }
            const tariffe = pk.tariffe || [];
            tariffe.forEach(function (t) {
                aggiungiVoce({
                    tariffa_id:       t.id || '',
                    codice:           t.codice || '',
                    descrizione:      t.descrizione || '',
                    frequenza:        t.frequenza || 'a_prestazione',
                    importo_unitario: t.importo_min || '0.00',
                    tipo_voce:        'pacchetto',
                    pacchetto_id:     pk.id || '',
                    quantita:         1,
                });
            });
            bootstrap.Modal.getInstance(modalPacchetti).hide();
        });
    });

    // ----------------------------------------------------------------
    // Voce manuale
    // ----------------------------------------------------------------
    document.getElementById('btn-voce-manuale').addEventListener('click', function () {
        aggiungiVoce({
            tipo_voce:  'manuale',
            frequenza:  'a_prestazione',
            quantita:   1,
        });
    });

    // ----------------------------------------------------------------
    // Cedolini via fetch
    // ----------------------------------------------------------------
    const btnCedCalc = document.getElementById('btn-ced-calcola');
    const cedNumero  = document.getElementById('ced-numero');
    const cedMensil  = document.getElementById('ced-mensil');
    const cedImporto = document.getElementById('ced-importo');

    cedNumero.addEventListener('input', function () {
        cedImporto.value = '';
    });
    cedMensil.addEventListener('change', function () {
        cedImporto.value = '';
    });

    btnCedCalc.addEventListener('click', async function () {
        const n    = parseInt(cedNumero.value, 10);
        const mesi = parseInt(cedMensil.value, 10);
        if (!n || n < 1) {
            cedNumero.focus();
            return;
        }

        btnCedCalc.disabled = true;
        btnCedCalc.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Calcolo...';

        try {
            const res = await fetch(
                <?= json_encode(View::url('api/cedolini-calcolo')) ?> +
                '?n=' + n + '&mesi=' + mesi
            );
            const data = await res.json();
            if (data.importo !== undefined) {
                cedImporto.value = fmt(data.importo);
                aggiungiVoce({
                    codice:           data.codice || 'CED',
                    descrizione:      data.descrizione || 'Elaborazione cedolini paghe (' + n + ' dipendenti)',
                    frequenza:        'mensile',
                    importo_unitario: data.importo,
                    mesi:             mesi,
                    tipo_voce:        'tariffa',
                    tariffa_id:       data.tariffa_id || '',
                    quantita:         1,
                });
            } else {
                alert('Impossibile calcolare l\'importo cedolini.');
            }
        } catch (err) {
            alert('Errore di comunicazione con il server.');
        } finally {
            btnCedCalc.disabled = false;
            btnCedCalc.innerHTML = '<i class="bi bi-calculator me-1"></i>Calcola e Aggiungi';
        }
    });

    // ----------------------------------------------------------------
    // Listener su sconti e IVA per ricalcolo
    // ----------------------------------------------------------------
    [inputSconto1, inputSconto2].forEach(function (inp) {
        inp.addEventListener('input', aggiornaTotali);
    });
    selectIva.addEventListener('change', function () {
        lblIva.textContent = this.value;
        aggiornaTotali();
    });

    // ----------------------------------------------------------------
    // Al submit: serializza le voci come hidden inputs
    // ----------------------------------------------------------------
    formPreventivo.addEventListener('submit', function () {
        // Rimuovi hidden precedenti
        vociHidden.innerHTML = '';

        voceLista.querySelectorAll('.voce-item').forEach(function (voceEl, i) {
            const freq  = voceEl.querySelector('.voce-frequenza').value;
            const mesi  = voceEl.querySelector('.voce-mesi').value;
            const qta   = voceEl.querySelector('.voce-quantita').value;
            const unit  = voceEl.querySelector('.voce-importo-unitario').value;

            let rigaVal = parseNum(unit) * (freq === 'mensile' ? parseNum(mesi) : parseNum(qta));
            rigaVal = Math.round(rigaVal * 100) / 100;

            const campi = {
                tipo_voce:        voceEl.querySelector('.voce-tipo-voce').value,
                tariffa_id:       voceEl.querySelector('.voce-tariffa-id').value,
                pacchetto_id:     voceEl.querySelector('.voce-pacchetto-id').value,
                codice:           voceEl.querySelector('.voce-codice').value,
                descrizione:      voceEl.querySelector('.voce-descrizione').value,
                frequenza:        freq,
                mesi:             mesi,
                quantita:         qta,
                importo_unitario: unit,
                importo_riga:     rigaVal.toFixed(2),
                note:             voceEl.querySelector('.voce-note').value,
            };

            Object.entries(campi).forEach(function ([key, val]) {
                const inp = document.createElement('input');
                inp.type  = 'hidden';
                inp.name  = 'voci[' + i + '][' + key + ']';
                inp.value = val;
                vociHidden.appendChild(inp);
            });
        });
    });

    // ----------------------------------------------------------------
    // Caricamento pagina: ripristina voci esistenti
    // ----------------------------------------------------------------
    <?php if (!empty($voci)): ?>
    const vociEsistenti = <?= json_encode(array_values($voci), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    vociEsistenti.forEach(function (v) {
        aggiungiVoce({
            tariffa_id:       v.tariffa_id || '',
            pacchetto_id:     v.pacchetto_id || '',
            codice:           v.codice || '',
            descrizione:      v.descrizione || '',
            frequenza:        v.frequenza || 'a_prestazione',
            mesi:             v.mesi || 12,
            quantita:         v.quantita || 1,
            importo_unitario: v.importo_unitario || '0.00',
            note:             v.note || '',
            tipo_voce:        v.tipo_voce || 'tariffa',
        });
    });
    <?php endif; ?>

    // Pulizia del modal tariffe all'apertura (reset ricerca)
    modalTariffe.addEventListener('show.bs.modal', function () {
        ricercaTariffe.value = '';
        document.querySelectorAll('.tariffa-riga').forEach(function (r) {
            r.style.display = '';
        });
    });

    // Ricalcolo iniziale
    aggiornaTotali();

})();
</script>
