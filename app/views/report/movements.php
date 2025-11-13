<?php
/**
 * Reporte de movimientos dentro del layout del sistema
 */
?>

<div class="report-container">
  <div class="report-header d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2 class="mb-1">Reporte de Movimientos</h2>
      <p class="text-muted mb-0">Rango: <?= htmlspecialchars($from) ?> — <?= htmlspecialchars($to) ?> | Generado: <?= date('Y-m-d H:i') ?></p>
    </div>
    <button onclick="window.print()" class="btn btn-primary no-print">
      <i class="bi bi-printer me-1"></i> Imprimir / Guardar PDF
    </button>
  </div>

  <div class="table-responsive card shadow-sm">
    <table class="table table-hover">
      <thead class="table-light">
        <tr>
          <th>Fecha</th>
          <th>Tipo</th>
          <th>Cant.</th>
          <th>Código</th>
          <th>Ítem</th>
          <th>Oficina</th>
          <th>Usuario</th>
          <th>Motivo</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
        <tr>
          <td><?= htmlspecialchars($r['created_at']) ?></td>
          <td>
            <span class="badge <?= $r['tipo'] === 'ENTRADA' ? 'bg-success' : 'bg-danger' ?>">
              <?= htmlspecialchars($r['tipo']) ?>
            </span>
          </td>
          <td><?= (int)$r['cantidad'] ?></td>
          <td><?= htmlspecialchars($r['codigo']) ?></td>
          <td><?= htmlspecialchars($r['item']) ?></td>
          <td><?= htmlspecialchars($r['oficina'] ?? '-') ?></td>
          <td><?= htmlspecialchars($r['usuario'] ?? '-') ?></td>
          <td><?= nl2br(htmlspecialchars($r['motivo'] ?? '')) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <?php if (empty($rows)): ?>
    <div class="alert alert-info text-center mt-4">
      <i class="bi bi-info-circle me-2"></i> No se encontraron movimientos en el rango seleccionado
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
