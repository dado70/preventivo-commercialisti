<?php
use App\Core\Auth;
use App\Core\View;

$tipoLabels = [
    'fisso'             => ['label' => 'Fisso',          'badge' => 'bg-primary'],
    'minimo'            => ['label' => 'Minimo',         'badge' => 'bg-warning text-dark'],
    'aprev'             => ['label' => 'A Preventivo',   'badge' => 'bg-secondary'],
    'tabella_cedolini'  => ['label' => 'Tab. Cedolini',  'badge' => 'bg-info text-dark'],
    'formula'           => ['label' => 'Formula',        'badge' => 'bg-success'],
];

$freqLabels = [
    'una_tantum'    => 'Una Tantum',
    'mensile'       => 'Mensile',
    'trimestrale'   => 'Trimestrale',
    'semestrale'    => 'Semestrale',
    'annuale'       => 'Annuale',
    'a_prestazione' => 'A Prestazione',
];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-tags me-2 text-primary"></i>Listino Tariffe</h1>
    <?php if (Auth::isAdmin()): ?>
    <a href="<?= View::url('tariffe/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nuova Tariffa
    </a>
    <?php endif; ?>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body pb-2">
        <div class="row g-2 align-items-end">
            <div class="col-md-6 col-lg-4">
                <label for="searchInput" class="form-label small text-muted mb-1">Cerca</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchInput" class="form-control"
                           placeholder="Codice, categoria, descrizione...">
                </div>
            </div>
            <div class="col-md-3 col-lg-2">
                <label for="filterTipo" class="form-label small text-muted mb-1">Tipo</label>
                <select id="filterTipo" class="form-select">
                    <option value="">Tutti i tipi</option>
                    <?php foreach ($tipoLabels as $k => $v): ?>
                    <option value="<?= View::e($k) ?>"><?= View::e($v['label']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 col-lg-2">
                <label for="filterSezione" class="form-label small text-muted mb-1">Sezione</label>
                <select id="filterSezione" class="form-select">
                    <option value="">Tutte</option>
                    <?php
                    $sezioni = array_unique(array_column($tariffe, 'sezione'));
                    sort($sezioni);
                    foreach ($sezioni as $s): ?>
                    <option value="<?= View::e($s) ?>"><?= View::e($s) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 col-lg-2">
                <label for="filterAttiva" class="form-label small text-muted mb-1">Stato</label>
                <select id="filterAttiva" class="form-select">
                    <option value="">Tutti</option>
                    <option value="1">Attiva</option>
                    <option value="0">Non attiva</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="button" id="btnResetFilter" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle me-1"></i>Reset
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0" id="tariffaTable">
                <thead class="table-dark">
                    <tr>
                        <th>Codice</th>
                        <th>Sezione</th>
                        <th>Categoria</th>
                        <th>Descrizione</th>
                        <th>Tipo</th>
                        <th>Importo Min</th>
                        <th>Importo Max</th>
                        <th>Frequenza</th>
                        <th class="text-center">Attiva</th>
                        <th class="text-end">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($tariffe)): ?>
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                            Nessuna tariffa presente.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($tariffe as $t): ?>
                    <?php
                        $tipo  = $t['tipo'] ?? '';
                        $badge = $tipoLabels[$tipo]['badge'] ?? 'bg-secondary';
                        $tipoL = $tipoLabels[$tipo]['label'] ?? View::e($tipo);
                        $freq  = $freqLabels[$t['frequenza'] ?? ''] ?? View::e($t['frequenza'] ?? '');
                        $attiva = !empty($t['attivo']);
                    ?>
                    <tr
                        data-search="<?= strtolower(View::e($t['codice'] ?? '') . ' ' . View::e($t['categoria'] ?? '') . ' ' . View::e($t['descrizione'] ?? '')) ?>"
                        data-tipo="<?= View::e($tipo) ?>"
                        data-sezione="<?= View::e($t['sezione'] ?? '') ?>"
                        data-attiva="<?= $attiva ? '1' : '0' ?>"
                    >
                        <td class="font-monospace fw-semibold"><?= View::e($t['codice'] ?? '') ?></td>
                        <td><?= View::e($t['sezione'] ?? '') ?></td>
                        <td><?= View::e($t['categoria'] ?? '') ?></td>
                        <td><?= View::e($t['descrizione'] ?? '') ?></td>
                        <td>
                            <span class="badge <?= $badge ?>">
                                <?= $tipoL ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($tipo === 'aprev'): ?>
                                <span class="text-muted fst-italic">a preventivo</span>
                            <?php else: ?>
                                <?= isset($t['importo_min']) && $t['importo_min'] !== null ? View::euro($t['importo_min']) : '<span class="text-muted">—</span>' ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($tipo === 'aprev'): ?>
                                <span class="text-muted fst-italic">a preventivo</span>
                            <?php else: ?>
                                <?= isset($t['importo_max']) && $t['importo_max'] !== null ? View::euro($t['importo_max']) : '<span class="text-muted">—</span>' ?>
                            <?php endif; ?>
                        </td>
                        <td><?= View::e($freq) ?></td>
                        <td class="text-center">
                            <?php if ($attiva): ?>
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Attiva</span>
                            <?php else: ?>
                                <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Non attiva</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <?php if (Auth::isAdmin()): ?>
                            <a href="<?= View::url('tariffe/' . (int)$t['id'] . '/edit') ?>"
                               class="btn btn-sm btn-outline-primary" title="Modifica">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer text-muted small">
        <span id="tableCount"><?= count($tariffe) ?></span> tariffa/e mostrata/e su <?= count($tariffe) ?> totali
    </div>
</div>

<script>
(function () {
    const searchInput   = document.getElementById('searchInput');
    const filterTipo    = document.getElementById('filterTipo');
    const filterSezione = document.getElementById('filterSezione');
    const filterAttiva  = document.getElementById('filterAttiva');
    const btnReset      = document.getElementById('btnResetFilter');
    const tableCount    = document.getElementById('tableCount');
    const rows          = document.querySelectorAll('#tariffaTable tbody tr[data-search]');
    const total         = rows.length;

    function applyFilters() {
        const search  = searchInput.value.toLowerCase().trim();
        const tipo    = filterTipo.value;
        const sezione = filterSezione.value;
        const attiva  = filterAttiva.value;
        let visible   = 0;

        rows.forEach(function (row) {
            const matchSearch  = !search  || row.dataset.search.includes(search);
            const matchTipo    = !tipo    || row.dataset.tipo === tipo;
            const matchSezione = !sezione || row.dataset.sezione === sezione;
            const matchAttiva  = attiva === '' || row.dataset.attiva === attiva;

            const show = matchSearch && matchTipo && matchSezione && matchAttiva;
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        tableCount.textContent = visible;
    }

    searchInput.addEventListener('input', applyFilters);
    filterTipo.addEventListener('change', applyFilters);
    filterSezione.addEventListener('change', applyFilters);
    filterAttiva.addEventListener('change', applyFilters);

    btnReset.addEventListener('click', function () {
        searchInput.value    = '';
        filterTipo.value     = '';
        filterSezione.value  = '';
        filterAttiva.value   = '';
        applyFilters();
    });

    applyFilters();
})();
</script>
