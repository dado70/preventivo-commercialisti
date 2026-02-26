<?php
use App\Core\View;

$isNew  = empty($pacchetto['id']);
$title  = $isNew ? 'Nuovo Pacchetto' : 'Modifica Pacchetto: ' . ($pacchetto['nome'] ?? '');
$action = $isNew ? View::url('pacchetti/store') : View::url('pacchetti/' . (int)($pacchetto['id']) . '/update');

// Tariffe selezionate indicizzate per id
$selIds = [];
foreach (($tariffe_sel ?? []) as $ts) {
    $selIds[(int)$ts['id']] = true;
}

// Raggruppa tutte le tariffe per sezione
$tariffeBySez = [];
foreach (($tariffe ?? []) as $t) {
    $sez = $t['sezione'] ?? 'Altro';
    $tariffeBySez[$sez][] = $t;
}
ksort($tariffeBySez);

$v = function (string $key) use ($pacchetto): string {
    return View::e($pacchetto[$key] ?? '');
};
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="bi bi-<?= $isNew ? 'plus-circle' : 'pencil-square' ?> me-2 text-primary"></i>
        <?= View::e($title) ?>
    </h1>
    <a href="<?= View::url('pacchetti') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Torna ai Pacchetti
    </a>
</div>

<form action="<?= $action ?>" method="POST" novalidate>
    <?php if (!$isNew): ?>
    <input type="hidden" name="_method" value="PUT">
    <?php endif; ?>

    <div class="row g-4">

        <!-- Dati pacchetto -->
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light fw-semibold">
                    <i class="bi bi-info-circle me-2 text-primary"></i>Dati Pacchetto
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" id="nome" name="nome" class="form-control"
                               value="<?= $v('nome') ?>" required maxlength="100"
                               placeholder="Nome del pacchetto">
                    </div>

                    <div class="mb-3">
                        <label for="descrizione" class="form-label">Descrizione</label>
                        <textarea id="descrizione" name="descrizione" class="form-control" rows="4"
                                  placeholder="Descrizione del pacchetto..."><?= $v('descrizione') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="prezzo_fisso" class="form-label">
                            Prezzo Fisso (&euro;)
                            <span class="text-muted small">(lascia vuoto per calcolo automatico)</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-currency-euro"></i></span>
                            <input type="number" id="prezzo_fisso" name="prezzo_fisso" class="form-control"
                                   value="<?= $v('prezzo_fisso') ?>" step="0.01" min="0"
                                   placeholder="Es. 500.00">
                        </div>
                        <div class="form-text">Se non specificato il prezzo sarà calcolato dalla somma delle tariffe.</div>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch"
                               id="attivo" name="attivo" value="1"
                               <?= !empty($pacchetto['attivo']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="attivo">Pacchetto attivo</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Selezione tariffe -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light fw-semibold d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-tags me-2 text-primary"></i>Tariffe Incluse</span>
                    <span class="badge bg-primary" id="countSel">
                        <?= count($selIds) ?> selezionate
                    </span>
                </div>
                <div class="card-body" style="max-height: 520px; overflow-y: auto;">

                    <div class="mb-3">
                        <input type="text" id="searchTariffe" class="form-control form-control-sm"
                               placeholder="Cerca tariffa per codice o descrizione...">
                    </div>

                    <?php if (empty($tariffeBySez)): ?>
                    <p class="text-muted text-center py-3">Nessuna tariffa disponibile.</p>
                    <?php else: ?>
                        <?php foreach ($tariffeBySez as $sez => $tList): ?>
                        <div class="mb-3 sez-group" data-sez="<?= View::e($sez) ?>">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge bg-secondary"><?= View::e($sez) ?></span>
                                <hr class="flex-grow-1 my-0">
                                <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-1 small btn-sel-all"
                                        data-sez="<?= View::e($sez) ?>" title="Seleziona tutti">
                                    <i class="bi bi-check-all"></i>
                                </button>
                            </div>
                            <div class="row g-1">
                                <?php foreach ($tList as $t): ?>
                                <div class="col-12 tariffa-item"
                                     data-search="<?= strtolower(View::e($t['codice'] ?? '') . ' ' . View::e($t['descrizione'] ?? '')) ?>">
                                    <div class="form-check border rounded px-3 py-2
                                        <?= isset($selIds[(int)$t['id']]) ? 'border-primary bg-primary bg-opacity-10' : 'border-light' ?>">
                                        <input class="form-check-input chk-tariffa" type="checkbox"
                                               name="tariffe[]"
                                               value="<?= (int)$t['id'] ?>"
                                               id="t<?= (int)$t['id'] ?>"
                                               <?= isset($selIds[(int)$t['id']]) ? 'checked' : '' ?>>
                                        <label class="form-check-label w-100" for="t<?= (int)$t['id'] ?>">
                                            <span class="font-monospace fw-semibold text-primary me-2">
                                                <?= View::e($t['codice'] ?? '') ?>
                                            </span>
                                            <span class="text-dark"><?= View::e($t['descrizione'] ?? '') ?></span>
                                            <?php if (!empty($t['importo_min'])): ?>
                                            <span class="text-muted small ms-2">
                                                da <?= View::euro($t['importo_min']) ?>
                                            </span>
                                            <?php endif; ?>
                                        </label>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </div>
            </div>
        </div>

    </div><!-- /.row -->

    <hr class="my-4">

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-floppy me-1"></i>Salva
        </button>
        <a href="<?= View::url('pacchetti') ?>" class="btn btn-outline-secondary px-4">
            <i class="bi bi-x-lg me-1"></i>Annulla
        </a>
    </div>

</form>

<script>
(function () {
    const searchInput = document.getElementById('searchTariffe');
    const items       = document.querySelectorAll('.tariffa-item');
    const countBadge  = document.getElementById('countSel');
    const checkboxes  = document.querySelectorAll('.chk-tariffa');

    function updateCount() {
        const checked = document.querySelectorAll('.chk-tariffa:checked').length;
        countBadge.textContent = checked + ' selezionate';
    }

    function filterTariffe() {
        const q = searchInput.value.toLowerCase().trim();
        items.forEach(function (item) {
            const match = !q || item.dataset.search.includes(q);
            item.style.display = match ? '' : 'none';
        });
        // Nascondi sezioni vuote
        document.querySelectorAll('.sez-group').forEach(function (grp) {
            const visible = grp.querySelectorAll('.tariffa-item:not([style*="none"])').length;
            grp.style.display = visible ? '' : 'none';
        });
    }

    // Highlight checkbox selezionata
    checkboxes.forEach(function (chk) {
        chk.addEventListener('change', function () {
            const wrap = this.closest('.form-check');
            if (this.checked) {
                wrap.classList.add('border-primary', 'bg-primary', 'bg-opacity-10');
                wrap.classList.remove('border-light');
            } else {
                wrap.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');
                wrap.classList.add('border-light');
            }
            updateCount();
        });
    });

    // Seleziona tutti per sezione
    document.querySelectorAll('.btn-sel-all').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const sez  = this.dataset.sez;
            const grp  = document.querySelector('.sez-group[data-sez="' + CSS.escape(sez) + '"]');
            const chks = grp.querySelectorAll('.chk-tariffa');
            const allChecked = Array.from(chks).every(function (c) { return c.checked; });
            chks.forEach(function (c) {
                c.checked = !allChecked;
                c.dispatchEvent(new Event('change'));
            });
        });
    });

    searchInput.addEventListener('input', filterTariffe);
    updateCount();
})();
</script>
