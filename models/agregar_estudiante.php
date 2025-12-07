<?php
include_once("database.php");
$conn = Database::getInstance()->getConnection();

$cedula     = $_POST["ID_EST"];
$nombre     = $_POST["NOM_EST"];
$apellido   = $_POST["APE_EST"];
$telefono   = $_POST["TEL_EST"];
$correo     = $_POST["COR_EST"];
$direccion  = $_POST["DIR_EST"];
$fechaNac   = $_POST["FEC_NAC"];

$sqlInsert = "
    INSERT INTO ESTUDIANTES (
        ID_EST,
        NOM_EST,
        APE_EST,
        TEL_EST,
        COR_EST,
        DIR_EST,
        FEC_NAC,
        FEC_CRE
    ) VALUES (
        :cedula,
        :nombre,
        :apellido,
        :telefono,
        :correo,
        :direccion,
        :fechaNac,
        DATE(CONVERT_TZ(UTC_TIMESTAMP(), 'UTC', 'America/Guayaquil'))
    )
";

try {
    $stmt = $conn->prepare($sqlInsert);
    $stmt->bindParam(':cedula', $cedula);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':apellido', $apellido);
    $stmt->bindParam(':telefono', $telefono);
    $stmt->bindParam(':correo', $correo);
    $stmt->bindParam(':direccion', $direccion);
    $stmt->bindParam(':fechaNac', $fechaNac);
    $stmt->execute();
    
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        "success" => true,
        "mensaje" => "Estudiante insertado correctamente."
    ]);
} catch (PDOException $e) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "errorMsg" => "Error al insertar el estudiante: " . $e->getMessage()
    ]);
}
