<?php
    class EnlacesPagina{
        public static function enlacesPaginasModel($action){
            if($action == "inicio" || $action == "servicios" || $action == "nosotros" || $action == "contactanos" || $action == "agregar_estudiante"){
                $modulo = "views/".$action.".php";
            } else {
                $modulo="views/inicio.php";
            }
            return $modulo;
        }
    }
?>