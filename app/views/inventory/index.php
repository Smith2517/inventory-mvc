<?php
// Necesita que el controlador pase $items (inventario) y $oficinas (para selects)
$cfg   = require __DIR__ . '/../../config/config.php';
$base  = $cfg['app']['base_url'];
$csrf  = $csrf ?? ($_SESSION['csrf'] ?? '');

// Por si el layout no inyectó la base: definimos una variable global JS aquí también.
echo '<script>window.__APP_BASE__ = ' . json_encode($base) . ';</script>';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3><i class="bi bi-grid me-2"></i>Inventario</h3>
  <!-- Botón NUEVO que abre modal -->
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#itemModal">
    <i class="bi bi-plus-lg me-1"></i> Nuevo Ítem
  </button>
</div>

<div class="card p-2 p-md-3 shadow-sm">
  <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
    <table class="table table-striped table-hover datatable mb-0">
      <thead>
        <tr>
          <th class="col-fit">#</th>
          <th class="col-code">Código</th>
          <th class="col-name">Nombre</th>
          <th class="col-qty">Cant.</th>
          <th class="col-status d-none d-md-table-cell">Estado</th>
          <th class="col-condition d-none d-lg-table-cell">Condición</th>
          <th class="col-office d-none d-xl-table-cell">Oficina</th>
          <th class="col-actions">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($items as $i => $row): ?>
          <tr class="<?= ($row['estado'] === 'AGOTADO') ? 'table-danger' : '' ?>">
            <td class="text-nowrap"><?= $i + 1 ?></td>
            <td class="text-nowrap text-monospace"><?= htmlspecialchars($row['codigo']) ?></td>
            <td>
              <div><?= htmlspecialchars($row['nombre']) ?></div>
              <small class="text-muted d-block d-md-none"><?= htmlspecialchars($row['serie'] ?? '-') ?></small>
              <small class="text-muted d-block d-lg-none"><?= htmlspecialchars($row['descripcion'] ?? '-') ?></small>
            </td>
            <td class="text-center"><span class="badge bg-primary"><?= (int)$row['cantidad'] ?></span></td>
            <td class="d-none d-md-table-cell">
              <div>
                <?php if ($row['estado'] === 'AGOTADO'): ?>
                  <span class="badge bg-danger">AGOTADO</span>
              <?php else: ?>
                  <span class="badge bg-success">DISPONIBLE</span>
              <?php endif; ?>
              </div>
              <small class="d-block d-md-none">
                <?php if ($row['estado_2'] === 'BUENO'): ?>
                  <span class="badge bg-success">BUENO</span>
              <?php elseif ($row['estado_2'] === 'MALO'): ?>
                  <span class="badge bg-danger">MALO</span>
              <?php elseif ($row['estado_2'] === 'REGULAR'): ?>
                  <span class="badge bg-info text-dark">REGULAR</span>
              <?php elseif ($row['estado_2'] === 'BAJA'): ?>
                  <span class="badge bg-secondary">BAJA</span>
              <?php elseif ($row['estado_2'] === 'NUEVO'): ?>
                  <span class="badge bg-warning text-dark">NUEVO</span>
              <?php endif; ?>
              </small>
            </td>
            <td class="d-none d-lg-table-cell">
              <?php if ($row['estado_2'] === 'BUENO'): ?>
                <span class="badge bg-success">BUENO</span>
              <?php elseif ($row['estado_2'] === 'MALO'): ?>
                <span class="badge bg-danger">MALO</span>
              <?php elseif ($row['estado_2'] === 'REGULAR'): ?>
                <span class="badge bg-info text-dark">REGULAR</span>
              <?php elseif ($row['estado_2'] === 'BAJA'): ?>
                <span class="badge bg-secondary">BAJA</span>
              <?php elseif ($row['estado_2'] === 'NUEVO'): ?>
                <span class="badge bg-warning text-dark">NUEVO</span>
              <?php endif; ?>
            </td>
            <td class="d-none d-xl-table-cell">
              <div><?= htmlspecialchars($row['oficina'] ?? '-') ?></div>
              <small class="text-muted">
                <?= htmlspecialchars($row['estante'] ?? '-') ?>
              </small>
            </td>
            <td class="actions">
              <!-- Etiqueta (modal) -->
              <div class="btn-group btn-group-sm d-flex justify-content-center">
                <button class="btn btn-sm btn-secondary"
                  data-bs-toggle="modal"
                  data-bs-target="#labelModal"
                  data-id="<?= $row['id'] ?>"
                  title="Etiqueta">
                  <i class="bi bi-upc-scan"></i>
                </button>

                <!-- Detalle (modal) -->
                <button class="btn btn-sm btn-outline-primary"
                  data-bs-toggle="modal"
                  data-bs-target="#detailModal"
                  data-id="<?= $row['id'] ?>"
                  title="Detalle">
                  <i class="bi bi-eye"></i>
                </button>

                <!-- Editar (modal) -->
                <button class="btn btn-sm btn-outline-success edit-item-btn"
                  data-bs-toggle="modal"
                  data-bs-target="#editItemModal"
                  data-id="<?= $row['id'] ?>"
                  data-codigo="<?= htmlspecialchars($row['codigo']) ?>"
                  data-nombre="<?= htmlspecialchars($row['nombre']) ?>"
                  data-cantidad="<?= (int)$row['cantidad'] ?>"
                  data-serie="<?= htmlspecialchars($row['serie']) ?>"
                  data-descripcion="<?= htmlspecialchars($row['descripcion'] ?? '') ?>"
                  data-estante="<?= htmlspecialchars($row['estante'] ?? '') ?>"
                  data-estado_2="<?= htmlspecialchars($row['estado_2']) ?>"
                  data-oficina_id="<?= (int)$row['oficina_id'] ?>"
                  title="Editar">
                  <i class="bi bi-pencil"></i>
                </button>

                <!-- Descontar (modal) -->
                <button class="btn btn-sm btn-warning"
                  data-id="<?= $row['id'] ?>"
                  data-codigo="<?= htmlspecialchars($row['codigo']) ?>"
                  data-nombre="<?= htmlspecialchars($row['nombre']) ?>"
                  data-max="<?= (int)$row['cantidad'] ?>"
                  data-bs-toggle="modal" data-bs-target="#discountModal"
                  title="Descontar">
                  <i class="bi bi-dash-circle"></i>
                </button>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- ===========================
     MODAL: NUEVO ÍTEM (form)
