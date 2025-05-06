<?php
require_once __DIR__ . '/../../../../CONNECTION/conexion.php';

if (!$conn) {
    echo "<option value='' disabled>-- Sin conexión a DB --</option>";
    exit;
}

try {
    // Ejecutar la consulta usando PDO
    $stmt = $conn->prepare("SELECT idcine, nombre_cine FROM cine ORDER BY nombre_cine");
    $stmt->execute();

    // Verificar si hay resultados
    $cines = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($cines) > 0) {
        foreach ($cines as $cine) {
            echo "<option value='" . htmlspecialchars($cine['idcine']) . "'>"
                . htmlspecialchars($cine['nombre_cine']) . "</option>";
        }
    } else {
        echo "<option value='' disabled>-- No hay cines --</option>";
    }
} catch (PDOException $e) {
    echo "<option value='' disabled>-- Error en la consulta --</option>";
    exit;
}
?>
