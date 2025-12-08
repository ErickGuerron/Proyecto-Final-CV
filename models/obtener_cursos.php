<?php 
require_once 'database.php';

header('Content-Type: application/json; charset=utf-8');

$sqlSelect = "SELECT * FROM CURSOS";

try {
    $conn = Database::getInstance()->getConnection();

    $stmt = $conn->query($sqlSelect);

    $result = $stmt->fetchAll();

    if (!$result || count($result) === 0) {
        echo json_encode([]);
        exit;
    }

    echo json_encode($result);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error'   => 'Error al consultar cursos.',
        'detalle' => $e->getMessage() 
    ]);
}
