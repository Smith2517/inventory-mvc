<?php
$cfg = require __DIR__ . '/../../config/config.php';
$base = $cfg['app']['base_url'];
$csrf = $csrf ?? ($_SESSION['csrf'] ?? '');
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-2">
  <h3><i class="bi bi-card-checklist me-2"></i>Cargos/Alquileres</h3>
  <div class="d-flex flex-wrap gap-2 w-100 w-md-auto">
    <div class="position-relative flex-grow-1" style="max-width: 300px;">
      <input type="text" id="searchInput" class="form-control" placeholder="Buscar cargos...">
      <i class="bi bi-search position-absolute" style="right: 10px; top: 50%; transform: translateY(-50%); color: #6c757d;"></i>
    </div>
    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#activeCargosModal">
      <i class="bi bi-clock-history me-1"></i> Activos
    </button>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCargoModal">
      <i class="bi bi-plus-lg me-1"></i> Nuevo Cargo
    </button>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-body p-3">
    <div class="table-responsive">
      <table class="table table-striped table-hover datatable" id="cargoTable">
        <thead>
          <tr>
            <th>#</th>
            <th>Ítem</th>
            <th>Cant</th>
            <th>Usuario</th>
            <th>Oficina</th>
            <th>Fecha Ini</th>
            <th>Fecha Fin</th>
            <th>Estado</th>
            <th>Motivo</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($cargos as $i => $cargo): ?>
          <tr>
            <td class="text-nowrap"><?= $i + 1 ?></td>
            <td>
              <div><strong><?= htmlspecialchars($cargo['codigo']) ?></strong></div>
              <small class="text-muted"><?= htmlspecialchars($cargo['nombre']) ?></small>
            </td>
            <td class="text-center"><span class="badge bg-primary"><?= (int)$cargo['cantidad'] ?></span></td>
            <td><?= htmlspecialchars($cargo['usuario_destino']) ?></td>
            <td><?= htmlspecialchars($cargo['oficina_destino'] ?? '-') ?></td>
            <td><?= htmlspecialchars($cargo['fecha_inicio']) ?></td>
            <td><?= htmlspecialchars($cargo['fecha_fin'] ?? '-') ?></td>
            <td>
              <span class="badge <?= $cargo['estado'] === 'ACTIVO' ? 'bg-warning text-dark' : 'bg-success' ?>">
                <?= $cargo['estado'] ?>
              </span>
            </td>
            <td><?= htmlspecialchars($cargo['motivo'] ?? '') ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal para Nuevo Cargo -->
<div class="modal fade" id="createCargoModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-card-checklist me-2"></i>Nuevo Cargo/Alquiler</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body" id="createCargoContent">
        <div class="text-center p-4">
          <i class="bi bi-arrow-repeat text-muted" style="font-size: 2rem; animation: spin 1s linear infinite;"></i>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal para Cargos Activos -->
