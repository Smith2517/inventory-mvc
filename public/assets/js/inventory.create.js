(function(){
  // Stopwords para la abreviatura
  const stop = new Set(['DE','DEL','LA','LAS','LOS','Y','EL','A','EN','PARA','POR','AL']);

  function abbr(t){
    if(!t) return 'GEN';
    // Quitar acentos (incluye Ñ), dejar letras y espacios, mayúsculas
    const up = t.normalize('NFD').replace(/[\u0300-\u036f]/g,'').toUpperCase(); // Á->A, Ñ->N
    const clean = up.replace(/[^A-Z\s]/g,' ');
    const words = clean.trim().split(/\s+/).filter(Boolean).filter(w => !stop.has(w));
    if (!words.length) return 'GEN';
    let letters = words.map(w => w[0]).join('').slice(0,3);
    if (letters.length < 3) letters = (words[0] || 'GEN').slice(0,3);
    return letters || 'GEN';
  }

  function getOficinaNombre(){
    const sel = document.getElementById('oficina_id');
    if (!sel || !sel.value) return '';
    return sel.options[sel.selectedIndex]?.text || '';
  }

  function updatePreview(){
    const ofName = getOficinaNombre();
    const nombre = document.getElementById('nombre')?.value || '';
    const prefix = abbr(ofName) + '-' + abbr(nombre);
    const prev = document.getElementById('preview-codigo');
    if (prev) prev.value = (prefix === 'GEN-GEN' ? 'PEND-PEND' : prefix) + '-####';
  }

  window.addEventListener('DOMContentLoaded', function(){
    const nombre = document.getElementById('nombre');
    const oficina = document.getElementById('oficina_id');
    if (nombre) nombre.addEventListener('input', updatePreview);
    if (oficina) oficina.addEventListener('change', updatePreview);
    updatePreview();
  });
})();
