<?php
include_once("database.php");

header('Content-Type: application/json; charset=utf-8');

try {
    $conn = Database::getInstance()->getConnection();

    // El ID de la inscripción viene por GET, los datos a actualizar por POST
    $id_ins = $_GET["ID_INS"] ?? null;
    $id_est_ins = $_POST["ID_EST_INS"] ?? null;
    $id_cur_ins = $_POST["ID_CUR_INS"] ?? null;

    // Validaciones mínimas
    if (
        empty($id_ins) ||
        empty($id_est_ins) ||
        empty($id_cur_ins)
    ) {
        http_response_code(400);
        echo json_encode([
            "ok"      => false,
            "mensaje" => "Todos los campos son obligatorios."
        ]);
        exit;
    }

    $sqlUpdate = "
        UPDATE INSCRIPCIONES
        SET
            ID_EST_INS = :id_est_ins,
            ID_CUR_INS = :id_cur_ins
        WHERE ID_INS = :id_ins
    ";

    $stmt = $conn->prepare($sqlUpdate);
    $stmt->execute([
        ':id_est_ins' => $id_est_ins,
        ':id_cur_ins' => $id_cur_ins,
        ':id_ins'     => $id_ins,
    ]);

    if ($stmt->rowCount() === 0) {
        echo json_encode([
            "ok"      => false,
            "mensaje" => "No se actualizó ningún registro. Verifique el ID de la inscripción o si los datos son iguales a los actuales."
        ]);
        exit;
    }

    echo json_encode([
        "ok"      => true,
        "mensaje" => "Se actualizó la inscripción correctamente."
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "ok"      => false,
        "mensaje" => "Error al actualizar la inscripción.",
        "detalle" => $e->getMessage()
    ]);
}
