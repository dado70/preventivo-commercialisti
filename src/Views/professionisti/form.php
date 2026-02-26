<?php
use App\Core\View;

$isNew  = empty($prof['id']);
$title  = $isNew ? 'Nuovo Professionista' : 'Modifica: ' . ($prof['titolo'] ?? '') . ' ' . ($prof['nome'] ?? '') . ' ' . ($prof['cognome'] ?? '');
$action = $isNew ? View::url('professionisti/store') : View::url('professionisti/' . (int)($prof['id']) . '/update');

$titoli = ['', 'Dott.', 'Dott.ssa', 'Rag.', 'Avv.', 'altro'];

$v = function (string $key) use ($prof): string {
    return View::e($prof[$key] ?? '');
};
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="bi bi-<?= $isNew ? 'plus-circle' : 'pencil-square' ?> me-2 text-primary"></i>
        <?= View::e($title) ?>
    </h1>
    <a href="<?= View::url('professionisti') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Torna ai Professionisti
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
                    <i class="bi bi-person me-2 text-primary"></i>Dati Anagrafici
                </div>
                <div class="card-body">

                    <div class="row g-3">

                        <!-- Titolo -->
                        <div class="col-md-4">
                            <label for="titolo" class="form-label">Titolo</label>
                            <select id="titolo" name="titolo" class="form-select">
                                <?php foreach ($titoli as $t): ?>
                                <option value="<?= View::e($t) ?>" <?= ($prof['titolo'] ?? '') === $t ? 'selected' : '' ?>>
                                    <?= $t === '' ? '-- Nessuno --' : View::e($t) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Titolo testo (visibile solo se "altro") -->
                        <div class="col-md-4" id="fieldTitoloAltro"
                             style="display:<?= ($prof['titolo'] ?? '') === 'altro' ? '' : 'none' ?>">
                            <label for="titolo_testo" class="form-label">Titolo personalizzato</label>
                            <input type="text" id="titolo_testo" name="titolo_testo" class="form-control"
                                   value="<?= $v('titolo_testo') ?>" maxlength="30"
                                   placeholder="es. Prof.">
                        </div>

                        <div class="col-md-4">
                            <!-- spacer when titolo_altro hidden -->
                        </div>

                        <div class="col-md-6">
                            <label for="nome" class="form-label">Nome <span class="text-danger">*</span></label>
                            <input type="text" id="nome" name="nome" class="form-control"
                                   value="<?= $v('nome') ?>" required maxlength="100"
                                   placeholder="Nome">
                        </div>

                        <div class="col-md-6">
                            <label for="cognome" class="form-label">Cognome <span class="text-danger">*</span></label>
                            <input type="text" id="cognome" name="cognome" class="form-control"
                                   value="<?= $v('cognome') ?>" required maxlength="100"
                                   placeholder="Cognome">
                        </div>

                        <div class="col-12">
                            <label for="qualifica" class="form-label">Qualifica</label>
                            <input type="text" id="qualifica" name="qualifica" class="form-control"
                                   value="<?= $v('qualifica') ?>" maxlength="100"
                                   placeholder="es. Dottore Commercialista, Revisore Legale">
                        </div>

                        <div class="col-md-6">
                            <label for="codice_fiscale" class="form-label">Codice Fiscale</label>
                            <input type="text" id="codice_fiscale" name="codice_fiscale"
                                   class="form-control font-monospace"
                                   value="<?= $v('codice_fiscale') ?>" maxlength="16"
                                   placeholder="XXXXXXXXXXXXXXXX">
                        </div>

                        <div class="col-md-6">
                            <label for="partita_iva" class="form-label">Partita IVA</label>
                            <input type="text" id="partita_iva" name="partita_iva"
                                   class="form-control font-monospace"
                                   value="<?= $v('partita_iva') ?>" maxlength="11"
                                   placeholder="00000000000">
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

                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" id="email" name="email" class="form-control"
                                       value="<?= $v('email') ?>" maxlength="100"
                                       placeholder="nome@studio.it">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="pec" class="form-label">PEC</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                                <input type="email" id="pec" name="pec" class="form-control"
                                       value="<?= $v('pec') ?>" maxlength="100"
                                       placeholder="nome@pec.it">
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="telefono" class="form-label">Telefono</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                <input type="tel" id="telefono" name="telefono" class="form-control"
                                       value="<?= $v('telefono') ?>" maxlength="20"
                                       placeholder="+39 000 0000000">
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Dati Albo -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light fw-semibold">
                    <i class="bi bi-award me-2 text-primary"></i>Dati Albo Professionale
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        <div class="col-md-4">
                            <label for="ordine_professionale" class="form-label">Ordine Professionale</label>
                            <input type="text" id="ordine_professionale" name="ordine_professionale" class="form-control"
                                   value="<?= $v('ordine_professionale') ?>" maxlength="100"
                                   placeholder="es. Ordine dei Dottori Commercialisti">
                        </div>

                        <div class="col-md-2">
                            <label for="n_iscrizione_ordine" class="form-label">N. Iscrizione</label>
                            <input type="text" id="n_iscrizione_ordine" name="n_iscrizione_ordine"
                                   class="form-control font-monospace"
                                   value="<?= $v('n_iscrizione_ordine') ?>" maxlength="50"
                                   placeholder="000/A">
                        </div>

                        <div class="col-md-2">
                            <label for="sez_registro" class="form-label">Sezione</label>
                            <input type="text" id="sez_registro" name="sez_registro" class="form-control"
                                   value="<?= $v('sez_registro') ?>" maxlength="50"
                                   placeholder="A / B">
                        </div>

                        <div class="col-md-4">
                            <label for="provincia_ordine" class="form-label">Provincia Ordine</label>
                            <input type="text" id="provincia_ordine" name="provincia_ordine" class="form-control"
                                   value="<?= $v('provincia_ordine') ?>" maxlength="50"
                                   placeholder="es. Milano">
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
                                  placeholder="Note interne sul professionista..."><?= $v('note') ?></textarea>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch"
                               id="attivo" name="attivo" value="1"
                               <?= !empty($prof['attivo']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="attivo">Professionista attivo</label>
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
        <a href="<?= View::url('professionisti') ?>" class="btn btn-outline-secondary px-4">
            <i class="bi bi-x-lg me-1"></i>Annulla
        </a>
    </div>

</form>

<script>
(function () {
    const titoloSel    = document.getElementById('titolo');
    const fieldAltro   = document.getElementById('fieldTitoloAltro');

    titoloSel.addEventListener('change', function () {
        fieldAltro.style.display = this.value === 'altro' ? '' : 'none';
    });
})();
</script>
