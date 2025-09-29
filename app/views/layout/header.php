<?php
$cfg = require __DIR__ . '/../../config/config.php';
$base = $cfg['app']['base_url'];
$company = $cfg['company']['name'] ?? 'Mi Entidad';
$logo = $cfg['company']['logo'] ?? null;
$isLogged = !empty($_SESSION['user']);
?>
<!doctype html>
<html lang="es">
<script>window.__APP_BASE__ = "<?= (require __DIR__ . '/../../config/config.php')['app']['base_url'] ?>";</script>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inventario MVC</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
  <link href="<?= $base ?>/assets/css/styles.css" rel="stylesheet">
</head>

<body class="<?= $isLogged ? 'has-sidebar' : 'login-bg' ?>">
  <?php if ($isLogged): ?>
    <aside class="sidebar shadow-sm">
      <div class="sidebar-brand">
        <i class="bi bi-box-seam me-2"></i> Inventario
      </div>
      <div class="sidebar-company">
        <?php if ($logo): ?>
          <img src="<?= htmlspecialchars($logo) ?>" alt="logo">
        <?php endif; ?>
        <div class="company-name"><?= htmlspecialchars($company) ?></div>
      </div>
      <ul class="sidebar-menu">
        <li><a href="<?= $base ?>/?controller=inventory&action=index"><i class="bi bi-grid"></i> Inventario</a></li>
        
        <li><a href="<?= $base ?>/?controller=office&action=index"><i class="bi bi-buildings"></i> Oficinas</a></li>
        <li class="menu-title">Reportes</li>
        <li><a target="_blank" href="<?= $base ?>/?controller=report&action=inventory"><i class="bi bi-file-earmark-spreadsheet"></i> Inventario</a></li>
        <li><a target="_blank" href="<?= $base ?>/?controller=report&action=movements"><i class="bi bi-clock-history"></i> Movimientos</a></li>
      </ul>
      <div class="sidebar-footer">
        <div class="user">
          <i class="bi bi-person-circle me-2"></i>
          <span><?= htmlspecialchars($_SESSION['user']['nombre'] ?? $_SESSION['user']['username']) ?></span>
        </div>
        <a class="btn btn-sm btn-outline-light mt-2 w-100" href="<?= $base ?>/?controller=auth&action=logout">
          <i class="bi bi-box-arrow-right me-1"></i> Salir
        </a>
      </div>
    </aside>

    <div class="page">
      
      <main class="content container-fluid py-4">
      <?php else: ?>
        <main class="login-container container py-5">
        <?php endif; ?>