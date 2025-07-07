document.getElementById('form-pelicula').addEventListener('submit', function(e) {
    const horaInput = document.getElementById('hora');
    const hora = horaInput.value.trim();
    
    // Validar formato básico
    if (!/^\d{2}:\d{2}$/.test(hora)) {
        e.preventDefault();
        alert('Formato de hora inválido. Debe ser HH:MM (ej: 14:30)');
        horaInput.focus();
        return;
    }
    
    // Validar componentes numéricos
    const partes = hora.split(':');
    const horas = parseInt(partes[0], 10);
    const minutos = parseInt(partes[1], 10);
    
    if (isNaN(horas) || isNaN(minutos)) {
        e.preventDefault();
        alert('Hora inválida. Use solo números (ej: 09:45)');
        horaInput.focus();
        return;
    }
    
    // Validar rango de horas
    if (horas < 0 || horas > 23) {
        e.preventDefault();
        alert('Hora inválida. Debe estar entre 00 y 23');
        horaInput.focus();
        return;
    }
    
    // Validar rango de minutos
    if (minutos < 0 || minutos > 59) {
        e.preventDefault();
        alert('Minutos inválidos. Deben estar entre 00 y 59');
        horaInput.focus();
        return;
    }
    
    // Validar fecha futura
    const fechaInput = document.getElementById('fecha');
    const fechaHora = new Date(`${fechaInput.value}T${hora}:00`);
    
    if (fechaHora <= new Date()) {
        e.preventDefault();
        alert('Debe seleccionar una fecha y hora futuras');
    }
});

// Opcional: Agregar máscara de entrada para formato HH:MM
document.getElementById('hora').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    
    if (value.length > 4) value = value.substr(0,4);
    if (value.length > 2) {
        value = value.substr(0,2) + ':' + value.substr(2,2);
    }
    
    e.target.value = value;
});