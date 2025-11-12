<?php
// base URL para CSS opcional
$base = (require __DIR__ . '/../../config/config.php')['app']['base_url'];
$isPartial = !empty($_GET['partial']); // si viene desde el modal/iframe
?>

<?php if ($isPartial): ?>
  <!-- Cuando se carga dentro del iframe SIN layout, incluimos los estilos aquí -->
  <link rel="stylesheet" href="<?= $base ?>/assets/css/show.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<?php endif; ?>

<div class="container-fluid detail-view">
  <div class="row">
    <div class="col-12">
      <!-- Header con información principal -->
      <div class="card mb-4">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0">
              <i class="bi bi-box-seam me-2"></i>
              Detalle del Ítem: <?= htmlspecialchars($item['codigo']) ?>
            </h4>
            <span class="badge <?= $item['estado'] === 'AGOTADO' ? 'bg-danger' : 'bg-success' ?> fs-6">
              <?= $item['estado'] ?>
            </span>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <h5 class="text-primary"><?= htmlspecialchars($item['nombre']) ?></h5>
              <div class="row mt-3">
                <div class="col-sm-6">
                  <p class="mb-1"><strong>Cantidad Actual:</strong></p>
                  <p class="fs-4 text-success fw-bold"><?= (int)$item['cantidad'] ?></p>
                </div>
                <div class="col-sm-6">
                  <p class="mb-1"><strong>Condición:</strong></p>
                  <p>
                    <span class="badge <?= $item['estado_2'] === 'BUENO' ? 'bg-success' : 
                          ($item['estado_2'] === 'MALO' ? 'bg-warning text-dark' : 
                          ($item['estado_2'] === 'REGULAR' ? 'bg-primary' : 
                          ($item['estado_2'] === 'BAJA' ? 'bg-danger' : 'bg-info'))) ?>">
                      <?= htmlspecialchars($item['estado_2']) ?>
                    </span>
                  </p>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <div class="col-sm-6">
                  <p class="mb-1"><strong>Serie:</strong></p>
                  <p class="text-muted"><?= htmlspecialchars($item['serie']) ?></p>
                </div>
                <div class="col-sm-6">
                  <p class="mb-1"><strong>Oficina:</strong></p>
                  <p class="text-muted"><?= htmlspecialchars($item['nameOficina'] ?: '-') ?></p>
                </div>
                <div class="col-sm-6">
                  <p class="mb-1"><strong>Estante:</strong></p>
                  <p class="text-muted"><?= htmlspecialchars($item['estante'] ?? '-') ?></p>
                </div>
                
              </div>
            </div>
          </div>
          
          <?php if (!empty($item['descripcion'])): ?>
          <div class="row mt-3">
            <div class="col-12">
              <p class="mb-1"><strong>Descripción:</strong></p>
              <p class="text-muted"><?= htmlspecialchars($item['descripcion']) ?></p>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>
      
      <!-- Tabla de movimientos -->
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">
            <i class="bi bi-clock-history me-2"></i>
            Historial de Movimientos
          </h5>
          <span class="badge bg-secondary">Total: <?= count($movs) ?></span>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th width="15%">Fecha</th>
                  <th width="10%" class="text-center">Tipo</th>
                  <th width="10%" class="text-end">Cantidad</th>
                  <th width="20%">Oficina</th>
                  <th width="15%">Usuario</th>
                  <th width="30%">Motivo</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($movs)): ?>
                  <?php foreach ($movs as $m): ?>
                    <tr>
                      <td><?= htmlspecialchars($m['created_at']) ?></td>
                      <td class="text-center">
                        <span class="badge <?= $m['tipo'] === 'SALIDA' ? 'bg-danger' : 'bg-success' ?>">
                          <?= $m['tipo'] ?>
                        </span>
                      </td>
                      <td class="text-end">
                        <span class="fw-bold"><?= (int)$m['cantidad'] ?></span>
                      </td>
                      <td><?= htmlspecialchars($m['oficina'] ?? 'N/A') ?></td>
                      <td><?= htmlspecialchars($m['usuario'] ?? 'Sistema') ?></td>
                      <td class="text-truncate" title="<?= htmlspecialchars($m['motivo'] ?? '') ?>">
                        <?= htmlspecialchars($m['motivo'] ?? '') ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                      <i class="bi bi-inbox fs-1 mb-2"></i>
                      <p class="mb-0">No hay movimientos registrados para este ítem</p>
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>