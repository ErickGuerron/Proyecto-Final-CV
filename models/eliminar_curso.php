<?php
include_once("database.php");

header('Content-Type: application/json; charset=utf-8');

try {
    $conn = Database::getInstance()->getConnection();

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

    $sqlDelete = "
        DELETE FROM CURSOS
        WHERE ID_CUR = :id_cur
    ";

    $stmt = $conn->prepare($sqlDelete);
    $stmt->execute([
        ':id_cur' => $id_cur,
    ]);

    if ($stmt->rowCount() === 0) {
        // No se encontró el registro
        echo json_encode([
            'success'      => false,
            'mensaje' => 'No se eliminó ningún registro. Verifique el ID del curso.'
        ]);
        exit;
    }

    echo json_encode([
        'success'      => true,
        'mensaje' => 'Curso eliminado correctamente.'
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success'      => false,
        'mensaje' => 'Error al eliminar el curso.',
        'detalle' => $e->getMessage() // Puedes omitir "detalle" en producción
    ]);
}
