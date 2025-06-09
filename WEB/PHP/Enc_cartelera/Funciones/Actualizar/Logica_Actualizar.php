<?php
include_once("../../../../CONNECTION/conexion.php");
echo '<pre>';
var_dump($_POST);
echo '</pre>';
// die();  // Descomenta si quieres parar aquí y ver el volcado


if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    //Tomamos los datos de los inputs
    $id_pelicula = $_POST['id_pelicula'] ?? '';
    $id_sala = $_POST['id_sala'] ?? '';
    $fechahora_original = $_POST['fechahora_original'] ?? '';
    $fechahora_nueva = $_POST['fechahora_nueva'] ?? '';

    //Validamos los datos.

    
    if (
        empty($id_pelicula)       ||
        empty($id_sala)   ||
        empty($fechahora_original) ||
        empty($fechahora_nueva)
    ) {
        die("Faltan datos obligatorios.");
    }

    // Convertir nueva fecha/hora al formato correcto
    $fechahora_nueva = date('Y-m-d H:i:s', strtotime($fechahora_nueva));

    try {
        // Preparar consulta de actualización
        $query = "UPDATE Funcion 
                  SET FechaHora = :nueva_fecha
                  WHERE Id_Pelicula = :id_pelicula 
                    AND Id_Sala = :id_sala 
                    AND FechaHora = :fecha_original";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':nueva_fecha', $fechahora_nueva);
        $stmt->bindParam(':id_pelicula', $id_pelicula, PDO::PARAM_INT);
        $stmt->bindParam(':id_sala', $id_sala, PDO::PARAM_INT);
        $stmt->bindParam(':fecha_original', $fechahora_original);

        $stmt->execute();

        // Redirigir al listado con un indicador de éxito
        header("Location: ../../Funciones.php?actualizado=1");
        exit;
    } catch (PDOException $e) {
        die("Error al actualizar función: " . htmlspecialchars($e->getMessage()));
    }
} else {
    // Si no se accedió por POST, redirigir de vuelta
    header("Location: ../../Funciones.php?actualizado=1");
    exit;
}