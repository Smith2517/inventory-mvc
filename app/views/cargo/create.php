<?php
$cfg = require __DIR__ . '/../../config/config.php';
$base = $cfg['app']['base_url'];
$csrf = $csrf ?? ($_SESSION['csrf'] ?? '');
$form_data = $form_data ?? [];
$errors = $_SESSION['flash_errors'] ?? [];
$isPartial = $isPartial ?? false;
unset($_SESSION['flash_errors']);
?>

<?php if (!$isPartial): ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3><i class="bi bi-card-checklist me-2"></i>Nuevo Cargo/Alquiler</h3>
  <a href="<?= $base ?>/?controller=cargo&action=index" class="btn btn-secondary">
    <i class="bi bi-arrow-left me-1"></i> Volver
  </a>
</div>
<?php endif; ?>

<div class="container-fluid">
  <div class="card">
    <div class="card-body">
      <form method="post" class="cargo-create-form" data-redirect-url="<?= $base ?>/?controller=cargo&action=index">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
        
        <div class="row g-3">
          <div class="col-md-12">
            <label class="form-label">Buscar Ítem de Inventario* <span class="text-danger">*</span></label>
            <input type="text" id="search-inventario" class="form-control" placeholder="Escriba para buscar ítem por código o nombre..." autocomplete="off">
            <div id="suggestions-container" class="suggestions-dropdown position-absolute bg-white border border-secondary rounded shadow-sm mt-1 w-100 overflow-auto" 
                 style="max-height: 200px; z-index: 1000; display: none;"></div>
          </div>
          
          <div class="col-md-6">
            <label class="form-label">Ítem Seleccionado</label>
            <div class="input-group">
              <input type="hidden" name="inventario_id" id="inventario-id" required>
              <input type="text" id="inventario-display" class="form-control" placeholder="Seleccione un ítem" readonly>
              <button class="btn btn-outline-secondary" type="button" onclick="clearSelectedItem()">
                <i class="bi bi-x-lg"></i>
              </button>
            </div>
            <div id="item-details" class="mt-2" style="display: none;">
              <small class="text-muted">
                <div><strong>Código:</strong> <span id="item-codigo"></span></div>
                <div><strong>Nombre:</strong> <span id="item-nombre"></span></div>
                <div><strong>Stock disponible:</strong> <span id="item-stock" class="fw-bold"></span></div>
                <div><strong>Oficina actual:</strong> <span id="item-oficina"></span></div>
              </small>
            </div>
            <?php if (isset($errors['inventario_id'])): ?>
              <div class="text-danger small mt-1"><?= $errors['inventario_id'] ?></div>
            <?php endif; ?>
          </div>
          
          <div class="col-md-6">
            <label class="form-label">Cantidad a Prestar* <span class="text-danger">*</span></label>
            <input type="number" name="cantidad" id="cantidad-input" min="1" class="form-control" 
                   placeholder="Ingrese cantidad a prestar" required disabled>
            <div class="form-text" id="cantidad-info">Seleccione un ítem para habilitar</div>
            <?php if (isset($errors['cantidad'])): ?>
              <div class="text-danger small mt-1"><?= $errors['cantidad'] ?></div>
            <?php endif; ?>
          </div>
          
          <div class="col-md-6">
            <label class="form-label">Usuario que Recibe* <span class="text-danger">*</span></label>
            <input type="text" name="usuario_destino" class="form-control" 
                   value="<?= htmlspecialchars($form_data['usuario_destino'] ?? '') ?>" 
                   placeholder="Nombre del usuario que recibe" required>
            <?php if (isset($errors['usuario_destino'])): ?>
              <div class="text-danger small mt-1"><?= $errors['usuario_destino'] ?></div>
            <?php endif; ?>
          </div>
          
          <div class="col-md-6">
            <label class="form-label">Oficina de Destino</label>
            <input type="text" name="oficina_destino" class="form-control" 
                   value="<?= htmlspecialchars($form_data['oficina_destino'] ?? '') ?>" 
                   placeholder="Nombre de la oficina de destino">
          </div>
          
          <div class="col-md-6">
            <label class="form-label">Fecha de Inicio</label>
            <input type="date" name="fecha_inicio" class="form-control" 
                   value="<?= htmlspecialchars($form_data['fecha_inicio'] ?? date('Y-m-d')) ?>" required>
          </div>
          
          <div class="col-12">
            <label class="form-label">Motivo/Descripción del Préstamo</label>
            <textarea name="motivo" class="form-control" rows="2" 
                      placeholder="Descripción del propósito del préstamo"><?= htmlspecialchars($form_data['motivo'] ?? '') ?></textarea>
          </div>
        </div>
        
        <div class="mt-4 d-flex flex-column flex-sm-row gap-2">
          <button type="submit" class="btn btn-primary flex-fill" id="submit-btn" disabled>
            <i class="bi bi-save2 me-1"></i> Registrar
          </button>
          <?php if (!$isPartial): ?>
          <a href="<?= $base ?>/?controller=cargo&action=index" class="btn btn-secondary flex-fill">
            <i class="bi bi-x me-1"></i> Cerrar
          </a>
          <?php else: ?>
          <button type="button" class="btn btn-secondary flex-fill" data-bs-dismiss="modal">
            <i class="bi bi-x me-1"></i> Cerrar
          </button>
          <?php endif; ?>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
