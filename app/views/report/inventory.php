<?php
/**
 * Reporte de inventario dentro del layout del sistema
 */
?>

<div class="report-container">
  <div class="report-header d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2 class="mb-1">Reporte de Inventario</h2>
      <p class="text-muted mb-0">Generado: <?= date('Y-m-d H:i') ?></p>
    </div>
    <button onclick="window.print()" class="btn btn-primary no-print">
      <i class="bi bi-printer me-1"></i> Imprimir / Guardar PDF
    </button>
  </div>

  <?php if (!empty($q)): ?>
    <div class="alert alert-info mb-4">
      <i class="bi bi-search me-1"></i> Búsqueda: <strong><?= htmlspecialchars($q) ?></strong>
    </div>
  <?php endif; ?>

  <div class="table-responsive card shadow-sm">
    <table class="table table-hover">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Código</th>
          <th>Nombre</th>
          <th>Cant.</th>
          <th>Estado</th>
          <th>Oficina</th>
          <th>Estante</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($items as $i => $row): ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td><?= htmlspecialchars($row['codigo']) ?></td>
          <td><?= htmlspecialchars($row['nombre']) ?></td>
          <td><?= (int)$row['cantidad'] ?></td>
          <td>
            <span class="badge <?= $row['cantidad'] > 0 ? 'bg-success' : 'bg-danger' ?>">
              <?= htmlspecialchars($row['estado']) ?>
            </span>
          </td>
          <td><?= htmlspecialchars($row['oficina'] ?? '-') ?></td>
          <td><?= htmlspecialchars($row['estante'] ?? '-') ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <?php if (empty($items)): ?>
    <div class="alert alert-info text-center mt-4">
      <i class="bi bi-info-circle me-2"></i> No se encontraron registros
    </div>
  <?php endif; ?>
</div>

<style>
  @media print {
    .no-print {
      display: none !important;
    }
    .report-container {
      margin: 0;
      padding: 20px;
    }
    .table {
      font-size: 11px;
    }
    .table th,
    .table td {
      padding: 4px !important;
    }
    .badge {
      font-size: 10px;
    }
  }
</style>
