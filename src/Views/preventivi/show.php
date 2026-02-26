<?php
use App\Core\View;

$p    = $preventivo ?? [];
$voci = $voci ?? [];

$freqLabels = [
    'una_tantum'    => 'Una Tantum',
    'mensile'       => 'Mensile',
    'trimestrale'   => 'Trimestrale',
    'semestrale'    => 'Semestrale',
    'annuale'       => 'Annuale',
    'a_prestazione' => 'A Prestazione',
];

$statiBadge = [
    'bozza'    => 'secondary',
    'inviato'  => 'info',
    'accettato'=> 'success',
    'rifiutato'=> 'danger',
    'scaduto'  => 'warning',
];
$stato      = $p['stato'] ?? 'bozza';
$statoBadge = $statiBadge[$stato] ?? 'secondary';

// Calcolo totali
$lordo       = (float)($p['imponibile_lordo'] ?? array_sum(array_column($voci, 'importo_riga')));
$sc1Perc     = (float)($p['sconto1'] ?? 0);
$sc2Perc     = (float)($p['sconto2'] ?? 0);
$ivaPerc     = (float)($p['iva_perc'] ?? ALIQUOTA_IVA);
$sc1Imp      = round($lordo * $sc1Perc / 100, 2);
$dopoSc1     = $lordo - $sc1Imp;
$sc2Imp      = round($dopoSc1 * $sc2Perc / 100, 2);
$netto       = round($dopoSc1 - $sc2Imp, 2);
$ivaImp      = round($netto * $ivaPerc / 100, 2);
$totale      = round($netto + $ivaImp, 2);

// Usa valori dal DB se disponibili
if (isset($p['totale'])) {
    $totale  = (float)$p['totale'];
    $netto   = (float)($p['imponibile_netto'] ?? $netto);
    $ivaImp  = (float)($p['importo_iva'] ?? $ivaImp);
}

// Dati studio
$studio = $studio ?? [];

// Data formattata
function fmtData(?string $d): string {
    if (!$d) return '—';
    try { return (new \DateTime($d))->format('d/m/Y'); } catch (\Exception $e) { return $d; }
}
?>

