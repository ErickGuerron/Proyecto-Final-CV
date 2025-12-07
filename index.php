<?php
    // Habilita buffering para evitar "headers already sent" cuando se hacen redirecciones
    ob_start();
    session_start();

    include_once("controllers/controller.php");
    
    $controller = new EnlacesPaginaController();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->loginController();
    } else {
        $controller->plantilla();
    }

    // Vacía el buffer y envía todo el contenido
    ob_end_flush();
?>
