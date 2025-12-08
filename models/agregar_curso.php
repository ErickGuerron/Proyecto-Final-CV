<?php
include_once("database.php");
$conn = Database::getInstance()->getConnection();

header('Content-Type: application/json; charset=utf-8');

try {
    // Obtener el nombre del curso del POST
    $nom_cur = $_POST["NOM_CUR"];
    $des_cur = $_POST["DES_CUR"];

    // Validar si el curso ya existe por NOM_CUR (insensible a mayúsculas/minúsculas)
    $sqlCheck = "SELECT COUNT(*) FROM CURSOS WHERE LOWER(NOM_CUR) = LOWER(:nom_cur)";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bindParam(':nom_cur', $nom_cur);
    $stmtCheck->execute();
    $count = $stmtCheck->fetchColumn();

    if ($count > 0) {
        http_response_code(409); // Conflict
        echo json_encode([
            "success" => false,
            "errorMsg" => "Error: Ya existe un curso con el nombre '" . $nom_cur . "'."
        ]);
        exit();
    }

    // 1. Obtener el máximo ID_CUR
    $stmtMax = $conn->query("SELECT MAX(ID_CUR) as max_id FROM CURSOS");
    $max_id_row = $stmtMax->fetch(PDO::FETCH_ASSOC);
    $max_id = $max_id_row['max_id'];

    $new_id_num = 1;
    if ($max_id) {
        // 2. Extraer el número, ej: de "CUR-000003" a 3
        $num_part = (int) substr($max_id, 4);
        // 3. Incrementar
        $new_id_num = $num_part + 1;
    }

    // 4. Formatear el nuevo ID, ej: "CUR-000004"
    $new_id_cur = 'CUR-' . str_pad($new_id_num, 6, '0', STR_PAD_LEFT);

    // 5. Insertar con el nuevo ID
    $sqlInsert = "
        INSERT INTO CURSOS (
            ID_CUR,
            NOM_CUR,
            DES_CUR,
            FEC_CRE
        ) VALUES (
            :id_cur,
            :nom_cur,
            :des_cur,
            CURDATE()
        )
    ";

    $stmt = $conn->prepare($sqlInsert);
    $stmt->bindParam(':id_cur', $new_id_cur);
    $stmt->bindParam(':nom_cur', $nom_cur);
    $stmt->bindParam(':des_cur', $des_cur);
    $stmt->execute();
    
    echo json_encode([
        "success" => true,
        "mensaje" => "Curso insertado correctamente con el ID: " . $new_id_cur
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "errorMsg" => "Error al insertar el curso: " . $e->getMessage()
    ]);
}