<style>
@media print {
    .no-print { display: none !important; }
    .card { border: none !important; box-shadow: none !important; }
    body { background: white !important; }
    .navbar, footer, .alert { display: none !important; }
    .container-fluid { padding: 0 !important; }
    .preventivo-sheet { padding: 0 !important; }
    .table { font-size: 0.85rem; }
    .badge { border: 1px solid #999; }
}
@media screen {
    .preventivo-sheet {
        background: white;
        border-radius: .5rem;
        box-shadow: 0 0 20px rgba(0,0,0,.08);
        padding: 2rem;
        max-width: 960px;
        margin: 0 auto;
    }
}
</style>

<!-- Pulsanti azione (no-print) -->
<div class="d-flex justify-content-between align-items-center mb-3 no-print">
    <div class="d-flex gap-2 align-items-center">
        <a href="<?= View::url('preventivi') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Elenco
        </a>
        <a href="<?= View::url('preventivi/' . (int)($p['id'] ?? 0) . '/edit') ?>"
           class="btn btn-outline-primary btn-sm">
            <i class="bi bi-pencil me-1"></i>Modifica
        </a>
        <a href="<?= View::url('export/' . (int)($p['id'] ?? 0) . '/pdf') ?>"
           class="btn btn-danger btn-sm" target="_blank">
            <i class="bi bi-file-earmark-pdf me-1"></i>Esporta PDF
        </a>
        <a href="<?= View::url('export/' . (int)($p['id'] ?? 0) . '/ods') ?>"
           class="btn btn-success btn-sm">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i>Esporta ODS
        </a>
        <button type="button" class="btn btn-outline-secondary btn-sm"
                onclick="window.print()">
            <i class="bi bi-printer me-1"></i>Stampa
        </button>
    </div>
    <div>
        <span class="badge bg-<?= $statoBadge ?> fs-6 text-capitalize"><?= View::e($stato) ?></span>
    </div>
</div>

<!-- Form cambio stato (no-print) -->
<div class="no-print mb-4">
    <form action="<?= View::url('preventivi/' . (int)($p['id'] ?? 0) . '/stato') ?>"
          method="POST" class="d-inline-flex align-items-center gap-2">
        <label class="form-label mb-0 small text-muted">Cambia stato:</label>
        <select name="stato" class="form-select form-select-sm" style="width:auto">
            <?php
            $stati = ['bozza' => 'Bozza', 'inviato' => 'Inviato', 'accettato' => 'Accettato', 'rifiutato' => 'Rifiutato', 'scaduto' => 'Scaduto'];
            foreach ($stati as $k => $l): ?>
            <option value="<?= $k ?>" <?= $stato === $k ? 'selected' : '' ?>><?= $l ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-arrow-right-circle me-1"></i>Aggiorna
        </button>
    </form>
</div>

<!-- ================================================================
     FOGLIO PREVENTIVO
     ================================================================ -->
<div class="preventivo-sheet">

    <!-- INTESTAZIONE: Studio e Cliente -->
    <table class="table table-borderless mb-4" style="font-size:.93rem">
        <tbody>
            <tr>
                <!-- Dati studio -->
                <td class="align-top pe-4" style="width:50%">
                    <?php if (!empty($studio['logo_path'])): ?>
                    <img src="<?= View::asset($studio['logo_path']) ?>"
                         alt="Logo Studio" class="mb-2"
                         style="max-height:80px; max-width:200px; object-fit:contain">
                    <br>
                    <?php endif; ?>
                    <strong class="fs-5"><?= View::e($studio['ragione_sociale'] ?? '') ?></strong>
                    <?php if (!empty($studio['forma_giuridica'])): ?>
                        <span class="text-muted"> - <?= View::e($studio['forma_giuridica']) ?></span>
                    <?php endif; ?>
                    <br>
                    <?php if (!empty($studio['indirizzo'])): ?>
                    <?= View::e($studio['indirizzo']) ?>,
                    <?= View::e($studio['cap'] ?? '') ?>
                    <?= View::e($studio['citta'] ?? '') ?>
                    <?php if (!empty($studio['provincia'])): ?>
                        (<?= View::e($studio['provincia']) ?>)
                    <?php endif; ?><br>
                    <?php endif; ?>
                    <?php if (!empty($studio['partita_iva'])): ?>
                    P.IVA: <strong><?= View::e($studio['partita_iva']) ?></strong><br>
                    <?php endif; ?>
                    <?php if (!empty($studio['telefono'])): ?>
                    Tel: <?= View::e($studio['telefono']) ?><br>
                    <?php endif; ?>
                    <?php if (!empty($studio['pec'])): ?>
                    PEC: <?= View::e($studio['pec']) ?><br>
                    <?php endif; ?>
                    <?php if (!empty($studio['ordine_professionale'])): ?>
                    <small class="text-muted">Iscritto all'<?= View::e($studio['ordine_professionale']) ?>
                    <?php if (!empty($studio['n_iscrizione_ordine'])): ?>
                        n. <?= View::e($studio['n_iscrizione_ordine']) ?>
                    <?php endif; ?></small>
                    <?php endif; ?>
                </td>

                <!-- Dati cliente -->
                <td class="align-top ps-4 border-start" style="width:50%">
                    <div class="text-muted small text-uppercase mb-1">Spettabile</div>
                    <strong class="fs-5"><?= View::e($p['cliente_nome'] ?? '') ?></strong><br>
                    <?php if (!empty($p['cliente_indirizzo'])): ?>
                    <?= View::e($p['cliente_indirizzo']) ?>,
                    <?= View::e($p['cliente_cap'] ?? '') ?>
                    <?= View::e($p['cliente_citta'] ?? '') ?>
                    <?php if (!empty($p['cliente_prov'])): ?>
                        (<?= View::e($p['cliente_prov']) ?>)
                    <?php endif; ?><br>
                    <?php endif; ?>
                    <?php if (!empty($p['cliente_piva'])): ?>
                    P.IVA: <strong><?= View::e($p['cliente_piva']) ?></strong><br>
                    <?php endif; ?>
                    <?php if (!empty($p['cliente_cf'])): ?>
                    C.F.: <?= View::e($p['cliente_cf']) ?><br>
                    <?php endif; ?>
                    <?php if (!empty($p['cliente_pec'])): ?>
                    PEC: <?= View::e($p['cliente_pec']) ?><br>
                    <?php endif; ?>
                </td>
            </tr>
        </tbody>
    </table>

    <hr>

    <!-- TITOLO PREVENTIVO -->
    <div class="text-center my-4">
        <h2 class="fw-bold text-uppercase letter-spacing-1" style="letter-spacing:.05em">
            Preventivo Onorari
        </h2>
        <div class="fs-5">
            <strong>N. <?= View::e($p['numero'] ?? '—') ?></strong>
            del <?= fmtData($p['data_preventivo'] ?? null) ?>
            <?php if (!empty($p['data_scadenza'])): ?>
            &mdash; <span class="text-muted">valido fino al <?= fmtData($p['data_scadenza']) ?></span>
            <?php endif; ?>
        </div>
        <?php if (!empty($p['anno_riferimento'])): ?>
        <div class="text-muted small mt-1">Anno di riferimento: <?= (int)$p['anno_riferimento'] ?></div>
        <?php endif; ?>
    </div>

    <!-- OGGETTO -->
    <?php if (!empty($p['titolo'])): ?>
    <div class="mb-4 p-3 bg-light rounded">
        <strong>Oggetto:</strong> <?= View::e($p['titolo']) ?>
    </div>
    <?php endif; ?>

    <!-- TABELLA VOCI -->
    <div class="table-responsive mb-4">
        <table class="table table-bordered table-hover align-middle" id="tbl-voci">
            <thead class="table-dark">
                <tr>
                    <th class="text-center" style="width:40px">N&deg;</th>
                    <th style="width:80px">Codice</th>
                    <th>Descrizione</th>
                    <th class="text-center" style="width:110px">Frequenza</th>
                    <th class="text-center" style="width:80px">Qta/Mesi</th>
                    <th class="text-end" style="width:110px">Importo Unit.</th>
                    <th class="text-end" style="width:110px">Importo</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($voci)): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        Nessuna voce presente.
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($voci as $i => $voce):
                    $freq     = $voce['frequenza'] ?? '';
                    $freqLbl  = $freqLabels[$freq] ?? $freq;
                    $qtaMesi  = $freq === 'mensile'
                        ? ($voce['mesi'] ?? 12) . ' mesi'
                        : 'x' . number_format((float)($voce['quantita'] ?? 1), 0);
                ?>
                <tr>
                    <td class="text-center text-muted"><?= $i + 1 ?></td>
                    <td class="font-monospace small"><?= View::e($voce['codice'] ?? '') ?></td>
                    <td>
                        <?= View::e($voce['descrizione'] ?? '') ?>
                        <?php if (!empty($voce['note'])): ?>
                        <br><small class="text-muted fst-italic"><?= View::e($voce['note']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark border"><?= View::e($freqLbl) ?></span>
                    </td>
                    <td class="text-center"><?= View::e($qtaMesi) ?></td>
                    <td class="text-end"><?= View::euro($voce['importo_unitario'] ?? 0) ?></td>
                    <td class="text-end fw-semibold"><?= View::euro($voce['importo_riga'] ?? 0) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- RIEPILOGO IMPORTI (allineato a destra) -->
    <div class="row justify-content-end mb-4">
        <div class="col-md-5 col-lg-4">
            <table class="table table-sm table-borderless">
                <tbody>
                    <tr>
                        <td class="text-muted">Imponibile lordo</td>
                        <td class="text-end"><?= View::euro($lordo) ?></td>
                    </tr>
                    <?php if ($sc1Perc > 0): ?>
                    <tr class="text-danger">
                        <td>Sconto <?= number_format($sc1Perc, 2) ?>%</td>
                        <td class="text-end">- <?= View::euro($sc1Imp) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($sc2Perc > 0): ?>
                    <tr class="text-danger">
                        <td>Sconto aggiuntivo <?= number_format($sc2Perc, 2) ?>%</td>
                        <td class="text-end">- <?= View::euro($sc2Imp) ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr class="border-top">
                        <td>Imponibile netto</td>
                        <td class="text-end"><?= View::euro($netto) ?></td>
                    </tr>
                    <tr>
                        <td>IVA <?= number_format($ivaPerc, 0) ?>%</td>
                        <td class="text-end"><?= View::euro($ivaImp) ?></td>
                    </tr>
                    <tr class="border-top table-primary">
                        <td class="fw-bold fs-5">TOTALE</td>
                        <td class="text-end fw-bold fs-5"><?= View::euro($totale) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- NOTE PER IL CLIENTE -->
    <?php if (!empty($p['note_cliente'])): ?>
    <div class="mb-4 p-3 border rounded bg-light">
        <strong class="d-block mb-2"><i class="bi bi-info-circle me-1 no-print"></i>Note:</strong>
        <?= nl2br(View::e($p['note_cliente'])) ?>
    </div>
    <?php endif; ?>

    <!-- DISCLAIMER -->
    <div class="mb-4 p-3 border-start border-4 border-warning bg-warning bg-opacity-10">
        <small class="text-muted fst-italic">
            I presenti onorari sono basati sugli onorari consigliati ANC (Associazione Nazionale Commercialisti).
            Agli importi si applica la scontistica indicata nel mandato professionale.
            Il presente preventivo ha validit&agrave; indicata in calce alla data di emissione,
            salvo variazioni del listino o delle condizioni di mandato.
        </small>
    </div>

    <!-- FOOTER STUDIO E FIRMA -->
    <hr>
    <div class="row mt-4">
        <div class="col-md-6">
            <small class="text-muted">
                <strong><?= View::e($studio['ragione_sociale'] ?? '') ?></strong><br>
                <?= View::e(($studio['indirizzo'] ?? '') . ' - ' . ($studio['cap'] ?? '') . ' ' . ($studio['citta'] ?? '')) ?><br>
                <?php if (!empty($studio['partita_iva'])): ?>
                P.IVA: <?= View::e($studio['partita_iva']) ?><br>
                <?php endif; ?>
                <?php if (!empty($studio['email'])): ?>
                <?= View::e($studio['email']) ?>
                <?php endif; ?>
            </small>
        </div>
        <div class="col-md-6 text-end">
            <?php if (!empty($p['professionista_nome'])): ?>
            <small class="text-muted d-block mb-4">
                Il Professionista<br>
                <strong><?= View::e(trim($p['professionista_nome'])) ?></strong>
            </small>
            <?php endif; ?>
            <div style="border-top: 1px solid #aaa; display:inline-block; width:180px; margin-top:2rem">
                <small class="text-muted">Firma</small>
            </div>
            <div class="mt-4">
                <div style="border-top: 1px solid #aaa; display:inline-block; width:180px">
                    <small class="text-muted">Data e Timbro</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Numero preventivo in piccolo in fondo -->
    <div class="text-center text-muted small mt-4">
        <small>Preventivo n. <?= View::e($p['numero'] ?? '') ?> &mdash; emesso il <?= fmtData($p['data_preventivo'] ?? null) ?></small>
    </div>

</div><!-- /preventivo-sheet -->
