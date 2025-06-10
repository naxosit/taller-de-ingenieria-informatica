<?php
include_once("../../CONNECTION/conexion.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cartelera por Cine</title>
    <link rel="stylesheet" href="../../CSS/styles.css">
    <link rel="stylesheet" href="../../CSS/cartelera.css">
</head>
<body>
    <header>
        <div class="logo">Web Cine - Cartelera</div>
        <nav>
            <a href="vista_encargado.php">Peliculas</a>
            <a href="vista_salas.php">Salas</a>
            <a href="Funciones.php">Funciones</a>
        </nav>
    </header>

    <main class="cartelera-container">
        <?php
        try {
            // Cargar cines
            $queryCines = "SELECT idcine, nombre_cine FROM cine ORDER BY nombre_cine";
            $stmtCines = $conn->query($queryCines);
            $cines = $stmtCines->fetchAll(PDO::FETCH_ASSOC);

            // Cine seleccionado
            $cineSeleccionado = $_GET['cine'] ?? '';

            // Formulario desplegable
            echo '<form method="GET">';
            echo '<label for="cine">Selecciona un cine:</label> ';
            echo '<select name="cine" id="cine" onchange="this.form.submit()">';
            echo '<option value="">-- Todos los cines --</option>';
            foreach ($cines as $cine) {
                $selected = ($cineSeleccionado == $cine['idcine']) ? 'selected' : '';
                echo "<option value=\"{$cine['idcine']}\" $selected>" . htmlspecialchars($cine['nombre_cine']) . "</option>";
            }
            echo '</select>';
            echo '</form>';

            if (!empty($cineSeleccionado)) {
                // Películas con funciones en ese cine
                $queryPeliculas = "
                    SELECT DISTINCT p.*
                    FROM Pelicula p
                    INNER JOIN Funcion f ON f.Id_Pelicula = p.idpelicula
                    INNER JOIN Sala s ON f.Id_Sala = s.idsala
                    WHERE s.Cine_idCine = :cineID
                    ORDER BY p.Nombre
                ";
                $stmtPeliculas = $conn->prepare($queryPeliculas);
                $stmtPeliculas->execute([':cineID' => $cineSeleccionado]);
                $peliculas = $stmtPeliculas->fetchAll(PDO::FETCH_ASSOC);

                foreach ($peliculas as $pelicula):
        ?>
        <div class="pelicula-card">
            <div class="pelicula-content">
                <img src="<?= htmlspecialchars($pelicula['imagen'] ?? 'https://via.placeholder.com/150') ?>" 
                    class="pelicula-imagen" 
                    alt="<?= htmlspecialchars($pelicula['nombre']) ?>">

                <div class="detalles-pelicula">
                    <h2><?= htmlspecialchars($pelicula['nombre']) ?></h2>
                    <div class="meta-info">
                        <p><strong>Género:</strong> <?= htmlspecialchars($pelicula['genero']) ?></p>
                        <p><strong>Duración:</strong> <?= htmlspecialchars($pelicula['duracion']) ?> min</p>
                        <p><strong>Director:</strong> <?= htmlspecialchars($pelicula['director']) ?></p>
                    </div>
                    <p class="sinopsis"><?= nl2br(htmlspecialchars($pelicula['sinopsis'])) ?></p>
                </div>
            </div>

            <div class="funciones-placeholder">
                <h3>Funciones</h3>
                <?php
                $queryFunciones = "
                    SELECT f.FechaHora, s.Nombre AS sala, s.Tipo_pantalla, c.Nombre_cine,
                           f.Id_Pelicula, f.Id_Sala
                    FROM Funcion f
                    INNER JOIN Sala s ON f.Id_Sala = s.idSala
                    INNER JOIN Cine c ON s.Cine_idCine = c.idCine
                    WHERE f.Id_Pelicula = :idPelicula AND c.idCine = :cineID
                    ORDER BY f.FechaHora
                ";
                $stmtFunciones = $conn->prepare($queryFunciones);
                $stmtFunciones->execute([
                    ':idPelicula' => $pelicula['idpelicula'],
                    ':cineID' => $cineSeleccionado
                ]);
                $funciones = $stmtFunciones->fetchAll(PDO::FETCH_ASSOC);

                if ($funciones):
                    echo '<ul class="lista-funciones">';
                    foreach ($funciones as $f) {
                        $fecha = date("d-m-Y H:i", strtotime($f['fechahora']));
                        $url = "../Boleteria.php?pelicula=" . urlencode($f['id_pelicula']) .
                               "&sala=" . urlencode($f['id_sala']) .
                               "&fecha=" . urlencode($f['fechahora']);

                        echo "<li>
                                <strong>{$fecha}</strong><br>
                                {$f['sala']} ({$f['tipo_pantalla']})<br>
                                <a href='{$url}' class='boton-funcion'>Seleccionar Butacas</a>
                              </li>";
                    }
                    echo '</ul>';
                else:
                    echo '<div class="funciones-mensaje">Próximamente - Horarios disponibles</div>';
                endif;
                ?>
            </div>
        </div>
        <?php endforeach; } else {
                echo "<p>Selecciona un cine para ver su cartelera.</p>";
            } ?>
        <?php } catch (PDOException $e) {
            echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        } ?>
    </main>
</body>
</html>
