<?php
include_once("database.php");

header('Content-Type: application/json; charset=utf-8');

try {
    $conn = Database::getInstance()->getConnection();

    // Datos recibidos por POST
    $id_cur  = $_POST["ID_CUR"] ?? null;
    $nom_cur = $_POST["NOM_CUR"] ?? null;
    $des_cur = $_POST["DES_CUR"] ?? null;

    // Validaciones mínimas
    if (
        empty($id_cur) ||
        empty($nom_cur) ||
        empty($des_cur)
    ) {
        http_response_code(400);
        echo json_encode([
            "ok"      => false,
            "mensaje" => "Todos los campos son obligatorios."
        ]);
        exit;
    }

    $sqlUpdate = "
        UPDATE CURSOS
        SET
            NOM_CUR = :nom_cur,
            DES_CUR = :des_cur
        WHERE ID_CUR = :id_cur
    ";

    $stmt = $conn->prepare($sqlUpdate);
    $stmt->execute([
        ':nom_cur' => $nom_cur,
        ':des_cur' => $des_cur,
        ':id_cur'  => $id_cur,
    ]);

    if ($stmt->rowCount() === 0) {
        echo json_encode([
            "ok"      => false,
            "mensaje" => "No se actualizó ningún registro. Verifique el ID del curso o si los datos son iguales a los actuales."
        ]);
        exit;
    }

    echo json_encode([
        "ok"      => true,
        "mensaje" => "Se actualizó el curso correctamente."
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "ok"      => false,
        "mensaje" => "Error al actualizar el curso.",
        "detalle" => $e->getMessage()
    ]);
}
