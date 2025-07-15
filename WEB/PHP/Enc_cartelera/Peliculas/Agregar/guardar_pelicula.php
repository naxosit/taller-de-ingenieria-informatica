<?php
session_start();
require_once __DIR__ . '/../../../../CONNECTION/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger y limpiar los datos
    $nombre = trim($_POST['nombre'] ?? '');
    $duracion = trim($_POST['duracion'] ?? '');
    $sinopsis = trim($_POST['sinopsis'] ?? '');
    $director = trim($_POST['director'] ?? '');
    $genero = trim($_POST['genero'] ?? '');
    $imagen = trim($_POST['imagen'] ?? '');

    // Guardar datos antiguos en sesión
    $_SESSION['old'] = [
        'nombre' => $nombre,
        'duracion' => $duracion,
        'sinopsis' => $sinopsis,
        'director' => $director,
        'genero' => $genero,
        'imagen' => $imagen
    ];

    // Validaciones
    $errores = [];

    if (empty($nombre) || ctype_space($nombre)) {
        $errores['nombre'] = "El nombre no puede estar vacío ni contener solo espacios.";
    }

    if (empty($duracion) || !is_numeric($duracion) || $duracion < 1) {
        $errores['duracion'] = "La duración debe ser un número mayor a 0.";
    }

    if (empty($sinopsis) || ctype_space($sinopsis)) {
        $errores['sinopsis'] = "La sinopsis no puede estar vacía.";
    }

    if (empty($director) || ctype_space($director)) {
        $errores['director'] = "El director no puede estar vacío.";
    }

    if (empty($genero) || ctype_space($genero)) {
        $errores['genero'] = "El género no puede estar vacío.";
    }

    if (empty($imagen)) {
        $errores['imagen'] = "Debe ingresar la URL de la imagen.";
    } elseif (!filter_var($imagen, FILTER_VALIDATE_URL)) {
        $errores['imagen'] = "La URL de la imagen no es válida.";
    }

    // Si hay errores, redirigir con errores
    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        header("Location: Agregar_pelicula.php");
        exit;
    }

    // Si pasa las validaciones, insertar en la base de datos
    try {
        $sqlPelicula = "INSERT INTO Pelicula (Nombre, Duracion, Sinopsis, Director, Genero, Imagen)
                        VALUES (:nombre, :duracion, :sinopsis, :director, :genero, :imagen)";
        $stmt = $conn->prepare($sqlPelicula);
        $stmt->execute([
            ':nombre' => $nombre,
            ':duracion' => $duracion,
            ':sinopsis' => $sinopsis,
            ':director' => $director,
            ':genero' => $genero,
            ':imagen' => $imagen
        ]);

        // Limpiar datos antiguos
        unset($_SESSION['old']);

        // Redirigir con éxito
        header("Location: ../../Peliculas.php?mensaje=" . urlencode("Película guardada con éxito"));
        exit;
    } catch (PDOException $e) {
        header("Location: Agregar_pelicula.php?mensaje=" . urlencode("Error al guardar: " . $e->getMessage()) . "&error=1");
        exit;
    }
} else {
    header("Location: ../../Peliculas.php?mensaje=" . urlencode("Acceso inválido") . "&error=1");
    exit;
}
