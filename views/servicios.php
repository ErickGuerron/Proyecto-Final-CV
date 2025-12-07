<?php 
    if(!isset($_SESSION)) { session_start(); }
    $rol = $_SESSION['user']['ROL_USU'] ?? 'GUEST'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Estudiantes - Tech Indigo</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Hoja de Estilos Principal -->
    <link href="assets/css/main.css" rel="stylesheet">
</head>

<body>
    <div class="container my-5">
        
        <!-- Encabezado -->
        <div class="card card-custom p-4 mb-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h2 class="mb-1">Gestión de Estudiantes</h2>
                    <p class="text-muted mb-0">
                        <?php echo ($rol === 'SECRETARIO') ? 'Administración completa de registros.' : 'Visualización y generación de reportes.'; ?>
                    </p>
                </div>
            </div>
        </div>

        <?php if ($rol === 'SECRETARIO' || $rol === 'ADMIN'): ?>
            
            <!-- Barra de Herramientas -->
            <div class="card card-custom mb-4">
                <div class="card-body toolbar-section">
                    <div class="row align-items-center g-3">
                        <div class="col-md-7 col-lg-6">
                            <?php if ($rol === 'SECRETARIO'): ?>
                                <button class="btn btn-success text-white px-4 rounded-pill" onclick="openModalForNew()">
                                    <i class="fas fa-plus-circle me-2"></i>Nuevo Estudiante
                                </button>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-5 col-lg-6">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search"></i></span>
                                <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Buscar por Cédula...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla -->
            <div class="card card-custom">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-custom mb-0 align-middle">
                            <thead>
                                <tr id="tableHeader">
                                    <!-- Las columnas se generarán dinámicamente -->
                                </tr>
                            </thead>
                            <tbody id="tableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="card card-custom text-center py-5">
                <div class="card-body">
                    <h3>Acceso Restringido</h3>
                    <p>No tienes permisos para acceder a esta sección.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="entityModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="entityForm" novalidate>
                        <!-- Los campos del formulario se generarán dinámicamente -->
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="saveEntity()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.appConfig = { role: "<?php echo $rol; ?>" };
    </script>
    <script src="assets/js/main-entity.js"></script>
    <script>
        const studentConfig = {
            entityName: 'Estudiante',
            pluralEntityName: 'Estudiantes',
            primaryKey: 'ID_EST',
            urls: {
                getAll: 'models/obtener_estudiante.php',
                getById: 'models/obtener_estudiante_id.php',
                add: 'models/agregar_estudiante.php',
                update: 'models/actualizar_estudiante.php',
                delete: 'models/eliminar_estudiante.php'
            },
            searchEnabled: true,
            searchPlaceholder: 'Buscar por Cédula...',
            tableColumns: [
                { header: 'Cédula', field: 'ID_EST' },
                { header: 'Nombre', field: 'NOM_EST' },
                { header: 'Apellido', field: 'APE_EST' },
                { header: 'Teléfono', field: 'TEL_EST' },
                { header: 'Correo', field: 'COR_EST' },
                { header: 'Dirección', field: 'DIR_EST' },
                { 
                    header: 'Fec. Nac.', 
                    field: 'FEC_NAC', 
                    formatter: (val) => {
                        if (!val) return '';
                        const [year, month, day] = val.split('-').map(Number);
                        const date = new Date(year, month - 1, day); // Month is 0-indexed
                        return date.toLocaleDateString('es-EC', { year: 'numeric', month: '2-digit', day: '2-digit' });
                    }
                }
            ],
            modalFields: [
                { id: 'ID_EST', label: 'Cédula', type: 'text', required: true, pattern: '[0-9]{10}' },
                { id: 'NOM_EST', label: 'Nombre', type: 'text', required: true },
                { id: 'APE_EST', label: 'Apellido', type: 'text', required: true },
                { id: 'TEL_EST', label: 'Teléfono', type: 'tel', required: true, pattern: '[0-9]{10}' },
                { id: 'COR_EST', label: 'Correo', type: 'email', required: true },
                { id: 'DIR_EST', label: 'Dirección', type: 'text', required: true },
                { id: 'FEC_NAC', label: 'Fecha de Nacimiento', type: 'date', required: true }
            ]
        };

        // Función para renderizar el formulario dinámicamente
        function renderFormFields(fields) {
            const form = document.getElementById('entityForm');
            form.innerHTML = '';
            fields.forEach(field => {
                const div = document.createElement('div');
                div.className = 'mb-3';
                div.innerHTML = `
                    <label for="${field.id}" class="form-label">${field.label}</label>
                    <input type="${field.type}" class="form-control" id="${field.id}" name="${field.id}" 
                           ${field.required ? 'required' : ''} 
                           ${field.pattern ? `pattern="${field.pattern}"` : ''}>
                    <div class="invalid-feedback">Campo inválido.</div>
                `;
                form.appendChild(div);
            });
        }
        
        // Función para renderizar el header de la tabla
        function renderTableHeader(columns) {
            const header = document.getElementById('tableHeader');
            header.innerHTML = '';
            columns.forEach(col => {
                const th = document.createElement('th');
                th.textContent = col.header;
                header.appendChild(th);
            });
            if (window.appConfig.role === 'SECRETARIO') {
                const th = document.createElement('th');
                th.className = 'text-end';
                th.textContent = 'Acciones';
                header.appendChild(th);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            renderTableHeader(studentConfig.tableColumns);
            renderFormFields(studentConfig.modalFields);
            initializeEntityManagement(studentConfig);
        });
    </script>
</body>
</html>