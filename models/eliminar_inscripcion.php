<?php
include_once("database.php");

header('Content-Type: application/json; charset=utf-8');

try {
    $conn = Database::getInstance()->getConnection();

    $id_ins = $_POST['ID_INS'] ?? $_POST['id_ins'] ?? null;

    // Validación mínima
    if (empty($id_ins)) {
        http_response_code(400);
        echo json_encode([
            'success'      => false,
            'mensaje' => 'El identificador de la inscripción (ID_INS) es obligatorio.'
        ]);
        exit;
    }

    $sqlDelete = "
        DELETE FROM INSCRIPCIONES
        WHERE ID_INS = :id_ins
    ";

    $stmt = $conn->prepare($sqlDelete);
    $stmt->execute([
        ':id_ins' => $id_ins,
    ]);

    if ($stmt->rowCount() === 0) {
        // No se encontró el registro
        echo json_encode([
            'success'      => false,
            'mensaje' => 'No se eliminó ningún registro. Verifique el ID de la inscripción.'
        ]);
        exit;
    }

    echo json_encode([
        'success'      => true,
        'mensaje' => 'Inscripción eliminada correctamente.'
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success'      => false,
        'mensaje' => 'Error al eliminar la inscripción.',
        'detalle' => $e->getMessage() // Puedes omitir "detalle" en producción
    ]);
}
