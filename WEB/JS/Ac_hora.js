    document.getElementById('form-funcion').addEventListener('submit', function(e) {
        const fechaInput = document.getElementById('fecha_nueva');
        const horaInput = document.getElementById('hora_nueva');
        const hora = horaInput.value.trim();
        
        // Validar formato básico
        if (!/^\d{2}:\d{2}$/.test(hora)) {
            e.preventDefault();
            alert('Formato de hora inválido. Debe ser HH:MM (ej: 14:30)');
            horaInput.focus();
            return;
        }
        
        const partes = hora.split(':');
        const horas = parseInt(partes[0], 10);
        const minutos = parseInt(partes[1], 10);
        
        if (isNaN(horas) || isNaN(minutos)) {
            e.preventDefault();
            alert('Hora inválida. Use solo números (ej: 09:45)');
            horaInput.focus();
            return;
        }
        
        if (horas < 0 || horas > 23) {
            e.preventDefault();
            alert('Hora inválida. Debe estar entre 00 y 23');
            horaInput.focus();
            return;
        }
        
        if (minutos < 0 || minutos > 59) {
            e.preventDefault();
            alert('Minutos inválidos. Deben estar entre 00 y 59');
            horaInput.focus();
            return;
        }
        
        // Validar fecha futura
        const fechaHoraCompleta = new Date(`${fechaInput.value}T${hora}:00`);
        if (fechaHoraCompleta <= new Date()) {
            e.preventDefault();
            alert('Debe seleccionar una fecha y hora futuras');
        }
    });

    // Máscara para la hora (formato automático)
    document.getElementById('hora_nueva').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 4) value = value.substr(0,4);
        if (value.length > 2) {
            value = value.substr(0,2) + ':' + value.substr(2,2);
        }
        e.target.value = value;
    });