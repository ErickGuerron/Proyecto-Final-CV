<?php 
require_once 'database.php';

header('Content-Type: application/json; charset=utf-8');

$sqlSelect = "
    SELECT 
        i.ID_INS, 
        i.FEC_INS, 
        i.ID_EST_INS, 
        i.ID_CUR_INS,
        e.NOM_EST, 
        e.APE_EST,
        c.NOM_CUR
    FROM 
        INSCRIPCIONES i
    JOIN 
        ESTUDIANTES e ON i.ID_EST_INS = e.ID_EST
    JOIN 
        CURSOS c ON i.ID_CUR_INS = c.ID_CUR
";

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
        'error'   => 'Error al consultar inscripciones.',
        'detalle' => $e->getMessage() 
    ]);
}
