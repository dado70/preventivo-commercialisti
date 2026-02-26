<?php                                                                                                                                                       
  use App\Core\View;                                                                                                                                          
  use App\Core\Auth;                                                                                                                                        
  ?>
  <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="h4 mb-0"><i class="bi bi-file-text me-2"></i>Preventivi</h2>
      <a href="<?= View::url('preventivi/create') ?>" class="btn btn-primary">
          <i class="bi bi-plus-lg me-1"></i>Nuovo Preventivo
      </a>
  </div>
  <div class="card mb-3"><div class="card-body py-2">
  <form method="GET" action="<?= View::url('preventivi') ?>" class="row g-2 align-items-end">
  <div class="col-md-3"><label class="form-label small mb-1">Anno</label>
  <select name="anno" class="form-select form-select-sm">
  <?php for ($y = date('Y')+1; $y >= date('Y')-3; $y--): ?>
  <option value="<?= $y ?>" <?= ($filtri['anno']??date('Y'))==$y?'selected':'' ?>><?= $y ?></option>
  <?php endfor; ?></select></div>
  <div class="col-md-3"><label class="form-label small mb-1">Stato</label>
  <select name="stato" class="form-select form-select-sm">
  <option value="">Tutti</option>
  <option value="bozza" <?= ($filtri['stato']??'')==='bozza'?'selected':'' ?>>Bozza</option>
  <option value="inviato" <?= ($filtri['stato']??'')==='inviato'?'selected':'' ?>>Inviato</option>
  <option value="accettato" <?= ($filtri['stato']??'')==='accettato'?'selected':'' ?>>Accettato</option>
  <option value="rifiutato" <?= ($filtri['stato']??'')==='rifiutato'?'selected':'' ?>>Rifiutato</option>
  <option value="scaduto" <?= ($filtri['stato']??'')==='scaduto'?'selected':'' ?>>Scaduto</option>
  </select></div>
  <div class="col-md-4"><label class="form-label small mb-1">Cliente</label>
  <select name="cliente_id" class="form-select form-select-sm">
  <option value="">Tutti i clienti</option>
  <?php foreach($clienti as $c): ?>
  <option value="<?= $c['id'] ?>" <?= ($filtri['cliente_id']??0)==$c['id']?'selected':'' ?>><?= View::e($c['ragione_sociale']) ?></option>
  <?php endforeach; ?></select></div>
  <div class="col-md-2"><button type="submit" class="btn btn-secondary btn-sm w-100"><i class="bi bi-funnel me-1"></i>Filtra</button></div>
  </form></div></div>
  <div class="card"><div class="card-body p-0">
  <?php if(empty($preventivi)): ?>
  <div class="text-center py-5 text-muted">
  <i class="bi bi-file-text display-4 d-block mb-3 opacity-25"></i>
  <p class="mb-2">Nessun preventivo trovato.</p>
  <a href="<?= View::url('preventivi/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Crea il primo preventivo</a>
  </div>
  <?php else: ?>
  <div class="table-responsive"><table class="table table-hover align-middle mb-0">
  <thead class="table-light"><tr>
  <th>Numero</th><th>Cliente</th><th>Data</th><th>Scadenza</th><th>Stato</th>
  <th class="text-end">Imponibile</th><th class="text-end">Totale IVA incl.</th><th class="text-center">Azioni</th>
  </tr></thead><tbody>
  <?php foreach($preventivi as $p):
  $sb=match($p['stato']){'bozza'=>'secondary','inviato'=>'info','accettato'=>'success','rifiutato'=>'danger','scaduto'=>'warning',default=>'secondary'};
  $sl=match($p['stato']){'bozza'=>'Bozza','inviato'=>'Inviato','accettato'=>'Accettato','rifiutato'=>'Rifiutato','scaduto'=>'Scaduto',default=>$p['stato']};
  ?>
  <tr>
  <td><a href="<?= View::url('preventivi/'.$p['id']) ?>" class="fw-semibold text-decoration-none"><?= View::e($p['numero']) ?></a></td>
  <td><?= View::e($p['cliente_nome']??'') ?></td>
  <td><?= View::data($p['data_preventivo']) ?></td>
  <td><?= $p['data_scadenza'] ? View::data($p['data_scadenza']) : '<span class="text-muted">—</span>' ?></td>
  <td><span class="badge bg-<?= $sb ?>"><?= $sl ?></span></td>
  <td class="text-end"><?= View::euro($p['imponibile_scontato']??$p['imponibile']??0) ?></td>
  <td class="text-end fw-semibold"><?= View::euro($p['totale']??0) ?></td>
  <td class="text-center"><div class="btn-group btn-group-sm">
  <a href="<?= View::url('preventivi/'.$p['id']) ?>" class="btn btn-outline-primary" title="Visualizza"><i class="bi bi-eye"></i></a>
  <a href="<?= View::url('preventivi/'.$p['id'].'/edit') ?>" class="btn btn-outline-secondary" title="Modifica"><i class="bi bi-pencil"></i></a>
  <a href="<?= View::url('preventivi/'.$p['id'].'/pdf') ?>" class="btn btn-outline-danger" title="PDF" target="_blank"><i class="bi bi-file-pdf"></i></a>
  <a href="<?= View::url('preventivi/'.$p['id'].'/ods') ?>" class="btn btn-outline-success" title="ODS"><i class="bi bi-file-spreadsheet"></i></a>
  <?php if(Auth::isAdmin()): ?>
  <form method="POST" action="<?= View::url('preventivi/'.$p['id'].'/delete') ?>" class="d-inline" onsubmit="return confirm('Eliminare questo preventivo?')">
  <button type="submit" class="btn btn-outline-danger" title="Elimina"><i class="bi bi-trash"></i></button>
  </form>
  <?php endif; ?>
  </div></td></tr>
  <?php endforeach; ?>
  </tbody></table></div>
  <div class="px-3 py-2 border-top text-muted small"><?= count($preventivi) ?> preventivo/i trovato/i</div>
  <?php endif; ?>
  </div></div>
