<?php
session_start();
include_once("../../../../CONNECTION/conexion.php");
include_once("cargar_ciudad.php");
$ciudades = cargarCiudad();
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Cine - Web Cine</title>
    <link rel="stylesheet" href="../../../../CSS/styles.css">
    <link rel="stylesheet" href="../../../../CSS/formulario.css">
  <script>
    function validarCampo(input) {
        const valor = input.value.trim();
        if (valor === '') {
            input.setCustomValidity('Este campo no puede estar vacío');
        } else {
            input.setCustomValidity('');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const correoInput = document.getElementById('correo');
        const telefonoInput = document.getElementById('telefono');

        correoInput.addEventListener('input', function() {
            const permitido = /[^a-zA-Z0-9@.%+\-]/g;
            if (permitido.test(this.value)) {
                this.value = this.value.replace(permitido, '');
            }
            const partes = this.value.split('@');
            if (partes.length > 2) {
                this.value = partes[0] + '@' + partes.slice(1).join('');
            }
        });

        telefonoInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    });

    function validarFormulario() {
        const campos = [
            document.getElementById('nombre'),
            document.getElementById('correo'),
            document.getElementById('telefono'),
            document.getElementById('ubicacion')
        ];

        for (const campo of campos) {
            validarCampo(campo);
            if (!campo.checkValidity()) {
                campo.reportValidity();
                return false;
            }
        }

        const correoInput = document.getElementById('correo');
        const correo = correoInput.value.trim().toLowerCase();
        const regexCorreo = /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (!regexCorreo.test(correo)) {
            correoInput.setCustomValidity('Por favor, ingrese un correo con formato válido.');
            correoInput.reportValidity();
            return false;
        }
        
        const dominioUsuario = correo.split('@')[1];
        const dominiosPermitidos = ['gmail.com', 'hotmail.com', 'outlook.com', 'yahoo.com', 'yahoo.es', 'icloud.com'];
        if (!dominiosPermitidos.includes(dominioUsuario)) {
            correoInput.setCustomValidity('Dominio no permitido. Use un correo de Gmail, Hotmail, Outlook, etc.');
            correoInput.reportValidity();
            return false;
        } else {
            correoInput.setCustomValidity('');
        }

        // --- VALIDACIÓN DE TELÉFONO CHILENO ACTUALIZADA ---
        const telefonoInput = document.getElementById('telefono');
        // El patrón ^9[0-9]{8}$ significa: debe empezar con 9 (^) y ser seguido de 8 dígitos más.
        if (!/^9[0-9]{8}$/.test(telefonoInput.value.trim())) {
            telefonoInput.setCustomValidity('El teléfono debe empezar con 9 y tener 9 dígitos.');
            telefonoInput.reportValidity();
            return false;
        } else {
            telefonoInput.setCustomValidity('');
        }

        return true;
    }
</script>
</head>
<body>
    <header class="header">
        <div class="logo">Web Cine - Gestión de Cines</div>
        <nav><a href="../../Cines.php">Volver</a></nav>
    </header>
    <div class="capa"></div>
    <div class="container">
        <?php if (isset($_GET['error'])): ?>
            <div class="mensaje error"><?= htmlspecialchars(urldecode($_GET['error'])) ?></div>
        <?php endif; ?>
        <div class="formulario-agregar">
            <form id="form-sala" action="Guardar_cine.php" method="POST" onsubmit="return validarFormulario()">
                <table class="form-table">
                    <tr>
                        <td><label for="nombre">Nombre del Cine:</label></td>
                        <td><input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($form_data['nombre'] ?? '') ?>" required class="form-input" maxlength="100" oninput="validarCampo(this)"></td>
                    </tr>
                    <tr>
                        <td><label for="correo">Correo:</label></td>
                        <td><input type="email" id="correo" name="correo" value="<?= htmlspecialchars($form_data['correo'] ?? '') ?>" required class="form-input" maxlength="100" placeholder="ejemplo@gmail.com" oninput="validarCampo(this)"></td>
                    </tr>
                    <tr>
                        <td><label for="telefono">Teléfono:</label></td>
                        <td>
                            <input type="tel" id="telefono" name="telefono" value="<?= htmlspecialchars($form_data['telefono'] ?? '') ?>" required class="form-input" maxlength="9" pattern="9[0-9]{8}" title="El teléfono debe empezar con 9 y tener 9 dígitos." oninput="validarCampo(this)">
                        </td>
                    </tr>
                    <tr>
                        <td><label for="ubicacion">Ubicación:</label></td>
                        <td><input type="text" id="ubicacion" name="ubicacion" value="<?= htmlspecialchars($form_data['ubicacion'] ?? '') ?>" required class="form-input" maxlength="100" oninput="validarCampo(this)"></td>
                    </tr>
                    <tr>
                        <td><label for="ciudad_id">Ciudad:</label></td>
                        <td>
                            <select id="ciudad_id" name="ciudad_id" required class="form-input">
                                <option value="">Seleccione una ciudad...</option>
                                <?php if (!empty($ciudades)): ?>
                                    <?php foreach ($ciudades as $ciudad): ?>
                                        <option value="<?= htmlspecialchars($ciudad['id']) ?>" <?= (isset($form_data['ciudad_id']) && $form_data['ciudad_id'] == $ciudad['id']) ? 'selected' : '' ?>>
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
                        <td colspan="2" style="text-align: center;"><button type="submit" class="btn-submit">Agregar Cine</button></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</body>
</html>