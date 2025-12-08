// =========================================
// CONFIGURACIÓN Y VARIABLES GLOBALES
// =========================================
let currentUrl = ''; 
let searchTimer;
let modalInstance;
let currentSearchTerm = '';
let isLoading = false;
let entityConfig; // Se inicializará desde la página específica

// Rol del usuario (viene desde PHP via window.appConfig)
const userRole = window.appConfig?.role || 'GUEST';

// =========================================
// INICIALIZACIÓN GENÉRICA
// =========================================
function initializeEntityManagement(config) {
    entityConfig = config;
    console.log(`🚀 Iniciando gestión de: ${entityConfig.pluralEntityName}`);
    console.log('👤 Rol de usuario:', userRole);
    
    // Llamar a las funciones de inicialización directamente
    initializeModal();
    loadTableData();
    if (entityConfig.searchEnabled) {
        setupSearchHandler();
    }
    setupEventListeners();
}

// =========================================
// INICIALIZAR MODAL DE BOOTSTRAP
// =========================================
function initializeModal() {
    const modalEl = document.getElementById('entityModal');
    if (modalEl) {
        modalInstance = new bootstrap.Modal(modalEl, {
            backdrop: 'static',
            keyboard: true
        });
        
        modalEl.addEventListener('hidden.bs.modal', function() {
            const form = document.getElementById('entityForm');
            form.reset();
            form.classList.remove('was-validated');
            const pkField = document.getElementById(entityConfig.primaryKey);
            if (pkField) {
                pkField.readOnly = false;
            }
        });
        
        console.log('✅ Modal inicializado correctamente');
    } else {
        console.warn('⚠️ No se encontró el elemento modal');
    }
}

// =========================================
// CONFIGURAR MANEJADOR DE BÚSQUEDA
// =========================================
function setupSearchHandler() {
    const searchInput = document.getElementById('searchInput');
    if (!searchInput) {
        console.warn('⚠️ No se encontró el input de búsqueda');
        return;
    }
    
    searchInput.addEventListener('input', function(e) {
        clearTimeout(searchTimer);
        const valor = this.value.trim();
        searchTimer = setTimeout(() => {
            currentSearchTerm = valor;
            loadTableData(valor);
        }, 300);
    });
    
    searchInput.addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            clearTimeout(searchTimer);
            const valor = this.value.trim();
            currentSearchTerm = valor;
            loadTableData(valor);
        }
    });
    
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            this.value = '';
            currentSearchTerm = '';
            loadTableData();
        }
    });
    
    console.log('✅ Buscador configurado');
}

// =========================================
// EVENT LISTENERS ADICIONALES
// =========================================
function setupEventListeners() {
    const entityForm = document.getElementById('entityForm');
    if (entityForm) {
        entityForm.addEventListener('submit', (e) => {
            e.preventDefault();
            saveEntity();
        });

        // Configurar campos de búsqueda de FK
        entityConfig.modalFields.forEach(field => {
            if (field.type === 'searchable-foreign-key') {
                const input = document.getElementById(field.id);
                const dropdownWrapper = input.closest('.dropdown');
                const dropdownMenu = dropdownWrapper.querySelector('.dropdown-menu');
                let searchTimer;

                input.addEventListener('keyup', () => {
                    clearTimeout(searchTimer);
                    searchTimer = setTimeout(() => {
                        handleFkSearch(input, dropdownMenu, field.searchConfig);
                    }, 300); // Debounce de 300ms
                });
            }
        });
        
        // Un solo listener para cerrar los dropdowns
        document.addEventListener('click', (e) => {
            const openDropdownMenu = document.querySelector('.dropdown-menu.show');
            if (openDropdownMenu && !openDropdownMenu.closest('.dropdown').contains(e.target)) {
                openDropdownMenu.classList.remove('show');
            }
        });
    }
}

function handleFkSearch(input, dropdownMenu, searchConfig) {
    const searchTerm = input.value.trim();

    if (searchTerm.length < 1) {
        dropdownMenu.classList.remove('show');
        return;
    }

    const searchUrl = `${searchConfig.url}?${searchConfig.searchKey}=${encodeURIComponent(searchTerm)}`;
    
    fetch(searchUrl)
        .then(response => response.json())
        .then(data => {
            renderFkResults(input, dropdownMenu, data, searchConfig);
        })
        .catch(error => {
            console.error('Error en búsqueda de FK:', error);
            dropdownMenu.innerHTML = '<li><a class="dropdown-item text-danger" href="#">Error al buscar</a></li>';
            dropdownMenu.classList.add('show');
        });
}

