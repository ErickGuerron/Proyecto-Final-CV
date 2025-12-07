<?php
// views/template.php
// session_start() is already called in index.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tech Indigo Académico</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- CSS Personalizado -->
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Fondo de Partículas/Blobs -->
    <div class="bg-shape-wrapper">
        <div class="bg-grid-pattern"></div>
        <div class="shape-blob blob-1"></div>
        <div class="shape-blob blob-2"></div>
    </div>

    <!-- NAVBAR GLASS -->
    <header class="sticky-top">
        <nav class="navbar navbar-expand-lg navbar-glass navbar-dark">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2" href="index.php?action=inicio">
                    <i class="bi bi-cpu-fill text-warning"></i> 
                    <span>Tech Indigo</span>
                </a>
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarMain">
                    <ul class="navbar-nav ms-auto align-items-center gap-1">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?action=inicio">Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?action=contactanos">Contáctanos</a>
                        </li>
                        
                        <?php if (isset($_SESSION['user'])): ?>
                            <?php if ($_SESSION['user']['ROL_USU'] === 'SECRETARIO' || $_SESSION['user']['ROL_USU'] === 'ADMIN'): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="index.php?action=servicios">Servicios</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="index.php?action=cursos">Cursos</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="index.php?action=inscripciones">Inscripciones</a>
                                </li>
                            <?php endif; ?>
                            <?php if ($_SESSION['user']['ROL_USU'] === 'SECRETARIO' || $_SESSION['user']['ROL_USU'] === 'ADMIN'): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="index.php?action=nosotros">Nosotros</a>
                                </li>
                            <?php endif; ?>
                            <li class="nav-item ms-lg-3 d-flex align-items-center">
                                <span class="navbar-text me-3 text-white">
                                    <i class="bi bi-person-circle me-1"></i>
                                    <?php echo htmlspecialchars($_SESSION['user']['ROL_USU']); ?>
                                </span>
                                <a href="index.php?action=logout" class="btn btn-outline-light btn-sm rounded-pill px-3">
                                    <i class="bi bi-box-arrow-right me-1"></i> Salir
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link locked" href="#" data-bs-toggle="modal" data-bs-target="#loginModal" title="Requiere autenticación">
                                    <i class="bi bi-lock-fill small"></i> Servicios
                                </a>
                            </li>
                             <li class="nav-item">
                                <a class="nav-link locked" href="#" data-bs-toggle="modal" data-bs-target="#loginModal" title="Requiere autenticación">
                                    <i class="bi bi-lock-fill small"></i> Nosotros
                                </a>
                            </li>
                            <li class="nav-item ms-lg-3">
                                <button class="btn btn-outline-light btn-sm rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#loginModal">
                                    Ingresar
                                </button>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- CONTENIDO DINÁMICO -->
    <main id="content-article" class="flex-grow-1">
        <?php
            // Display success/error messages
            if (isset($_SESSION['success_message'])) {
                echo '<div class="container mt-4"><div class="alert alert-success alert-dismissible fade show text-center" role="alert" id="auto-dismiss-alert">' . $_SESSION['success_message'] . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div></div>';
                unset($_SESSION['success_message']);
            }
            if (isset($_SESSION['error'])) {
                 echo '<div class="container mt-4"><div class="alert alert-danger text-center" role="alert">' . $_SESSION['error'] . '</div></div>';
                // Don't unset error on page load, unset it on successful login
            }

            $mvc = new EnlacesPaginaController();
            $mvc->enlacesPaginaController();
        ?>
    </main>

    <!-- FOOTER -->
    <footer class="footer-modern py-5 mt-auto">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-5">
                    <h5 class="fw-bold text-indigo mb-3">Tech Indigo Académico</h5>
                    <p class="text-muted small">
                        Formación tecnológica de vanguardia. Impulsamos tu carrera con programas certificados y mentores expertos.
                    </p>
                </div>
                <div class="col-lg-3 offset-lg-1">
                    <h6 class="fw-bold mb-3 text-dark-custom">Enlaces</h6>
                    <ul class="list-unstyled small text-muted d-flex flex-column gap-2">
                        <li><a href="index.php?action=inicio" class="text-decoration-none text-muted">Inicio</a></li>
                        <li><a href="index.php?action=contactanos" class="text-decoration-none text-muted">Contáctanos</a></li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h6 class="fw-bold mb-3 text-dark-custom">Contacto</h6>
                    <p class="small text-muted mb-1"><i class="bi bi-envelope me-2"></i>info@techindigo.edu</p>
                </div>
            </div>
            <div class="border-top mt-4 pt-3 text-center small text-muted">
                &copy; 2024 Tech Indigo. Todos los derechos reservados.
            </div>
        </div>
    </footer>

    <!-- MODAL LOGIN (Modular) -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <div class="modal-header border-bottom-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 pt-0">
                    <?php include "views/auth.php"; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-dismiss alert
        const autoDismissAlert = document.getElementById('auto-dismiss-alert');
        if (autoDismissAlert) {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(autoDismissAlert);
                bsAlert.close();
            }, 2000);
        }
    </script>
</body>
</html>