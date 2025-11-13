<?php
$cfg = require __DIR__ . '/../../config/config.php';
$base = $cfg['app']['base_url'];
$csrf = $csrf ?? ($_SESSION['csrf'] ?? '');
$isPartial = !empty($_GET['partial']);
?>

<?php if (!$isPartial): ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3><i class="bi bi-clock-history me-2"></i>Cargos Activos</h3>
  <div class="d-flex gap-2">
    <a href="<?= $base ?>/?controller=cargo&action=index" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i> Todos
    </a>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCargoModal">
      <i class="bi bi-plus-lg me-1"></i> Nuevo Cargo
    </button>
  </div>
</div>
<?php endif; ?>

<div class="container-fluid">
  <div class="card p-3 shadow-sm">
    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th class="d-none d-md-table-cell">#</th>
            <th>Ítem</th>
            <th class="text-center">Cant</th>
            <th class="d-none d-lg-table-cell">Usuario</th>
            <th class="d-none d-xl-table-cell">Oficina</th>
            <th class="d-none d-lg-table-cell">Fecha Ini</th>
            <th class="d-none d-xl-table-cell">Motivo</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($cargos as $i => $cargo): ?>
          <tr>
            <td class="d-none d-md-table-cell"><?= $i + 1 ?></td>
            <td>
              <div><strong><?= htmlspecialchars($cargo['codigo']) ?></strong></div>
              <small class="text-muted d-block d-lg-none"><?= htmlspecialchars($cargo['nombre']) ?></small>
              <small class="text-muted d-none d-lg-block"><?= htmlspecialchars($cargo['nombre']) ?></small>
            </td>
            <td class="text-center"><span class="badge bg-primary d-md-none"><?= (int)$cargo['cantidad'] ?></span><span class="d-none d-md-block"><?= (int)$cargo['cantidad'] ?></span></td>
            <td class="d-none d-lg-table-cell text-break"><?= htmlspecialchars($cargo['usuario_destino']) ?></td>
            <td class="d-none d-xl-table-cell text-break"><?= htmlspecialchars($cargo['oficina_destino'] ?? '-') ?></td>
            <td class="d-none d-lg-table-cell"><?= htmlspecialchars($cargo['fecha_inicio']) ?></td>
            <td class="d-none d-xl-table-cell text-break"><?= htmlspecialchars($cargo['motivo'] ?? '') ?></td>
            <td>
              <button class="btn btn-sm btn-success return-item-btn" 
                      data-id="<?= $cargo['id'] ?>" 
                      data-item="<?= htmlspecialchars($cargo['nombre']) ?>"
                      title="Devolver item">
                <i class="bi bi-arrow-return-left me-1 d-none d-sm-inline"></i><span class="d-inline d-sm-none">Dev</span>
              </button>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php if (empty($cargos)): ?>
  <div class="text-center p-5">
    <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
    <h5 class="mt-3">No hay cargos activos</h5>
    <p class="text-muted">Todos los items han sido devueltos</p>
    <?php if (!$isPartial): ?>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCargoModal">
      <i class="bi bi-plus-lg me-1"></i> Crear nuevo cargo
    </button>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Handler para botones de devolución
  const returnButtons = document.querySelectorAll('.return-item-btn');
  
  returnButtons.forEach(button => {
    button.addEventListener('click', function() {
      const itemId = this.getAttribute('data-id');
      const itemName = this.getAttribute('data-item');
      
      if (confirm(`¿Está seguro de devolver el ítem "${itemName}"?`)) {
        const formData = new FormData();
        formData.append('id', itemId);
        formData.append('csrf', '<?= $csrf ?>');
        
        // Determinar si estamos en modal o no
        const inModal = !!document.querySelector('.modal.show');
        
        fetch('<?= $base ?>/?controller=cargo&action=returnItem' + (inModal ? '&partial=1' : ''), {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.ok) {
            showNotification('success', data.message || 'Item devuelto exitosamente');
            
            // Recargar la tabla o cerrar modal
            if (inModal) {
              // Si es modal, cerrar y recargar el contenedor
              const modalElement = document.closest('.modal');
              if (modalElement) {
                const modalInstance = bootstrap.Modal.getInstance(modalElement);
                if (modalInstance) {
                  modalInstance.hide();
                }
              }
              window.location.reload();
            } else {
              window.location.reload();
            }
          } else {
            showNotification('error', 'Error: ' + (data.error || 'No se pudo devolver el ítem'));
          }
        })
        .catch(error => {
          showNotification('error', 'Error de red: ' + error.message);
        });
      }
    });
  });
});
</script>