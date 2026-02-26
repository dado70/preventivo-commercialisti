<?php
use App\Core\View;

$isNew  = empty($cliente['id']);
$title  = $isNew ? 'Nuovo Cliente' : 'Modifica: ' . ($cliente['ragione_sociale'] ?? '');
$action = $isNew ? View::url('clienti/store') : View::url('clienti/' . (int)($cliente['id']) . '/update');

$formeGiuridiche = [
    ''      => '-- Seleziona --',
    'SRL'   => 'S.r.l.',
    'SRLS'  => 'S.r.l.s.',
    'SPA'   => 'S.p.A.',
    'SNC'   => 'S.n.c.',
    'SAS'   => 'S.a.s.',
    'SS'    => 'Società Semplice',
    'IND'   => 'Ditta Individuale',
    'PF'    => 'Persona Fisica',
    'ALTRO' => 'Altro',
];

$tipiContabilita = [
    ''              => '-- Seleziona --',
    'ordinaria'     => 'Ordinaria',
    'semplificata'  => 'Semplificata',
    'forfettario'   => 'Regime Forfettario',
    'enti'          => 'Enti / Associazioni',
];

$regimiFiscali = [
    ''              => '-- Seleziona --',
    'ordinario'     => 'Regime Ordinario',
    'forfettario'   => 'Regime Forfettario',
    'minimi'        => 'Regime dei Minimi',
    'esente'        => 'Esente IVA',
];

$v = function (string $key) use ($cliente): string {
    return View::e($cliente[$key] ?? '');
};
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="bi bi-<?= $isNew ? 'person-plus' : 'pencil-square' ?> me-2 text-primary"></i>
        <?= View::e($title) ?>
    </h1>
    <a href="<?= View::url('clienti') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Torna ai Clienti
    </a>
</div>

