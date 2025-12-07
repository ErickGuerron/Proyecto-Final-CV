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
        if(!isset($_SESSION)) { session_start(); }
        $rol = isset($_SESSION['user']['ROL_USU']) ? $_SESSION['user']['ROL_USU'] : 'GUEST'; 
    ?>

    <!-- Fondo Decorativo -->
    <div class="bg-shape-wrapper">
        <div class="shape-blob blob-1"></div>
        <div class="shape-blob blob-2"></div>
    </div>

    <!-- CONTENEDOR DE NOTIFICACIONES (TOASTS) -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
        <!-- Las notificaciones se inyectarán aquí dinámicamente -->
    </div>

    <div class="container my-5">
        
        <!-- Encabezado -->
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
                                <span class="badge badge-custom"><?php echo $rol; ?></span>
                            </div>
                        </div>
                        <p class="text-muted mb-0 ps-1">
                            <i class="fas fa-info-circle me-2"></i>
                            Panel de administración académica (Bootstrap).
                        </p>
                    <?php else: ?>
                        <div class="d-flex align-items-center mb-2">
                            <div class="icon-box-indigo me-3"><i class="fas fa-university"></i></div>
                            <div><h2 class="mb-1">Sobre Tech Indigo</h2></div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="d-none d-lg-block">
                    <div style="position: relative; width: 100px; height: 100px;">
                        <i class="fas fa-university fa-4x" style="color: rgba(87, 92, 188, 0.1); position: absolute; top: 0; left: 0;"></i>
                        <i class="fas fa-users fa-2x" style="color: rgba(101, 198, 142, 0.3); position: absolute; bottom: 10px; right: 10px;"></i>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($rol === 'SECRETARIO' || $rol === 'ADMIN'): ?>
            
            <!-- NAVEGACIÓN DE MÓDULOS -->
            <ul class="nav nav-pills mb-4 gap-2" id="moduleTabs">
                <li class="nav-item">
                    <button class="nav-link active rounded-pill px-4 fw-bold" onclick="loadModule('students')">
                        <i class="fas fa-user-graduate me-2"></i>Estudiantes
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link rounded-pill px-4 fw-bold" onclick="loadModule('courses')">
                        <i class="fas fa-book-open me-2"></i>Cursos
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link rounded-pill px-4 fw-bold" onclick="loadModule('enrollments')">
                        <i class="fas fa-clipboard-list me-2"></i>Inscripciones
                    </button>
                </li>
            </ul>

            <!-- Barra de Herramientas -->
            <div class="card card-custom mb-4">
                <div class="card-body toolbar-section">
                    <div class="row align-items-center g-3">
                        <div class="col-md-7 col-lg-6">
                            <div class="d-flex flex-wrap gap-2">
                                <?php if ($rol === 'SECRETARIO' || $rol === 'ADMIN'): ?>
                                    <button class="btn btn-success text-white px-4 rounded-pill" id="btnNew" onclick="openCreateModal()">
                                        <i class="fas fa-plus-circle me-2"></i>Nuevo Estudiante
                                    </button>
                                <?php endif; ?>
                                
                                <button class="btn btn-outline-secondary rounded-pill px-3" onclick="generarReporte()" title="Generar reporte PDF">
                                    <i class="fas fa-file-pdf me-1"></i> Reporte Estudiante
                                </button>
                                <button class="btn btn-outline-primary rounded-pill px-3" onclick="generarReporteListado()" title="Reporte de estudiantes">
                                    <i class="fas fa-users me-1"></i> Estudiantes/Cursos
                                </button>
                                <button class="btn btn-outline-info rounded-pill px-3" onclick="verEstadisticas()" title="Ver estadísticas">
                                    <i class="fas fa-chart-line me-1"></i> Stats
                                </button>
                            </div>
                        </div>

                        <div class="col-md-5 col-lg-6">
                            <div class="input-group">
                                <span class="input-group-text input-group-text-custom bg-white border-end-0">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" id="searchInput" class="form-control search-input border-start-0 ps-0" placeholder="Buscar...">
                            </div>
                            <small class="text-muted ms-2 d-block mt-1">
                                <i class="fas fa-keyboard me-1"></i>Presiona ESC para limpiar
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla Dinámica -->
            <div class="card card-custom">
                <div class="card-body p-0">
                    <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom">
                        <h5 class="mb-0 fw-bold" style="color: var(--color-primary-indigo);" id="tableTitle">
                            <i class="fas fa-table me-2"></i>Registro de Estudiantes
                        </h5>
                        <span id="recordCount" class="badge" style="background-color: rgba(87, 92, 188, 0.15); color: var(--color-primary-indigo); font-size: 0.9rem;">
                            <i class="fas fa-spinner fa-spin me-1"></i>Cargando...
                        </span>
                    </div>

                    <div class="table-responsive table-wrapper">
                        <table class="table table-custom mb-0 align-middle">
                            <thead id="tableHead">
                                <!-- Cabeceras Dinámicas -->
                            </thead>
                            <tbody id="tableBody">
                                <!-- Filas Dinámicas -->
                            </tbody>
                        </table>
                    </div>

                    <div class="px-4 py-3 bg-light border-top">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <small class="text-muted"><i class="fas fa-info-circle me-1"></i>Gestión dinámica</small>
                            <small class="text-muted"><i class="fas fa-clock me-1"></i>Actualizado: <span id="lastUpdate">--:--</span></small>
                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <div class="card card-custom text-center py-5">
                <div class="card-body">
                    <i class="fas fa-lock fa-4x mb-4 text-muted"></i>
                    <h3>Acceso Restringido</h3>
                    <p class="text-muted">No tienes permisos para acceder.</p>
                    <a href="index.php" class="btn btn-outline-indigo">Volver</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- ========================================== -->
    <!--             MODALES (BOOTSTRAP)            -->
    <!-- ========================================== -->

    <!-- 1. MODAL ESTUDIANTE -->
    <div class="modal fade" id="studentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                
                <!-- Header del Modal con Gradiente -->
                <div class="modal-header border-0" style="background: linear-gradient(135deg, rgba(87, 92, 188, 0.08), rgba(101, 198, 142, 0.05)); padding: 1.5rem;">
                    <div class="d-flex align-items-center">
                        <div class="icon-box-indigo me-3" style="width: 50px; height: 50px; font-size: 1.3rem;">
                            <i class="fas fa-users"></i>
                        </div>
                        <h5 class="modal-title fw-bold mb-0" id="modalTitle" style="color: var(--color-primary-indigo); font-size: 1.4rem;">
                            Estudiante
                        </h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="studentForm">
                        <div class="form-floating mb-3">
                            <input type="text" name="ID_EST" id="ID_EST" class="form-control" placeholder="Cédula" required maxlength="10">
                            <label>Cédula</label>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-floating mb-3">
                                <input type="text" name="NOM_EST" id="NOM_EST" class="form-control" placeholder="Nombre" required>
                                <label>Nombre</label>
                            </div>
                            <div class="col-md-6 form-floating mb-3">
                                <input type="text" name="APE_EST" id="APE_EST" class="form-control" placeholder="Apellido" required>
                                <label>Apellido</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-floating mb-3">
                                <input type="tel" name="TEL_EST" id="TEL_EST" class="form-control" placeholder="Teléfono" required>
                                <label>Teléfono</label>
                            </div>
                            <div class="col-md-6 form-floating mb-3">
                                <input type="date" name="FEC_NAC" id="FEC_NAC" class="form-control" required>
                                <label>Fecha Nacimiento</label>
                            </div>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="email" name="COR_EST" id="COR_EST" class="form-control" placeholder="Correo" required>
                            <label>Correo</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" name="DIR_EST" id="DIR_EST" class="form-control" placeholder="Dirección" required>
                            <label>Dirección</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary rounded-pill" onclick="saveData()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. MODAL CURSO -->
    <div class="modal fade" id="courseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 bg-light p-4">
                    <h5 class="modal-title fw-bold text-primary"><i class="fas fa-book-open me-2"></i>Datos Curso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="courseForm">
                        <div class="form-floating mb-3" id="div_id_cur" style="display:none;">
                            <input type="text" name="ID_CUR" id="ID_CUR" class="form-control" placeholder="ID" readonly>
                            <label>ID Curso</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" name="NOM_CUR" id="NOM_CUR" class="form-control" placeholder="Nombre" required>
                            <label>Nombre del Curso</label>
                        </div>
                        <div class="form-floating mb-3">
                            <textarea name="DES_CUR" id="DES_CUR" class="form-control" placeholder="Descripción" style="height: 100px" required></textarea>
                            <label>Descripción</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary rounded-pill" onclick="saveData()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. MODAL INSCRIPCION -->
    <div class="modal fade" id="enrollmentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 bg-light p-4">
                    <h5 class="modal-title fw-bold text-primary"><i class="fas fa-clipboard-list me-2"></i>Inscripción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="enrollmentForm">
                        <input type="hidden" name="ID_INS" id="ID_INS">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Estudiante</label>
                            <select class="form-select" name="ID_EST_INS" id="ID_EST_INS" required>
                                <option value="">Cargando...</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Curso</label>
                            <select class="form-select" name="ID_CUR_INS" id="ID_CUR_INS" required>
                                <option value="">Cargando...</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary rounded-pill" onclick="saveData()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. MODAL CONFIRMACIÓN ELIMINAR -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-body p-4 text-center">
                    <div class="mb-3 text-danger">
                        <i class="fas fa-exclamation-circle fa-4x"></i>
                    </div>
                    <h5 class="fw-bold mb-2">¿Estás seguro?</h5>
                    <p class="text-muted small mb-4">Esta acción no se puede deshacer.</p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-danger rounded-pill px-3" id="confirmDeleteBtn">Sí, Eliminar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal visor de reportes -->
    <div class="modal fade" id="reportViewerModal" tabindex="-1" aria-labelledby="reportViewerTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" style="width:90vw;max-width:1100px">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="reportViewerTitle">Reporte</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body p-0" style="height:80vh;max-height:850px;min-height:480px; background:#f6f7fb;">
                    <iframe id="reportViewerFrame" src="" title="Visor de reportes" style="width:100%; height:100%; border:0;"></iframe>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- LÓGICA INTEGRADA -->
    <script>
        // --- CONFIGURACIÓN ---
        let currentModule = 'students';
        let isEditing = false;
        let idToDelete = null;
        
        const modules = {
            students: {
                title: 'Registro de Estudiantes',
                btnText: 'Nuevo Estudiante',
                modalId: 'studentModal',
                pk: 'ID_EST',
                urlGet: 'models/obtener_estudiante.php',
                urlAdd: 'models/agregar_estudiante.php',
                urlUpdate: 'models/actualizar_estudiante.php',
                urlDelete: 'models/eliminar_estudiante.php',
                headers: `
                    <tr>
                        <th>Cédula</th><th>Nombre</th><th>Apellido</th><th>Teléfono</th><th>Correo</th><th>Fec. Nac.</th>
                        <th class="text-end">Acciones</th>
                    </tr>`
            },
            courses: {
                title: 'Catálogo de Cursos',
                btnText: 'Nuevo Curso',
                modalId: 'courseModal',
                pk: 'ID_CUR',
                urlGet: 'models/obtener_cursos.php',
                urlAdd: 'models/agregar_curso.php',
                urlUpdate: 'models/actualizar_curso.php',
                urlDelete: 'models/eliminar_curso.php',
                headers: `
                    <tr>
                        <th>ID</th><th>Curso</th><th>Descripción</th><th>Fec. Creación</th>
                        <th class="text-end">Acciones</th>
                    </tr>`
            },
            enrollments: {
                title: 'Inscripciones Activas',
                btnText: 'Nueva Inscripción',
                modalId: 'enrollmentModal',
                pk: 'ID_INS',
                urlGet: 'models/obtener_inscripciones.php',
                urlAdd: 'models/agregar_inscripcion.php',
                urlUpdate: 'models/actualizar_inscripcion.php',
                urlDelete: 'models/eliminar_inscripcion.php',
                headers: `
                    <tr>
                        <th>ID</th><th>Estudiante</th><th>Curso</th><th>Fecha</th>
                        <th class="text-end">Acciones</th>
                    </tr>`
            }
        };

        // --- INICIALIZACIÓN ---
        document.addEventListener('DOMContentLoaded', () => {
            loadModule('students');
            updateTimestamp();
            


            // Buscador
            document.getElementById('searchInput').addEventListener('keyup', function() {
                const term = this.value.toLowerCase();
                document.querySelectorAll('#tableBody tr').forEach(row => {
                    row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
                });
            });
        });

        // --- SISTEMA DE NOTIFICACIONES (REEMPLAZO DE ALERT) ---
        function showNotification(message, type = 'success') {
            const container = document.querySelector('.toast-container');
            const color = type === 'success' ? 'bg-success' : (type === 'error' ? 'bg-danger' : 'bg-info');
            const icon = type === 'success' ? 'check-circle' : (type === 'error' ? 'exclamation-triangle' : 'info-circle');

            const toastId = 'toast-' + Date.now();
            const toastHtml = `
                <div id="${toastId}" class="toast align-items-center text-white ${color} border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body d-flex align-items-center">
                            <i class="fas fa-${icon} fa-lg me-2"></i> 
                            <div>${message}</div>
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', toastHtml);
            const toastEl = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastEl, { delay: 4000 });
            toast.show();

            toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
        }

        // --- CAMBIAR MÓDULO ---
        function loadModule(name) {
            currentModule = name;
            const config = modules[name];
            
            document.getElementById('tableTitle').innerHTML = `<i class="fas fa-table me-2"></i>${config.title}`;
            document.getElementById('tableHead').innerHTML = config.headers;
            
            const btn = document.getElementById('btnNew');
            if(btn) btn.innerHTML = `<i class="fas fa-plus-circle me-2"></i>${config.btnText}`;
            
            document.querySelectorAll('.nav-link').forEach(btn => {
                btn.classList.remove('active');
                if (btn.textContent.includes('Estudiantes') && name === 'students') {
                    btn.classList.add('active');
                } else if (btn.textContent.includes('Cursos') && name === 'courses') {
                    btn.classList.add('active');
                } else if (btn.textContent.includes('Inscripciones') && name === 'enrollments') {
                    btn.classList.add('active');
                }
            });

            fetchData();
        }

        // --- CARGAR DATOS ---
        function fetchData() {
            const config = modules[currentModule];
            const tbody = document.getElementById('tableBody');
            const counter = document.getElementById('recordCount');
            
            tbody.innerHTML = '<tr><td colspan="7" class="text-center py-5 text-muted"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Cargando...</p></td></tr>';

            fetch(config.urlGet)
                .then(res => res.json())
                .then(data => {
                    tbody.innerHTML = '';
                    if(!data || data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4">No hay registros encontrados.</td></tr>';
                        counter.innerHTML = '<i class="fas fa-users me-1"></i>0 registros';
                        return;
                    }
                    
                    counter.innerHTML = `<i class="fas fa-users me-1"></i>${data.length} registros`;

                    data.forEach(item => {
                        let html = '';
                        const jsonItem = JSON.stringify(item).replace(/"/g, '&quot;');
                        
                        if(currentModule === 'students') {
                            html = `<td>${item.ID_EST}</td><td>${item.NOM_EST}</td><td>${item.APE_EST}</td><td>${item.TEL_EST}</td><td>${item.COR_EST}</td><td>${item.FEC_NAC}</td>`;
                        } else if(currentModule === 'courses') {
                            html = `<td>${item.ID_CUR}</td><td>${item.NOM_CUR}</td><td>${item.DES_CUR}</td><td>${item.FEC_CRE}</td>`;
                        } else {
                            html = `<td>${item.ID_INS}</td><td>${item.NOM_EST} ${item.APE_EST}</td><td>${item.NOM_CUR}</td><td>${item.FEC_INS}</td>`;
                        }

                        html += `
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-warning btn-circle" onclick="openEdit(${jsonItem})" title="Editar"><i class="fas fa-pen"></i></button>
                                <button class="btn btn-sm btn-outline-danger btn-circle" onclick="deleteItem('${item[config.pk]}')" title="Eliminar"><i class="fas fa-trash"></i></button>
                            </td>`;
                        
                        const tr = document.createElement('tr');
                        tr.innerHTML = html;
                        tbody.appendChild(tr);
                    });
                })
                .catch(err => {
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error al cargar datos.</td></tr>';
                    showNotification('Error de conexión con el servidor', 'error');
                });
        }

        // --- ACCIONES MODAL ---
        function openCreateModal() {
            isEditing = false;
            const config = modules[currentModule];
            document.querySelector(`#${config.modalId} form`).reset();
            
            if(currentModule === 'courses') document.getElementById('div_id_cur').style.display = 'none';
            if(currentModule === 'students') document.getElementById('ID_EST').readOnly = false;
            if(currentModule === 'enrollments') loadCombos();

            new bootstrap.Modal(document.getElementById(config.modalId)).show();
        }

        function openEdit(item) {
            isEditing = true;
            const config = modules[currentModule];
            const form = document.querySelector(`#${config.modalId} form`);
            
            for (const key in item) {
                if(form.elements[key]) form.elements[key].value = item[key];
            }

            if(currentModule === 'courses') {
                document.getElementById('div_id_cur').style.display = 'block';
                document.getElementById('ID_CUR').value = item.ID_CUR;
            }
            if(currentModule === 'students') document.getElementById('ID_EST').readOnly = true;
            if(currentModule === 'enrollments') loadCombos(item.ID_EST_INS, item.ID_CUR_INS);

            new bootstrap.Modal(document.getElementById(config.modalId)).show();
        }

        function saveData() {
            const config = modules[currentModule];
            const form = document.querySelector(`#${config.modalId} form`);
            
            // Validación HTML5
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);
            const url = isEditing ? config.urlUpdate : config.urlAdd;

            fetch(url, { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if(data.success || data.ok || (!data.error && !data.errorMsg)) {
                        bootstrap.Modal.getInstance(document.getElementById(config.modalId)).hide();
                        fetchData();
                        showNotification('Operación realizada con éxito', 'success');
                    } else {
                        showNotification(data.errorMsg || data.error || 'Error desconocido', 'error');
                    }
                })
                .catch(err => showNotification('Error al procesar la solicitud', 'error'));
        }

        function deleteItem(id) {
            // 1. Guardamos el ID en la variable global
            idToDelete = id; 
            
            // 2. Abrimos el modal de confirmación de Bootstrap
            const deleteModalEl = document.getElementById('deleteModal');
            if (deleteModalEl) {
                new bootstrap.Modal(deleteModalEl).show();
            } else {
                console.error('Error: No se encontró el modal #deleteModal');
            }
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        const config = modules[currentModule];
        const formData = new FormData();
        formData.append(config.pk, idToDelete);

        // 1. Ocultar modal
        const modalInstance = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
        if (modalInstance) modalInstance.hide();

        // 2. Petición al servidor
        fetch(config.urlDelete, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if(data.success || data.ok) {
                    fetchData(); // Recargar tabla
                    showNotification('Registro eliminado correctamente', 'success');
                } else {
                    showNotification(data.errorMsg || 'Error al eliminar', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                showNotification('Error de conexión', 'error');
            });
    });

        // --- UTILIDADES ---
        function loadCombos(selEst = null, selCur = null) {
            // Estudiantes
            fetch('models/obtener_estudiante.php').then(r=>r.json()).then(d => {
                const s = document.getElementById('ID_EST_INS');
                s.innerHTML = '<option value="">Seleccione Estudiante...</option>';
                d.forEach(e => {
                    const opt = new Option(`${e.NOM_EST} ${e.APE_EST} (${e.ID_EST})`, e.ID_EST);
                    if(e.ID_EST == selEst) opt.selected = true;
                    s.add(opt);
                });
            });
            // Cursos
            fetch('models/obtener_cursos.php').then(r=>r.json()).then(d => {
                const s = document.getElementById('ID_CUR_INS');
                s.innerHTML = '<option value="">Seleccione Curso...</option>';
                d.forEach(c => {
                    const opt = new Option(c.NOM_CUR, c.ID_CUR);
                    if(c.ID_CUR == selCur) opt.selected = true;
                    s.add(opt);
                });
            });
        }

        function generarReporte() {
            showNotification('Generando reporte PDF, por favor espere...', 'info');
            // Aquí puedes redirigir a tu script de reporte real
            // window.open('views/report_estudiante.php', '_blank'); 
        }

        function verEstadisticas() {
            showNotification('Cargando módulo de estadísticas...', 'info');
            // Lógica futura
        }
        
        function updateTimestamp() {
            const now = new Date();
            const time = now.toLocaleTimeString('es-EC', {hour: '2-digit', minute:'2-digit'});
            const el = document.getElementById('lastUpdate');
            if(el) el.textContent = time;
        }
    </script>

    <!-- Conexión con los nuevos reportes -->
    <script>
        (function () {
            let reportModalInstance = null;

            function getOrCreateReportModal() {
                const modalEl = document.getElementById('reportViewerModal');
                if (!modalEl) {
                    console.error('No se encontró el modal de visor de reportes.');
                    return null;
                }
                if (!reportModalInstance) {
                    reportModalInstance = new bootstrap.Modal(modalEl);
                }
                return reportModalInstance;
            }

            // Abre el visor de reportes en el modal
            window.openReportViewer = function (url, title) {
                const frame = document.getElementById('reportViewerFrame');
                const titleEl = document.getElementById('reportViewerTitle');
                const modal = getOrCreateReportModal();

                if (!frame || !modal) {
                    console.error('No se pudo inicializar el visor de reportes.');
                    return;
                }

                frame.src = url;
                if (titleEl) {
                    titleEl.textContent = title || 'Reporte';
                }

                modal.show();
            };

            // Reporte por estudiante
            window.generarReporte = function () {
                const searchInput = document.getElementById('searchInput');
                const filtro = searchInput ? searchInput.value.trim() : '';
                let idEst = filtro;

                // Si no hay filtro, usar la fila seleccionada en la tabla
                if (!idEst && window.selectedStudentId) {
                    idEst = String(window.selectedStudentId).trim();
                }

                if (!idEst) {
                    alert('Para generar este reporte debe seleccionar un estudiante en la tabla o escribir una cédula en el buscador.');
                    return;
                }

                const url = '/views/report_estudiante.php?id_est=' + encodeURIComponent(idEst);
                openReportViewer(url, 'Reporte académico del estudiante ' + idEst);
            };

            // Reporte general de estudiantes por curso
            window.generarReporteListado = function () {
                const url = '/views/report_estudiantes_cursos.php';
                openReportViewer(url, 'Reporte de estudiantes por curso');
            };

            // Reporte de estadísticas de cursos
            window.verEstadisticas = function () {
                const url = '/views/report_grafico_cursos.php';
                openReportViewer(url, 'Estadísticas de cursos');
            };

            // Manejo de selección de fila en la tabla para obtener ID_EST
            document.addEventListener('DOMContentLoaded', function () {
                const tbody = document.getElementById('tableBody');
                if (!tbody) return;

                tbody.addEventListener('click', function (e) {
                    const row = e.target.closest('tr');
                    if (!row || row.classList.contains('loading-state') || row.classList.contains('empty-state')) {
                        return;
                    }

                    // Quitar selección previa
                    tbody.querySelectorAll('tr.table-active').forEach(function (tr) {
                        tr.classList.remove('table-active');
                    });

                    // Marcar fila activa
                    row.classList.add('table-active');

                    // Tomar la cédula de la primera celda
                    const firstCell = row.querySelector('td');
                    if (firstCell) {
                        window.selectedStudentId = firstCell.textContent.trim();
                        // Opcional: log para depuración
                        console.log('Estudiante seleccionado:', window.selectedStudentId);
                    }
                });
            });
        })();
    </script>
</body>
</html>

