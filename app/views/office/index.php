<?php
$cfg  = require __DIR__ . '/../../config/config.php';
$base = $cfg['app']['base_url'];
$csrf = $csrf ?? ($_SESSION['csrf'] ?? '');
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3><i class="bi bi-buildings me-2"></i>Oficinas</h3>
  <!-- Botón que abre el modal de creación -->
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#officeModal">
    <i class="bi bi-plus-lg me-1"></i> Nueva Oficina
  </button>
</div>

<div class="card p-3 shadow-sm">
  <div class="table-responsive">
    <table class="table table-striped table-hover datatable">
      <thead>
        <tr>
          <th>#</th>
          <th>Nombre</th>
          <th>Descripción</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($oficinas as $i => $row): ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td><?= htmlspecialchars($row['nombre']) ?></td>
          <td><?= htmlspecialchars($row['descripcion']) ?></td>
          <td>
            <?php if ($row['estado']): ?>
              <span class="badge bg-success">ACTIVO</span>
            <?php else: ?>
              <span class="badge bg-danger">INACTIVO</span>
            <?php endif; ?>
          </td>
          <td class="d-flex gap-1">
            <!-- (Opcional) Acciones futuras -->
            <a class="btn btn-sm btn-outline-primary"
               href="<?= $base ?>/?controller=office&action=edit&id=<?= $row['id'] ?>"
               title="Editar">
              <i class="bi bi-pencil"></i>
            </a>
            <a class="btn btn-sm btn-outline-secondary"
               href="<?= $base ?>/?controller=office&action=show&id=<?= $row['id'] ?>"
               title="Detalle">
              <i class="bi bi-eye"></i>
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- ===========================
     MODAL: NUEVA OFICINA
=========================== -->
<div class="modal fade" id="officeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-scrollable">
    <div class="modal-content" style="background:#0f1833; color:#e5e7eb;">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-buildings me-2"></i>Nueva Oficina</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <form method="post" action="<?= $base ?>/?controller=office&action=create">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nombre*</label>
            <input type="text" name="nombre" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control" rows="3" placeholder="Ej: Área de TI, almacén secundario, etc."></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Estado</label>
            <select name="estado" class="form-select">
              <option value="1">Activo</option>
              <option value="0">Inactivo</option>
            </select>
          </div>
          <div class="form-text">
            Al guardar, la oficina se registrará y volverás al listado.
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-primary">
            <i class="bi bi-save2 me-1"></i> Guardar
          </button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </form>

    </div>
  </div>
</div>