function renderFkResults(input, dropdownMenu, data, searchConfig) {
    dropdownMenu.innerHTML = ''; // Limpiar resultados anteriores

    if (data.length === 0) {
        dropdownMenu.innerHTML = '<li><a class="dropdown-item text-muted" href="#">No se encontraron resultados</a></li>';
        dropdownMenu.classList.add('show');
        return;
    }

    data.forEach(item => {
        const li = document.createElement('li');
        const a = document.createElement('a');
        a.className = 'dropdown-item';
        a.href = '#';
        
        let displayHtml = searchConfig.displayTpl;
        for (const key in item) {
            displayHtml = displayHtml.replace(`\${${key}}`, item[key]);
        }
        a.innerHTML = displayHtml;

        a.addEventListener('click', (e) => {
            e.preventDefault();
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = displayHtml;
            input.value = tempDiv.textContent || tempDiv.innerText || '';
            input.dataset.selectedId = item[searchConfig.searchKey];
            dropdownMenu.classList.remove('show');
            input.focus(); // Devolver el foco al input
        });

        li.appendChild(a);
        dropdownMenu.appendChild(li);
    });

    dropdownMenu.classList.add('show');
}

// =========================================
// CARGAR DATOS DE LA TABLA
// =========================================
function loadTableData(searchTerm = '') {
    if (isLoading) return;
    isLoading = true;
    const tbody = document.getElementById('tableBody');
    
    let url = entityConfig.urls.getAll;
    if (searchTerm && searchTerm.trim() !== '' && entityConfig.searchEnabled) {
        url = `${entityConfig.urls.getById}?${entityConfig.primaryKey}=${encodeURIComponent(searchTerm.trim())}`;
    }

    showLoadingState(tbody);

    fetch(url)
        .then(response => {
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            return response.json();
        })
        .then(data => {
            renderRows(data);
        })
        .catch(error => {
            console.error('❌ Error al cargar datos:', error);
            showErrorState(tbody, error.message);
        })
        .finally(() => {
            isLoading = false;
        });
}

// =========================================
// ESTADOS VISUALES DE LA TABLA
// =========================================
function showLoadingState(tbody) {
    const colSpan = entityConfig.tableColumns.length + (userRole === 'SECRETARIO' ? 1 : 0);
    tbody.innerHTML = `<tr><td colspan="${colSpan}" class="text-center py-5 loading-state"><i class="fas fa-spinner fa-spin fa-2x mb-3"></i><p class="mb-0 text-muted fw-semibold">Cargando...</p></td></tr>`;
}

function showErrorState(tbody, errorMsg) {
    const colSpan = entityConfig.tableColumns.length + (userRole === 'SECRETARIO' ? 1 : 0);
    tbody.innerHTML = `<tr><td colspan="${colSpan}" class="text-center py-5"><i class="fas fa-exclamation-triangle fa-2x text-danger mb-3"></i><p class="mb-2 text-danger fw-bold">Error</p><small class="text-muted">${errorMsg}</small><br><button class="btn btn-sm btn-outline-danger mt-3" onclick="loadTableData('${currentSearchTerm}')"><i class="fas fa-redo me-1"></i> Reintentar</button></td></tr>`;
}

function showEmptyState(tbody, isSearch = false) {
    const colSpan = entityConfig.tableColumns.length + (userRole === 'SECRETARIO' ? 1 : 0);
    const message = isSearch ? `No se encontraron resultados para '${currentSearchTerm}'` : `No hay ${entityConfig.pluralEntityName.toLowerCase()} registrados.`;
    tbody.innerHTML = `<tr><td colspan="${colSpan}" class="text-center py-5 empty-state"><i class="fas fa-search fa-3x mb-3"></i><p class="mb-0 text-muted fw-semibold">${message}</p></td></tr>`;
}

// =========================================
// RENDERIZAR FILAS
// =========================================
function renderRows(data) {
    const tbody = document.getElementById('tableBody');
    if (!data || data.length === 0) {
        showEmptyState(tbody, currentSearchTerm !== '');
        return;
    }
    tbody.innerHTML = '';
    data.forEach((item, index) => {
        const row = createTableRow(item, index);
        tbody.appendChild(row);
    });
}

function createTableRow(item, index) {
    const tr = document.createElement('tr');
    tr.style.animationDelay = `${index * 0.03}s`;
    
    entityConfig.tableColumns.forEach(col => {
        const td = document.createElement('td');
        let value = item[col.field];
        if (col.formatter) {
            value = col.formatter(value, item); // Pasa el item completo
        }
        td.innerHTML = value;
        tr.appendChild(td);
    });

    if (userRole === 'SECRETARIO') {
        const actionsTd = document.createElement('td');
        actionsTd.className = 'text-end';
        actionsTd.innerHTML = `
            <button class="btn btn-action-edit btn-circle" title="Editar ${entityConfig.entityName}"><i class="fas fa-pen"></i></button>
            <button class="btn btn-action-delete btn-circle" title="Eliminar ${entityConfig.entityName}"><i class="fas fa-trash-alt"></i></button>
        `;
        actionsTd.querySelector('.btn-action-edit').addEventListener('click', (e) => { e.stopPropagation(); editEntity(item); });
        actionsTd.querySelector('.btn-action-delete').addEventListener('click', (e) => { e.stopPropagation(); destroyEntity(item); });
        tr.appendChild(actionsTd);
    }
    
    return tr;
}

