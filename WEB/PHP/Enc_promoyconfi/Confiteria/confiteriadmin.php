<?php
include_once("../../../CONNECTION/conexion.php");
session_start();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confiteria - Web Cine</title>
    <link rel="stylesheet" href="../../../CSS/styles.css">

    <link rel="stylesheet" href="../../../CSS/botones.css">
</head>

<body>
    <header class="header">
        <div class="logo">Web Cine - Gestión de Productos</div>
        <nav>
            <a href="../Enc_promoyconfi.php">Volver</a>
        </nav>
    </header>

    <div class="container">
        <?php if (isset($_GET['mensaje'])): ?>
            <div class="mensaje <?= isset($_GET['error']) ? 'error' : 'success' ?>">
                <?= htmlspecialchars(urldecode($_GET['mensaje'])) ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['eliminado'])): ?>
            <div class="mensaje success">
                Producto eliminado correctamente!
            </div>
        <?php endif; ?>

        <div class="contenedor-boton-agregar">
            <a href="agregar_producto.php" class="boton-agregar">Agregar Producto</a>
        </div>

        <h2>Listado de Productos</h2>
        <table class="productos-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Categoría</th>
                    <th>Precio</th>
                    <th>Imagen</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    $query = "SELECT id_producto, nombre, descripcion, categoria, precio, imagen FROM confiteria ORDER BY categoria, nombre";
                    $stmt = $conn->prepare($query);
                    $stmt->execute();
                    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (count($productos) > 0) {
                foreach ($productos as $producto) {
                    echo "<tr>
                        <td>{$producto['id_producto']}</td>
                        <td>{$producto['nombre']}</td>
                        <td>{$producto['descripcion']}</td>
                        <td>{$producto['categoria']}</td>
                        <td>$" . number_format($producto['precio'], 0,',','.') . "</td>
                        <td><img src='{$producto['imagen']}' alt='{$producto['nombre']}' style='width: 50px; height: auto;'></td>
                        <td class='acciones'>
                            <a href='actualizar_producto.php?id={$producto['id_producto']}' 
                               class='button-actualizar'>
                                <i class='fas fa-edit'></i> Actualizar
                            </a>
                            <a href='eliminar_producto.php?id_producto={$producto['id_producto']}' 
                               class='button-eliminar' 
                               onclick='return confirm(\"¿Estás seguro de eliminar este producto?\");'>
                                <i class='fas fa-trash-alt'></i> Eliminar
                            </a>
                        </td>
                    </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No hay productos registrados</td></tr>";
                    }
                } catch (PDOException $e) {
                    echo "<tr><td colspan='7'>Error al cargar productos: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>