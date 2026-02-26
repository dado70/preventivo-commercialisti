<?php
use App\Core\Auth;
use App\Core\View;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-box me-2 text-primary"></i>Pacchetti di Tariffe</h1>
    <?php if (Auth::isAdmin()): ?>
    <a href="<?= View::url('pacchetti/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nuovo Pacchetto
    </a>
    <?php endif; ?>
</div>

<?php if (empty($pacchetti)): ?>
<div class="card shadow-sm">
    <div class="card-body text-center py-5 text-muted">
        <i class="bi bi-inbox fs-1 d-block mb-3"></i>
        <p class="mb-0">Nessun pacchetto presente. <?php if (Auth::isAdmin()): ?><a href="<?= View::url('pacchetti/create') ?>">Crea il primo pacchetto</a>.<?php endif; ?></p>
    </div>
</div>
<?php else: ?>
<div class="row g-4">
    <?php foreach ($pacchetti as $p): ?>
    <div class="col-md-6 col-xl-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">
                    <i class="bi bi-box me-2"></i><?= View::e($p['nome'] ?? '') ?>
                </span>
                <?php if (!empty($p['attivo'])): ?>
                    <span class="badge bg-light text-success"><i class="bi bi-check-circle"></i> Attivo</span>
                <?php else: ?>
                    <span class="badge bg-light text-danger"><i class="bi bi-x-circle"></i> Non attivo</span>
                <?php endif; ?>
            </div>
            <div class="card-body d-flex flex-column">

                <?php if (!empty($p['descrizione'])): ?>
                <p class="text-muted small mb-3"><?= View::e($p['descrizione']) ?></p>
                <?php endif; ?>

                <!-- Tariffe incluse -->
                <div class="mb-3">
                    <p class="small fw-semibold text-uppercase text-muted mb-2">
                        <i class="bi bi-tags me-1"></i>Tariffe incluse
                        <span class="badge bg-secondary ms-1"><?= count($p['tariffe'] ?? []) ?></span>
                    </p>
                    <div class="d-flex flex-wrap gap-1">
                        <?php if (!empty($p['tariffe'])): ?>
                            <?php foreach ($p['tariffe'] as $t): ?>
                            <span class="badge bg-light text-dark border" title="<?= View::e($t['descrizione'] ?? '') ?>">
                                <?= View::e($t['codice'] ?? '') ?>
                            </span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="text-muted small fst-italic">Nessuna tariffa associata</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Prezzo -->
                <div class="mt-auto pt-3 border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small text-muted">Prezzo</span>
                        <?php if (isset($p['prezzo_fisso']) && $p['prezzo_fisso'] !== null && $p['prezzo_fisso'] !== ''): ?>
                            <span class="fw-bold text-success fs-5"><?= View::euro($p['prezzo_fisso']) ?></span>
                        <?php else: ?>
                            <span class="fst-italic text-secondary small">Calcolato dalle tariffe</span>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
            <div class="card-footer bg-transparent d-flex gap-2 justify-content-end">
                <?php if (Auth::isAdmin()): ?>
                <a href="<?= View::url('pacchetti/' . (int)$p['id'] . '/edit') ?>"
                   class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-pencil me-1"></i>Modifica
                </a>
                <button type="button" class="btn btn-sm btn-outline-danger"
                        data-bs-toggle="modal" data-bs-target="#modalElimina"
                        data-id="<?= (int)$p['id'] ?>"
                        data-nome="<?= View::e($p['nome'] ?? '') ?>">
                    <i class="bi bi-trash me-1"></i>Elimina
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Modale conferma eliminazione -->
<?php if (Auth::isAdmin()): ?>
<div class="modal fade" id="modalElimina" tabindex="-1" aria-labelledby="modalEliminaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalEliminaLabel">
                    <i class="bi bi-trash me-2"></i>Elimina Pacchetto
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Sei sicuro di voler eliminare il pacchetto <strong id="modalNomePacchetto"></strong>?
                <p class="text-danger mt-2 mb-0 small">L'operazione non &egrave; reversibile.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <form id="formElimina" method="POST">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Elimina
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const modal = document.getElementById('modalElimina');
    modal.addEventListener('show.bs.modal', function (event) {
        const btn  = event.relatedTarget;
        const id   = btn.getAttribute('data-id');
        const nome = btn.getAttribute('data-nome');
        document.getElementById('modalNomePacchetto').textContent = nome;
        document.getElementById('formElimina').action = '<?= View::url('pacchetti/') ?>' + id + '/delete';
    });
})();
</script>
<?php endif; ?>
