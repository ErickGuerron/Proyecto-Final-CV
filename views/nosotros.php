<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión Estudiantil - Tech Indigo</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Tu Hoja de Estilos Personalizada -->
    <link href="assets/css/tabla.css" rel="stylesheet">
</head>

<body>
    <?php 
        // Simulación de sesión
        if(!isset($_SESSION)) { session_start(); }
        $rol = isset($_SESSION['user']['ROL_USU']) ? $_SESSION['user']['ROL_USU'] : 'GUEST'; 
    ?>

    <!-- Fondo Decorativo con Blobs -->
    <div class="bg-shape-wrapper">
        <div class="shape-blob blob-1"></div>
        <div class="shape-blob blob-2"></div>
    </div>

    <div class="container my-5">
        
        <!-- Encabezado / Tarjeta Informativa Mejorada -->
        <div class="card card-custom p-4 mb-4">
            <div class="d-flex align-items-center justify-content-between">
                <div class="flex-grow-1">
                    <?php if ($rol === 'ADMIN' || $rol === 'SECRETARIO'): ?>
                        <div class="d-flex align-items-center mb-2">
                            <div class="icon-box-indigo me-3">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <div>
                                <h2 class="mb-1">Gestión Institucional</h2>
                                <span class="badge badge-custom">
                                    <?php echo $rol; ?>
                                </span>
                            </div>
                        </div>
                        <p class="text-muted mb-0 ps-1">
                            <i class="fas fa-info-circle me-2"></i>
                            <?php echo ($rol === 'SECRETARIO') ? 'Panel de administración académica y control de estudiantes.' : 'Visualización de reportes y métricas institucionales.'; ?>
                        </p>
                    <?php else: ?>
                        <div class="d-flex align-items-center mb-2">
                            <div class="icon-box-indigo me-3">
                                <i class="fas fa-university"></i>
                            </div>
                            <div>
                                <h2 class="mb-1">Sobre Tech Indigo</h2>
                                <span class="badge badge-solid">Institución Educativa</span>
                            </div>
                        </div>
                        <p class="text-muted mb-0 ps-1">
                            <i class="fas fa-lightbulb me-2"></i>
                            Formando el futuro con tecnología y excelencia académica.
                        </p>
                    <?php endif; ?>
                </div>
                <div class="d-none d-lg-block">
                    <!-- Ilustración decorativa mejorada -->
                    <div style="position: relative; width: 100px; height: 100px;">
                        <i class="fas fa-university fa-4x" style="color: rgba(87, 92, 188, 0.1); position: absolute; top: 0; left: 0;"></i>
                        <i class="fas fa-users fa-2x" style="color: rgba(101, 198, 142, 0.3); position: absolute; bottom: 10px; right: 10px;"></i>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($rol === 'SECRETARIO' || $rol === 'ADMIN'): ?>
            
            <!-- Barra de Herramientas Mejorada -->
            <div class="card card-custom mb-4">
                <div class="card-body toolbar-section">
                    <div class="row align-items-center g-3">
                        <!-- Botones de Acción -->
                        <div class="col-md-7 col-lg-6">
                            <div class="d-flex flex-wrap gap-2">
                                <?php if ($rol === 'SECRETARIO'): ?>
                                    <button class="btn btn-success text-white px-4 rounded-pill" onclick="openModal('new')">
                                        <i class="fas fa-plus-circle me-2"></i>Nuevo Estudiante
                                    </button>
                                <?php endif; ?>
                                
                                <button class="btn btn-outline-secondary rounded-pill px-3" onclick="generarReporte()" title="Generar reporte PDF">
                                    <i class="fas fa-file-pdf me-1"></i> Reporte
                                </button>
                                <button class="btn btn-outline-info rounded-pill px-3" onclick="verEstadisticas()" title="Ver estadísticas">
                                    <i class="fas fa-chart-line me-1"></i> Stats
                                </button>
                            </div>
                        </div>

                        <!-- Buscador Estilizado Mejorado -->
                        <div class="col-md-5 col-lg-6">
                            <div class="input-group">
                                <span class="input-group-text input-group-text-custom bg-white border-end-0">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input 
                                    type="text" 
                                    id="searchInput" 
                                    class="form-control search-input border-start-0 ps-0" 
                                    placeholder="Buscar por Cédula o Nombre..."
                                    autocomplete="off"
                                    aria-label="Buscar estudiante">
                            </div>
                            <small class="text-muted ms-2 d-block mt-1">
                                <i class="fas fa-keyboard me-1"></i>Presiona ESC para limpiar
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla Tech Indigo Mejorada -->
            <div class="card card-custom">
                <div class="card-body p-0">
                    <!-- Encabezado de Tabla (Opcional: contador de registros) -->
                    <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom">
                        <h5 class="mb-0 fw-bold" style="color: var(--color-primary-indigo);">
                            <i class="fas fa-table me-2"></i>Registro de Estudiantes
                        </h5>
                        <span id="recordCount" class="badge" style="background-color: rgba(87, 92, 188, 0.15); color: var(--color-primary-indigo); font-size: 0.9rem;">
                            <i class="fas fa-users me-1"></i>Cargando...
                        </span>
                    </div>

                    <div class="table-responsive table-wrapper">
                        <table class="table table-custom mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-id-card me-2"></i>Cédula</th>
                                    <th><i class="fas fa-user me-2"></i>Nombre</th>
                                    <th><i class="fas fa-user-tag me-2"></i>Apellido</th>
                                    <th><i class="fas fa-phone me-2"></i>Teléfono</th>
                                    <th><i class="fas fa-envelope me-2"></i>Correo</th>
                                    <th><i class="fas fa-map-marker-alt me-2"></i>Dirección</th>
                                    <th><i class="fas fa-calendar-alt me-2"></i>Fecha Nac.</th>
                                    <?php if ($rol === 'SECRETARIO'): ?>
                                        <th class="text-end"><i class="fas fa-cog me-2"></i>Acciones</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <!-- Datos inyectados por JS -->
                                <tr>
                                    <td colspan="8" class="text-center py-5 loading-state">
                                        <i class="fas fa-spinner fa-spin fa-2x mb-3" style="color: var(--color-primary-indigo);"></i>
                                        <p class="mb-0 text-muted fw-semibold">Cargando información...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer de Tabla (Info adicional) -->
                    <div class="px-4 py-3 bg-light border-top">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                <?php if ($rol === 'SECRETARIO'): ?>
                                    Haz clic en una fila para ver detalles
                                <?php else: ?>
                                    Vista de solo lectura
                                <?php endif; ?>
                            </small>
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                Última actualización: <span id="lastUpdate">--:--</span>
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección de Tips (Opcional) -->
            <div class="mt-4">
                <div class="alert alert-info border-0 shadow-sm" style="border-left: 4px solid var(--color-primary-indigo) !important; background-color: rgba(87, 92, 188, 0.05);">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-lightbulb fa-2x me-3" style="color: var(--color-accent-orange);"></i>
                        <div>
                            <h6 class="alert-heading fw-bold mb-2" style="color: var(--color-primary-indigo);">
                                💡 Tips de uso
                            </h6>
                            <ul class="mb-0 small">
                                <li>Usa el buscador para filtrar por cédula o nombre en tiempo real</li>
                                <li>Presiona <kbd>ESC</kbd> para limpiar la búsqueda rápidamente</li>
                                <?php if ($rol === 'SECRETARIO'): ?>
                                    <li>Los botones de acción aparecen al hacer hover sobre las filas</li>
                                    <li>La cédula no puede ser modificada una vez registrado el estudiante</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- Vista para usuarios no autorizados -->
            <div class="card card-custom text-center py-5">
                <div class="card-body">
                    <i class="fas fa-lock fa-4x mb-4" style="color: var(--color-text-muted); opacity: 0.3;"></i>
                    <h3 style="color: var(--color-primary-indigo);">Acceso Restringido</h3>
                    <p class="text-muted mb-4">No tienes permisos para acceder a esta sección.</p>
                    <a href="index.php" class="btn btn-outline-indigo">
                        <i class="fas fa-home me-2"></i>Volver al Inicio
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal Bootstrap Mejorado -->
    <div class="modal fade" id="studentModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                
                <!-- Header del Modal con Gradiente -->
                <div class="modal-header border-0" style="background: linear-gradient(135deg, rgba(87, 92, 188, 0.08), rgba(101, 198, 142, 0.05)); padding: 1.5rem;">
                    <div class="d-flex align-items-center">
                        <div class="icon-box-indigo me-3" style="width: 50px; height: 50px; font-size: 1.3rem;">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h5 class="modal-title fw-bold mb-0" id="modalTitle" style="color: var(--color-primary-indigo); font-size: 1.4rem;">
                            Estudiante
                        </h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <!-- Body del Modal -->
                <div class="modal-body p-4" style="background-color: #fafbfc;">
                    <form id="studentForm" novalidate>
                        
                        <!-- Cédula -->
                        <div class="form-floating mb-3">
                            <input 
                                type="text" 
                                name="ID_EST" 
                                id="ID_EST" 
                                class="form-control" 
                                placeholder="Cédula" 
                                required
                                maxlength="10"
                                pattern="[0-9]{10}"
                                autocomplete="off">
                            <label><i class="fas fa-id-card me-2"></i>Cédula de Identidad</label>
                            <div class="invalid-feedback">
                                Por favor ingrese una cédula válida (10 dígitos)
                            </div>
                        </div>

                        <!-- Nombre y Apellido -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input 
                                        type="text" 
                                        name="NOM_EST" 
                                        id="NOM_EST" 
                                        class="form-control" 
                                        placeholder="Nombre" 
                                        required
                                        pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+"
                                        autocomplete="given-name">
                                    <label><i class="fas fa-user me-2"></i>Nombre(s)</label>
                                    <div class="invalid-feedback">
                                        El nombre es requerido
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input 
                                        type="text" 
                                        name="APE_EST" 
                                        id="APE_EST" 
                                        class="form-control" 
                                        placeholder="Apellido" 
                                        required
                                        pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+"
                                        autocomplete="family-name">
                                    <label><i class="fas fa-user-tag me-2"></i>Apellido(s)</label>
                                    <div class="invalid-feedback">
                                        El apellido es requerido
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Teléfono y Fecha de Nacimiento -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input 
                                        type="tel" 
                                        name="TEL_EST" 
                                        id="TEL_EST" 
                                        class="form-control" 
                                        placeholder="Teléfono" 
                                        required
                                        pattern="[0-9]{10}"
                                        maxlength="10"
                                        autocomplete="tel">
                                    <label><i class="fas fa-phone me-2"></i>Teléfono</label>
                                    <div class="invalid-feedback">
                                        Ingrese un teléfono válido (10 dígitos)
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input 
                                        type="date" 
                                        name="FEC_NAC" 
                                        id="FEC_NAC" 
                                        class="form-control" 
                                        required
                                        max="<?php echo date('Y-m-d', strtotime('-5 years')); ?>"
                                        autocomplete="bday">
                                    <label><i class="fas fa-calendar-alt me-2"></i>Fecha de Nacimiento</label>
                                    <div class="invalid-feedback">
                                        Seleccione una fecha válida
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Correo -->
                        <div class="form-floating mb-3">
                            <input 
                                type="email" 
                                name="COR_EST" 
                                id="COR_EST" 
                                class="form-control" 
                                placeholder="Correo" 
                                required
                                autocomplete="email">
                            <label><i class="fas fa-envelope me-2"></i>Correo Electrónico</label>
                            <div class="invalid-feedback">
                                Ingrese un correo electrónico válido
                            </div>
                        </div>

                        <!-- Dirección -->
                        <div class="form-floating mb-3">
                            <input 
                                type="text" 
                                name="DIR_EST" 
                                id="DIR_EST" 
                                class="form-control" 
                                placeholder="Dirección" 
                                required
                                autocomplete="street-address">
                            <label><i class="fas fa-map-marker-alt me-2"></i>Dirección Domiciliaria</label>
                            <div class="invalid-feedback">
                                La dirección es requerida
                            </div>
                        </div>

                    </form>
                </div>

                <!-- Footer del Modal -->
                <div class="modal-footer border-0 bg-light" style="padding: 1.5rem;">
                    <button type="button" class="btn btn-light text-muted px-4 rounded-pill" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button 
                        type="button" 
                        class="btn btn-primary px-5 rounded-pill fw-bold" 
                        style="background: linear-gradient(135deg, var(--color-primary-indigo), var(--color-indigo-hover)); border: none; box-shadow: 0 4px 12px rgba(87, 92, 188, 0.3);" 
                        onclick="saveUser()">
                        <i class="fas fa-save me-2"></i>Guardar Datos
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Configuración PHP para JS -->
    <script>
        window.appConfig = {
            role: "<?php echo $rol; ?>"
        };

        // Actualizar timestamp de última actualización
        function updateTimestamp() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('es-EC', { 
                hour: '2-digit', 
                minute: '2-digit' 
            });
            const lastUpdateEl = document.getElementById('lastUpdate');
            if (lastUpdateEl) {
                lastUpdateEl.textContent = timeString;
            }
        }
        
        // Actualizar cada minuto
        updateTimestamp();
        setInterval(updateTimestamp, 60000);
    </script>
    
    <!-- Lógica Externa -->
    <script src="assets/js/table-actions.js"></script>

    <!-- Script adicional para contador de registros -->
    <script>
        // Observar cambios en la tabla para actualizar contador
        const observer = new MutationObserver(function() {
            const tbody = document.getElementById('tableBody');
            const recordCount = document.getElementById('recordCount');
            if (tbody && recordCount) {
                const rows = tbody.querySelectorAll('tr:not([class*="loading-state"]):not([class*="empty-state"])');
                const validRows = Array.from(rows).filter(row => !row.querySelector('td[colspan]'));
                const count = validRows.length;
                if (count > 0) {
                    recordCount.innerHTML = `<i class="fas fa-users me-1"></i>${count} estudiante${count !== 1 ? 's' : ''}`;
                } else {
                    recordCount.innerHTML = '<i class="fas fa-users me-1"></i>Sin registros';
                }
            }
        });

        // Iniciar observador cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
            const tbody = document.getElementById('tableBody');
            if (tbody) {
                observer.observe(tbody, { childList: true, subtree: true });
            }
        });
    </script>
</body>
</html>