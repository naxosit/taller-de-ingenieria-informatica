<?php
include_once("../../../CONNECTION/conexion.php");
session_start();

$errores = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $categoria = $_POST['categoria'] ?? '';
        $precio = $_POST['precio'] ?? 0;
        $imagen = trim($_POST['imagen'] ?? '');

        // Validar que no estén vacíos
        if (empty($nombre)) {
            $errores['nombre'] = "El nombre no puede estar vacío";
        }
        if (empty($descripcion)) {
            $errores['descripcion'] = "La descripción no puede estar vacía";
        }
        if (empty($imagen)) {
            $errores['imagen'] = "La URL de la imagen es requerida";
        }

        // Validar categoría
        $categoriasPermitidas = ['Snacks', 'Bebidas', 'Promos'];
        if (!in_array($categoria, $categoriasPermitidas)) {
            $errores['categoria'] = "Categoría no válida";
        }

        // Validar precio
        if (!is_numeric($precio)) {
            $errores['precio'] = "El precio debe ser un número.";
        } elseif ($precio < 500) {
            $errores['precio'] = "El precio mínimo es $500.";
        } elseif ((float)$precio != (int)$precio) {
            $errores['precio'] = "No se aceptan precios con decimales.";    
        } elseif ($precio % 10 !== 0) {
            $errores['precio'] = "El precio debe ser un múltiplo de 10.";
        } else {
            $precio = (int)$precio; // conversión segura si pasa todas las validaciones
        }
        



        // Verificar que la URL sea válida
        if (!empty($imagen)) {
            if (!filter_var($imagen, FILTER_VALIDATE_URL)) {
                $errores[] = "La URL de la imagen no es válida";
            }
        }

        if (empty($errores)) {
            $query = "INSERT INTO confiteria (nombre, descripcion, categoria, precio, imagen) 
                  VALUES (:nombre, :descripcion, :categoria, :precio, :imagen)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':categoria', $categoria);
            $stmt->bindParam(':precio', $precio);
            $stmt->bindParam(':imagen', $imagen);
            $stmt->execute();

        $mensaje = "Producto agregado con éxito!";
        header("Location: confiteriadmin.php?mensaje=" . urlencode($mensaje));
        exit();
        }
        // si hay errores no hacemos nada y el formulario se muestra con mensajes

    } catch (Exception $e) {
        $mensaje = "Error al agregar producto:<br>" . $e->getMessage();
        $error = true;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Producto - Web Cine</title>
    <link rel="stylesheet" href="../../../CSS/styles.css">
    <link rel="stylesheet" href="../../../CSS/formulario.css">
    <link rel="stylesheet" href="../../../CSS/botones.css">
    <style>
        .error-message {
            color: #ff0000;
            font-size: 0.9rem;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">Web Cine - Agregar Producto</div>
        <nav>
            <a href="confiteriadmin.php">Volver</a>
        </nav>
    </header>
    
    <?php if (isset($mensaje)): ?>
        <div class="mensaje <?= isset($error) && $error ? 'error' : 'success' ?>">
            <?= $mensaje ?>
        </div>
    <?php endif; ?>

    <div class="formulario-agregar">
        <h1>Agregar Nuevo Producto</h1>
        <form action="agregar_producto.php" method="POST">
            <table class="form-table">
                <tr>
                    <td><label for="nombre">Nombre:</label></td>
                    <td>
                        <input type="text" id="nombre" name="nombre" 
                               value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" 
                               required class="form-input">
                        <?php if (isset($errores['nombre'])): ?>
                            <div class="error-message"><?= $errores['nombre'] ?></div>
                        <?php endif; ?>
                    </td>
                </tr>
                
                <tr>
                    <td><label for="descripcion">Descripción:</label></td>
                    <td>
                        <textarea id="descripcion" name="descripcion" rows="3" 
                                  required class="form-input"><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
                        <?php if (isset($errores['descripcion'])): ?>
                            <div class="error-message"><?= $errores['descripcion'] ?></div>
                        <?php endif; ?>
                    </td>
                </tr>
                
                <tr>
                    <td><label for="categoria">Categoría:</label></td>
                    <td>
                        <select id="categoria" name="categoria" required class="form-input">
                            <option value="">Seleccione categoría</option>
                            <option value="Snacks" <?= ($_POST['categoria'] ?? '') == 'Snacks' ? 'selected' : '' ?>>Snacks</option>
                            <option value="Bebidas" <?= ($_POST['categoria'] ?? '') == 'Bebidas' ? 'selected' : '' ?>>Bebidas</option>
                            <option value="Promos" <?= ($_POST['categoria'] ?? '') == 'Promos' ? 'selected' : '' ?>>Promos</option>
                        </select>
                        <?php if (isset($errores['categoria'])): ?>
                            <div class="error-message"><?= $errores['categoria'] ?></div>
                        <?php endif; ?>
                    </td>
                </tr>
                
                <tr>
                    <td><label for="precio">Precio:</label></td>
                    <td>
                        <input type="number" id="precio" name="precio" 
                               min="500" step="10" 
                               value="<?= htmlspecialchars($_POST['precio'] ?? '') ?>" 
                               required class="form-input">
                        <small>Mínimo $500</small>
                        <?php if (isset($errores['precio'])): ?>
                            <div class="error-message"><?= $errores['precio'] ?></div>
                        <?php endif; ?>
                    </td>
                </tr>

                <tr>
                    <td><label for="imagen">URL de la imagen:</label></td>
                    <td>
                        <input type="text" id="imagen" name="imagen" 
                               value="<?= htmlspecialchars($_POST['imagen'] ?? '') ?>" 
                               required class="form-input">
                        <?php if (isset($errores['imagen'])): ?>
                            <div class="error-message"><?= $errores['imagen'] ?></div>
                        <?php endif; ?>
                    </td>
                </tr>
                
                <tr>
                    <td colspan="2" style="text-align: center;">
                        <button type="submit" class="btn-submit">Guardar Producto</button>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</body>
</html>