.suggestions-dropdown {
  border: 1px solid #ced4da;
  border-radius: 0.375rem;
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}
.suggestions-item {
  padding: 0.5rem 1rem;
  cursor: pointer;
  border-bottom: 1px solid #e9ecef;
}
.suggestions-item:hover, .suggestions-item.highlighted {
  background-color: #f8f9fa;
}
.suggestions-item:last-child {
  border-bottom: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('search-inventario');
  const suggestionsContainer = document.getElementById('suggestions-container');
  const inventarioIdInput = document.getElementById('inventario-id');
  const inventarioDisplay = document.getElementById('inventario-display');
  const cantidadInput = document.getElementById('cantidad-input');
  const cantidadInfo = document.getElementById('cantidad-info');
  const itemDetails = document.getElementById('item-details');
  const submitBtn = document.getElementById('submit-btn');
  
  let searchTimeout;
  let selectedItem = null;
  let currentIndex = -1;
  let suggestions = [];
  
  // Función para limpiar selección
  window.clearSelectedItem = function() {
    inventarioIdInput.value = '';
    inventarioDisplay.value = '';
    cantidadInput.value = '';
    cantidadInput.disabled = true;
    cantidadInfo.textContent = 'Seleccione un ítem para habilitar';
    itemDetails.style.display = 'none';
    submitBtn.disabled = true;
    selectedItem = null;
  };
  
  // Busqueda de items
  searchInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const query = this.value.trim();
    
    if (query.length < 2) {
      suggestionsContainer.style.display = 'none';
      return;
    }
    
    searchTimeout = setTimeout(() => {
      fetch(`<?= $base ?>/?controller=cargo&action=searchInventory&term=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
          suggestions = data;
          showSuggestions(data);
        })
        .catch(error => {
          console.error('Error en la búsqueda:', error);
          showNotification('error', 'Error al buscar inventario');
        });
    }, 300);
  });
  
  // Mostrar sugerencias
  function showSuggestions(items) {
    suggestionsContainer.innerHTML = '';
    
    if (items.length === 0) {
      suggestionsContainer.style.display = 'none';
      return;
    }
    
    items.forEach((item, index) => {
      const div = document.createElement('div');
      div.className = 'suggestions-item';
      div.innerHTML = `
        <div><strong>${item.codigo}</strong> - ${item.nombre}</div>
        <small class="text-muted">Stock: ${item.cantidad} | Oficina: ${item.oficina_nombre || 'No asignada'}</small>
      `;
      div.addEventListener('click', () => selectItem(item));
      suggestionsContainer.appendChild(div);
    });
    
    suggestionsContainer.style.display = 'block';
    currentIndex = -1;
  }
  
  // Seleccionar item
  function selectItem(item) {
    selectedItem = item;
    inventarioIdInput.value = item.id;
    inventarioDisplay.value = `${item.codigo} - ${item.nombre}`;
    
    // Mostrar detalles del item
    document.getElementById('item-codigo').textContent = item.codigo;
    document.getElementById('item-nombre').textContent = item.nombre;
    document.getElementById('item-stock').textContent = item.cantidad;
    document.getElementById('item-stock').className = item.cantidad > 0 ? 'text-success fw-bold' : 'text-danger fw-bold';
    document.getElementById('item-oficina').textContent = item.oficina_nombre || 'No asignada';
    itemDetails.style.display = 'block';
    
    // Habilitar campo de cantidad
    cantidadInput.disabled = false;
    cantidadInput.max = item.cantidad;
    cantidadInput.min = 1;
    cantidadInfo.textContent = `Máximo disponible: ${item.cantidad} unidades`;
    
    // Mostrar botón de submit
    submitBtn.disabled = false;
    
    // Ocultar sugerencias
    suggestionsContainer.style.display = 'none';
  }
  
  // Navegación con teclado
  searchInput.addEventListener('keydown', function(e) {
    if (e.key === 'ArrowDown') {
      e.preventDefault();
      currentIndex = Math.min(currentIndex + 1, suggestions.length - 1);
      updateHighlight();
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      currentIndex = Math.max(currentIndex - 1, 0);
      updateHighlight();
    } else if (e.key === 'Enter') {
      e.preventDefault();
      if (currentIndex >= 0 && suggestions[currentIndex]) {
        selectItem(suggestions[currentIndex]);
      }
    } else if (e.key === 'Escape') {
      suggestionsContainer.style.display = 'none';
      currentIndex = -1;
    }
  });
  
  function updateHighlight() {
    const items = suggestionsContainer.querySelectorAll('.suggestions-item');
    items.forEach((item, index) => {
      if (index === currentIndex) {
        item.classList.add('highlighted');
      } else {
        item.classList.remove('highlighted');
      }
    });
  }
  
  // Ocultar sugerencias cuando se pierde el foco
  searchInput.addEventListener('blur', function() {
    setTimeout(() => {
      suggestionsContainer.style.display = 'none';
      currentIndex = -1;
    }, 200);
  });
  
  searchInput.addEventListener('focus', function() {
    if (suggestions.length > 0) {
      suggestionsContainer.style.display = 'block';
    }
  });
  
  // Form submit handler
  const form = document.querySelector('.cargo-create-form');
  if (form) {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      
      if (!selectedItem) {
        showNotification('warning', 'Por favor seleccione un ítem de inventario');
        return;
      }
      
      const cantidad = parseInt(cantidadInput.value);
      if (cantidad <= 0 || cantidad > selectedItem.cantidad) {
        showNotification('warning', `La cantidad debe estar entre 1 y ${selectedItem.cantidad}`);
        return;
      }
      
      const usuarioDestino = form.querySelector('input[name="usuario_destino"]').value.trim();
      if (!usuarioDestino) {
        showNotification('warning', 'Por favor ingrese el nombre del usuario que recibe');
        return;
      }
      
      const formData = new FormData(form);
      const formDataObj = {};
      for (let [key, value] of formData.entries()) {
        formDataObj[key] = value;
      }
      
      fetch(form.getAttribute('action') || window.location.href, {
        method: 'POST',
        body: new URLSearchParams(formDataObj),
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        }
      })
      .then(response => {
        if (response.headers.get('Content-Type') && response.headers.get('Content-Type').includes('application/json')) {
          return response.json();
        } else {
          return response.text().then(text => {
            throw new Error(text);
          });
        }
      })
      .then(data => {
        if (data.ok) {
          if (typeof window.bootstrap !== 'undefined') {
            // Cerrar modal si estamos en uno
            const modalElement = form.closest('.modal');
            if (modalElement) {
              const modal = bootstrap.Modal.getInstance(modalElement);
              if (modal) {
                modal.hide();
              }
            }
          }
          showNotification('success', data.message || 'Cargo registrado exitosamente');
          
          // Recargar la página principal
          if (data.redirect) {
            window.location.href = data.redirect;
          } else {
            window.location.reload();
          }
        } else {
          showNotification('error', 'Error: ' + (data.error || 'No se pudo registrar el cargo'));
        }
      })
      .catch(error => {
        showNotification('error', 'Error de red: ' + error.message);
      });
    });
  }
});
</script>