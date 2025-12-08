<?php
include_once "./models/redirect.php";
include_once "./models/database.php";
include_once "./models/user.php";
include_once "./models/estudiante.php";

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
        $restricted = ["servicios", "nosotros", "agregar_estudiante"];

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
            if ($action === "nosotros" && !in_array($userRole, ['ADMIN', 'SECRETARIO'])) {
                $_SESSION['error'] = "Acceso denegado.";
                header('Location: index.php?action=inicio');
                exit();
            }
            if ($action === "agregar_estudiante" && $userRole !== 'SECRETARIO') {
                $_SESSION['error'] = "Acceso denegado. Solo SECRETARIO puede acceder.";
                header('Location: index.php?action=inicio');
                exit();
            }
        }

        if (in_array($action, ["agregar_estudiante", "servicios", "nosotros"]) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom_est'])) {
            $this->agregarEstudianteController();
            return;
        }

        $respuesta=EnlacesPagina::enlacesPaginasModel($action);
        
        include $respuesta;
    }

    public function agregarEstudianteController()
    {
        $estudianteModel = new Estudiante();
        $datos = [
            'NOM_EST' => $_POST['nom_est'],
            'APE_EST' => $_POST['ape_est'],
            'TEL_EST' => $_POST['tel_est'],
            'COR_EST' => $_POST['cor_est'],
            'DIR_EST' => $_POST['dir_est'],
            'FEC_NAC' => $_POST['fec_nac']
        ];

        try {
            $id = $estudianteModel->agregarEstudiante($datos);
            $_SESSION['success_message'] = "Estudiante agregado exitosamente con ID: " . $id;
        } catch (Exception $e) {
            $_SESSION['error'] = "Error al agregar estudiante: " . $e->getMessage();
        }

        header('Location: index.php?action=' . $_GET['action']);
        exit();
    }

    public function loginController()
    {
        // Check if it's an AJAX request
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $referrer = $_POST['referrer'] ?? 'index.php?action=inicio';

        if (empty($email) || empty($password)) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Por favor, ingrese correo y contraseña.']);
                exit();
            }
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
            
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'redirect' => 'index.php?action=servicios']);
                exit();
            }
            header('Location: index.php?action=servicios');
            exit();
        } else {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas.']);
                exit();
            }
            $_SESSION['error'] = "Credenciales incorrectas.";
            header('Location: ' . $referrer . '&login_error=1');
            exit();
        }
    }

    public function logoutController()
    {
        session_unset();
        session_destroy();
        header("Location: index.php?action=inicio");
        exit();
    }
}
