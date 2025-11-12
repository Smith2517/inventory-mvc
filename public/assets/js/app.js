document.addEventListener("DOMContentLoaded", () => {
  // ===== Base URL exacta desde PHP =====
  const BASE =
    (typeof window.__APP_BASE__ === "string" && window.__APP_BASE__) || "";

  // ====== Sidebar Mobile Toggle ======
  function setupSidebarToggle() {
    // Create overlay if it doesn't exist
    let overlay = document.querySelector('.sidebar-overlay');
    if (!overlay) {
      overlay = document.createElement('div');
      overlay.className = 'sidebar-overlay';
      document.body.appendChild(overlay);
    }

    // Handle overlay click
    overlay.addEventListener('click', function() {
      toggleSidebar(false);
    });

    // Add click event to sidebar toggle button
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    if (sidebarToggle) {
      sidebarToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleSidebar();
      });
    }

    // Function to toggle sidebar
    function toggleSidebar(forceState) {
      const sidebar = document.querySelector('.sidebar');
      const overlay = document.querySelector('.sidebar-overlay');
      
      if (typeof forceState === 'boolean') {
        if (forceState) {
          sidebar.classList.add('active');
          overlay.classList.add('active');
          document.body.classList.add('sidebar-open');
        } else {
          sidebar.classList.remove('active');
          overlay.classList.remove('active');
          document.body.classList.remove('sidebar-open');
        }
      } else {
        if (sidebar.classList.contains('active')) {
          sidebar.classList.remove('active');
          overlay.classList.remove('active');
          document.body.classList.remove('sidebar-open');
        } else {
          sidebar.classList.add('active');
          overlay.classList.add('active');
          document.body.classList.add('sidebar-open');
        }
      }
    }

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
      const sidebar = document.querySelector('.sidebar');
      const sidebarToggle = document.querySelector('.sidebar-toggle');
      
      if (window.innerWidth < 992 && 
          sidebar && 
          sidebar.classList.contains('active') && 
          !sidebar.contains(event.target) && 
          !sidebarToggle.contains(event.target)) {
        toggleSidebar(false);
      }
    });

    // Handle window resize to adjust layout
    window.addEventListener('resize', debounce(() => {
      const sidebar = document.querySelector('.sidebar');
      if (window.innerWidth >= 992 && sidebar) {
        // On larger screens, always show sidebar and remove overlay
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
        document.body.classList.remove('sidebar-open');
      } else if (window.innerWidth < 992) {
        // On smaller screens, hide sidebar by default
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
        document.body.classList.remove('sidebar-open');
      }
    }, 250));
  }

  // ====== Modal Descontar ======
  const discountModal = document.getElementById("discountModal");
  if (discountModal) {
    discountModal.addEventListener("show.bs.modal", (event) => {
      const btn = event.relatedTarget;
      if (!btn) return;
      const id = btn.getAttribute("data-id");
      const codigo = btn.getAttribute("data-codigo");
      const nombre = btn.getAttribute("data-nombre");
      const max = btn.getAttribute("data-max");

      discountModal.querySelector("#d-inventario-id").value = id;
      const info = discountModal.querySelector("#d-item-info");
      if (info) info.value = `${codigo} — ${nombre}`;

      const hint = discountModal.querySelector("#d-max-hint");
      if (hint) hint.textContent = `Stock disponible: ${max}`;

      const cantidadInput = discountModal.querySelector("#d-cantidad");
      if (cantidadInput) {
        cantidadInput.setAttribute("max", max);
        cantidadInput.value = 1; // Default to 1 instead of empty
      }
    });
  }

  // ====== Modal Etiqueta (iframe) ======
  const labelModal = document.getElementById("labelModal");
  const labelFrame = document.getElementById("labelFrame");
  const printBtn = document.getElementById("btnPrintLabel");

  if (labelModal && labelFrame) {
    labelModal.addEventListener("show.bs.modal", (event) => {
      const btn = event.relatedTarget;
      if (!btn) return;
      const id = btn.getAttribute("data-id");
      // Add cache busting
      const url = `${BASE}/?controller=inventory&action=label&id=${encodeURIComponent(
        id
      )}&_=${Date.now()}`;
      labelFrame.src = url;
    });
    labelModal.addEventListener("hidden.bs.modal", () => {
      labelFrame.src = "about:blank";
    });
    if (printBtn) {
      printBtn.addEventListener("click", () => {
        if (labelFrame && labelFrame.contentWindow) {
          try {
            labelFrame.contentWindow.focus();
            labelFrame.contentWindow.print();
          } catch (e) {
            console.error("Error al imprimir:", e);
            alert("No se pudo imprimir la etiqueta. Intente abrir en nueva pestaña.");
          }
        }
      });
    }
  }

  // ====== Modal Detalle (iframe) ======
  const detailModal = document.getElementById('detailModal');
  const detailFrame = document.getElementById('detailFrame');

  if (detailModal && detailFrame) {
    detailModal.addEventListener('show.bs.modal', event => {
      const btn = event.relatedTarget;
      if (!btn) return;
      const id = btn.getAttribute('data-id');
      // Cache busting and partial parameter
      const url = `${BASE}/?controller=inventory&action=show&id=${encodeURIComponent(id)}&partial=1&_=${Date.now()}`;
      detailFrame.src = url;
    });
    detailModal.addEventListener('hidden.bs.modal', () => {
      detailFrame.src = 'about:blank';
    });
    
    // Handle responsive iframe content
    detailFrame.addEventListener('load', () => {
      try {
        const iframeDoc = detailFrame.contentDocument || detailFrame.contentWindow.document;
        if (iframeDoc) {
          // Ensure proper styling in iframe
          const style = iframeDoc.createElement('style');
          style.textContent = `
            body {
              margin: 0;
              padding: 0;
              font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
              background-color: #ffffff;
              color: #1e293b;
            }
            .container-fluid {
              padding: 0.75rem;
            }
            @media (max-width: 768px) {
              .container-fluid {
                padding: 0.5rem;
              }
              .table th, .table td {
                padding: 0.4rem;
                font-size: 0.8rem;
              }
              .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
              }
            }
          `;
          iframeDoc.head.appendChild(style);
        }
      } catch (e) {
        // Cross-origin restriction - ignore
      }
    });
  }

  // ====== Improved DataTables with responsive options ======
  if (window.jQuery && typeof jQuery.fn.DataTable === "function") {
    // Initialize DataTables with responsive settings
    const initializeDataTables = () => {
      jQuery(".datatable").each(function() {
        if ($.fn.DataTable.isDataTable(this)) {
          // If already initialized, destroy and re-initialize
          $(this).DataTable().destroy();
        }
        
        // Initialize with responsive settings
        $(this).DataTable({
          pageLength: 10,
          lengthMenu: [5, 10, 25, 50, 100],
          order: [],
          responsive: true,
          language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
          },
          dom: "Bfrtip",
          buttons: [
            { extend: "excel", className: "btn btn-success btn-sm" },
            { extend: "pdf", className: "btn btn-danger btn-sm" },
            { extend: "print", className: "btn btn-secondary btn-sm" },
          ],
          // Adjust column widths to prevent breaking
          columnDefs: [
            { targets: '_all', className: 'responsive-nowrap' }
          ]
        });
      });
    };

    // Initialize DataTables after a short delay to ensure DOM is ready
    setTimeout(initializeDataTables, 100);
  }

  // ====== Improved Responsive Table Handling ======
  function setupResponsiveTables() {
    const tables = document.querySelectorAll('.table-responsive');
    tables.forEach(table => {
      // Add horizontal scroll with momentum on touch devices
      if ('ontouchstart' in window) {
        table.style.webkitOverflowScrolling = 'touch';
      }
      
      // Add resize observer to handle table resizing
      if (window.ResizeObserver) {
        const resizeObserver = new ResizeObserver(entries => {
          for (let entry of entries) {
            // Adjust table behavior on resize
            if (entry.target.clientWidth < 768) {
              entry.target.classList.add('table-responsive-mobile');
            } else {
              entry.target.classList.remove('table-responsive-mobile');
            }
          }
        });
        resizeObserver.observe(table);
      }
    });
  }

  // ====== Handle Window Resize for Responsive Design ======
  window.addEventListener('resize', debounce(() => {
    // Adjust iframe heights on window resize
    if (detailFrame) {
      detailFrame.style.height = `${window.innerHeight * 0.6}px`;
    }
    
    // Re-init DataTables if needed
    if (window.jQuery && typeof jQuery.fn.DataTable === "function") {
      jQuery('.datatable').each(function() {
        if (this.DataTable && this.DataTable.responsive) {
          this.DataTable.responsive.recalc();
        }
      });
    }
  }, 250));

  // Set initial iframe height
  if (detailFrame) {
    detailFrame.style.height = '60vh';
  }

  // ====== Initialize Everything ======
  setupSidebarToggle();
  setupResponsiveTables();

  // ====== Utility functions for form validation ======
  function validateForm(form) {
    let valid = true;
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    
    inputs.forEach(input => {
      if (!input.value.trim()) {
        input.classList.add('is-invalid');
        valid = false;
      } else {
        input.classList.remove('is-invalid');
      }
    });
    
    return valid;
  }

  // ====== Debounced function utility ======
  function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  }
  
});