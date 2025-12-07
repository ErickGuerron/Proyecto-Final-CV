<?php
include_once("database.php");

header('Content-Type: application/json; charset=utf-8');

try {
    $conn = Database::getInstance()->getConnection();
    $conn->beginTransaction(); // Start transaction

    $id_cur = $_POST['ID_CUR'] ?? $_POST['id_cur'] ?? null;

    // Validación mínima
    if (empty($id_cur)) {
        http_response_code(400);
        echo json_encode([
            'success'      => false,
            'mensaje' => 'El identificador del curso (ID_CUR) es obligatorio.'
        ]);
        exit;
    }

    // 1. Eliminar inscripciones asociadas
    $sqlDeleteInscripciones = "
        DELETE FROM INSCRIPCIONES
        WHERE ID_CUR_INS = :id_cur
    ";
    error_log("SQL DELETE INSCRIPCIONES: " . $sqlDeleteInscripciones);
    error_log("Param ID_CUR: " . $id_cur);
    $stmtInscripciones = $conn->prepare($sqlDeleteInscripciones);
    $stmtInscripciones->execute([
        ':id_cur' => $id_cur,
    ]);

    // 2. Eliminar el curso
    $sqlDeleteCurso = "
        DELETE FROM CURSOS
        WHERE ID_CUR = :id_cur
    ";
    error_log("SQL DELETE CURSOS: " . $sqlDeleteCurso);
    error_log("Param ID_CUR: " . $id_cur);
    $stmtCurso = $conn->prepare($sqlDeleteCurso);
    $stmtCurso->execute([
        ':id_cur' => $id_cur,
    ]);

    if ($stmtCurso->rowCount() === 0) {
        // If the course wasn't found, rollback and report
        $conn->rollBack();
        echo json_encode([
            'success'      => false,
            'mensaje' => 'No se eliminó ningún registro. Verifique el ID del curso.'
        ]);
        exit;
    }

    $conn->commit(); // Commit transaction if all successful

    echo json_encode([
        'success'      => true,
        'mensaje' => 'Curso y sus inscripciones asociadas eliminados correctamente.'
    ]);
} catch (PDOException $e) {
    $conn->rollBack(); // Rollback on error
    http_response_code(500);
    echo json_encode([
        'success'      => false,
        'mensaje' => 'Error al eliminar el curso y sus inscripciones.',
        'detalle' => $e->getMessage() // Puedes omitir "detalle" en producción
    ]);
}
