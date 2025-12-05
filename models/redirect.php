<?php
    class EnlacesPagina{
        public static function enlacesPaginasModel($enlacesModel){
            if($enlacesModel == "inicio" || $enlacesModel == "servicios" || $enlacesModel == "nosotros" || $enlacesModel == "contactanos"){
                $modulo = "views/".$enlacesModel.".php";
            } else {
                $modulo="views/inicio.php";
            }
            return $modulo;
        }
    }
?>