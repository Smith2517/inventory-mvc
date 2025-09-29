<?php $base = (require __DIR__ . '/../../config/config.php')['app']['base_url']; $company=(require __DIR__.'/../../config/config.php')['company']['name'] ?? 'Mi Entidad'; ?>
<div class="row justify-content-center">
  <div class="col-md-5 col-lg-4">
    <div class="login-card shadow-lg">
      <div class="login-header text-center">
        <div class="login-badge">INVENTARIO</div>
        <h1 class="h5 mb-0"><?= htmlspecialchars($company) ?></h1>
        <p class="text-muted mb-0">Acceso al sistema</p>
      </div>
      <div class="login-body">
        <?php if (!empty($error)): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post" action="<?= $base ?>/?controller=auth&action=login">
          <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
          <div class="mb-3">
            <label class="form-label">Usuario</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-person"></i></span>
              <input type="text" name="username" class="form-control" required>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Contraseña</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
              <input type="password" name="password" class="form-control" required>
            </div>
          </div>
          <button class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-box-arrow-in-right"></i> Entrar
          </button>
        </form>
      </div>
      <div class="login-footer text-center text-muted">
        &copy; <?= date('Y') ?> — Sistema de Inventario
      </div>
    </div>
  </div>
</div>
