<?php $base = (require __DIR__ . '/../../config/config.php')['app']['base_url']; ?>
<div class="row">
  <div class="col-md-7">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Nueva Oficina</h5>
        <form method="post" action="<?= $base ?>/?controller=office&action=create">
          <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
          <div class="mb-2">
            <label class="form-label">Nombre*</label>
            <input type="text" name="nombre" class="form-control" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control"></textarea>
          </div>
          <div class="mb-2">
            <label class="form-label">Estado</label>
            <select name="estado" class="form-select">
              <option value="1">Activo</option>
              <option value="0">Inactivo</option>
            </select>
          </div>
          <div class="mt-2">
            <button class="btn btn-primary">Guardar</button>
            <a href="<?= $base ?>/?controller=office&action=index" class="btn btn-outline-secondary">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
