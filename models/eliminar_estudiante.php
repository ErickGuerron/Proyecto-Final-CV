<?php
include_once("database.php");

header('Content-Type: application/json; charset=utf-8');

try {
    $conn = Database::getInstance()->getConnection();

    $cedula = $_POST['ID_EST'] ?? $_POST['id_est'] ?? null;

    // Validación mínima
    if (empty($cedula)) {
        http_response_code(400);
        echo json_encode([
            'success'      => false,
            'mensaje' => 'El identificador del estudiante (ID_EST) es obligatorio.'
        ]);
        exit;
    }

    $sqlDelete = "
        DELETE FROM ESTUDIANTES
        WHERE ID_EST = :cedula
    ";

    $stmt = $conn->prepare($sqlDelete);
    $stmt->execute([
        ':cedula' => $cedula,
    ]);

    if ($stmt->rowCount() === 0) {
        // No se encontró el registro
        echo json_encode([
            'success'      => false,
            'mensaje' => 'No se eliminó ningún registro. Verifique el ID del estudiante.'
        ]);
        exit;
    }

    echo json_encode([
        'success'      => true,
        'mensaje' => 'Estudiante eliminado correctamente.'
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success'      => false,
        'mensaje' => 'Error al eliminar el estudiante.',
        'detalle' => $e->getMessage() // Puedes omitir "detalle" en producción
    ]);
}
