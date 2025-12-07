<?php
include_once("database.php");

header('Content-Type: application/json; charset=utf-8');

try {
    $conn = Database::getInstance()->getConnection();

    // Datos recibidos por POST (mismos nombres que vienes usando)
    $cedula     = $_POST["ID_EST"]  ?? null;
    $nombre     = $_POST["NOM_EST"] ?? null;
    $apellido   = $_POST["APE_EST"] ?? null;
    $telefono   = $_POST["TEL_EST"] ?? null;
    $correo     = $_POST["COR_EST"] ?? null;
    $direccion  = $_POST["DIR_EST"] ?? null;
    $fechaNac   = $_POST["FEC_NAC"] ?? null;  // 'YYYY-MM-DD'

    // Validaciones mínimas
    if (
        empty($cedula) ||
        empty($nombre) ||
        empty($apellido) ||
        empty($telefono) ||
        empty($correo) ||
        empty($direccion) ||
        empty($fechaNac)
    ) {
        http_response_code(400);
        echo json_encode([
            "ok"      => false,
            "mensaje" => "Todos los campos son obligatorios."
        ]);
        exit;
    }

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaNac)) {
        http_response_code(400);
        echo json_encode([
            "ok"      => false,
            "mensaje" => "El formato de FEC_NAC debe ser YYYY-MM-DD."
        ]);
        exit;
    }

    $sqlUpdate = "
        UPDATE ESTUDIANTES
        SET
            NOM_EST = :nombre,
            APE_EST = :apellido,
            TEL_EST = :telefono,
            COR_EST = :correo,
            DIR_EST = :direccion,
            FEC_NAC = :fechaNac
        WHERE ID_EST = :cedula
    ";

    $stmt = $conn->prepare($sqlUpdate);
    $stmt->execute([
        ':nombre'    => $nombre,
        ':apellido'  => $apellido,
        ':telefono'  => $telefono,
        ':correo'    => $correo,
        ':direccion' => $direccion,
        ':fechaNac'  => $fechaNac,
        ':cedula'    => $cedula,
    ]);

    if ($stmt->rowCount() === 0) {
        echo json_encode([
            "ok"      => false,
            "mensaje" => "No se actualizó ningún registro. Verifique el ID del estudiante o si los datos son iguales a los actuales."
        ]);
        exit;
    }

    echo json_encode([
        "ok"      => true,
        "mensaje" => "Se actualizó el estudiante correctamente."
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "ok"      => false,
        "mensaje" => "Error al actualizar el estudiante.",
        "detalle" => $e->getMessage()
    ]);
}
