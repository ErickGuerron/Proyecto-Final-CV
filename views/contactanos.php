<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contáctanos - Tech Indigo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/contactanos.css">
</head>
<body>


    <!-- BACKGROUND -->
    <div class="bg-shape-wrapper">
        <div class="shape-blob blob-1"></div>
        <div class="shape-blob blob-2"></div>
        <div class="bg-grid-pattern"></div>
    </div>

    <!-- CONTENIDO PRINCIPAL -->
    <main style="padding-top: 100px; padding-bottom: 80px;">
        <div class="container">
            <!-- Header -->
            <div class="text-center mb-5">
                <span class="badge-custom mb-3">NUESTRO EQUIPO</span>
                <h1 class="display-4 mb-3">Conoce a los Desarrolladores</h1>
                <p class="lead text-muted-custom">El talento detrás de Tech Indigo</p>
            </div>

            <!-- Cards de Desarrolladores -->
            <div class="row g-4" id="teamCardsContainer">
                <!-- Las tarjetas se generarán dinámicamente aquí -->
            </div>
        </div>
    </main>

    <!-- MODAL -->
    <div class="modal fade" id="developerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" id="modalContent">
                    <!-- Contenido dinámico del modal -->
                </div>
            </div>
        </div>
    </div>

   

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/contact-script.js"></script>
</body>
</html>