<div class="modal fade" id="activeCargosModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-clock-history me-2"></i>Cargos Activos</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body" id="activeCargosContent">
        <div class="text-center p-4">
          <i class="bi bi-arrow-repeat text-muted" style="font-size: 2rem; animation: spin 1s linear infinite;"></i>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const BASE = "<?= $base ?>";

  // Cargar contenido para modal de nuevo cargo
  const createModalEl = document.getElementById('createCargoModal');
  if (createModalEl) {
    createModalEl.addEventListener('show.bs.modal', function() {
      const content = document.getElementById('createCargoContent');
      content.innerHTML = '<div class="text-center p-4"><i class="bi bi-arrow-repeat text-muted" style="font-size: 2rem; animation: spin 1s linear infinite;"></i></div>';
      
      fetch(`${BASE}/?controller=cargo&action=create&partial=1`)
        .then(response => response.text())
        .then(html => {
          content.innerHTML = html;
          // Inicializar la funcionalidad de búsqueda después de cargar el contenido
          // Usar un pequeño retraso para asegurar que el DOM esté completamente cargado
          setTimeout(() => {
            initializeCargoForm();
          }, 100);
        })
        .catch(error => {
          console.error('Error al cargar el formulario:', error);
          content.innerHTML = '<div class="alert alert-danger">Error al cargar el formulario</div>';
        });
    });
  }

  // Cargar contenido para modal de cargos activos
  const activeModalEl = document.getElementById('activeCargosModal');
  if (activeModalEl) {
    activeModalEl.addEventListener('show.bs.modal', function() {
      const content = document.getElementById('activeCargosContent');
      content.innerHTML = '<div class="text-center p-4"><i class="bi bi-arrow-repeat text-muted" style="font-size: 2rem; animation: spin 1s linear infinite;"></i></div>';
      
      fetch(`${BASE}/?controller=cargo&action=active&partial=1`)
        .then(response => response.text())
        .then(html => {
          content.innerHTML = html;
        })
        .catch(error => {
          content.innerHTML = '<div class="alert alert-danger">Error al cargar los datos</div>';
        });
    });
  }
  
  // Búsqueda en tiempo real para la tabla de cargos - con manejo de posibles conflictos de DataTables
  const searchInput = document.getElementById('searchInput');
  const table = document.getElementById('cargoTable');
  
  if (searchInput && table) {
    // Usar la funcionalidad de búsqueda de DataTables si está disponible
    if (window.jQuery && typeof jQuery.fn.DataTable === "function") {
      // Asegurarse de que la tabla no esté ya inicializada
      if ($.fn.DataTable.isDataTable(table)) {
        // Si ya está inicializada, destruirla y volver a inicializarla
        $(table).DataTable().destroy();
      }
      
      const dataTable = $(table).DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        order: [],
        responsive: true, // Usar configuración básica de responsive
        language: {
          url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
        },
        dom: "frtip", // Solo búsqueda, resultados y paginación (sin botones de exportar)
        // Ajuste de columnas
        columnDefs: [
          { targets: '_all', className: 'responsive-nowrap' },
          { targets: [0], width: '1%' }, // Número
          { targets: [2], width: '60px', className: 'text-center' } // Cantidad
        ],
        // Mostrar un indicador de más información en móviles
        drawCallback: function() {
          // Asegurar que los estilos responsive se apliquen correctamente
          $(table).find('td').addClass('text-truncate');
        }
      });
      
      // Escuchar el input de búsqueda
      searchInput.addEventListener('keyup', function() {
        dataTable.search(this.value).draw();
      });
    } else {
      // Si no está disponible DataTables, usar filtro simple
      searchInput.addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = table.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
          const rowText = row.textContent.toLowerCase();
          if (rowText.includes(searchTerm)) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        });
      });
    }
  }
});

// Función para re-inicializar DataTable si es necesario en navegación
window.initializeCargoTable = function() {
  const table = document.getElementById('cargoTable');
  if (table && window.jQuery && typeof jQuery.fn.DataTable === "function") {
    if ($.fn.DataTable.isDataTable(table)) {
      $(table).DataTable().destroy();
    }
    
    $(table).DataTable({
      pageLength: 10,
      lengthMenu: [5, 10, 25, 50, 100],
      order: [],
      responsive: true,
      language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
      },
      dom: "frtip",
      columnDefs: [
        { targets: '_all', className: 'responsive-nowrap' },
        { targets: [0], width: '1%' },
        { targets: [2], width: '60px', className: 'text-center' }
      ],
      drawCallback: function() {
        $(table).find('td').addClass('text-truncate');
      }
    });
  }
};

