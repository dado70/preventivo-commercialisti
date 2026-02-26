<?php
use App\Core\Auth;
use App\Core\View;

// Defaults sicuri per le variabili
$stats  = $stats  ?? ['totale' => 0, 'bozze' => 0, 'inviati' => 0, 'accettati' => 0, 'valore_accettati' => 0];
$recenti = $recenti ?? [];
$studio  = $studio  ?? [];
?>

<!-- Intestazione -->
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
    <div>
        <h1 class="h3 fw-bold mb-0">
            Benvenuto, <?= View::e(Auth::userName()) ?>!
        </h1>
        <p class="text-muted mb-0 small">
            <i class="bi bi-calendar3 me-1"></i>
            <?= (new DateTime())->format('l j F Y') ?>
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= View::url('preventivi/nuovo') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Nuovo Preventivo
        </a>
        <a href="<?= View::url('preventivi') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-list me-1"></i>Vedi tutti
        </a>
    </div>
</div>

<!-- Alert studio non configurato -->
<?php if (empty($studio['ragione_sociale'])): ?>
<div class="alert alert-info alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
    <i class="bi bi-building me-3 fs-5 flex-shrink-0"></i>
    <div>
        <strong>Studio non configurato.</strong>
        Completa i dati del tuo studio per poter generare preventivi completi.
        <a href="<?= View::url('studio') ?>" class="alert-link ms-1">
            Configura ora <i class="bi bi-arrow-right-short"></i>
        </a>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Schede statistiche -->
<div class="row g-3 mb-4">

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width: 52px; height: 52px;">
                    <i class="bi bi-file-text fs-4 text-secondary"></i>
                </div>
                <div>
                    <div class="h3 fw-bold mb-0"><?= View::e($stats['totale']) ?></div>
                    <div class="text-muted small">Totale Preventivi Anno</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 border-start border-warning border-4">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width: 52px; height: 52px;">
                    <i class="bi bi-hourglass fs-4 text-warning"></i>
                </div>
                <div>
                    <div class="h3 fw-bold mb-0"><?= View::e($stats['inviati']) ?></div>
                    <div class="text-muted small">In attesa di risposta</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 border-start border-success border-4">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width: 52px; height: 52px;">
                    <i class="bi bi-check-circle fs-4 text-success"></i>
                </div>
                <div>
                    <div class="h3 fw-bold mb-0"><?= View::e($stats['accettati']) ?></div>
                    <div class="text-muted small">Accettati</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 border-start border-primary border-4">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width: 52px; height: 52px;">
                    <i class="bi bi-currency-euro fs-4 text-primary"></i>
                </div>
                <div>
                    <div class="h3 fw-bold mb-0"><?= View::euro($stats['valore_accettati']) ?></div>
                    <div class="text-muted small">Valore accettato</div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Tabella preventivi recenti -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3">
        <h2 class="h6 fw-bold mb-0">
            <i class="bi bi-clock-history me-2 text-primary"></i>Preventivi recenti
        </h2>
        <a href="<?= View::url('preventivi') ?>" class="btn btn-sm btn-outline-primary">
            Vedi tutti <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>

    <div class="card-body p-0">
        <?php if (empty($recenti)): ?>
        <div class="text-center py-5 px-3">
            <i class="bi bi-file-earmark-text display-4 text-muted mb-3 d-block"></i>
            <h3 class="h6 text-muted fw-semibold">Nessun preventivo ancora.</h3>
            <p class="text-muted small mb-3">Inizia creando il tuo primo preventivo!</p>
            <a href="<?= View::url('preventivi/nuovo') ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Crea il primo preventivo
            </a>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 fw-semibold text-muted small text-uppercase">Numero</th>
                        <th class="fw-semibold text-muted small text-uppercase">Cliente</th>
                        <th class="fw-semibold text-muted small text-uppercase">Data</th>
                        <th class="fw-semibold text-muted small text-uppercase">Stato</th>
                        <th class="text-end fw-semibold text-muted small text-uppercase">Totale</th>
                        <th class="text-end pe-3 fw-semibold text-muted small text-uppercase">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($recenti as $p): ?>
                    <?php
                    $statoBadge = match ($p['stato'] ?? '') {
                        'bozza'    => ['secondary', 'Bozza'],
                        'inviato'  => ['info',      'Inviato'],
                        'accettato'=> ['success',   'Accettato'],
                        'rifiutato'=> ['danger',    'Rifiutato'],
                        'scaduto'  => ['warning',   'Scaduto'],
                        default    => ['secondary', View::e($p['stato'] ?? '')],
                    };
                    ?>
                    <tr>
                        <td class="ps-3">
                            <span class="fw-semibold text-dark"><?= View::e($p['numero'] ?? '') ?></span>
                        </td>
                        <td>
                            <span class="text-dark"><?= View::e($p['cliente'] ?? $p['cliente_nome'] ?? '&mdash;') ?></span>
                        </td>
                        <td>
                            <span class="text-muted small">
                                <?php
                                $data = $p['data'] ?? $p['created_at'] ?? null;
                                echo $data ? View::e((new DateTime($data))->format('d/m/Y')) : '&mdash;';
                                ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-<?= $statoBadge[0] ?> text-<?= $statoBadge[0] === 'warning' ? 'dark' : 'white' ?>">
                                <?= $statoBadge[1] ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <span class="fw-semibold"><?= View::euro($p['totale'] ?? 0) ?></span>
                        </td>
                        <td class="text-end pe-3">
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="<?= View::url('preventivi/' . View::e($p['id'])) ?>"
                                   class="btn btn-sm btn-outline-primary"
                                   title="Apri preventivo">
                                    <i class="bi bi-eye me-1"></i>Apri
                                </a>
                                <a href="<?= View::url('preventivi/' . View::e($p['id']) . '/modifica') ?>"
                                   class="btn btn-sm btn-outline-secondary"
                                   title="Modifica preventivo">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Azioni rapide in fondo -->
<?php if (!empty($recenti)): ?>
<div class="d-flex gap-2 justify-content-center mt-2">
    <a href="<?= View::url('preventivi/nuovo') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nuovo Preventivo
    </a>
    <a href="<?= View::url('preventivi') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-list me-1"></i>Vedi tutti i preventivi
    </a>
</div>
<?php endif; ?>
