    </main>
  <?php if ($isLogged): ?>
  </div> <!-- /.page -->
  <?php endif; ?>

<!-- ===== Modal: Etiqueta ===== -->
<div class="modal fade" id="labelModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="background:#0f1833; color:#e5e7eb;">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-upc-scan me-2"></i>Etiqueta del Ítem</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body p-0">
        <!-- Mostramos la etiqueta dentro de un iframe -->
        <iframe id="labelFrame" src="about:blank" style="width:100%; height:420px; border:0; background:#fff;"></iframe>
      </div>
      <div class="modal-footer">
        <button id="btnPrintLabel" type="button" class="btn btn-primary">
          <i class="bi bi-printer me-1"></i> Imprimir
        </button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<!-- ============================ -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= (require __DIR__ . '/../../config/config.php')['app']['base_url'] ?>/assets/js/app.js"></script>
<!-- jQuery (necesario para DataTables) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<!-- Extensión Buttons (para exportar) -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

</body>
</html>
