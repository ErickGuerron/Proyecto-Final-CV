<?php
include_once("database.php");
$conn = Database::getInstance()->getConnection();

$cedula     = $_POST["ID_EST"];
$nombre     = $_POST["NOM_EST"];
$apellido   = $_POST["APE_EST"];
$telefono   = $_POST["TEL_EST"];
$correo     = $_POST["COR_EST"];
$direccion  = $_POST["DIR_EST"];
$fechaNac   = $_POST["FEC_NAC"];     
$fechaCreat = date("Y-m-d");          

$sqlInsert = "
    INSERT INTO ESTUDIANTES (
        ID_EST,
        NOM_EST,
        APE_EST,
        TEL_EST,
        COR_EST,
        DIR_EST,
        FEC_NAC,
        FEC_CRE
    ) VALUES (
        '$cedula',
        '$nombre',
        '$apellido',
        '$telefono',
        '$correo',
        '$direccion',
        '$fechaNac',
        '$fechaCreat' 
    )
";

try {
    $filasAfectadas = $conn->exec($sqlInsert); 
    echo json_encode("Se insertó el estudiante");
} catch (PDOException $e) {
    echo json_encode("Error: " . $e->getMessage());
}
