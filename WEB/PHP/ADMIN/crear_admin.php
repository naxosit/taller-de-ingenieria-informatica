<?php
require_once '../../CONNECTION/conexion.php';

//Insertamos al primer superusuario con el rol admin.
$RUT = '21445918-2';
$Nombre = 'Admin';
$Apellido = 'General';
$Correo_electronico = 'admin@cine.com';
$Rol = 'admin';
$Contraseña = 'admin123';

try {
    // Verifica si ya existe
    $stmt = $conn->prepare("SELECT * FROM Perfil WHERE RUT = :rut");
    $stmt->bindParam(':rut', $RUT);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        $conn->beginTransaction();

        // Insertar el perfil si no existe
        $stmt = $conn->prepare("INSERT INTO Perfil (RUT, Nombre, Apellido, Correo_electronico, Rol) VALUES (:rut, :nombre, :apellido, :correo, :rol)");
        $stmt->execute([
            ':rut' => $RUT,
            ':nombre' => $Nombre,
            ':apellido' => $Apellido,
            ':correo' => $Correo_electronico,
            ':rol' => $Rol
        ]);

        // Insertar contraseña en texto plano
        $stmt = $conn->prepare("INSERT INTO Contraseña (RUT, Contraseña) VALUES (:rut, :pass)");
        $stmt->execute([
            ':rut' => $RUT,
            ':pass' => $Contraseña
        ]);

        $conn->commit();
        echo "Admin creado con éxito.";
    } else {
        echo "El admin ya existe.";
    }
} catch (PDOException $e) {
    $conn->rollBack();
    echo "Error: " . $e->getMessage();
}
?>
