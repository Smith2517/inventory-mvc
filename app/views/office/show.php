<?php
$cfg = require __DIR__ . '/../../config/config.php';
$base = $cfg['app']['base_url'];
$isPartial = !empty($_GET['partial']); // si viene desde modal/iframe

if (!$isPartial): ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3><i class="bi bi-eye me-2"></i>Detalle de Oficina</h3>
  <a href="<?= $base ?>/?controller=office&action=index" class="btn btn-secondary">
    <i class="bi bi-arrow-left me-1"></i> Volver
  </a>
</div>
<?php endif; ?>

<div class="container-fluid">
  <div class="card">
    <div class="card-header">
      <h4 class="card-title mb-0"><?= htmlspecialchars($oficina['nombre']) ?></h4>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <p><strong>Nombre:</strong></p>
          <p class="text-muted"><?= htmlspecialchars($oficina['nombre']) ?></p>
        </div>
        <div class="col-md-6">
          <p><strong>Estado:</strong></p>
          <p>
            <span class="badge <?= $oficina['estado'] ? 'bg-success' : 'bg-danger' ?>">
              <?= $oficina['estado'] ? 'ACTIVO' : 'INACTIVO' ?>
            </span>
          </p>
        </div>
      </div>
      <div class="row">
        <div class="col-12">
          <p><strong>Descripción:</strong></p>
          <p class="text-muted">
            <?= htmlspecialchars($oficina['descripcion']) ?: 'No tiene descripción' ?>
          </p>
        </div>
      </div>
    </div>
    <?php if (!$isPartial): ?>
    <div class="card-footer">
      <div class="d-flex gap-2">
        <a href="<?= $base ?>/?controller=office&action=edit&id=<?= $oficina['id'] ?>" class="btn btn-primary">
          <i class="bi bi-pencil me-1"></i> Editar
        </a>
        <a href="<?= $base ?>/?controller=office&action=index" class="btn btn-secondary">
          <i class="bi bi-arrow-left me-1"></i> Volver al listado
        </a>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>