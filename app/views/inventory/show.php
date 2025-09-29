<?php
// base URL para CSS opcional
$base = (require __DIR__ . '/../../config/config.php')['app']['base_url'];
$isPartial = !empty($_GET['partial']); // si viene desde el modal/iframe
?>

<?php if ($isPartial): ?>
  <!-- Cuando se carga dentro del iframe SIN layout, incluimos los estilos aquí -->
  <link rel="stylesheet" href="<?= $base ?>/assets/css/styles.css">
  <!-- Si usas Bootstrap Icons, descomenta: -->
  <!-- <link rel="stylesheet" href="<?= $base ?>/assets/icons/bootstrap-icons.css"> -->
  <!-- Tu CSS principal si aplica -->
  <!-- <link rel="stylesheet" href="<?= $base ?>/assets/css/custom.css"> -->
<?php endif; ?>

<div class="container-fluid">

  <!-- Encabezado -->
  <div class="d-flex align-items-center mb-3 border-bottom pb-2">
    <h4 class="me-auto mb-0">
      Detalle: <?= htmlspecialchars($item['codigo']) ?> - <?= htmlspecialchars($item['nombre']) ?>
    </h4>
    <button class="btn btn-sm btn-secondary"
      data-bs-toggle="modal"
      data-bs-target="#labelModal"
      data-id="<?= $item['id'] ?>">
      Etiqueta
    </button>
  </div>

  <div class="row g-3">

    <!-- Columna izquierda -->
    <div class="col-md-5">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <p><strong>Cantidad:</strong> <?= (int)$item['cantidad'] ?></p>
          <p>
            <strong>Estado:</strong>
            <span class="badge <?= $item['estado'] === 'AGOTADO' ? 'bg-danger' : 'bg-success' ?>">
              <?= $item['estado'] ?>
            </span>
          </p>
          <p><strong>Oficina:</strong> <?= $item['nameOficina'] ?: '-' ?></p>
          <p><strong>Estante:</strong> <?= htmlspecialchars($item['estante'] ?? '-') ?></p>
        </div>
      </div>
    </div>

    <!-- Columna derecha -->
    <div class="col-md-7">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <h6 class="mb-3">Movimientos</h6>
          <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>Fecha</th>
                  <th>Tipo</th>
                  <th>Cant.</th>
                  <th>Oficina</th>
                  <th>Usuario</th>
                  <th>Motivo</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($movs)): ?>
                  <?php foreach ($movs as $m): ?>
                    <tr>
                      <td><?= htmlspecialchars($m['created_at']) ?></td>
                      <td>
                        <span class="badge <?= $m['tipo'] === 'SALIDA' ? 'bg-warning text-dark' : 'bg-primary' ?>">
                          <?= $m['tipo'] ?>
                        </span>
                      </td>
                      <td><?= (int)$m['cantidad'] ?></td>
                      <td><?= htmlspecialchars($m['oficina'] ?? '-') ?></td>
                      <td><?= htmlspecialchars($m['usuario'] ?? '-') ?></td>
                      <td><?= nl2br(htmlspecialchars($m['motivo'] ?? '')) ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="6" class="text-center text-muted">No hay movimientos registrados</td>
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


