<?php
include_once "./models/redirect.php";
class EnlacesPaginaController
{   
    public function plantilla()
    {
        include "./views/template.php";
    }
    
    public function enlacesPaginaController()
    {
        if (isset($_GET["accion"])) {
            $enlacesController = $_GET["accion"];
        } else 
        {
            $enlacesController="auth";
        }
        $respuesta=EnlacesPagina::enlacesPaginasModel($enlacesController);
        include $respuesta;
    }
}
