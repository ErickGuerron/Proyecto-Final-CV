<?php
include_once "./models/redirect.php";
include_once "./models/database.php";
include_once "./models/user.php";

class EnlacesPaginaController
{   
    public function plantilla()
    {
        include "views/template.php";
    }
    
    public function enlacesPaginaController()
    {
        $action = $_GET["action"] ?? "inicio";

        if ($action == "logout") {
            $this->logoutController();
            return; 
        }

        // Define access rules
        $allowedPublic = ["inicio", "contactanos"];
        $restricted = ["servicios", "nosotros"];

        if (in_array($action, $restricted)) {
            if (!isset($_SESSION['user'])) {
                $_SESSION['error'] = "Necesitas iniciar sesión para acceder a esta página.";
                header('Location: index.php?action=inicio&login_required=1');
                exit();
            }

            $userRole = $_SESSION['user']['ROL_USU'];
            if ($action === "servicios" && !in_array($userRole, ['ADMIN', 'SECRETARIO'])) {
                $_SESSION['error'] = "Acceso denegado.";
                header('Location: index.php?action=inicio');
                exit();
            }
            if ($action === "nosotros" && $userRole !== 'SECRETARIO') {
                $_SESSION['error'] = "Acceso denegado.";
                header('Location: index.php?action=inicio');
                exit();
            }
        }

        $respuesta=EnlacesPagina::enlacesPaginasModel($action);
        
        include $respuesta;
    }

    public function loginController()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $referrer = $_POST['referrer'] ?? 'index.php?action=inicio';


        if (empty($email) || empty($password)) {
            $_SESSION['error'] = "Por favor, ingrese correo y contraseña.";
            header('Location: ' . $referrer . '&login_error=1');
            exit();
        }

        $userModel = new User();
        $user = $userModel->getUserByEmail($email);

        if ($user && $password === $user['CON_USU']) {
            $_SESSION['user'] = $user;
            $_SESSION['success_message'] = "¡Inicio de sesión exitoso!";
            unset($_SESSION['error']);
            
            header('Location: index.php?action=servicios');
            exit();
        } else {
            $_SESSION['error'] = "Credenciales incorrectas.";
            header('Location: ' . $referrer . '&login_error=1');
            exit();
        }
    }

    public function logoutController()
    {
        session_unset();
        session_destroy();
        header("Location: index.php?action=inicio&msg=logout_success");
        exit();
    }
}
