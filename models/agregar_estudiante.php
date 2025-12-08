<?php
include_once("database.php");
$conn = Database::getInstance()->getConnection();

header('Content-Type: application/json; charset=utf-8');

$cedula     = $_POST["ID_EST"];
$nombre     = $_POST["NOM_EST"];
$apellido   = $_POST["APE_EST"];
$telefono   = $_POST["TEL_EST"];
$correo     = $_POST["COR_EST"];
$direccion  = $_POST["DIR_EST"];
$fechaNac   = $_POST["FEC_NAC"];

try {
    // Validar si el estudiante ya existe por ID_EST
    $sqlCheck = "SELECT COUNT(*) FROM ESTUDIANTES WHERE ID_EST = :cedula";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bindParam(':cedula', $cedula);
    $stmtCheck->execute();
    $count = $stmtCheck->fetchColumn();

    if ($count > 0) {
        http_response_code(409); // Conflict
        echo json_encode([
            "success" => false,
            "errorMsg" => "Error: Ya existe un estudiante con la cédula " . $cedula . "."
        ]);
        exit();
    }

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
            CURDATE()
        )
    ";

    $stmt = $conn->prepare($sqlInsert);
    $stmt->bindParam(':cedula', $cedula);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':apellido', $apellido);
    $stmt->bindParam(':telefono', $telefono);
    $stmt->bindParam(':correo', $correo);
    $stmt->bindParam(':direccion', $direccion);
    $stmt->bindParam(':fechaNac', $fechaNac);
    $stmt->execute();
    
    echo json_encode([
        "success" => true,
        "mensaje" => "Estudiante insertado correctamente."
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "errorMsg" => "Error al insertar el estudiante: " . $e->getMessage()
    ]);
}
