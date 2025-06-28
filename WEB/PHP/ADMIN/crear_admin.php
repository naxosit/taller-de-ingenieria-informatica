<?php
require_once '../../CONNECTION/conexion.php';

$RUT = '21.445.918-2';
$Nombre = 'Admin';
$Apellido = 'General';
$Correo_electronico = 'admin@cine.com';
$Rol = 'admin';
$Contraseña = 'admin123';

try {
    // Verificar si el admin ya existe
    $stmt = $conn->prepare("SELECT 1 FROM Perfil WHERE RUT = :rut");
    $stmt->bindParam(':rut', $RUT);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        $conn->beginTransaction();

        // 1. Insertar primero el perfil
        $stmt = $conn->prepare("INSERT INTO Perfil (RUT, Nombre, Apellido, Correo_electronico, Rol) 
                                VALUES (:rut, :nombre, :apellido, :correo, :rol)");
        $stmt->execute([
            ':rut' => $RUT,
            ':nombre' => $Nombre,
            ':apellido' => $Apellido,
            ':correo' => $Correo_electronico,
            ':rol' => $Rol
        ]);

        // 2. Insertar la contraseña asociada al RUT
        $stmt = $conn->prepare("INSERT INTO Contraseña (ContraseñaUsuario, Rut) 
                                VALUES (:pass, :rut)");
        $stmt->execute([
            ':pass' => $Contraseña,
            ':rut' => $RUT
        ]);

        $conn->commit();
        echo "Admin creado con éxito.";
    } else {
        echo "El admin ya existe.";
    }
} catch (PDOException $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    echo "Error: " . $e->getMessage();
}
?>