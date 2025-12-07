<?php
include_once("database.php");
$conn = Database::getInstance()->getConnection();

$id_est_ins = $_POST["ID_EST_INS"];
$id_cur_ins = $_POST["ID_CUR_INS"];

$sqlInsert = "
    INSERT INTO INSCRIPCIONES (
        ID_EST_INS,
        ID_CUR_INS,
        FEC_INS
    ) VALUES (
        :id_est_ins,
        :id_cur_ins,
        DATE(CONVERT_TZ(UTC_TIMESTAMP(), 'UTC', 'America/Guayaquil'))
    )
";

try {
    $stmt = $conn->prepare($sqlInsert);
    $stmt->bindParam(':id_est_ins', $id_est_ins);
    $stmt->bindParam(':id_cur_ins', $id_cur_ins);
    $stmt->execute();
    
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        "success" => true,
        "mensaje" => "Inscripción insertada correctamente."
    ]);
} catch (PDOException $e) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "errorMsg" => "Error al insertar la inscripción: " . $e->getMessage()
    ]);
}
