<?php
/**
 * Reporte imprimible de inventario (Guardar como PDF desde el navegador)
 */
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Reporte de Inventario</title>
  <style>
    @media print { .no-print { display:none; } }
    body { font-family: Arial, sans-serif; font-size: 12px; margin: 16px; }
    h2 { margin: 0 0 8px 0; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
    th { background: #f4f4f4; }
  </style>
</head>
<body>
  <div class="no-print" style="margin-bottom:10px;">
    <button onclick="window.print()">Imprimir / Guardar PDF</button>
  </div>
  <h2>Reporte de Inventario</h2>
  <p>Generado: <?= date('Y-m-d H:i') ?></p>
  <table>
    <thead>
      <tr>
        <th>#</th><th>Código</th><th>Nombre</th><th>Cant.</th><th>Estado</th><th>Oficina ID</th><th>Estante</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($items as $i => $row): ?>
      <tr>
        <td><?= $i+1 ?></td>
        <td><?= htmlspecialchars($row['codigo']) ?></td>
        <td><?= htmlspecialchars($row['nombre']) ?></td>
        <td><?= (int)$row['cantidad'] ?></td>
        <td><?= htmlspecialchars($row['estado']) ?></td>
        <td><?= (int)($row['oficina_id'] ?? 0) ?></td>
        <td><?= htmlspecialchars($row['estante'] ?? '-') ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</body>
</html>
