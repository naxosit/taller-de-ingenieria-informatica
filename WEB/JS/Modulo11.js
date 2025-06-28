// Función para validar RUT chileno con módulo 11
function validarRut(rutCompleto) {
  // Eliminar puntos y guiones, convertir a mayúsculas
  rutCompleto = rutCompleto.replace(/\./g, '').replace(/\-/g, '').toUpperCase();
  
  // Separar cuerpo y dígito verificador
  const cuerpo = rutCompleto.slice(0, -1);
  const dv = rutCompleto.slice(-1);
  
  // Validar que el cuerpo sea numérico y tenga 7-8 dígitos
  if (!/^\d{7,8}$/.test(cuerpo)) {
    return false;
  }
  
  // ... resto del código sin cambios ...
}

// Función para formatear RUT mientras se escribe
function formatearRut(rut) {
  // Eliminar caracteres no válidos
  rut = rut.replace(/[^0-9kK\-]/g, '').toUpperCase();
  
  // Separar cuerpo y DV
  let cuerpo = rut.slice(0, -1);
  const dv = rut.slice(-1);
  
  // Eliminar puntos existentes
  cuerpo = cuerpo.replace(/\./g, '');
  
  // Limitar cuerpo a 8 dígitos (máximo permitido en Chile)
  if (cuerpo.length > 8) cuerpo = cuerpo.substring(0, 8);
  
  // Agregar puntos cada 3 dígitos (de derecha a izquierda)
  let cuerpoFormateado = '';
  for (let i = cuerpo.length - 1, j = 1; i >= 0; i--, j++) {
    cuerpoFormateado = cuerpo[i] + cuerpoFormateado;
    if (j % 3 === 0 && i > 0) {
      cuerpoFormateado = '.' + cuerpoFormateado;
    }
  }
  
  // Combinar cuerpo formateado con DV
  return cuerpoFormateado + '-' + dv;
}

// Evento para formatear RUT mientras se escribe
document.getElementById('rut').addEventListener('input', function(e) {
  const input = e.target;
  let rut = input.value.replace(/\./g, '').replace(/\-/g, '');
  
  // Limitar a 9 caracteres (8 cuerpo + 1 DV)
  if (rut.length > 9) {
    rut = rut.substring(0, 9);
  }
  
  // Solo formatear si hay más de 1 caracter
  if (rut.length > 1) {
    input.value = formatearRut(rut);
  }
  
  // ... resto del código sin cambios ...
});