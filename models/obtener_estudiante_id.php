<?php
include_once "database.php";

header('Content-Type: application/json; charset=utf-8');

try {
    $conn = Database::getInstance()->getConnection();

    // Aceptar el parámetro tanto en mayúsculas como en minúsculas, por GET o POST
    $idEst = $_POST['ID_EST']  ?? $_POST['id_est']
          ?? $_GET['ID_EST']   ?? $_GET['id_est']
          ?? '';

    $idEst = trim($idEst);

    // SELECT base reutilizable
    $baseSql = "
        SELECT 
            ID_EST,
            NOM_EST,
            APE_EST,
            TEL_EST,
            COR_EST,
            DIR_EST,
            FEC_NAC
        FROM ESTUDIANTES
    ";

    if ($idEst !== '') {
        // Modo filtrado por ID, Nombre o Apellido (prefijo)
        $sql = $baseSql . " 
            WHERE ID_EST LIKE :id_est 
            OR NOM_EST LIKE :id_est
            OR APE_EST LIKE :id_est
            ORDER BY ID_EST";
        $stmt = $conn->prepare($sql);
        $searchTerm = '%' . $idEst . '%'; // Buscar en cualquier parte del nombre/apellido
        $stmt->execute([
            ':id_est' => $searchTerm
        ]);
    } else {
        // Modo listado completo
        $sql = $baseSql . " ORDER BY ID_EST";
        $stmt = $conn->query($sql);
    }

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Importante: se mantiene como array plano, sin 'success' ni 'rows'
    echo json_encode($rows, JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success'  => false,
        'errorMsg' => 'Error al obtener estudiantes.',
        'detalle'  => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
