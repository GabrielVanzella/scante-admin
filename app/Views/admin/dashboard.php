<?php if ($flash): ?>
<div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : ($flash['type'] === 'warning' ? 'warning' : 'danger') ?> alert-dismissible fade show">
  <?= $flash['message'] ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="d-flex align-items-center justify-content-between mb-4">
  <h4 class="fw-bold mb-0">Dashboard</h4>
  <a href="<?= APP_URL ?>/admin/licencas/gerar" class="btn btn-accent"><i class="bi bi-plus-lg me-1"></i>Gerar Licença</a>
</div>

<!-- Estatísticas -->
<div class="row g-3 mb-4">
  <div class="col-md-2 col-6">
    <div class="card stat-card text-center">
      <div class="value text-success"><?= $stats['ativas'] ?? 0 ?></div>
      <div class="label">Ativas</div>
    </div>
  </div>
  <div class="col-md-2 col-6">
    <div class="card stat-card text-center">
      <div class="value text-warning"><?= $stats['trial'] ?? 0 ?></div>
      <div class="label">Trial</div>
    </div>
  </div>
  <div class="col-md-2 col-6">
    <div class="card stat-card text-center">
      <div class="value text-danger"><?= $stats['expiradas'] ?? 0 ?></div>
      <div class="label">Expiradas</div>
    </div>
  </div>
  <div class="col-md-2 col-6">
    <div class="card stat-card text-center">
      <div class="value"><?= $stats['vitalicias'] ?? 0 ?></div>
      <div class="label">Vitalícias</div>
    </div>
  </div>
  <div class="col-md-2 col-6">
    <div class="card stat-card text-center">
      <div class="value text-danger"><?= $stats['expirando_7d'] ?? 0 ?></div>
      <div class="label">Expirando (7d)</div>
    </div>
  </div>
  <div class="col-md-2 col-6">
    <div class="card stat-card text-center">
      <div class="value text-primary"><?= $empresas ?></div>
      <div class="label">Empresas</div>
    </div>
  </div>
</div>

<!-- Licenças recentes -->
<div class="card">
  <div class="card-body">
    <h6 class="fw-bold mb-3">Licenças Recentes</h6>
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-light">
          <tr><th>Chave</th><th>Empresa</th><th>Tipo</th><th>Status</th><th>Dispositivo</th><th>Expira</th><th></th></tr>
        </thead>
        <tbody>
        <?php foreach ($recentes as $l): ?>
          <tr>
            <td><code><?= $l['chave'] ?></code></td>
            <td><?= htmlspecialchars($l['empresa_nome'] ?? '—') ?></td>
            <td><?= ucfirst($l['tipo']) ?></td>
            <td><span class="badge badge-<?= $l['status'] ?>"><?= ucfirst($l['status']) ?></span></td>
            <td><?= $l['device_id'] ? '<i class="bi bi-phone-fill text-success"></i> Vinculado' : '<span class="text-muted">Livre</span>' ?></td>
            <td><?= $l['expira_em'] ? date('d/m/Y', strtotime($l['expira_em'])) : '—' ?></td>
            <td><a href="<?= APP_URL ?>/admin/licencas/<?= $l['id'] ?>" class="btn btn-sm btn-outline-secondary">Ver</a></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
