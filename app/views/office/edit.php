<?php
$cfg = require __DIR__ . '/../../config/config.php';
$base = $cfg['app']['base_url'];
$csrf = $csrf ?? ($_SESSION['csrf'] ?? '');
$isPartial = !empty($_GET['partial']); // si viene desde modal/iframe

if (!$isPartial): ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3><i class="bi bi-pencil me-2"></i>Editar Oficina</h3>
  <a href="<?= $base ?>/?controller=office&action=index" class="btn btn-secondary">
    <i class="bi bi-arrow-left me-1"></i> Volver
  </a>
</div>
<?php endif; ?>

<div class="container-fluid">
  <div class="card">
    <div class="card-body">
      <form method="post" action="<?= $base ?>/?controller=office&action=edit&id=<?= $oficina['id'] ?>" 
            class="office-edit-form" 
            data-redirect-url="<?= $base ?>/?controller=office&action=index">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
        
        <div class="mb-3">
          <label class="form-label">Nombre*</label>
          <input type="text" name="nombre" class="form-control" 
                 value="<?= htmlspecialchars($oficina['nombre'] ?? '') ?>" required>
        </div>
        
        <div class="mb-3">
          <label class="form-label">Descripción</label>
          <textarea name="descripcion" class="form-control" rows="3" 
                    placeholder="Ej: Área de TI, almacén secundario, etc.">
            <?= htmlspecialchars($oficina['descripcion'] ?? '') ?>
          </textarea>
        </div>
        
        <div class="mb-3">
          <label class="form-label">Estado</label>
          <select name="estado" class="form-select">
            <option value="1" <?= ($oficina['estado'] == 1) ? 'selected' : '' ?>>Activo</option>
            <option value="0" <?= ($oficina['estado'] == 0) ? 'selected' : '' ?>>Inactivo</option>
          </select>
        </div>
        
        <?php if (!$isPartial): ?>
        <div class="d-flex gap-2">
          <button class="btn btn-primary">
            <i class="bi bi-save2 me-1"></i> Guardar Cambios
          </button>
          <a href="<?= $base ?>/?controller=office&action=index" class="btn btn-secondary">
            <i class="bi bi-x me-1"></i> Cancelar
          </a>
        </div>
        <?php endif; ?>
      </form>
    </div>
  </div>
</div>