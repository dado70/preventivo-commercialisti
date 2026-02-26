<?php
use App\Core\Auth;
use App\Core\View;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-people me-2 text-primary"></i>Clienti</h1>
    <a href="<?= View::url('clienti/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nuovo Cliente
    </a>
</div>

<!-- Barra di ricerca -->
<div class="card shadow-sm mb-4">
    <div class="card-body pb-2">
        <div class="row g-2 align-items-end">
            <div class="col-md-5 col-lg-4">
                <label for="searchInput" class="form-label small text-muted mb-1">Cerca</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchInput" class="form-control"
                           placeholder="Ragione sociale, P.IVA, citt&agrave;...">
                </div>
            </div>
            <div class="col-md-3 col-lg-2">
                <label for="filterContab" class="form-label small text-muted mb-1">Contabilit&agrave;</label>
                <select id="filterContab" class="form-select">
                    <option value="">Tutte</option>
                    <option value="ordinaria">Ordinaria</option>
                    <option value="semplificata">Semplificata</option>
                    <option value="forfettario">Forfettario</option>
                    <option value="enti">Enti</option>
                </select>
            </div>
            <div class="col-md-2 col-lg-2">
                <label for="filterAttivo" class="form-label small text-muted mb-1">Stato</label>
                <select id="filterAttivo" class="form-select">
                    <option value="">Tutti</option>
                    <option value="1">Attivo</option>
                    <option value="0">Non attivo</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="button" id="btnReset" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle me-1"></i>Reset
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0" id="clientiTable">
                <thead class="table-dark">
                    <tr>
                        <th>Ragione Sociale</th>
                        <th>Tipo Contabilit&agrave;</th>
                        <th>P.IVA</th>
                        <th>Citt&agrave;</th>
                        <th>Telefono</th>
                        <th>Professionista</th>
                        <th class="text-center">Attivo</th>
                        <th class="text-end">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($clienti)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                            Nessun cliente presente.
                            <a href="<?= View::url('clienti/create') ?>">Aggiungi il primo cliente</a>.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($clienti as $c): ?>
                    <?php
                        $attivo = !empty($c['attivo']);
                        $contab = $c['tipo_contabilita'] ?? '';
                        $contabLabel = match($contab) {
                            'ordinaria'    => 'Ordinaria',
                            'semplificata' => 'Semplificata',
                            'forfettario'  => 'Forfettario',
                            'enti'         => 'Enti',
                            default        => View::e($contab),
                        };
                        $contabBadge = match($contab) {
                            'ordinaria'    => 'bg-primary',
                            'semplificata' => 'bg-info text-dark',
                            'forfettario'  => 'bg-warning text-dark',
                            'enti'         => 'bg-secondary',
                            default        => 'bg-light text-dark',
                        };
                    ?>
                    <tr
                        data-search="<?= strtolower(View::e($c['ragione_sociale'] ?? '') . ' ' . View::e($c['partita_iva'] ?? '') . ' ' . View::e($c['citta'] ?? '')) ?>"
                        data-contab="<?= View::e($contab) ?>"
                        data-attivo="<?= $attivo ? '1' : '0' ?>"
                    >
                        <td class="fw-semibold"><?= View::e($c['ragione_sociale'] ?? '') ?></td>
                        <td>
                            <span class="badge <?= $contabBadge ?>"><?= $contabLabel ?></span>
                        </td>
                        <td class="font-monospace small"><?= View::e($c['partita_iva'] ?? '') ?></td>
                        <td><?= View::e($c['citta'] ?? '') ?></td>
                        <td>
                            <?php if (!empty($c['telefono'])): ?>
                            <a href="tel:<?= View::e($c['telefono']) ?>" class="text-decoration-none small">
                                <?= View::e($c['telefono']) ?>
                            </a>
                            <?php else: ?>
                            <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($c['professionista_nome'])): ?>
                                <span class="small">
                                    <?= View::e(($c['professionista_titolo'] ?? '') . ' ' . ($c['professionista_nome'] ?? '') . ' ' . ($c['professionista_cognome'] ?? '')) ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if ($attivo): ?>
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>S&igrave;</span>
                            <?php else: ?>
                                <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>No</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <a href="<?= View::url('clienti/' . (int)$c['id'] . '/edit') ?>"
                               class="btn btn-sm btn-outline-primary" title="Modifica">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="<?= View::url('preventivi/create?cliente_id=' . (int)$c['id']) ?>"
                               class="btn btn-sm btn-outline-success ms-1" title="Nuovo Preventivo">
                                <i class="bi bi-file-plus"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer text-muted small">
        <span id="tableCount"><?= count($clienti) ?></span> cliente/i mostrato/i su <?= count($clienti) ?> totali
    </div>
</div>

<script>
(function () {
    const searchInput  = document.getElementById('searchInput');
    const filterContab = document.getElementById('filterContab');
    const filterAttivo = document.getElementById('filterAttivo');
    const btnReset     = document.getElementById('btnReset');
    const tableCount   = document.getElementById('tableCount');
    const rows         = document.querySelectorAll('#clientiTable tbody tr[data-search]');

    function applyFilters() {
        const search = searchInput.value.toLowerCase().trim();
        const contab = filterContab.value;
        const attivo = filterAttivo.value;
        let visible  = 0;

        rows.forEach(function (row) {
            const matchSearch = !search || row.dataset.search.includes(search);
            const matchContab = !contab || row.dataset.contab === contab;
            const matchAttivo = attivo === '' || row.dataset.attivo === attivo;
            const show = matchSearch && matchContab && matchAttivo;
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        tableCount.textContent = visible;
    }

    searchInput.addEventListener('input', applyFilters);
    filterContab.addEventListener('change', applyFilters);
    filterAttivo.addEventListener('change', applyFilters);

    btnReset.addEventListener('click', function () {
        searchInput.value   = '';
        filterContab.value  = '';
        filterAttivo.value  = '';
        applyFilters();
    });

    applyFilters();
})();
</script>
