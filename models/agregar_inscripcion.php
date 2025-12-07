<?php
include_once("database.php");
$conn = Database::getInstance()->getConnection();

header('Content-Type: application/json; charset=utf-8');

$id_est_ins = $_POST["ID_EST_INS"];
$id_cur_ins = $_POST["ID_CUR_INS"];
// FEC_INS se generará automáticamente en el backend

try {
    // Validar si la inscripción ya existe
    $sqlCheck = "SELECT COUNT(*) FROM INSCRIPCIONES WHERE ID_EST_INS = :id_est_ins AND ID_CUR_INS = :id_cur_ins";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bindParam(':id_est_ins', $id_est_ins);
    $stmtCheck->bindParam(':id_cur_ins', $id_cur_ins);
    $stmtCheck->execute();
    $count = $stmtCheck->fetchColumn();

    if ($count > 0) {
        http_response_code(409); // Conflict
        echo json_encode([
            "success" => false,
            "errorMsg" => "Error: Este estudiante ya está inscrito en este curso."
        ]);
        exit();
    }

    $sqlInsert = "
        INSERT INTO INSCRIPCIONES (
            ID_EST_INS,
            ID_CUR_INS,
            FEC_INS
        ) VALUES (
            :id_est_ins,
            :id_cur_ins,
            CURDATE()
        )
    ";

    $stmt = $conn->prepare($sqlInsert);
    $stmt->bindParam(':id_est_ins', $id_est_ins);
    $stmt->bindParam(':id_cur_ins', $id_cur_ins);
    $stmt->execute();
    
    echo json_encode([
        "success" => true,
        "mensaje" => "Inscripción creada correctamente."
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "errorMsg" => "Error al crear la inscripción: " . $e->getMessage()
    ]);
}
