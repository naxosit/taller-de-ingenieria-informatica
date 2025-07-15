<?php
session_start(); // Iniciar sesión para mantener datos
include_once("../../../../CONNECTION/conexion.php");
include_once("cargar_ciudad.php");

$ciudades = cargarCiudad();

// Recuperar datos de formulario si existen en sesión
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Sala - Web Cine</title>
    <link rel="stylesheet" href="../../../../CSS/styles.css">
    <link rel="stylesheet" href="../../../../CSS/formulario.css">
  <script>
    function validarCampo(input) {
        const valor = input.value.trim();
        if (valor === '') {
            input.setCustomValidity('Este campo no puede estar vacío');
        } else if (!/\S/.test(valor)) {
            input.setCustomValidity('No se permiten campos con solo espacios');
        } else {
            input.setCustomValidity('');
        }
    }

    function validarFormulario() {
        const campos = [
            document.getElementById('nombre'),
            document.getElementById('correo'),
            document.getElementById('telefono'),
            document.getElementById('ubicacion')
        ];

        let valido = true;

        // Validación general
        for (const campo of campos) {
            validarCampo(campo);
            if (campo.validity.customError) {
                campo.reportValidity();
                valido = false;
            }
        }

        // Validación específica para correo
        const correoInput = document.getElementById('correo');
        const correo = correoInput.value.trim();
        const regexCorreo = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

        if (valido && !regexCorreo.test(correo)) {
            correoInput.setCustomValidity('Por favor, ingrese un correo válido (ejemplo: usuario@gmail.com)');
            correoInput.reportValidity();
            valido = false;
        } else {
            correoInput.setCustomValidity('');
        }

        return valido;
    }
</script>

</head>

<body>
    <header class="header">
        <div class="logo">Web Cine - Gestión de Cines</div>
        <nav>
            <a href="../../Salas.php">Lista de Salas</a>
            <a href="../../Peliculas.php">Películas</a>
            <a href="#">Cines</a>
        </nav>
    </header>

    <div class="capa"></div>

    <div class="container">
        <?php if (isset($_GET['error'])): ?>
            <div class="mensaje error">
                <?= htmlspecialchars(urldecode($_GET['error'])) ?>
            </div>
        <?php endif; ?>

        <div class="formulario-agregar">
            <form id="form-sala" action="Guardar_cine.php" method="POST" onsubmit="return validarFormulario()">
                <table class="form-table">
                    <tr>
                        <td><label for="nombre">Nombre del Cine:</label></td>
                        <td>
                            <input type="text" id="nombre" name="nombre" 
                                   value="<?= htmlspecialchars($form_data['nombre'] ?? '') ?>" 
                                   required class="form-input" maxlength="100"
                                   oninput="validarCampo(this)">
                        </td>
                    </tr>

                    <tr>
                        <td><label for="correo">Correo:</label></td>
                        <td>
                            <input type="text" id="correo" name="correo" 
                                   value="<?= htmlspecialchars($form_data['correo'] ?? '') ?>" 
                                   required class="form-input" maxlength="100"
                                   oninput="validarCampo(this)">
                        </td>
                    </tr>

                    <tr>
                        <td><label for="telefono">Teléfono:</label></td>
                        <td>
                            <input type="text" id="telefono" name="telefono" 
                                   value="<?= htmlspecialchars($form_data['telefono'] ?? '') ?>" 
                                   required class="form-input" maxlength="100"
                                   oninput="validarCampo(this)">
                        </td>
                    </tr>

                    <tr>
                        <td><label for="ubicacion">Ubicación:</label></td>
                        <td>
                            <input type="text" id="ubicacion" name="ubicacion" 
                                   value="<?= htmlspecialchars($form_data['ubicacion'] ?? '') ?>" 
                                   required class="form-input" maxlength="100"
                                   oninput="validarCampo(this)">
                        </td>
                    </tr>
                    
                    <tr>
                        <td><label for="ciudad_id">Ciudad:</label></td>
                        <td>
                            <select id="ciudad_id" name="ciudad_id" required class="form-input">
                                <option value="">Seleccione una ciudad...</option>
                                <?php if (!empty($ciudades)): ?>
                                    <?php foreach ($ciudades as $ciudad): ?>
                                        <option value="<?= htmlspecialchars($ciudad['id']) ?>"
                                            <?= (isset($form_data['ciudad_id']) && $form_data['ciudad_id'] == $ciudad['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($ciudad['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" disabled>No hay ciudades disponibles</option>
                                <?php endif; ?>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <td colspan="2" style="text-align: center;">
                            <button type="submit" class="btn-submit">Agregar Cine</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>

<!-- Script de validación para CORREO -->
<script>
function validarCorreo() {
    const correoInput = document.getElementById('correo');
    const correo = correoInput.value.trim();
    const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    if (!regex.test(correo)) {
        alert('Por favor, ingrese un correo válido (ejemplo: usuario@gmail.com)');
        correoInput.focus();
        return false; // Evita el envío del formulario
    }
    return true; // Permite el envío
}
</script>


</body>
</html>