// Función para inicializar la funcionalidad del formulario de cargo
function initializeCargoForm() {
  // Esperar a que los elementos estén disponibles en el DOM
  const checkElements = () => {
    const searchInput = document.getElementById('search-inventario');
    const suggestionsContainer = document.getElementById('suggestions-container');
    const inventarioIdInput = document.getElementById('inventario-id');
    const inventarioDisplay = document.getElementById('inventario-display');
    const cantidadInput = document.getElementById('cantidad-input');
    const cantidadInfo = document.getElementById('cantidad-info');
    const itemDetails = document.getElementById('item-details');
    const submitBtn = document.getElementById('submit-btn');

    if (searchInput && suggestionsContainer && inventarioIdInput && inventarioDisplay && 
        cantidadInput && cantidadInfo && itemDetails && submitBtn) {
      
      // Si ya se inicializó este formulario, salir para evitar duplicados
      if (searchInput.dataset.initialized === 'true') {
        return;
      }
      
      // Marcar como inicializado
      searchInput.dataset.initialized = 'true';

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
        if (submitBtn) submitBtn.disabled = true;
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
          const baseUrl = window.location.origin + window.location.pathname;
      fetch(`${baseUrl}?controller=cargo&action=searchInventory&term=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
              suggestions = data;
              showSuggestions(data);
            })
            .catch(error => {
              console.error('Error en la búsqueda:', error);
              if (typeof showNotification !== 'undefined') {
                showNotification('error', 'Error al buscar inventario');
              }
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
        const codigoElement = document.getElementById('item-codigo');
        const nombreElement = document.getElementById('item-nombre');
        const stockElement = document.getElementById('item-stock');
        const oficinaElement = document.getElementById('item-oficina');
        
        if (codigoElement) codigoElement.textContent = item.codigo;
        if (nombreElement) nombreElement.textContent = item.nombre;
        if (stockElement) {
          stockElement.textContent = item.cantidad;
          stockElement.className = item.cantidad > 0 ? 'text-success fw-bold' : 'text-danger fw-bold';
        }
        if (oficinaElement) oficinaElement.textContent = item.oficina_nombre || 'No asignada';
        
        itemDetails.style.display = 'block';

        // Habilitar campo de cantidad
        cantidadInput.disabled = false;
        cantidadInput.max = item.cantidad;
        cantidadInput.min = 1;
        cantidadInfo.textContent = `Máximo disponible: ${item.cantidad} unidades`;

        // Mostrar botón de submit
        if (submitBtn) submitBtn.disabled = false;

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
        // Remover cualquier listener previo para evitar duplicados
        const newForm = form.cloneNode(true);
        form.parentNode.replaceChild(newForm, form);
        const clonedForm = newForm;

        clonedForm.addEventListener('submit', function(e) {
          e.preventDefault();

          if (!selectedItem) {
            if (typeof showNotification !== 'undefined') {
              showNotification('warning', 'Por favor seleccione un ítem de inventario');
            }
            return;
          }

          const cantidad = parseInt(cantidadInput.value);
          if (cantidad <= 0 || cantidad > selectedItem.cantidad) {
            if (typeof showNotification !== 'undefined') {
              showNotification('warning', `La cantidad debe estar entre 1 y ${selectedItem.cantidad}`);
            }
            return;
          }

          const usuarioDestino = clonedForm.querySelector('input[name="usuario_destino"]').value.trim();
          if (!usuarioDestino) {
            if (typeof showNotification !== 'undefined') {
              showNotification('warning', 'Por favor ingrese el nombre del usuario que recibe');
            }
            return;
          }

          const formData = new FormData(clonedForm);
          const formDataObj = {};
          for (let [key, value] of formData.entries()) {
            formDataObj[key] = value;
          }

          fetch(clonedForm.getAttribute('action') || window.location.href, {
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
                const modalElement = clonedForm.closest('.modal');
                if (modalElement) {
                  const modal = bootstrap.Modal.getInstance(modalElement);
                  if (modal) {
                    modal.hide();
                  }
                }
              }
              if (typeof showNotification !== 'undefined') {
                showNotification('success', data.message || 'Cargo registrado exitosamente');
              }

              // Recargar la página principal
              if (data.redirect) {
                window.location.href = data.redirect;
              } else {
                window.location.reload();
              }
            } else {
              if (typeof showNotification !== 'undefined') {
                showNotification('error', 'Error: ' + (data.error || 'No se pudo registrar el cargo'));
              }
            }
          })
          .catch(error => {
            if (typeof showNotification !== 'undefined') {
              showNotification('error', 'Error de red: ' + error.message);
            }
          });
        });
      }
    } else {
      // Si los elementos no están disponibles aún, reintentar
      setTimeout(checkElements, 50);
    }
  };

  // Iniciar la verificación
  checkElements();
}
</script>