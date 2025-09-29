document.addEventListener("DOMContentLoaded", () => {
  // ===== Base URL exacta desde PHP =====
  const BASE =
    (typeof window.__APP_BASE__ === "string" && window.__APP_BASE__) || "";

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
      if (cantidadInput) cantidadInput.setAttribute("max", max);
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
      const url = `${BASE}/?controller=inventory&action=label&id=${encodeURIComponent(
        id
      )}`;
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
            alert("No se pudo imprimir la etiqueta. Abre en nueva pestaña.");
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
    // 🔹 partial=1 + anti-cache
    const url = `${BASE}/?controller=inventory&action=show&id=${encodeURIComponent(id)}&partial=1&_=${Date.now()}`;
    detailFrame.src = url;
  });
  detailModal.addEventListener('hidden.bs.modal', () => {
    detailFrame.src = 'about:blank';
  });
}

  // ====== DataTables (si la usas aquí) ======
  if (window.jQuery && typeof jQuery.fn.DataTable === "function") {
    jQuery(".datatable").DataTable({
      pageLength: 10,
      lengthMenu: [5, 10, 25, 50, 100],
      order: [],
      language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
      },
      dom: "Bfrtip",
      buttons: [
        { extend: "excel", className: "btn btn-success btn-sm" },
        { extend: "pdf", className: "btn btn-danger btn-sm" },
        { extend: "print", className: "btn btn-secondary btn-sm" },
      ],
    });
  }
});
