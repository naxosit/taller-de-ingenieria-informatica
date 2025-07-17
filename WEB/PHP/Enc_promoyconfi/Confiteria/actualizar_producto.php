<?php
include_once("../../../CONNECTION/conexion.php");
session_start();

// Recuperar errores y datos de sesión si existen
$errores = $_SESSION['errores_actualizar'] ?? [];
$datosFormulario = $_SESSION['datos_actualizar'] ?? [];

// Limpiar los datos de sesión después de usarlos
unset($_SESSION['errores_actualizar']);
unset($_SESSION['datos_actualizar']);

if (isset($_GET['id'])) {
    $id_producto = $_GET['id'];
    
    // Si hay datos del formulario en sesión, usarlos, sino cargar de la BD
    if (!empty($datosFormulario)) {
        $producto = $datosFormulario;
        $producto['id_producto'] = $id_producto;
    } else {
        $stmt = $conn->prepare("SELECT * FROM confiteria WHERE id_producto = ?");
        $stmt->execute([$id_producto]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Producto - Web Cine</title>
    <link rel="stylesheet" href="../../../CSS/styles.css">
    <link rel="stylesheet" href="../../../CSS/formulario.css">
    <link rel="stylesheet" href="../../../CSS/botonesconfi.css">
    <style>
        .imagen-preview {
            margin-top: 10px;
            max-width: 200px;
        }
        .imagen-preview img {
            max-width: 100%;
            border-radius: 5px;
            border: 1px solid #ddd;
            padding: 5px;
        }
        .error-message {
            color: #ff0000;
            font-size: 0.9rem;
            margin-top: 5px;
        }
        .url-container {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .url-input {
            display: flex;
            gap: 5px;
        }
        .url-input input {
            flex-grow: 1;
        }
        .error-field {
            border: 1px solid red;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">Web Cine - Gestión de Productos</div>
        <nav>
            <a href="confiteriadmin.php">Volver</a>
        </nav>
    </header>

    <div class="container">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="mensaje error">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="formulario-agregar">
            <h1>Actualizar Producto</h1>
            <form id="form-producto" action="logica_actualizar.php" method="POST">
                <input type="hidden" name="id_producto" value="<?= htmlspecialchars($producto['id_producto'] ?? '') ?>">
                
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required
                    value="<?= htmlspecialchars($producto['nombre'] ?? '') ?>" 
                    class="form-input <?= isset($errores['nombre']) ? 'error-field' : '' ?>"
                    oninvalid="this.setCustomValidity('El nombre no puede estar vacío o tener solo espacios')"
                    oninput="this.setCustomValidity('')">

                </div>
                
                <div class="form-group">
                    <label for="descripcion">Descripción:</label>
                    <textarea id="descripcion" name="descripcion" rows="3" required
                    class="form-input <?= isset($errores['descripcion']) ? 'error-field' : '' ?>"
                    oninvalid="this.setCustomValidity('La descripción no puede estar vacía o tener solo espacios')"
                    oninput="this.setCustomValidity('')"><?= htmlspecialchars($producto['descripcion'] ?? '') ?></textarea>

                </div>
                
                <div class="form-group">
                    <label for="categoria">Categoría:</label>
                    <select id="categoria" name="categoria" 
                            class="form-input <?= isset($errores['categoria']) ? 'error-field' : '' ?>">
                        <option value="Bebidas" <?= ($producto['categoria'] ?? '') == 'Bebidas' ? 'selected' : '' ?>>Bebidas</option>
                        <option value="Dulces" <?= ($producto['categoria'] ?? '') == 'Dulces' ? 'selected' : '' ?>>Dulces</option>
                        <option value="Snacks" <?= ($producto['categoria'] ?? '') == 'Snacks' ? 'selected' : '' ?>>Snacks</option>
                        <option value="Combos" <?= ($producto['categoria'] ?? '') == 'Combos' ? 'selected' : '' ?>>Combos</option>
                    </select>
                    <?php if (isset($errores['categoria'])): ?>
                        <div class="error-message"><?= $errores['categoria'] ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="precio">Precio ($):</label>
                    <input type="number" id="precio" name="precio" required
                           value="<?= htmlspecialchars($producto['precio'] ?? '') ?>" 
                           min="500" step="10" required 
                           class="form-input <?= isset($errores['precio']) ? 'error-field' : '' ?>">
                    <?php if (isset($errores['precio'])): ?>
                        <div class="error-message"><?= $errores['precio'] ?></div>
                    <?php else: ?>
                    <?php endif; ?>

                </div>
                
                <div class="form-group">
                    <label for="imagen">Imagen:</label>
                    <div class="url-container">
                        <div class="url-input">
                            <input type="text" id="imagen" name="imagen" 
                            value="<?= htmlspecialchars($producto['imagen'] ?? '') ?>" 
                            required
                            class="form-input <?= isset($errores['imagen']) ? 'error-field' : '' ?>"
                            oninvalid="this.setCustomValidity('La URL de la imagen no puede estar vacía o tener solo espacios')"
                            oninput="this.setCustomValidity('')">
                            <button type="button" id="btn-actualizar-imagen" class="btn-actualizar">Actualizar</button>
                        </div>
                        <small>Ingrese la URL completa de la imagen (ej: https://ejemplo.com/imagen.jpg)</small>
                    </div>
                    
                    <?php if (!empty($producto['imagen'])): ?>
                    <div class="imagen-preview">
                        <p>Vista previa:</p>
                        <img id="imagen-preview" src="<?= htmlspecialchars($producto['imagen']) ?>" alt="Previsualización de imagen">
                        <p id="imagen-error" class="error-message">
                            <?= isset($errores['imagen']) ? $errores['imagen'] : '' ?>
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-submit">Actualizar Producto</button>
                    <a href="confiteriadmin.php" class="btn-cancelar">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Función para verificar si una URL de imagen es válida
        function validarImagen(url) {
            return new Promise((resolve) => {
                const img = new Image();
                img.onload = () => resolve(true);
                img.onerror = () => resolve(false);
                img.src = url;
            });
        }

        // Actualizar vista previa de imagen
        document.getElementById('btn-actualizar-imagen').addEventListener('click', async function() {
            const url = document.getElementById('imagen').value;
            const preview = document.getElementById('imagen-preview');
            const errorElement = document.getElementById('imagen-error');
            
            if (!url) {
                errorElement.textContent = 'Por favor ingrese una URL';
                return;
            }
            
            // Verificar si la URL es válida
            const esValida = await validarImagen(url);
            
            if (esValida) {
                preview.src = url;
                errorElement.textContent = '';
            } else {
                errorElement.textContent = 'La URL de la imagen no es válida o no se puede cargar';
            }
        });

        // Validación del formulario
        document.getElementById('form-producto').addEventListener('submit', async function(e) {
            const nombreInput = document.getElementById('nombre');
            const descripcionInput = document.getElementById('descripcion');
            const precioInput = document.getElementById('precio');
            const imagenInput = document.getElementById('imagen');
            let isValid = true;
            
            // Resetear mensajes de error
            document.querySelectorAll('.error-message').forEach(el => {
                if (el.id !== 'imagen-error') el.textContent = '';
            });
            
            // Validar nombre
            if (!nombreInput.value.trim()) {
                isValid = false;
                nombreInput.classList.add('error-field');
                nombreInput.nextElementSibling.textContent = 'El nombre no puede estar vacío';
            }
            
            // Validar descripción
            if (!descripcionInput.value.trim()) {
                isValid = false;
                descripcionInput.classList.add('error-field');
                descripcionInput.nextElementSibling.textContent = 'La descripción no puede estar vacía';
            }
            
            
        // Validar precio
        if (!is_numeric($precio)) {
            $errores['precio'] = "El precio debe ser un número.";
        } elseif ($precio < 500) {
            $errores['precio'] = "El precio mínimo es $500.";
        } elseif ($precio % 10 !== 0) {
            $errores['precio'] = "El precio debe ser un múltiplo de 10.";
        }
            
            // Validar imagen
            if (!imagenInput.value.trim()) {
                isValid = false;
                imagenInput.classList.add('error-field');
                const errorElement = document.getElementById('imagen-error');
                if (errorElement) {
                    errorElement.textContent = 'Por favor ingrese una URL de imagen';
                }
            } else {
                // Verificar si la URL de imagen es válida
                const esValida = await validarImagen(imagenInput.value);
                if (!esValida) {
                    isValid = false;
                    imagenInput.classList.add('error-field');
                    const errorElement = document.getElementById('imagen-error');
                    if (errorElement) {
                        errorElement.textContent = 'La URL de la imagen no es válida o no se puede cargar';
                    }
                }
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });

        // Cargar vista previa inicial si existe
        <?php if (!empty($producto['imagen'])): ?>
        window.addEventListener('DOMContentLoaded', () => {
            const preview = document.getElementById('imagen-preview');
            preview.src = "<?= htmlspecialchars($producto['imagen']) ?>";
        });
        <?php endif; ?>

        //validar campo descripcion
        document.querySelector('form').addEventListener('submit', function (e) {
        const descripcion = document.getElementById('descripcion');

        // Si contiene solo espacios, mostrar mensaje emergente
        if (!descripcion.value.trim()) {
        descripcion.setCustomValidity('La descripción no puede estar vacía o tener solo espacios');
        descripcion.reportValidity(); // Fuerza el mensaje emergente del navegador
        e.preventDefault(); // Detiene el envío del formulario
        } else {
        descripcion.setCustomValidity('');
        }
        });

        //validar imagen url
        document.querySelector('form').addEventListener('submit', function (e) {
        const imagen = document.getElementById('imagen');
        let valido = true;

        if (!imagen.value.trim()) {
        imagen.setCustomValidity('La URL de la imagen no puede estar vacía o tener solo espacios');
        imagen.reportValidity();
        valido = false;
        } else {
        imagen.setCustomValidity('');
        }

        if (!valido) {
        e.preventDefault(); // Detener el envío si hay error
        }
        });


    </script>
</body>
</html>