<form action="<?= $action ?>" method="POST" novalidate>
    <?php if (!$isNew): ?>
    <input type="hidden" name="_method" value="PUT">
    <?php endif; ?>

    <div class="row g-4">

        <!-- Dati Anagrafici -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light fw-semibold">
                    <i class="bi bi-building me-2 text-primary"></i>Dati Anagrafici
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        <div class="col-12">
                            <label for="ragione_sociale" class="form-label">
                                Ragione Sociale <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="ragione_sociale" name="ragione_sociale" class="form-control"
                                   value="<?= $v('ragione_sociale') ?>" required maxlength="200"
                                   placeholder="Ragione sociale o cognome e nome">
                        </div>

                        <div class="col-md-6">
                            <label for="nome_referente" class="form-label">Nome Referente</label>
                            <input type="text" id="nome_referente" name="nome_referente" class="form-control"
                                   value="<?= $v('nome_referente') ?>" maxlength="100"
                                   placeholder="Nome e cognome referente">
                        </div>

                        <div class="col-md-6">
                            <label for="forma_giuridica" class="form-label">Forma Giuridica</label>
                            <select id="forma_giuridica" name="forma_giuridica" class="form-select">
                                <?php foreach ($formeGiuridiche as $k => $label): ?>
                                <option value="<?= View::e($k) ?>" <?= ($cliente['forma_giuridica'] ?? '') === $k ? 'selected' : '' ?>>
                                    <?= View::e($label) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="partita_iva" class="form-label">Partita IVA</label>
                            <input type="text" id="partita_iva" name="partita_iva"
                                   class="form-control font-monospace"
                                   value="<?= $v('partita_iva') ?>" maxlength="11"
                                   placeholder="00000000000">
                        </div>

                        <div class="col-md-6">
                            <label for="codice_fiscale" class="form-label">Codice Fiscale</label>
                            <input type="text" id="codice_fiscale" name="codice_fiscale"
                                   class="form-control font-monospace"
                                   value="<?= $v('codice_fiscale') ?>" maxlength="16"
                                   placeholder="XXXXXXXXXXXXXXXX">
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Recapiti -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light fw-semibold">
                    <i class="bi bi-telephone me-2 text-primary"></i>Recapiti
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        <div class="col-12">
                            <label for="indirizzo" class="form-label">Indirizzo</label>
                            <input type="text" id="indirizzo" name="indirizzo" class="form-control"
                                   value="<?= $v('indirizzo') ?>" maxlength="200"
                                   placeholder="Via/Piazza, N.">
                        </div>

                        <div class="col-md-3">
                            <label for="cap" class="form-label">CAP</label>
                            <input type="text" id="cap" name="cap" class="form-control"
                                   value="<?= $v('cap') ?>" maxlength="5" placeholder="00000">
                        </div>
                        <div class="col-md-6">
                            <label for="citta" class="form-label">Citt&agrave;</label>
                            <input type="text" id="citta" name="citta" class="form-control"
                                   value="<?= $v('citta') ?>" maxlength="100" placeholder="Citt&agrave;">
                        </div>
                        <div class="col-md-3">
                            <label for="provincia" class="form-label">Prov.</label>
                            <input type="text" id="provincia" name="provincia" class="form-control"
                                   value="<?= $v('provincia') ?>" maxlength="2" placeholder="XX"
                                   style="text-transform:uppercase;">
                        </div>

                        <div class="col-md-6">
                            <label for="telefono" class="form-label">Telefono</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                <input type="tel" id="telefono" name="telefono" class="form-control"
                                       value="<?= $v('telefono') ?>" maxlength="20"
                                       placeholder="+39 000 0000000">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" id="email" name="email" class="form-control"
                                       value="<?= $v('email') ?>" maxlength="100"
                                       placeholder="email@cliente.it">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="pec" class="form-label">PEC</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                                <input type="email" id="pec" name="pec" class="form-control"
                                       value="<?= $v('pec') ?>" maxlength="100"
                                       placeholder="cliente@pec.it">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="sdi_codice" class="form-label">Codice SDI / Univoco</label>
                            <input type="text" id="sdi_codice" name="sdi_codice"
                                   class="form-control font-monospace"
                                   value="<?= $v('sdi_codice') ?>" maxlength="7"
                                   placeholder="XXXXXXX">
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Dati Fiscali -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light fw-semibold">
                    <i class="bi bi-receipt me-2 text-primary"></i>Dati Fiscali
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label for="tipo_contabilita" class="form-label">
                                Tipo Contabilit&agrave; <span class="text-danger">*</span>
                            </label>
                            <select id="tipo_contabilita" name="tipo_contabilita" class="form-select" required>
                                <?php foreach ($tipiContabilita as $k => $label): ?>
                                <option value="<?= View::e($k) ?>" <?= ($cliente['tipo_contabilita'] ?? '') === $k ? 'selected' : '' ?>>
                                    <?= View::e($label) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="regime_fiscale" class="form-label">Regime Fiscale</label>
                            <select id="regime_fiscale" name="regime_fiscale" class="form-select">
                                <?php foreach ($regimiFiscali as $k => $label): ?>
                                <option value="<?= View::e($k) ?>" <?= ($cliente['regime_fiscale'] ?? '') === $k ? 'selected' : '' ?>>
                                    <?= View::e($label) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="settore_attivita" class="form-label">Settore Attivit&agrave;</label>
                            <input type="text" id="settore_attivita" name="settore_attivita" class="form-control"
                                   value="<?= $v('settore_attivita') ?>" maxlength="100"
                                   placeholder="es. Commercio, Artigianato">
                        </div>

                        <div class="col-md-6">
                            <label for="codice_ateco" class="form-label">Codice ATECO</label>
                            <input type="text" id="codice_ateco" name="codice_ateco"
                                   class="form-control font-monospace"
                                   value="<?= $v('codice_ateco') ?>" maxlength="10"
                                   placeholder="00.00.0">
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Professionista e Sconti -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light fw-semibold">
                    <i class="bi bi-person-badge me-2 text-primary"></i>Professionista &amp; Sconti
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        <div class="col-12">
                            <label for="professionista_id" class="form-label">Professionista Referente</label>
                            <select id="professionista_id" name="professionista_id" class="form-select">
                                <option value="">-- Nessuno / Da assegnare --</option>
                                <?php foreach (($professionisti ?? []) as $prof): ?>
                                <option value="<?= (int)$prof['id'] ?>"
                                    <?= (int)($cliente['professionista_id'] ?? 0) === (int)$prof['id'] ? 'selected' : '' ?>>
                                    <?= View::e(($prof['titolo'] ?? '') . ' ' . ($prof['nome'] ?? '') . ' ' . ($prof['cognome'] ?? '')) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="sconto1" class="form-label">
                                Sconto 1 (%)
                                <span class="text-muted small">predefinito</span>
                            </label>
                            <div class="input-group">
                                <input type="number" id="sconto1" name="sconto1" class="form-control"
                                       value="<?= $v('sconto1') ?>" step="0.01" min="0" max="100"
                                       placeholder="0.00">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="sconto2" class="form-label">
                                Sconto 2 (%)
                                <span class="text-muted small">aggiuntivo</span>
                            </label>
                            <div class="input-group">
                                <input type="number" id="sconto2" name="sconto2" class="form-control"
                                       value="<?= $v('sconto2') ?>" step="0.01" min="0" max="100"
                                       placeholder="0.00">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Note e Attivo -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light fw-semibold">
                    <i class="bi bi-sticky me-2 text-primary"></i>Note e Stato
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="note" class="form-label">Note</label>
                        <textarea id="note" name="note" class="form-control" rows="3"
                                  placeholder="Note interne sul cliente..."><?= $v('note') ?></textarea>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch"
                               id="attivo" name="attivo" value="1"
                               <?= !empty($cliente['attivo']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="attivo">Cliente attivo</label>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- /.row -->

    <hr class="my-4">

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-floppy me-1"></i>Salva
        </button>
        <a href="<?= View::url('clienti') ?>" class="btn btn-outline-secondary px-4">
            <i class="bi bi-x-lg me-1"></i>Annulla
        </a>
        <?php if (!$isNew): ?>
        <a href="<?= View::url('preventivi/create?cliente_id=' . (int)($cliente['id'])) ?>"
           class="btn btn-outline-success ms-auto px-4">
            <i class="bi bi-file-plus me-1"></i>Nuovo Preventivo
        </a>
        <?php endif; ?>
    </div>

</form>
