<?php
// Opcional pero recomendable si usas PHP 7+
declare(strict_types=1);

include_once "database.php";

header('Content-Type: application/json; charset=utf-8');

try {
    $conn = Database::getInstance()->getConnection();

    // Aceptar el parámetro tanto en mayúsculas como en minúsculas, por GET o POST
    $idCur = $_POST['ID_CUR']  ?? $_POST['id_cur']
          ?? $_GET['ID_CUR']   ?? $_GET['id_cur']
          ?? '';

    $idCur = is_string($idCur) ? trim($idCur) : '';

    // SELECT base reutilizable
    $baseSql = "
        SELECT 
            ID_CUR,
            NOM_CUR,
            DES_CUR,
            FEC_CRE
        FROM CURSOS
    ";

    if ($idCur !== '') {
        // Modo filtrado por ID o Nombre (prefijo)
        $sql = $baseSql . " 
            WHERE ID_CUR  LIKE :id_cur
               OR NOM_CUR LIKE :nom_cur
            ORDER BY ID_CUR";

        $stmt = $conn->prepare($sql);
        $searchTerm = '%' . $idCur . '%'; // Buscar en cualquier parte del ID/nombre

        // Usamos parámetros distintos para evitar problemas con HY093
        $stmt->execute([
            ':id_cur'  => $searchTerm,
            ':nom_cur' => $searchTerm
        ]);
    } else {
        // Modo listado completo
        $sql = $baseSql . " ORDER BY ID_CUR";
        $stmt = $conn->query($sql);
    }

    if ($stmt === false) {
        throw new PDOException('Error al ejecutar la consulta de cursos.');
    }

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Se mantiene como array plano, sin 'success' ni 'rows'
    echo json_encode($rows, JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success'  => false,
        'errorMsg' => 'Error al obtener cursos.',
        'detalle'  => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
