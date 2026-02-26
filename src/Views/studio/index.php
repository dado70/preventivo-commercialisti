<?php
use App\Core\View;

$v = function (string $key) use ($studio): string {
    return View::e($studio[$key] ?? '');
};

$formeGiuridiche = [
    ''          => '-- Seleziona --',
    'SRL'       => 'S.r.l.',
    'SRLS'      => 'S.r.l.s.',
    'SPA'       => 'S.p.A.',
    'SNC'       => 'S.n.c.',
    'SAS'       => 'S.a.s.',
    'SS'        => 'Società Semplice',
    'IND'       => 'Individuale / Studio Associato',
    'ALTRO'     => 'Altro',
];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-building me-2 text-primary"></i>Anagrafica Studio</h1>
</div>

<form action="<?= View::url('studio/update') ?>" method="POST" enctype="multipart/form-data" novalidate>
    <input type="hidden" name="_method" value="PUT">

    <div class="row g-4">

        <!-- Colonna sinistra: Dati Aziendali -->
        <div class="col-lg-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light fw-semibold">
                    <i class="bi bi-building me-2 text-primary"></i>Dati Aziendali
                </div>
                <div class="card-body">

                    <div class="mb-3">
                        <label for="ragione_sociale" class="form-label">Ragione Sociale <span class="text-danger">*</span></label>
                        <input type="text" id="ragione_sociale" name="ragione_sociale" class="form-control"
                               value="<?= $v('ragione_sociale') ?>" required maxlength="200"
                               placeholder="Studio Associato...">
                    </div>

                    <div class="mb-3">
                        <label for="forma_giuridica" class="form-label">Forma Giuridica</label>
                        <select id="forma_giuridica" name="forma_giuridica" class="form-select">
                            <?php foreach ($formeGiuridiche as $k => $label): ?>
                            <option value="<?= View::e($k) ?>" <?= ($studio['forma_giuridica'] ?? '') === $k ? 'selected' : '' ?>>
                                <?= View::e($label) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="partita_iva" class="form-label">Partita IVA</label>
                            <input type="text" id="partita_iva" name="partita_iva" class="form-control font-monospace"
                                   value="<?= $v('partita_iva') ?>" maxlength="11"
                                   placeholder="00000000000">
                        </div>
                        <div class="col-md-6">
                            <label for="codice_fiscale" class="form-label">Codice Fiscale</label>
                            <input type="text" id="codice_fiscale" name="codice_fiscale" class="form-control font-monospace"
                                   value="<?= $v('codice_fiscale') ?>" maxlength="16"
                                   placeholder="XXXXXXXXXXXXXXXX">
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <label for="indirizzo" class="form-label">Indirizzo</label>
                        <input type="text" id="indirizzo" name="indirizzo" class="form-control"
                               value="<?= $v('indirizzo') ?>" maxlength="200"
                               placeholder="Via/Piazza, N.">
                    </div>

                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="cap" class="form-label">CAP</label>
                            <input type="text" id="cap" name="cap" class="form-control"
                                   value="<?= $v('cap') ?>" maxlength="5"
                                   placeholder="00000">
                        </div>
                        <div class="col-md-6">
                            <label for="citta" class="form-label">Citt&agrave;</label>
                            <input type="text" id="citta" name="citta" class="form-control"
                                   value="<?= $v('citta') ?>" maxlength="100"
                                   placeholder="Citt&agrave;">
                        </div>
                        <div class="col-md-3">
                            <label for="provincia" class="form-label">Prov.</label>
                            <input type="text" id="provincia" name="provincia" class="form-control"
                                   value="<?= $v('provincia') ?>" maxlength="2"
                                   placeholder="XX" style="text-transform:uppercase;">
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Colonna destra: Recapiti -->
        <div class="col-lg-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light fw-semibold">
                    <i class="bi bi-telephone me-2 text-primary"></i>Recapiti
                </div>
                <div class="card-body">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="telefono" class="form-label">Telefono</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                <input type="tel" id="telefono" name="telefono" class="form-control"
                                       value="<?= $v('telefono') ?>" maxlength="20"
                                       placeholder="+39 0000 000000">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="fax" class="form-label">Fax</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-printer"></i></span>
                                <input type="tel" id="fax" name="fax" class="form-control"
                                       value="<?= $v('fax') ?>" maxlength="20"
                                       placeholder="+39 0000 000000">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" id="email" name="email" class="form-control"
                                       value="<?= $v('email') ?>" maxlength="100"
                                       placeholder="info@studio.it">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="pec" class="form-label">PEC</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                                <input type="email" id="pec" name="pec" class="form-control"
                                       value="<?= $v('pec') ?>" maxlength="100"
                                       placeholder="studio@pec.it">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="sito_web" class="form-label">Sito Web</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-globe"></i></span>
                                <input type="url" id="sito_web" name="sito_web" class="form-control"
                                       value="<?= $v('sito_web') ?>" maxlength="200"
                                       placeholder="https://www.studio.it">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="iban" class="form-label">IBAN</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-bank"></i></span>
                                <input type="text" id="iban" name="iban" class="form-control font-monospace"
                                       value="<?= $v('iban') ?>" maxlength="34"
                                       placeholder="IT00 X000 0000 0000 0000 0000 000">
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="banca" class="form-label">Banca</label>
                            <input type="text" id="banca" name="banca" class="form-control"
                                   value="<?= $v('banca') ?>" maxlength="100"
                                   placeholder="Nome della banca">
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Dati Albo -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light fw-semibold">
                    <i class="bi bi-award me-2 text-primary"></i>Dati Albo Professionale
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label for="ordine_professionale" class="form-label">Ordine Professionale</label>
                            <input type="text" id="ordine_professionale" name="ordine_professionale" class="form-control"
                                   value="<?= $v('ordine_professionale') ?>" maxlength="100"
                                   placeholder="es. Ordine dei Dottori Commercialisti">
                        </div>
                        <div class="col-md-4">
                            <label for="n_iscrizione_ordine" class="form-label">N. Iscrizione Ordine</label>
                            <input type="text" id="n_iscrizione_ordine" name="n_iscrizione_ordine" class="form-control font-monospace"
                                   value="<?= $v('n_iscrizione_ordine') ?>" maxlength="50"
                                   placeholder="000/A">
                        </div>
                        <div class="col-md-3">
                            <label for="sez_registro" class="form-label">Sezione Registro</label>
                            <input type="text" id="sez_registro" name="sez_registro" class="form-control"
                                   value="<?= $v('sez_registro') ?>" maxlength="50"
                                   placeholder="A / B">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logo -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light fw-semibold">
                    <i class="bi bi-image me-2 text-primary"></i>Logo Studio
                </div>
                <div class="card-body text-center">
                    <?php if (!empty($studio['logo'])): ?>
                    <div class="mb-3">
                        <img src="<?= View::asset('img/' . View::e($studio['logo'])) ?>"
                             alt="Logo studio" class="img-fluid rounded border"
                             style="max-height: 100px; max-width: 100%;">
                        <p class="text-muted small mt-1 mb-0">Logo attuale</p>
                    </div>
                    <?php endif; ?>
                    <label for="logo" class="form-label d-block">
                        <?= !empty($studio['logo']) ? 'Sostituisci logo' : 'Carica logo' ?>
                    </label>
                    <input type="file" id="logo" name="logo" class="form-control"
                           accept="image/png,image/jpeg,image/svg+xml,image/gif">
                    <div class="form-text">PNG, JPG, SVG. Max 2MB.</div>
                </div>
            </div>
        </div>

        <!-- Note -->
        <div class="col-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light fw-semibold">
                    <i class="bi bi-sticky me-2 text-primary"></i>Note
                </div>
                <div class="card-body">
                    <textarea id="note" name="note" class="form-control" rows="3"
                              placeholder="Note interne sullo studio..."><?= $v('note') ?></textarea>
                </div>
            </div>
        </div>

    </div><!-- /.row -->

    <div class="d-flex gap-2 mb-4">
        <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-floppy me-1"></i>Salva Dati Studio
        </button>
    </div>

</form>
