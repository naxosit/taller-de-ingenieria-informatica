// asientos.js - Controlador de selección de asientos para Cine Azul

// Esperar a que el documento esté completamente cargado
document.addEventListener('DOMContentLoaded', () => {
    // Precio por asiento (podría venir de la base de datos)
    const precioPorAsiento = 2500;
    const idFuncion = window.idFuncion;  // Obtenido del script en el HTML
    
    // Referencias a elementos importantes
    const contenedorAsientos = document.getElementById('seatingGrid');
    const resumenSeleccion = document.getElementById('selectedSeats');
    const totalPrecio = document.getElementById('totalPrice');
    const botonConfirmar = document.getElementById('confirmButton');
    
    // Función para alternar la selección de un asiento
    function alternarSeleccionAsiento(evento) {
        const asiento = evento.currentTarget;
        
        // Solo si está disponible
        if (asiento.classList.contains('available')) {
            asiento.classList.toggle('selected');
            actualizarResumenSeleccion();
        }
    }
    
    // Actualizar el resumen de asientos seleccionados y el total
    function actualizarResumenSeleccion() {
        const asientosSeleccionados = document.querySelectorAll('.seat.selected');
        
        // Limpiar el resumen actual
        resumenSeleccion.innerHTML = '';
        
        // Agregar cada asiento seleccionado al resumen
        asientosSeleccionados.forEach(asiento => {
            const etiquetaAsiento = document.createElement('div');
            etiquetaAsiento.className = 'seat-tag';
            etiquetaAsiento.innerHTML = `<i class="fas fa-chair"></i> ${asiento.dataset.label}`;
            resumenSeleccion.appendChild(etiquetaAsiento);
        });
        
        // Calcular y mostrar el total
        const total = asientosSeleccionados.length * precioPorAsiento;
        totalPrecio.textContent = `Total: $${total.toFixed(2)}`;
        
        // Habilitar o deshabilitar el botón de confirmar
        botonConfirmar.disabled = asientosSeleccionados.length === 0;
    }
    
    // Función para manejar la confirmación de compra
    function confirmarCompra() {
        const asientosSeleccionados = document.querySelectorAll('.seat.selected');
        
        if (asientosSeleccionados.length > 0) {
            // Crear formulario para enviar
            const formulario = document.createElement('form');
            formulario.method = 'POST';
            formulario.action = 'procesar_compra.php';
            
            // Añadir ID de la función
            const campoFuncion = document.createElement('input');
            campoFuncion.type = 'hidden';
            campoFuncion.name = 'idFuncion';
            campoFuncion.value = idFuncion;
            formulario.appendChild(campoFuncion);
            
            // Añadir cada asiento seleccionado
            asientosSeleccionados.forEach(asiento => {
                const campoAsiento = document.createElement('input');
                campoAsiento.type = 'hidden';
                campoAsiento.name = 'asientos[]';
                campoAsiento.value = asiento.dataset.id;
                formulario.appendChild(campoAsiento);
            });
            
            // Enviar formulario
            document.body.appendChild(formulario);
            formulario.submit();
        }
    }
    
    // Asignar eventos a los asientos disponibles
    const asientosDisponibles = document.querySelectorAll('.seat.available');
    asientosDisponibles.forEach(asiento => {
        asiento.addEventListener('click', alternarSeleccionAsiento);
    });
    
    // Asignar evento al botón de confirmar
    botonConfirmar.addEventListener('click', confirmarCompra);
});