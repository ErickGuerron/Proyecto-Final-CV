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

    // Validar si la combinación estudiante-curso ya existe en OTRA inscripción
    $sqlCheck = "SELECT COUNT(*) FROM INSCRIPCIONES WHERE ID_EST_INS = :id_est_ins AND ID_CUR_INS = :id_cur_ins AND ID_INS != :id_ins";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bindParam(':id_est_ins', $id_est_ins);
    $stmtCheck->bindParam(':id_cur_ins', $id_cur_ins);
    $stmtCheck->bindParam(':id_ins', $id_ins);
    $stmtCheck->execute();
    $count = $stmtCheck->fetchColumn();

    if ($count > 0) {
        http_response_code(409); // Conflict
        echo json_encode([
            "ok"      => false,
            "mensaje" => "Error: Ya existe otra inscripción con este estudiante en este curso."
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
        // Podría ser que no hubo cambios o que el ID_INS no existe.
        // Si no hubo cambios, no es un error, pero el mensaje debe reflejarlo.
        // Si el ID_INS no existe, el frontend debería manejarlo.
        echo json_encode([
            "ok"      => true, // Consideramos que está "ok" si no hubo cambios, no es un error
            "mensaje" => "La inscripción se actualizó correctamente (o no hubo cambios)."
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
