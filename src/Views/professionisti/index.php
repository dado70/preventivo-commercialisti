<?php
use App\Core\Auth;
use App\Core\View;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-person-badge me-2 text-primary"></i>Professionisti</h1>
    <?php if (Auth::isAdmin()): ?>
    <a href="<?= View::url('professionisti/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nuovo Professionista
    </a>
    <?php endif; ?>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Titolo</th>
                        <th>Nome</th>
                        <th>Cognome</th>
                        <th>Qualifica</th>
                        <th>Ordine</th>
                        <th>N. Iscrizione</th>
                        <th>Email</th>
                        <th class="text-center">Attivo</th>
                        <th class="text-end">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($professionisti)): ?>
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                            Nessun professionista presente.
                            <?php if (Auth::isAdmin()): ?>
                            <a href="<?= View::url('professionisti/create') ?>">Aggiungine uno</a>.
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($professionisti as $p): ?>
                    <tr>
                        <td><?= View::e($p['titolo'] ?? '') ?></td>
                        <td><?= View::e($p['nome'] ?? '') ?></td>
                        <td class="fw-semibold"><?= View::e($p['cognome'] ?? '') ?></td>
                        <td>
                            <?php if (!empty($p['qualifica'])): ?>
                            <span class="badge bg-info text-dark"><?= View::e($p['qualifica']) ?></span>
                            <?php else: ?>
                            <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="small"><?= View::e($p['ordine_professionale'] ?? '') ?></span>
                        </td>
                        <td class="font-monospace small"><?= View::e($p['n_iscrizione_ordine'] ?? '') ?></td>
                        <td>
                            <?php if (!empty($p['email'])): ?>
                            <a href="mailto:<?= View::e($p['email']) ?>" class="text-decoration-none small">
                                <?= View::e($p['email']) ?>
                            </a>
                            <?php else: ?>
                            <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if (!empty($p['attivo'])): ?>
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>S&igrave;</span>
                            <?php else: ?>
                                <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>No</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <a href="<?= View::url('professionisti/' . (int)$p['id'] . '/edit') ?>"
                               class="btn btn-sm btn-outline-primary" title="Modifica">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <?php if (Auth::isAdmin()): ?>
                            <button type="button" class="btn btn-sm btn-outline-danger ms-1"
                                    data-bs-toggle="modal" data-bs-target="#modalElimina"
                                    data-id="<?= (int)$p['id'] ?>"
                                    data-nome="<?= View::e(($p['titolo'] ?? '') . ' ' . ($p['nome'] ?? '') . ' ' . ($p['cognome'] ?? '')) ?>"
                                    title="Elimina">
                                <i class="bi bi-trash"></i>
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if (!empty($professionisti)): ?>
    <div class="card-footer text-muted small">
        <?= count($professionisti) ?> professionista/i
    </div>
    <?php endif; ?>
</div>

<!-- Modale conferma eliminazione -->
<?php if (Auth::isAdmin()): ?>
<div class="modal fade" id="modalElimina" tabindex="-1" aria-labelledby="modalEliminaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalEliminaLabel">
                    <i class="bi bi-trash me-2"></i>Elimina Professionista
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Sei sicuro di voler eliminare il professionista <strong id="modalNomeProf"></strong>?
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
        document.getElementById('modalNomeProf').textContent = nome.trim();
        document.getElementById('formElimina').action = '<?= View::url('professionisti/') ?>' + id + '/delete';
    });
})();
</script>
<?php endif; ?>
