<?php
use App\Core\View;

$isNew  = empty($tariffa['id']);
$title  = $isNew ? 'Nuova Tariffa' : 'Modifica Tariffa: ' . ($tariffa['codice'] ?? '');
$action = $isNew ? View::url('tariffe/store') : View::url('tariffe/' . (int)($tariffa['id']) . '/update');

$sezioni = ['A.1', 'A.2', 'A.3', 'A.4', 'B', 'C.1'];

$tipi = [
    'fisso'            => 'Fisso',
    'minimo'           => 'Minimo',
    'aprev'            => 'A Preventivo',
    'tabella_cedolini' => 'Tabella Cedolini',
    'formula'          => 'Formula',
];

$frequenze = [
    'una_tantum'    => 'Una Tantum',
    'mensile'       => 'Mensile',
    'trimestrale'   => 'Trimestrale',
    'semestrale'    => 'Semestrale',
    'annuale'       => 'Annuale',
    'a_prestazione' => 'A Prestazione',
];

$v = function (string $key) use ($tariffa): string {
    return View::e($tariffa[$key] ?? '');
};
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="bi bi-<?= $isNew ? 'plus-circle' : 'pencil-square' ?> me-2 text-primary"></i>
        <?= View::e($title) ?>
    </h1>
    <a href="<?= View::url('tariffe') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Torna al Listino
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="<?= $action ?>" method="POST" novalidate>
            <?php if (!$isNew): ?>
            <input type="hidden" name="_method" value="PUT">
            <?php endif; ?>

            <div class="row g-3">

                <!-- Codice -->
                <div class="col-md-3">
                    <label for="codice" class="form-label">Codice <span class="text-danger">*</span></label>
                    <input type="text" id="codice" name="codice" class="form-control font-monospace"
                           value="<?= $v('codice') ?>"
                           <?= !$isNew ? 'readonly' : 'required' ?>
                           maxlength="20" placeholder="es. A1.01">
                    <?php if (!$isNew): ?>
                    <div class="form-text text-muted">Il codice non è modificabile.</div>
                    <?php endif; ?>
                </div>

                <!-- Sezione -->
                <div class="col-md-2">
                    <label for="sezione" class="form-label">Sezione <span class="text-danger">*</span></label>
                    <select id="sezione" name="sezione" class="form-select" required>
                        <option value="">-- Seleziona --</option>
                        <?php foreach ($sezioni as $s): ?>
                        <option value="<?= View::e($s) ?>" <?= ($tariffa['sezione'] ?? '') === $s ? 'selected' : '' ?>>
                            <?= View::e($s) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Categoria -->
                <div class="col-md-7">
                    <label for="categoria" class="form-label">Categoria</label>
                    <input type="text" id="categoria" name="categoria" class="form-control"
                           value="<?= $v('categoria') ?>" maxlength="100"
                           placeholder="es. Contabilità ordinaria">
                </div>

                <!-- Descrizione -->
                <div class="col-12">
                    <label for="descrizione" class="form-label">Descrizione <span class="text-danger">*</span></label>
                    <textarea id="descrizione" name="descrizione" class="form-control" rows="3"
                              required placeholder="Descrizione della tariffa..."><?= $v('descrizione') ?></textarea>
                </div>

                <!-- Tipo -->
                <div class="col-md-3">
                    <label for="tipo" class="form-label">Tipo <span class="text-danger">*</span></label>
                    <select id="tipo" name="tipo" class="form-select" required>
                        <option value="">-- Seleziona tipo --</option>
                        <?php foreach ($tipi as $k => $label): ?>
                        <option value="<?= View::e($k) ?>" <?= ($tariffa['tipo'] ?? '') === $k ? 'selected' : '' ?>>
                            <?= View::e($label) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Importo Min -->
                <div class="col-md-2" id="fieldImportoMin">
                    <label for="importo_min" class="form-label">Importo Min (&euro;)</label>
                    <input type="number" id="importo_min" name="importo_min" class="form-control"
                           value="<?= $v('importo_min') ?>" step="0.01" min="0"
                           placeholder="0.00">
                </div>

                <!-- Importo Max -->
                <div class="col-md-2" id="fieldImportoMax">
                    <label for="importo_max" class="form-label">Importo Max (&euro;)</label>
                    <input type="number" id="importo_max" name="importo_max" class="form-control"
                           value="<?= $v('importo_max') ?>" step="0.01" min="0"
                           placeholder="0.00">
                </div>

                <!-- Frequenza -->
                <div class="col-md-3">
                    <label for="frequenza" class="form-label">Frequenza</label>
                    <select id="frequenza" name="frequenza" class="form-select">
                        <option value="">-- Seleziona --</option>
                        <?php foreach ($frequenze as $k => $label): ?>
                        <option value="<?= View::e($k) ?>" <?= ($tariffa['frequenza'] ?? '') === $k ? 'selected' : '' ?>>
                            <?= View::e($label) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Unità -->
                <div class="col-md-2">
                    <label for="unita" class="form-label">Unit&agrave;</label>
                    <input type="text" id="unita" name="unita" class="form-control"
                           value="<?= $v('unita') ?>" maxlength="50"
                           placeholder="es. cedolino">
                </div>

                <!-- Ordine -->
                <div class="col-md-2">
                    <label for="ordine" class="form-label">Ordine</label>
                    <input type="number" id="ordine" name="ordine" class="form-control"
                           value="<?= $v('ordine') ?>" min="0" step="1"
                           placeholder="0">
                </div>

                <!-- Note -->
                <div class="col-12">
                    <label for="note" class="form-label">Note</label>
                    <textarea id="note" name="note" class="form-control" rows="2"
                              placeholder="Note aggiuntive..."><?= $v('note') ?></textarea>
                </div>

                <!-- Attivo -->
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch"
                               id="attivo" name="attivo" value="1"
                               <?= !empty($tariffa['attivo']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="attivo">
                            Tariffa attiva (visibile nel listino)
                        </label>
                    </div>
                </div>

            </div><!-- /.row -->

            <hr class="my-4">

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-floppy me-1"></i>Salva
                </button>
                <a href="<?= View::url('tariffe') ?>" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-x-lg me-1"></i>Annulla
                </a>
            </div>

        </form>
    </div>
</div>

<script>
(function () {
    const tipoSel       = document.getElementById('tipo');
    const fieldMin      = document.getElementById('fieldImportoMin');
    const fieldMax      = document.getElementById('fieldImportoMax');

    function toggleImporti() {
        const hide = tipoSel.value === 'aprev';
        fieldMin.style.opacity = hide ? '0.4' : '1';
        fieldMax.style.opacity = hide ? '0.4' : '1';
        fieldMin.querySelector('input').disabled = hide;
        fieldMax.querySelector('input').disabled = hide;
    }

    tipoSel.addEventListener('change', toggleImporti);
    toggleImporti();
})();
</script>
