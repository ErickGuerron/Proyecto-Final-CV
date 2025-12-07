<?php 
    if(!isset($_SESSION)) { session_start(); }
    $rol = $_SESSION['user']['ROL_USU'] ?? 'GUEST'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Cursos - Tech Indigo</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/tabla.css" rel="stylesheet">
</head>

<body>
    <div class="container my-5">
        
        <div class="card card-custom p-4 mb-4">
            <div>
                <h2 class="mb-1">Gestión de Cursos</h2>
                <p class="text-muted mb-0">Administración de la oferta académica.</p>
            </div>
        </div>

        <?php if ($rol === 'SECRETARIO' || $rol === 'ADMIN'): ?>
            
            <div class="card card-custom mb-4">
                <div class="card-body toolbar-section">
                    <div class="row align-items-center g-3">
                        <div class="col-md-7 col-lg-6">
                            <?php if ($rol === 'SECRETARIO'): ?>
                                <button class="btn btn-success text-white px-4 rounded-pill" onclick="openModalForNew()">
                                    <i class="fas fa-plus-circle me-2"></i>Nuevo Curso
                                </button>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-5 col-lg-6">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search"></i></span>
                                <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Buscar por ID de Curso...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-custom">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-custom mb-0 align-middle">
                            <thead><tr id="tableHeader"></tr></thead>
                            <tbody id="tableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <div class="card card-custom text-center py-5">
                <div class="card-body"><h3>Acceso Restringido</h3><p>No tienes permisos para acceder.</p></div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal Bootstrap Mejorado -->
    <div class="modal fade" id="entityModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                
                <!-- Header del Modal con Gradiente -->
                <div class="modal-header border-0" style="background: linear-gradient(135deg, rgba(87, 92, 188, 0.08), rgba(101, 198, 142, 0.05)); padding: 1.5rem;">
                    <div class="d-flex align-items-center">
                        <div class="icon-box-indigo me-3" style="width: 50px; height: 50px; font-size: 1.3rem;">
                            <i class="fas fa-book-open-reader"></i>
                        </div>
                        <h5 class="modal-title fw-bold mb-0" id="modalTitle" style="color: var(--color-primary-indigo); font-size: 1.4rem;">
                            Curso
                        </h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <!-- Body del Modal -->
                <div class="modal-body p-4" style="background-color: #fafbfc;">
                    <form id="entityForm" novalidate>
                        <!-- Los campos se inyectarán aquí por JS -->
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
                        onclick="saveEntity()">
                        <i class="fas fa-save me-2"></i>Guardar Datos
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script> window.appConfig = { role: "<?php echo $rol; ?>" }; </script>
    <script src="assets/js/main-entity.js"></script>
    <script>
        const courseConfig = {
            entityName: 'Curso',
            pluralEntityName: 'Cursos',
            primaryKey: 'ID_CUR',
            autoIncrement: true, // <- Nueva bandera
            urls: {
                getAll: 'models/obtener_cursos.php',
                getById: 'models/obtener_curso_id.php',
                add: 'models/agregar_curso.php',
                update: 'models/actualizar_curso.php',
                delete: 'models/eliminar_curso.php'
            },
            searchEnabled: true,
            searchPlaceholder: 'Buscar por ID...',
            tableColumns: [
                { header: 'ID', field: 'ID_CUR' },
                { header: 'Nombre', field: 'NOM_CUR' },
                { header: 'Descripción', field: 'DES_CUR' },
                { 
                    header: 'Fec. Creación', 
                    field: 'FEC_CRE', 
                    formatter: (val) => {
                        if (!val) return '';
                        const [year, month, day] = val.split('-').map(Number);
                        const date = new Date(year, month - 1, day); // Month is 0-indexed
                        return date.toLocaleDateString('es-EC', { year: 'numeric', month: '2-digit', day: '2-digit' });
                    }
                }
            ],
            modalFields: [
                { id: 'ID_CUR', label: 'ID Curso', type: 'text', required: true },
                { id: 'NOM_CUR', label: 'Nombre del Curso', type: 'text', required: true },
                { id: 'DES_CUR', label: 'Descripción', type: 'text', required: true }
            ]
        };

        function getIconForField(fieldId) {
            const icons = {
                'ID_CUR': 'fa-hashtag',
                'NOM_CUR': 'fa-font',
                'DES_CUR': 'fa-align-left'
            };
            return icons[fieldId] || 'fa-question-circle';
        }

        function renderFormFields(fields) {
            const form = document.getElementById('entityForm');
            form.innerHTML = '';
            fields.forEach(field => {
                const div = document.createElement('div');
                div.className = 'form-floating mb-3';
                
                const input = document.createElement('input');
                input.type = field.type;
                input.name = field.id;
                input.id = field.id;
                input.className = 'form-control';
                input.placeholder = field.label;
                if (field.required) input.required = true;
                if (field.pattern) input.pattern = field.pattern;
                if (field.maxLength) input.maxLength = field.maxLength;

                const label = document.createElement('label');
                label.htmlFor = field.id;
                label.innerHTML = `<i class="fas ${getIconForField(field.id)} me-2"></i>${field.label}`;

                const invalidFeedback = document.createElement('div');
                invalidFeedback.className = 'invalid-feedback';
                invalidFeedback.textContent = `Por favor, ingrese un valor válido para ${field.label}.`;

                div.appendChild(input);
                div.appendChild(label);
                div.appendChild(invalidFeedback);
                form.appendChild(div);
            });
        }
        
        // Sobreescribir la función de main-entity.js para personalizar el título
        function openModalForNew() {
            if (userRole !== 'SECRETARIO') return;
            const form = document.getElementById('entityForm');
            form.reset();
            form.classList.remove('was-validated');
            
            const pkField = document.getElementById(entityConfig.primaryKey);
            if (pkField) {
                pkField.readOnly = false;
                pkField.required = false; // <-- Desactivar validación para la creación
                pkField.closest('.form-floating').style.display = 'none';
            }

            document.getElementById('modalTitle').innerHTML = `<i class="fas fa-plus-circle me-2"></i> Nuevo ${entityConfig.entityName}`;
            currentUrl = entityConfig.urls.add;
            modalInstance.show();
        }
        
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
            renderTableHeader(courseConfig.tableColumns);
            renderFormFields(courseConfig.modalFields);
            initializeEntityManagement(courseConfig);
        });
    </script>
</body>
</html>