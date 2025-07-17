<?php
session_start();
include_once("../../../../CONNECTION/conexion.php");
include_once("cargar_cine.php");
include_once("../Agregar/cargar_ciudad.php");

if (isset($_GET['idCine'])) {
    $idCine = $_GET['idCine'];
    $cine = cargarCine($idCine);
    
    if (!$cine) {
        die("Cine no encontrado");
    }
    
    // Convertir todas las claves a minúsculas para consistencia
    $cine = array_change_key_case($cine, CASE_LOWER);
} else {
    die("ID de cine no proporcionado");
}

$ciudades = cargarCiudad();
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Cine - Web Cine</title>
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

   
    // Añadimos filtro en tiempo real al input correo para solo permitir caracteres válidos y un solo @
    document.addEventListener('DOMContentLoaded', () => {
        const correoInput = document.getElementById('correo');

        correoInput.addEventListener('input', function() {
            // Caracteres permitidos en emails (sin espacios ni símbolos extraños)
            const permitido = /[^a-zA-Z0-9@.%+\-]/g;
            if (permitido.test(this.value)) {
                this.value = this.value.replace(permitido, '');
            }

            // Permitir solo un '@'
            const partes = this.value.split('@');
            if (partes.length > 2) {
                // Si hay más de un '@', elimina los extras (dejando solo el primero)
                this.value = partes[0] + '@' + partes.slice(1).join('');
            }
        });
    });

    // Validación general + correo específico
    function validarFormulario() {
        const campos = [
            document.getElementById('nombre'),
            document.getElementById('correo'),
            document.getElementById('telefono'),
            document.getElementById('ubicacion')
        ];

        let valido = true;

        // Validación general campos vacíos o espacios
        for (const campo of campos) {
            validarCampo(campo);
            if (campo.validity.customError) {
                campo.reportValidity();
                valido = false;
            }
        }

        if (!valido) return false; // Si ya hay error general, no seguir

        // Validación específica para correo
        const correoInput = document.getElementById('correo');
        const correo = correoInput.value.trim();

        // Validar solo un @ (esto ya lo controla el filtro pero aquí confirmamos)
        const arrobas = correo.split('@').length - 1;
        if (arrobas === 0) {
            correoInput.setCustomValidity('El correo debe contener un símbolo "@"');
            correoInput.reportValidity();
            return false;
        } else if (arrobas > 1) {
            correoInput.setCustomValidity('Solo se permite un símbolo "@" en el correo');
            correoInput.reportValidity();
            return false;
        }

        // Validar formato general del correo
        const regexCorreo = /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (!regexCorreo.test(correo)) {
            correoInput.setCustomValidity('Por favor, ingrese un correo válido (ejemplo: usuario@gmail.com)');
            correoInput.reportValidity();
            return false;
        }

        correoInput.setCustomValidity('');
        return true;
    }
</script>

</head>
<body>
    <header class="header">
        <div class="logo">Web Cine - Gestión de Cines</div>
        <nav>
            <a href="../../Cines.php">Volver</a>
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
            <form id="form-cine" action="Logica_Actualizar_Cine.php" method="POST" onsubmit="return validarFormulario()">
                <input type="hidden" name="idCine" value="<?= htmlspecialchars($cine['idcine']) ?>">
                <table class="form-table">
                    <tr>
                        <td><label for="nombre">Nombre del Cine:</label></td>
                        <td>
                            <input type="text" id="nombre" name="nombre" 
                                    value="<?= htmlspecialchars($form_data['nombre'] ?? $cine['nombre_cine'] ?? '') ?>" 
                                    required class="form-input" maxlength="100"
                                    oninput="validarCampo(this)">
                        </td>
                    </tr>

                    <tr>
                        <td><label for="correo">Correo:</label></td>
                        <td>
                            <input type="text" id="correo" name="correo" 
                                   value="<?= htmlspecialchars($form_data['correo'] ?? $cine['correo_cine'] ?? '') ?>" 
                                   required class="form-input" maxlength="100"
                                   oninput="validarCampo(this)">
                        </td>
                    </tr>

                    <tr>
                        <td><label for="telefono">Teléfono:</label></td>
                        <td>
                            <input type="text" id="telefono" name="telefono" 
                                   value="<?= htmlspecialchars($form_data['telefono'] ?? $cine['telefono'] ?? '') ?>" 
                                   required class="form-input" maxlength="100"
                                   oninput="validarCampo(this)">
                        </td>
                    </tr>

                    <tr>
                        <td><label for="ubicacion">Ubicación:</label></td>
                        <td>
                            <input type="text" id="ubicacion" name="ubicacion" 
                                   value="<?= htmlspecialchars($form_data['ubicacion'] ?? $cine['ubicacion'] ?? '') ?>" 
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
                                        <?php 
                                        // Determinar si esta ciudad debe estar seleccionada
                                        $selected = false;
                                        
                                        // Si hay datos de formulario guardados
                                        if (isset($form_data['ciudad_id'])) {
                                            $selected = ($form_data['ciudad_id'] == $ciudad['id']);
                                        } 
                                        // Si no hay datos de formulario, usar datos del cine
                                        elseif (isset($cine['idciudad'])) {
                                            $selected = ($cine['idciudad'] == $ciudad['id']);
                                        }
                                        ?>
                                        
                                        <option value="<?php echo htmlspecialchars($ciudad['id']); ?>" 
                                            <?php echo $selected ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($ciudad['nombre']); ?>
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
                            <button type="submit" class="btn-submit">Actualizar Cine</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</body>
</html>