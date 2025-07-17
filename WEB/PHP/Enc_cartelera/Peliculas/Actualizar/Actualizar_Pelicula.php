<?php
session_start(); // Iniciar sesión para manejar errores
include_once("../../../../CONNECTION/conexion.php");
include_once("cargar_pelicula.php");

// Recuperar datos antiguos y errores desde la sesión
$old = $_SESSION['old'] ?? [];
$errores = $_SESSION['errores'] ?? [];

// Limpiar sesión para no volver a mostrarlos después
unset($_SESSION['old'], $_SESSION['errores']);

if (isset($_GET['id'])) {
    $id_pelicula = $_GET['id'];
    $pelicula = cargarPelicula($id_pelicula);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Película - Web Cine</title>
    <link rel="stylesheet" href="../../../../CSS/styles.css">
    <link rel="stylesheet" href="../../../../CSS/formulario.css">
    <style>
        .error-message {
            color: red;
            font-size: 0.9rem;
            margin-top: 5px;
        }
        /* Estilo para campos inválidos */
        input:invalid, textarea:invalid {
            border-color: #ff6b6b;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">Web Cine - Gestión de Películas</div>
        <nav>
            <a href="../../Peliculas.php">Lista de Peliculas</a>
            <a href="#"></a>
            <a href="#"></a>
        </nav>
    </header>

    <div class="capa"></div>

    <div class="container">
        <?php if (isset($_GET['mensaje'])): ?>
            <div class="mensaje <?= isset($_GET['error']) ? 'error' : 'success' ?>">
                <?= htmlspecialchars(urldecode($_GET['mensaje'])) ?>
            </div>
        <?php endif; ?>
        
        <div class="formulario-agregar">
            <form id="form-pelicula" action="logica_actualizar.php" method="POST">
            <?php 
            // Usar datos antiguos si existen, sino datos de la película
            $idPelicula = $old['id'] ?? $pelicula['id'] ?? '';
            ?>
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($idPelicula, ENT_QUOTES, 'UTF-8'); ?>">    
                <table class="form-table">
                    <tr>
                        <td><label for="nombre">Nombre:</label></td>
                        <td>
                            <input type="text" id="nombre" name="nombre" 
                                   value="<?= htmlspecialchars($old['nombre'] ?? $pelicula['nombre'] ?? '') ?>" 
                                   required class="form-input"
                                   title="Por favor ingrese el nombre de la película">
                            <?php if (isset($errores['nombre'])): ?>
                                <div class="error-message"><?= $errores['nombre'] ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    
                    <tr>
                        <td><label for="duracion">Duración (min):</label></td>
                        <td>
                            <input type="number" id="duracion" name="duracion" 
                                   value="<?= htmlspecialchars($old['duracion'] ?? $pelicula['duracion'] ?? '') ?>" 
                                   min="1" required class="form-input"
                                   title="Por favor ingrese la duración en minutos">
                            <?php if (isset($errores['duracion'])): ?>
                                <div class="error-message"><?= $errores['duracion'] ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    
                    <tr>
                        <td><label for="sinopsis">Sinopsis:</label></td>
                        <td>
                            <textarea id="sinopsis" name="sinopsis" rows="3" required class="form-input"
                                      title="Por favor ingrese la sinopsis"><?= htmlspecialchars($old['sinopsis'] ?? $pelicula['sinopsis'] ?? '') ?></textarea>
                            <?php if (isset($errores['sinopsis'])): ?>
                                <div class="error-message"><?= $errores['sinopsis'] ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    
                    <tr>
                        <td><label for="director">Director:</label></td>
                        <td>
                            <input type="text" id="director" name="director" 
                                   value="<?= htmlspecialchars($old['director'] ?? $pelicula['director'] ?? '') ?>" 
                                   required class="form-input"
                                   title="Por favor ingrese el nombre del director">
                            <?php if (isset($errores['director'])): ?>
                                <div class="error-message"><?= $errores['director'] ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    
                    <tr>
                        <td><label for="genero">Género:</label></td>
                        <td>
                            <input type="text" id="genero" name="genero" 
                                   value="<?= htmlspecialchars($old['genero'] ?? $pelicula['genero'] ?? '') ?>" 
                                   required class="form-input"
                                   title="Por favor ingrese el género de la película">
                            <?php if (isset($errores['genero'])): ?>
                                <div class="error-message"><?= $errores['genero'] ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <tr>
                        <td><label for="imagen">Url Portada:</label></td>
                        <td>
                            <input type="text" id="imagen" name="imagen" 
                                   value="<?= htmlspecialchars($old['imagen'] ?? $pelicula['imagen'] ?? '') ?>" 
                                   required class="form-input"
                                   title="Por favor ingrese la URL de la portada">
                            <?php if (isset($errores['imagen'])): ?>
                                <div class="error-message"><?= $errores['imagen'] ?></div>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" style="text-align: center;">
                            <button type="submit" class="btn-submit">Actualizar Película</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-pelicula');
    const fields = form.querySelectorAll('input, textarea');
    
    // Mensajes personalizados para cada campo
    const customMessages = {
        nombre: 'Por favor ingrese el nombre de la película',
        duracion: 'Por favor ingrese la duración en minutos',
        sinopsis: 'Por favor ingrese la sinopsis',
        director: 'Por favor ingrese el nombre del director',
        genero: 'Por favor ingrese el género de la película',
        imagen: 'Por favor ingrese la URL de la portada'
    };

    // Configurar validación personalizada para cada campo
    fields.forEach(field => {
        // Validar al enviar el formulario
        field.addEventListener('invalid', function() {
            this.setCustomValidity(customMessages[this.name] || 'Este campo es obligatorio');
        });

        // Limpiar mensaje al empezar a escribir
        field.addEventListener('input', function() {
            this.setCustomValidity('');
            
            // Validación adicional para espacios en blanco
            if (this.value.trim() === '') {
                this.setCustomValidity('El campo no puede estar vacío o contener solo espacios');
            }
        });
        
        // Validar espacios en blanco al perder el foco
        field.addEventListener('blur', function() {
            if (this.value.trim() === '') {
                this.setCustomValidity('El campo no puede estar vacío o contener solo espacios');
                this.reportValidity();
            }
        });
    });

    // Validación general al enviar el formulario
    form.addEventListener('submit', function(event) {
        let formIsValid = true;
        
        fields.forEach(field => {
            // Comprobar campos vacíos o con solo espacios
            if (field.value.trim() === '') {
                field.setCustomValidity('El campo no puede estar vacío o contener solo espacios');
                formIsValid = false;
            }
            
            // Forzar validación
            if (!field.checkValidity()) {
                formIsValid = false;
            }
        });
        
        if (!formIsValid) {
            event.preventDefault();
            // Mostrar mensaje en el primer campo inválido
            for (let field of fields) {
                if (!field.checkValidity()) {
                    field.reportValidity();
                    break;
                }
            }
        }
    });
});
</script>
</body>
</html>