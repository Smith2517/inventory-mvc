<?php
$base = (require __DIR__ . '/../../config/config.php')['app']['base_url'];
$cfg = require __DIR__ . '/../../config/config.php';
$company = $cfg['company']['name'] ?? 'Mi Entidad';
$logo = $cfg['company']['logo'] ?? null;
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Etiqueta - <?= htmlspecialchars($item['codigo']) ?></title>
  <style>
    @page { size: 70mm 40mm; margin: 5mm; }
    @media print { html, body { width: 70mm; height: 40mm; } }
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 1.5mm;
      width: 70mm; height: 50mm;
      box-sizing: border-box;
      background:#ffffff;
    }
    .label {
      border: 1px solid #1f6feb;
      border-radius: 1mm;
      padding: 1mm 1.5mm;
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      background:#fff;
    }
    .header { display: flex; align-items: center; gap: 2mm; border-bottom: 1px dashed #bcd; padding-bottom: 1mm; }
    .header .logo {
      width: 10mm; height: 10mm; background:#1f6feb11; border:1px solid #1f6feb55; border-radius:1mm;
      display:flex; align-items:center; justify-content:center; font-size:6pt; color:#1f6feb;
    }
    .header .company { font-size: 12pt; font-weight: bold; line-height: 1; max-width: 38mm; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .code { font-size: 11pt; font-weight: 700; line-height:2; margin-top: 1mm; }
    .name { font-size: 10pt; line-height: 1.15; max-height: 18pt; overflow: hidden; }
    .meta { font-size: 9pt; color: #222; display:flex; gap:1.5mm; flex-wrap:wrap; }
    .footer { display:flex; justify-content:space-between; align-items:center; font-size:8pt; color:#333; border-top:1px dashed #bcd; padding-top:1mm; }
    /* Botón Guardar PNG */
    .actions { position: fixed; right: 10px; bottom: 10px; }
    .btn { padding: 6px 10px; background: #0d6efd; color: #fff; border: none; border-radius: 4px; cursor:pointer; }
    @media print { .actions { display:none; } } /* por si alguien abre diálogo de impresión */
  </style>
</head>
<body>
  <div id="etiqueta" class="label">
    <div class="header">
      <?php if ($logo): ?>
        <img class="logo" src="<?= htmlspecialchars($logo) ?>" alt="logo" crossorigin="anonymous">
      <?php else: ?>
        <div class="logo">LOGO</div>
      <?php endif; ?>
      <div class="company"><?= htmlspecialchars($company) ?></div>
    </div>

    <div>
      <div class="code"><?= htmlspecialchars($item['codigo']) ?></div>
      <div class="name"><?= htmlspecialchars($item['nombre']) ?></div>
      <div class="meta">
     
        <div><b>Estante:</b> <?= htmlspecialchars($item['estante'] ?? '-') ?></div>
        <div><b>Serie:</b> <?= htmlspecialchars($item['serie']) ?></div>
        <div><b>Estado:</b> <?= htmlspecialchars($item['estado_2']) ?></div>
        <div><b>Cant. Inicial:</b> <?= (int)$item['cantidad'] ?></div>
      </div>
    </div>

    <div class="footer">
      <div>Imp: <?= date('Y-m-d') ?></div>
      <div>Of: <?= ($item['nameOficina']) ?></div>
    </div>
  </div>

