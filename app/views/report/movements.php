<?php
/**
 * Reporte imprimible de movimientos por rango de fechas.
 * Usa parámetros GET ?from=YYYY-mm-dd HH:ii:ss&to=YYYY-mm-dd HH:ii:ss
 */
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Reporte de Movimientos</title>
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
  <h2>Reporte de Movimientos</h2>
  <p>Rango: <?= htmlspecialchars($from) ?> — <?= htmlspecialchars($to) ?> | Generado: <?= date('Y-m-d H:i') ?></p>
  <table>
    <thead>
      <tr>
        <th>Fecha</th><th>Tipo</th><th>Cant.</th><th>Código</th><th>Ítem</th><th>Oficina</th><th>Usuario</th><th>Motivo</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $r): ?>
      <tr>
        <td><?= htmlspecialchars($r['created_at']) ?></td>
        <td><?= htmlspecialchars($r['tipo']) ?></td>
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
</body>
</html>