// =========================================
// ACCIONES CRUD
// =========================================
function openModalForNew() {
    if (userRole !== 'SECRETARIO') return;
    const form = document.getElementById('entityForm');
    form.reset();
    form.classList.remove('was-validated');
    
    // Iterar sobre todos los campos del modal para aplicar lógicas de visibilidad y 'required'
    entityConfig.modalFields.forEach(field => {
        const input = document.getElementById(field.id);
        if (input) {
            // Asegurarse de que el campo sea editable y requerido por defecto
            input.readOnly = false;
            input.required = field.required || false; // Restaurar required si se quitó

            // Ocultar si es el PK autoincremental o si tiene hideOnCreate: true
            if (
                (field.id === entityConfig.primaryKey && entityConfig.autoIncrement) ||
                field.hideOnCreate
            ) {
                input.closest('.form-floating').style.display = 'none';
                input.required = false; // Remover 'required' si está oculto
            } else {
                input.closest('.form-floating').style.display = 'block'; // Asegurarse de que esté visible
            }
        }
    });

    // El título se personaliza en cada vista (ej. cursos.php) si es necesario
    const titleEl = document.getElementById('modalTitle');
    if (!titleEl.innerHTML.includes('fa-plus-circle')) { // Solo si no está ya personalizado
        titleEl.innerText = `➕ Nuevo ${entityConfig.entityName}`;
    }
    
    currentUrl = entityConfig.urls.add;
    modalInstance.show();
}

function editEntity(item) {
    if (userRole !== 'SECRETARIO') return;
    const form = document.getElementById('entityForm');
    form.classList.remove('was-validated');
    
    // Iterar sobre todos los campos del modal para aplicar lógicas de visibilidad y 'required'
    entityConfig.modalFields.forEach(field => {
        const input = document.getElementById(field.id);
        if (input) {
            // Asegurarse de que el campo sea visible
            input.closest('.form-floating').style.display = 'block';
            // Restaurar el estado 'required' original
            input.required = field.required || false;

            // Lógica específica para el campo de clave primaria si es autoincremental
            if (field.id === entityConfig.primaryKey) {
                input.readOnly = true; // Siempre de solo lectura al editar el PK
            } else {
                input.readOnly = false; // Otros campos son editables
            }

            // Llenar el valor del campo
            delete input.dataset.selectedId; // Limpiar datos previos
            if (field.type === 'searchable-foreign-key') {
                const idValue = item[field.id];
                input.dataset.selectedId = idValue;
                
                let displayText = idValue;
                if (field.id === 'ID_EST_INS' && item.NOM_EST) {
                    displayText = `${item.NOM_EST} ${item.APE_EST}`;
                } else if (field.id === 'ID_CUR_INS' && item.NOM_CUR) {
                    displayText = item.NOM_CUR;
                }
                input.value = displayText;
            } else {
                input.value = item[field.id];
            }
        }
    });

    document.getElementById('modalTitle').innerText = `✏️ Editar ${entityConfig.entityName}`;
    currentUrl = `${entityConfig.urls.update}?${entityConfig.primaryKey}=${encodeURIComponent(item[entityConfig.primaryKey])}`;
    
    modalInstance.show();
}

function saveEntity() {
    const form = document.getElementById('entityForm');
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }

    const saveBtn = document.querySelector('.modal-footer .btn-primary');
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';

    const formData = new FormData(form);
    
    // Si es una nueva entidad, no enviar PK si es autoincremento, ni campos hideOnCreate
    if (currentUrl === entityConfig.urls.add) {
        if (entityConfig.autoIncrement) {
            formData.delete(entityConfig.primaryKey);
        }
        entityConfig.modalFields.forEach(field => {
            if (field.hideOnCreate) {
                formData.delete(field.id);
            }
        });
    }
    
    // Asegurarse de que los IDs se envíen para los campos de FK
    entityConfig.modalFields.forEach(field => {
        if (field.type === 'searchable-foreign-key') {
            const input = document.getElementById(field.id);
            if (input.dataset.selectedId) {
                formData.set(input.name, input.dataset.selectedId);
            }
        }
    });

    fetch(currentUrl, { method: 'POST', body: formData })
    .then(response => response.json())
    .then(result => {
        // Log the full result for debugging
        console.log("Backend Response:", result);

        // Determine success more robustly: check for explicit success/ok, AND ensure no "Error:" in message
        const isSuccess = (result.success === true || result.ok === true) && (!result.mensaje || !result.mensaje.includes('Error:'));

        if (isSuccess) {
            modalInstance.hide();
            showNotification(result.mensaje || '✅ Datos guardados', 'success');
            loadTableData(currentSearchTerm);
        } else {
            showNotification(result.errorMsg || result.mensaje || 'Error desconocido', 'error');
        }
    })
    .catch(error => showNotification('Error de conexión', 'error'))
    .finally(() => {
        saveBtn.disabled = false;
        saveBtn.innerHTML = '<i class="fas fa-save me-2"></i>Guardar Datos';
    });
}

