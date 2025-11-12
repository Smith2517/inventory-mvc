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
            <button class="btn btn-sm btn-outline-primary"
               data-bs-toggle="modal"
               data-bs-target="#showOfficeModal"
               data-id="<?= $row['id'] ?>"
               title="Detalle">
              <i class="bi bi-eye"></i>
            </button>
            <button class="btn btn-sm btn-outline-secondary"
               data-bs-toggle="modal"
               data-bs-target="#editOfficeModal"
               data-id="<?= $row['id'] ?>"
               title="Editar">
              <i class="bi bi-pencil"></i>
            </button>
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
    <div class="modal-content">
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

<!-- ===========================
     MODAL: DETALLE OFICINA
=========================== -->
<div class="modal fade" id="showOfficeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-eye me-2"></i>Detalle de Oficina</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body" id="showOfficeContent">
        <div class="text-center p-4">
          <i class="bi bi-info-circle text-muted" style="font-size: 3rem;"></i>
          <p class="mt-2">Seleccione una oficina para ver los detalles</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- ===========================
     MODAL: EDITAR OFICINA
=========================== -->
<div class="modal fade" id="editOfficeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Editar Oficina</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body" id="editOfficeContent">
        <div class="text-center p-4">
          <i class="bi bi-info-circle text-muted" style="font-size: 3rem;"></i>
          <p class="mt-2">Seleccione una oficina para editar</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- JavaScript para cargar contenido dinámicamente -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  const BASE = "<?= $base ?>";
  
  // Manejar el modal de detalle
  const showModalEl = document.getElementById('showOfficeModal');
  if (showModalEl) {
    showModalEl.addEventListener('show.bs.modal', function(event) {
      const button = event.relatedTarget;
      const id = button.getAttribute('data-id');
      const content = document.getElementById('showOfficeContent');
      
      content.innerHTML = '<div class="text-center p-4"><i class="bi bi-arrow-repeat text-muted" style="font-size: 2rem; animation: spin 1s linear infinite;"></i></div>';
      
      fetch(`${BASE}/?controller=office&action=show&id=${id}&partial=1`)
        .then(response => response.text())
        .then(html => {
          content.innerHTML = html;
        })
        .catch(error => {
          content.innerHTML = '<div class="alert alert-danger">Error al cargar los datos</div>';
        });
    });
  }
  
  // Manejar el modal de edición
  const editModalEl = document.getElementById('editOfficeModal');
  if (editModalEl) {
    editModalEl.addEventListener('show.bs.modal', function(event) {
      const button = event.relatedTarget;
      const id = button.getAttribute('data-id');
      const content = document.getElementById('editOfficeContent');
      
      content.innerHTML = '<div class="text-center p-4"><i class="bi bi-arrow-repeat text-muted" style="font-size: 2rem; animation: spin 1s linear infinite;"></i></div>';
      
      fetch(`${BASE}/?controller=office&action=edit&id=${id}&partial=1`)
        .then(response => response.text())
        .then(html => {
          content.innerHTML = html;
          
          // Agregar funcionalidad de submit al formulario
          const form = content.querySelector('form');
          if (form) {
            form.addEventListener('submit', function(e) {
              e.preventDefault();
              
              const formData = new FormData(form);
              const formDataObj = {};
              for (let [key, value] of formData.entries()) {
                formDataObj[key] = value;
              }
              
              fetch(form.getAttribute('action'), {
                method: 'POST',
                body: new URLSearchParams(formDataObj),
                headers: {
                  'Content-Type': 'application/x-www-form-urlencoded',
                }
              })
              .then(response => response.json())
              .then(data => {
                if (data.ok) {
                  // Cerrar modal y recargar tabla
                  const modal = bootstrap.Modal.getInstance(editModalEl);
                  modal.hide();
                  
                  // Mostrar mensaje de éxito
                  alert('Oficina actualizada correctamente');
                  
                  // Recargar la página para reflejar cambios
                  location.reload();
                } else {
                  alert('Error: ' + (data.error || 'No se pudo actualizar la oficina'));
                }
              })
              .catch(error => {
                alert('Error de red: ' + error.message);
              });
            });
          }
        })
        .catch(error => {
          content.innerHTML = '<div class="alert alert-danger">Error al cargar los datos</div>';
        });
    });
  }
});
</script>