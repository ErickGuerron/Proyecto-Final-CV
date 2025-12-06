<?php
    session_start();

    include_once("controllers/controller.php");
    
    $controller = new EnlacesPaginaController();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->loginController();
    } else {
        $controller->plantilla();
    }
?>