function destroyEntity(item) {
    if (userRole !== 'SECRETARIO') return;
    
    const itemName = item[entityConfig.tableColumns[1].field]; // Assume second column is the name
    
    showDeleteConfirmModal(item[entityConfig.primaryKey], itemName, () => {
        const formData = new FormData();
        formData.append(entityConfig.primaryKey, item[entityConfig.primaryKey]);

        return fetch(entityConfig.urls.delete, { method: 'POST', body: formData })
            .then(response => response.json())
            .then(result => {
                 const isSuccess = result.success === true || result.ok === true;
                 if (isSuccess) {
                    showNotification(result.mensaje || '✅ Eliminado correctamente', 'success');
                    loadTableData(currentSearchTerm);
                 } else {
                    showNotification(result.errorMsg || result.mensaje || 'Error al eliminar', 'error');
                 }
            });
    });
}

// =========================================
// MODAL DE CONFIRMACIÓN DE ELIMINACIÓN
// =========================================
let deleteModalInstance = null;
function showDeleteConfirmModal(id, name, onConfirm) {
    let modalEl = document.getElementById('deleteConfirmModal');
    if (!modalEl) {
        modalEl = document.createElement('div');
        modalEl.id = 'deleteConfirmModal';
        modalEl.className = 'modal fade';
        modalEl.innerHTML = `
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header border-0"><h5 class="modal-title text-danger fw-bold"><i class="fas fa-exclamation-triangle me-2"></i>Eliminar</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body"><p class="mb-2 delete-confirm-message"></p><p class="mb-0 text-muted" style="font-size: 0.9rem;">Esta acción no se puede deshacer.</p></div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteBtn"><i class="fas fa-trash-alt me-1"></i>Eliminar</button>
                    </div>
                </div>
            </div>`;
        document.body.appendChild(modalEl);
        deleteModalInstance = new bootstrap.Modal(modalEl);
    }

    modalEl.querySelector('.delete-confirm-message').textContent = `¿Seguro que quieres eliminar a ${name}?`;
    const confirmBtn = modalEl.querySelector('#confirmDeleteBtn');
    
    confirmBtn.onclick = () => {
        const originalText = confirmBtn.innerHTML;
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Eliminando...';
        
        onConfirm().finally(() => {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = originalText;
            deleteModalInstance.hide();
        });
    };
    
    deleteModalInstance.show();
}

// =========================================
// UTILIDADES Y NOTIFICACIONES
// =========================================
function showNotification(message, type = 'info') {
    console.log(`Notification: ${type.toUpperCase()} - ${message}`);
    const toast = document.createElement('div');
    const icons = { success: 'fa-check-circle', error: 'fa-exclamation-circle', warning: 'fa-exclamation-triangle', info: 'fa-info-circle' };
    const colors = { success: '#65c68e', error: '#ef4444', warning: '#e6924d', info: '#575cbc' };
    toast.innerHTML = `<i class="fas ${icons[type]} me-2"></i><span>${message}</span>`;
    toast.style.cssText = `position: fixed; top: 20px; right: 20px; background: white; padding: 1rem 1.5rem; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); z-index: 9999; animation: slideInRight 0.4s ease; display: flex; align-items: center; font-weight: 600; min-width: 300px; border-left: 4px solid ${colors[type]};`;
    toast.querySelector('i').style.color = colors[type];
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.4s ease';
        setTimeout(() => toast.remove(), 400);
    }, 4000);
}

// Animaciones CSS
const styleSheet = document.createElement('style');
styleSheet.textContent = `@keyframes slideInRight { from { transform: translateX(400px); opacity: 0; } to { transform: translateX(0); opacity: 1; } } @keyframes slideOutRight { from { transform: translateX(0); opacity: 1; } to { transform: translateX(400px); opacity: 0; } }`;
document.head.appendChild(styleSheet);
