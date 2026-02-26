<?php
use App\Core\Auth;
use App\Core\View;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><i class="bi bi-people-fill me-2 text-primary"></i>Utenti</h1>
    <a href="<?= View::url('utenti/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nuovo Utente
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Nome</th>
                        <th>Cognome</th>
                        <th>Email</th>
                        <th>Ruolo</th>
                        <th class="text-center">Attivo</th>
                        <th>Ultimo Accesso</th>
                        <th class="text-end">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($utenti)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                            Nessun utente presente.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($utenti as $u): ?>
                    <?php
                        $ruolo = $u['ruolo'] ?? '';
                        $ruoloBadge = match($ruolo) {
                            'admin'     => 'bg-danger',
                            'operatore' => 'bg-secondary',
                            default     => 'bg-light text-dark',
                        };
                        $ruoloLabel = match($ruolo) {
                            'admin'     => 'Admin',
                            'operatore' => 'Operatore',
                            default     => View::e($ruolo),
                        };
                        $attivo       = !empty($u['attivo']);
                        $currentUser  = Auth::userId() === (int)($u['id'] ?? 0);
                    ?>
                    <tr>
                        <td><?= View::e($u['nome'] ?? '') ?></td>
                        <td class="fw-semibold"><?= View::e($u['cognome'] ?? '') ?></td>
                        <td>
                            <a href="mailto:<?= View::e($u['email'] ?? '') ?>" class="text-decoration-none small">
                                <?= View::e($u['email'] ?? '') ?>
                            </a>
                        </td>
                        <td>
                            <span class="badge <?= $ruoloBadge ?>">
                                <?= $ruoloLabel ?>
                            </span>
                            <?php if ($currentUser): ?>
                            <span class="badge bg-light text-primary border ms-1 small">
                                <i class="bi bi-person-fill me-1"></i>Tu
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if ($attivo): ?>
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>S&igrave;</span>
                            <?php else: ?>
                                <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>No</span>
                            <?php endif; ?>
                        </td>
                        <td class="small text-muted">
                            <?php if (!empty($u['ultimo_accesso'])): ?>
                                <?= View::e(date('d/m/Y H:i', strtotime($u['ultimo_accesso']))) ?>
                            <?php else: ?>
                                <span class="fst-italic">Mai</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <a href="<?= View::url('utenti/' . (int)$u['id'] . '/edit') ?>"
                               class="btn btn-sm btn-outline-primary" title="Modifica">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <?php if (!$currentUser): ?>
                            <button type="button" class="btn btn-sm btn-outline-danger ms-1"
                                    data-bs-toggle="modal" data-bs-target="#modalElimina"
                                    data-id="<?= (int)$u['id'] ?>"
                                    data-nome="<?= View::e(($u['nome'] ?? '') . ' ' . ($u['cognome'] ?? '')) ?>"
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
    <?php if (!empty($utenti)): ?>
    <div class="card-footer text-muted small">
        <?= count($utenti) ?> utente/i
    </div>
    <?php endif; ?>
</div>

<!-- Modale conferma eliminazione -->
<div class="modal fade" id="modalElimina" tabindex="-1" aria-labelledby="modalEliminaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalEliminaLabel">
                    <i class="bi bi-trash me-2"></i>Elimina Utente
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Sei sicuro di voler eliminare l'utente <strong id="modalNomeUtente"></strong>?
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
        document.getElementById('modalNomeUtente').textContent = nome.trim();
        document.getElementById('formElimina').action = '<?= View::url('utenti/') ?>' + id + '/delete';
    });
})();
</script>