=========================== -->
<div class="modal fade" id="itemModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Nuevo Producto/Equipo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <!-- novalidate: evita que el navegador pida "Completa este campo" en Código -->
      <form method="post" action="<?= $base ?>/?controller=inventory&action=create" id="form-inv" novalidate>
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
        <div class="modal-body">
          <div class="row g-3">

            <!-- Código visible SOLO PREVIEW (readonly). No se envía al servidor. -->
            <div class="col-12 col-md-3">
              <label class="form-label" for="codigo-preview">Código (auto)</label>
              <input type="text" id="codigo-preview" class="form-control"
                placeholder="Seleccione oficina y nombre" readonly>
              <!-- Campo oculto opcional por compatibilidad; el backend lo ignora -->
              <input type="hidden" name="codigo" value="">
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Nombre*</label>
              <input type="text" name="nombre" class="form-control" required>
            </div>

            <div class="col-12 col-md-3">
              <label class="form-label">Serie*</label>
              <input type="text" name="serie" class="form-control" required>
            </div>

            <div class="col-12">
              <label class="form-label">Descripción</label>
              <textarea name="descripcion" class="form-control" rows="2"></textarea>
            </div>

            <div class="col-12 col-md-2">
              <label class="form-label">Cantidad*</label>
              <input type="number" name="cantidad" min="0" step="1" class="form-control" required>
            </div>

            <div class="col-12 col-md-4">
              <label class="form-label">Oficina*</label>
              <select name="oficina_id" class="form-select" required>
                <option value="">- Seleccionar -</option>
                <?php foreach ($oficinas as $o): ?>
                  <option value="<?= $o['id'] ?>"><?= htmlspecialchars($o['nombre']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <?php $estadoSel = $_POST['estado_2'] ?? ($item['estado_2'] ?? ''); ?>
            <div class="col-12 col-md-4">
              <label class="form-label">Estado*</label>
              <select name="estado_2" class="form-select" required>
                <option value="">- Seleccionar -</option>
                <option value="BUENO" <?= $estadoSel === 'BUENO'   ? 'selected' : '' ?>>BUENO</option>
                <option value="MALO" <?= $estadoSel === 'MALO'    ? 'selected' : '' ?>>MALO</option>
                <option value="REGULAR" <?= $estadoSel === 'REGULAR' ? 'selected' : '' ?>>REGULAR</option>
                <option value="BAJA" <?= $estadoSel === 'BAJA'    ? 'selected' : '' ?>>BAJA</option>
                <option value="NUEVO" <?= $estadoSel === 'NUEVO'    ? 'selected' : '' ?>>NUEVO</option>
              </select>
            </div>

            <div class="col-12 col-md-2">
              <label class="form-label">Estante</label>
              <input type="text" name="estante" class="form-control">
            </div>
          </div>

          <div class="form-text mt-2">
            Al guardar, se registrará una ENTRADA (stock inicial) y se abrirá la etiqueta del ítem.
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-primary"><i class="bi bi-save2 me-1"></i>Guardar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </form>

    </div>
  </div>
</div>

<script>
  (function() {
    const base = <?= json_encode($base) ?>;
    const modal = document.getElementById('itemModal');

    // Utilidad para prefijo local (fallback si falla AJAX)
    const stop = new Set(['DE', 'DEL', 'LA', 'LAS', 'LOS', 'Y', 'EL', 'A', 'EN', 'PARA', 'POR', 'AL']);

    function abbr(t) {
      if (!t) return 'GEN';
      const up = t.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toUpperCase();
      const clean = up.replace(/[^A-Z\s]/g, ' ');
      const words = clean.trim().split(/\s+/).filter(Boolean).filter(w => !stop.has(w));
      if (!words.length) return 'GEN';
      let letters = words.map(w => w[0]).join('').slice(0, 3);
      if (letters.length < 3) letters = (words[0] || 'GEN').slice(0, 3);
      return letters || 'GEN';
    }

    function initModalScope() {
      const form = modal.querySelector('form#form-inv');
      const prev = modal.querySelector('#codigo-preview');
      const ofi = modal.querySelector('select[name="oficina_id"]');
      const nom = modal.querySelector('input[name="nombre"]');
      const hiddenCodigo = modal.querySelector('input[type="hidden"][name="codigo"]');

      // Por si hay restos del template antiguo: desactivar cualquier input[name=codigo] visible
      const legacy = modal.querySelector('input.form-control[name="codigo"]');
      if (legacy) {
        legacy.removeAttribute('required');
        legacy.setAttribute('readonly', 'readonly');
        legacy.setAttribute('placeholder', 'Se generará automáticamente');
        legacy.id = 'codigo-preview';
      }

      let timer;
      const debounce = (fn, ms = 250) => {
        clearTimeout(timer);
        timer = setTimeout(fn, ms);
      };

      async function refreshPreview() {
        if (!prev) return;
        const oficina_id = parseInt(ofi?.value || 0, 10);
        const nombre = (nom?.value || '').trim();

        if (!oficina_id || !nombre) {
          prev.value = 'Seleccione oficina y nombre';
          if (hiddenCodigo) hiddenCodigo.value = '';
          return;
        }

        try {
          // Intento con endpoint real (muestra el próximo código exacto)
          const url = `${base}/?controller=inventory&action=nextCode&oficina_id=${encodeURIComponent(oficina_id)}&nombre=${encodeURIComponent(nombre)}`;
          const res = await fetch(url, {
            headers: {
              'X-Requested-With': 'XMLHttpRequest'
            }
          });
          if (!res.ok) throw new Error('HTTP ' + res.status);
          const data = await res.json();
          if (data && data.ok && data.code) {
            prev.value = data.code;
            if (hiddenCodigo) hiddenCodigo.value = ''; // backend genera el real
            return;
          }
          throw new Error('Respuesta inválida');
        } catch (e) {
          // Fallback local: solo prefijo + ####
          const ofText = ofi?.options[ofi.selectedIndex]?.text || '';
          prev.value = abbr(ofText) + '-' + abbr(nombre) + '-####';
          if (hiddenCodigo) hiddenCodigo.value = '';
        }
      }

      ofi?.addEventListener('change', () => debounce(refreshPreview));
      nom?.addEventListener('input', () => debounce(refreshPreview));

      // Al abrir el modal, refresca
      refreshPreview();
    }

    // Bootstrap: cuando el modal se muestra, inicializa los handlers
    if (modal) {
      modal.addEventListener('shown.bs.modal', initModalScope);
    } else {
      // Si no hay modal (render directo), inicializa igual
      document.addEventListener('DOMContentLoaded', initModalScope);
    }
  })();
</script>


<!-- ===========================
     MODAL: DESCONTAR
=========================== -->
<div class="modal fade" id="discountModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="<?= $base ?>/?controller=inventory&action=discount">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
        <input type="hidden" name="inventario_id" id="d-inventario-id">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-dash-circle me-2"></i>Descontar stock</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2">
            <label class="form-label">Ítem</label>
            <input type="text" id="d-item-info" class="form-control" readonly>
          </div>
          <div class="mb-2">
            <label class="form-label">Cantidad a descontar</label>
            <input type="number" min="1" step="1" class="form-control" id="d-cantidad" name="cantidad" required>
            <div class="form-text" id="d-max-hint"></div>
          </div>
          <div class="mb-2">
            <label class="form-label">Motivo / Detalle de uso</label>
            <textarea class="form-control" name="motivo" rows="2" placeholder="Ej: Instalado en PC de Sistemas, equipo XYZ..."></textarea>
          </div>
          <div class="mb-2">
            <label class="form-label">Oficina de uso</label>
            <select class="form-select" name="oficina_id">
              <option value="">- Seleccionar -</option>
              <?php foreach ($oficinas as $o): ?>
                <option value="<?= $o['id'] ?>"><?= htmlspecialchars($o['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-warning"><i class="bi bi-check2-circle me-1"></i>Descontar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ===========================
     MODAL: ETIQUETA (iframe)
=========================== -->
<div class="modal fade" id="labelModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-upc-scan me-2"></i>Etiqueta del Ítem</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body p-0">
        <iframe id="labelFrame" src="about:blank" style="width:100%; height:210px; border:0; background:#fff;"></iframe>
      </div>

      <div class="modal-footer">
        <!-- ⬇️ Nuevo botón: GUARDAR PNG (reemplaza al de imprimir) -->
        <button id="btnSaveLabel" type="button" class="btn btn-primary">
          <i class="bi bi-download me-1"></i> Guardar PNG
        </button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script>
  // Guarda como PNG el contenido de la etiqueta dentro del iframe.
  // Requiere que la etiqueta esté en el mismo dominio (same-origin).
  (function() {
    const iframe = document.getElementById('labelFrame');
    const btn = document.getElementById('btnSaveLabel');

    // Carga html2canvas dentro del iframe si no existe
    async function ensureHtml2Canvas(win) {
      if (win.html2canvas) return;
      await new Promise((resolve, reject) => {
        const s = win.document.createElement('script');
        s.src = "https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js";
        s.onload = resolve;
        s.onerror = () => reject(new Error('No se pudo cargar html2canvas dentro del iframe'));
        win.document.head.appendChild(s);
      });
    }

    function sanitize(name) {
      return (name || 'etiqueta').replace(/[^A-Za-z0-9_-]+/g, '_');
    }

    async function savePngFromIframe() {
      const win = iframe?.contentWindow;
      const doc = win?.document;
      if (!win || !doc) {
        showNotification('warning', 'La etiqueta no está lista.');
        return;
      }

      try {
        await ensureHtml2Canvas(win);

        // Encuentra el nodo de la etiqueta
        const node = doc.getElementById('etiqueta') || doc.querySelector('.label');
        if (!node) {
          showNotification('error', 'No se encontró el nodo de la etiqueta.');
          return;
        }

        // Toma el código para el nombre del archivo
        const code = (doc.querySelector('.code')?.textContent || 'etiqueta').trim();

        const canvas = await win.html2canvas(node, {
          backgroundColor: '#ffffff',
          scale: 3,
          useCORS: true
        });
        const dataUrl = canvas.toDataURL('image/png');

        const a = document.createElement('a');
        a.href = dataUrl;
        a.download = sanitize(code) + '.png';
        document.body.appendChild(a);
        a.click();
        a.remove();

      } catch (err) {
        console.error(err);
        showNotification('error', 'No se pudo generar la imagen de la etiqueta.');
      }
    }

    btn?.addEventListener('click', savePngFromIframe);
  })();
</script>

<script>
  // Manejar el evento de clic en el botón de edición usando delegación de eventos para mayor estabilidad
  document.addEventListener('click', function(e) {
    const btn = e.target.closest('.edit-item-btn');
    if (btn) {
      // Llenar el formulario de edición con los datos del ítem
      document.getElementById('edit-item-id').value = btn.getAttribute('data-id');
      document.getElementById('edit-codigo').value = btn.getAttribute('data-codigo');
      document.getElementById('edit-nombre').value = btn.getAttribute('data-nombre');
      document.getElementById('edit-cantidad').value = btn.getAttribute('data-cantidad');
      document.getElementById('edit-serie').value = btn.getAttribute('data-serie');
      document.getElementById('edit-descripcion').value = btn.getAttribute('data-descripcion');
      document.getElementById('edit-estante').value = btn.getAttribute('data-estante');
      document.getElementById('edit-estado_2').value = btn.getAttribute('data-estado_2');
      
      // Seleccionar la oficina en el dropdown
      const oficinaId = btn.getAttribute('data-oficina_id');
      const oficinaSelect = document.getElementById('edit-oficina-id');
      if (oficinaSelect) {
        oficinaSelect.value = oficinaId || '';
      }
    }
  });
</script>


<!-- ===========================
     MODAL: EDITAR ÍTEM (form)
=========================== -->
<div class="modal fade" id="editItemModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Editar Producto/Equipo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <form method="post" action="<?= $base ?>/?controller=inventory&action=update" id="edit-form-inv" novalidate>
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
        <input type="hidden" name="id" id="edit-item-id">
        <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
          <div class="row g-3">
            <!-- Código (solo lectura, no se puede editar) -->
            <div class="col-12">
              <label class="form-label">Código (no editable)</label>
              <input type="text" id="edit-codigo" class="form-control" readonly>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Nombre*</label>
              <input type="text" name="nombre" id="edit-nombre" class="form-control" required>
            </div>

            <div class="col-12 col-md-3">
              <label class="form-label">Serie*</label>
              <input type="text" name="serie" id="edit-serie" class="form-control" required>
            </div>

            <div class="col-12">
              <label class="form-label">Descripción</label>
              <textarea name="descripcion" id="edit-descripcion" class="form-control" rows="2"></textarea>
            </div>

            <div class="col-12 col-md-3">
              <label class="form-label">Cantidad*</label>
              <input type="number" name="cantidad" id="edit-cantidad" min="0" step="1" class="form-control" required>
            </div>

            <div class="col-12 col-md-4">
              <label class="form-label">Oficina*</label>
              <select name="oficina_id" id="edit-oficina-id" class="form-select" required>
                <option value="">- Seleccionar -</option>
                <?php foreach ($oficinas as $o): ?>
                  <option value="<?= $o['id'] ?>"><?= htmlspecialchars($o['nombre']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            
            <?php $estadoSel = $_POST['estado_2'] ?? ($item['estado_2'] ?? ''); ?>
            <div class="col-12 col-md-4">
              <label class="form-label">Estado*</label>
              <select name="estado_2" id="edit-estado_2" class="form-select" required>
                <option value="">- Seleccionar -</option>
                <option value="BUENO" <?= $estadoSel === 'BUENO'   ? 'selected' : '' ?>>BUENO</option>
                <option value="MALO" <?= $estadoSel === 'MALO'    ? 'selected' : '' ?>>MALO</option>
                <option value="REGULAR" <?= $estadoSel === 'REGULAR' ? 'selected' : '' ?>>REGULAR</option>
                <option value="BAJA" <?= $estadoSel === 'BAJA'    ? 'selected' : '' ?>>BAJA</option>
                <option value="NUEVO" <?= $estadoSel === 'NUEVO'    ? 'selected' : '' ?>>NUEVO</option>
              </select>
            </div>

            <div class="col-12 col-md-3">
              <label class="form-label">Estante</label>
              <input type="text" name="estante" id="edit-estante" class="form-control">
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-primary"><i class="bi bi-save2 me-1"></i>Actualizar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </form>

    </div>
  </div>
</div>

<!-- ===========================
     MODAL: DETALLE (iframe)
=========================== -->

<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-eye me-2"></i>Detalle de Ítem</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body p-0">
        <iframe id="detailFrame" src="about:blank" style="width:100%; height:60vh; border:0; background:#fff; "></iframe>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>