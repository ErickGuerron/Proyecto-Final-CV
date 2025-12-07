<?php 
    if(!isset($_SESSION)) { session_start(); }
    // Aseguramos que la variable ROL exista y le quitamos espacios en blanco por si acaso
    $rol = isset($_SESSION['user']['ROL_USU']) ? trim($_SESSION['user']['ROL_USU']) : 'GUEST'; 
?>

<!-- Estilos de EasyUI -->
<link rel="stylesheet" type="text/css" href="assets/jquery/themes/default/easyui.css">
<link rel="stylesheet" type="text/css" href="assets/jquery/themes/icon.css">
<link rel="stylesheet" type="text/css" href="assets/jquery/themes/color.css">

<!-- Scripts de EasyUI -->
<script type="text/javascript" src="assets/jquery/jquery.min.js"></script>
<script type="text/javascript" src="assets/jquery/jquery.easyui.min.js"></script>
<script type="text/javascript" src="assets/jquery/locale/easyui-lang-es.js"></script>

<!-- Estilos para los formularios dentro de los modales -->
<style>
    .fitem { margin-bottom: 15px; }
    .fitem label { display: inline-block; width: 120px; font-weight: bold; }
    .fitem input { width: 260px; }
</style>

<?php include "views/modules/servicios_jquery.php"; ?>
