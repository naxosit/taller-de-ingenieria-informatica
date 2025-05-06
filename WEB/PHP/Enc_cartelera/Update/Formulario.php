<?php
include_once("../../../CONNECTION/conexion.php");

// Asegúrate de que se recibe el ID de la película a editar
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Consulta para obtener los datos de la película
    $sql = "SELECT p.*, pr.Id_Cine FROM Pelicula p 
            LEFT JOIN Proyeccion pr ON p.idPelicula = pr.Id_Pelicula 
            WHERE p.idPelicula = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $pelicula = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pelicula) {
        echo "Película no encontrada.";
        exit;
    }
} else {
    echo "ID de película no especificado.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Película - Web Cine</title>
    <link rel="stylesheet" href="../../../CSS/styles.css">
    <link rel="stylesheet" href="../../../CSS/formulario.css">
</head>
<body>
    <header class="header">
        <div class="logo">Web Cine - Gestión de Películas</div>
        <nav>
            <a href="#">Agregar Película</a>
            <a href="#">Actualizar Película</a>
            <a href="#">Eliminar Película</a>
        </nav>
    </header>

    <div class="capa"></div>

    <div class="container">
        <div class="formulario-agregar">
            <form id="form-pelicula" action="logica/actualizar_pelicula.php" method="POST">
                <input type="hidden" name="id" value="<?= htmlspecialchars($pelicula['idPelicula']) ?>">


                <table class="form-table">
                    <tr>
                        <td><label for="nombre">Nombre:</label></td>
                        <td><input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($pelicula['nombre']) ?>" required class="form-input"></td>
                    </tr>
                    
                    <tr>
                        <td><label for="duracion">Duración (min):</label></td>
                        <td><input type="number" id="duracion" name="duracion" value="<?= htmlspecialchars($pelicula['duracion']) ?>" min="1" required class="form-input"></td>
                    </tr>
                    
                    <tr>
                        <td><label for="sinopsis">Sinopsis:</label></td>
                        <td><textarea id="sinopsis" name="sinopsis" rows="3" required class="form-input"><?= htmlspecialchars($pelicula['sinopsis']) ?></textarea></td>
                    </tr>
                    
                    <tr>
                        <td><label for="director">Director:</label></td>
                        <td><input type="text" id="director" name="director" value="<?= htmlspecialchars($pelicula['director']) ?>" required class="form-input"></td>
                    </tr>
                    
                    <tr>
                        <td><label for="genero">Género:</label></td>
                        <td><input type="text" id="genero" name="genero" value="<?= htmlspecialchars($pelicula['genero']) ?>" required class="form-input"></td>
                    </tr>
                    
                    <tr>
                        <td><label for="id_cine">Cine:</label></td>
                        <td>
                            <select id="id_cine" name="id_cine" required class="form-input">
                                <option value="">Seleccione un cine</option>
                                <?php include __DIR__ . "/cargarcine.php"; ?>
                            </select>
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
</body>
</html>
