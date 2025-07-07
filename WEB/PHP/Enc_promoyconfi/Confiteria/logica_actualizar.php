<?php
include_once("../../../CONNECTION/conexion.php");
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $errores = [];
        $id_producto = $_POST['id_producto'] ?? null;
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $categoria = $_POST['categoria'] ?? '';
        $precio = $_POST['precio'] ?? 0;
        $imagen = trim($_POST['imagen'] ?? '');
        
        // Validar ID del producto
        if (!$id_producto || !is_numeric($id_producto)) {
            throw new Exception("ID de producto inválido");
        }
        
        // Validar campos no vacíos
        if (empty($nombre)) {
            $errores['nombre'] = "El nombre no puede estar vacío";
        }
        if (empty($descripcion)) {
            $errores['descripcion'] = "La descripción no puede estar vacía";
        }
        
        // Validar precio (mínimo 500)
        if (!is_numeric($precio) || $precio < 500) {
            $errores['precio'] = "El precio debe ser un número y al menos $500";
        } else {
            $precio = (int)$precio;
        }
        
        // Validar URL de imagen
        if (empty($imagen)) {
            $errores['imagen'] = "La URL de la imagen es requerida";
        } elseif (!filter_var($imagen, FILTER_VALIDATE_URL)) {
            $errores['imagen'] = "La URL de la imagen no es válida";
        }
        
        // Si hay errores, guardarlos en sesión y redirigir
        if (!empty($errores)) {
            $_SESSION['errores_actualizar'] = $errores;
            $_SESSION['datos_actualizar'] = [
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'categoria' => $categoria,
                'precio' => $precio,
                'imagen' => $imagen
            ];
            header("Location: actualizar_producto.php?id=" . $id_producto);
            exit();
        }
        
        // Actualizar en base de datos
        $stmt = $conn->prepare("
            UPDATE confiteria 
            SET nombre = :nombre,
                descripcion = :descripcion,
                categoria = :categoria,
                precio = :precio,
                imagen = :imagen
            WHERE id_producto = :id_producto
        ");
        
        $stmt->execute([
            ':nombre' => $nombre,
            ':descripcion' => $descripcion,
            ':categoria' => $categoria,
            ':precio' => $precio,
            ':imagen' => $imagen,
            ':id_producto' => $id_producto
        ]);
        
        // Redirigir con mensaje de éxito
        $_SESSION['mensaje'] = "Producto actualizado correctamente";
        header("Location: confiteriadmin.php");
        exit();
        
    } catch (Exception $e) {
        // Manejar error y redirigir
        $_SESSION['error'] = "Error al actualizar: " . $e->getMessage();
        header("Location: actualizar_producto.php?id=" . $_POST['id_producto']);
        exit();
    }
} else {
    // Método no permitido
    header("Location: confiteriadmin.php");
    exit();
}