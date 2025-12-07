// =========================================
// CONFIGURACIÓN Y VARIABLES GLOBALES
// =========================================
let currentUrl = ''; 
let searchTimer;
let modalInstance;
let currentSearchTerm = '';
let isLoading = false;

// Rol del usuario (viene desde PHP via window.appConfig)
const userRole = window.appConfig?.role || 'GUEST';

// =========================================
// INICIALIZACIÓN AL CARGAR EL DOM
// =========================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Iniciando aplicación Tech Indigo...');
    console.log('👤 Rol de usuario:', userRole);
    
    initializeModal();
    loadTableData();
    setupSearchHandler();
    setupEventListeners();
});

// =========================================
// INICIALIZAR MODAL DE BOOTSTRAP
// =========================================
function initializeModal() {
    const modalEl = document.getElementById('studentModal');
    if (modalEl) {
        modalInstance = new bootstrap.Modal(modalEl, {
            backdrop: 'static',
            keyboard: true
        });
        
        // Limpiar formulario al cerrar
        modalEl.addEventListener('hidden.bs.modal', function() {
            const form = document.getElementById('studentForm');
            form.reset();
            form.classList.remove('was-validated');
            document.getElementById('ID_EST').readOnly = false;
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
    
    // Evento de búsqueda en tiempo real
    searchInput.addEventListener('input', function(e) {
        clearTimeout(searchTimer);
        const valor = this.value.trim();
        
        console.log('🔍 Búsqueda iniciada:', valor);
        
        // Búsqueda con debounce (espera 300ms después de que el usuario termine de escribir)
        searchTimer = setTimeout(() => {
            currentSearchTerm = valor;
            
            // Feedback visual
            if (valor) {
                this.style.borderColor = 'var(--color-primary-indigo)';
                this.style.boxShadow = '0 0 0 3px rgba(87, 92, 188, 0.1)';
            } else {
                this.style.borderColor = '#e2e8f0';
                this.style.boxShadow = 'none';
            }
            
            loadTableData(valor);
        }, 300);
    });
    
    // También escuchar el evento keyup para compatibilidad
    searchInput.addEventListener('keyup', function(e) {
        // Si presiona Enter, buscar inmediatamente
        if (e.key === 'Enter') {
            clearTimeout(searchTimer);
            const valor = this.value.trim();
            currentSearchTerm = valor;
            loadTableData(valor);
        }
    });
    
    // Limpiar búsqueda con ESC
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            this.value = '';
            this.style.borderColor = '#e2e8f0';
            this.style.boxShadow = 'none';
            currentSearchTerm = '';
            loadTableData();
        }
    });
    
    console.log('✅ Buscador configurado (input + keyup + ESC)');
}

// =========================================
// EVENT LISTENERS ADICIONALES
// =========================================
function setupEventListeners() {
    // Enter en el formulario
    const studentForm = document.getElementById('studentForm');
    if (studentForm) {
        studentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveUser();
        });
    }
}

// =========================================
// CARGAR DATOS DE LA TABLA
// =========================================
function loadTableData(searchTerm = '') {
    // Prevenir llamadas múltiples simultáneas
    if (isLoading) {
        console.log('⏳ Carga en progreso, ignorando petición duplicada');
        return;
    }
    
    isLoading = true;
    const tbody = document.getElementById('tableBody');
    
    // =======================
    // ⬇⬇⬇ CAMBIO IMPORTANTE ⬇⬇⬇
    // =======================
    let url;
    let method = 'GET';
    let body = null;
    let headers = {};

    if (searchTerm && searchTerm.trim() !== '') {
        // Modo filtrado: misma idea que tu doSearch()
        url = 'models/obtener_estudiante_id.php?id_est=' + encodeURIComponent(searchTerm.trim());
        console.log('🔍 Buscando por cédula:', searchTerm);
    } else {
        // Modo listado completo
        url = 'models/obtener_estudiante.php';
        console.log('📋 Cargando todos los registros');
    }
    // =======================
    // ⬆⬆⬆ CAMBIO IMPORTANTE ⬆⬆⬆
    // =======================

    // Mostrar estado de carga
    showLoadingState(tbody);

    // Realizar petición
    fetch(url, { method, headers, body })
        .then(response => {
            console.log('📡 Respuesta del servidor:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.text(); // Primero como texto para debug
        })
        .then(text => {
            console.log('📥 Datos recibidos (raw):', text);
            try {
                const data = JSON.parse(text);
                console.log('✅ JSON parseado:', data);
                renderRows(data);
            } catch (e) {
                console.error('❌ Error al parsear JSON:', e);
                console.error('Respuesta recibida:', text);
                throw new Error('Respuesta del servidor no es JSON válido');
            }
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
    const colSpan = userRole === 'SECRETARIO' ? 8 : 7;
    tbody.innerHTML = `
        <tr>
            <td colspan="${colSpan}" class="text-center py-5 loading-state">
                <i class="fas fa-spinner fa-spin fa-2x mb-3"></i>
                <p class="mb-0 text-muted fw-semibold">Cargando información...</p>
            </td>
        </tr>
    `;
}

function showErrorState(tbody, errorMsg) {
    const colSpan = userRole === 'SECRETARIO' ? 8 : 7;
    tbody.innerHTML = `
        <tr>
            <td colspan="${colSpan}" class="text-center py-5">
                <i class="fas fa-exclamation-triangle fa-2x text-danger mb-3"></i>
                <p class="mb-2 text-danger fw-bold">Error al cargar los datos</p>
                <small class="text-muted">${errorMsg}</small>
                <br>
                <button class="btn btn-sm btn-outline-danger mt-3" onclick="loadTableData('${currentSearchTerm}')">
                    <i class="fas fa-redo me-1"></i> Reintentar
                </button>
            </td>
        </tr>
    `;
}

function showEmptyState(tbody, isSearch = false) {
    const colSpan = userRole === 'SECRETARIO' ? 8 : 7;
    const message = isSearch 
        ? 'No se encontraron resultados para tu búsqueda' 
        : 'No hay estudiantes registrados en el sistema';
    const icon = isSearch ? 'fa-search' : 'fa-users';
    
    tbody.innerHTML = `
        <tr>
            <td colspan="${colSpan}" class="text-center py-5 empty-state">
                <i class="fas ${icon} fa-3x mb-3"></i>
                <p class="mb-0 text-muted fw-semibold">${message}</p>
                ${isSearch ? '<small class="text-muted d-block mt-2">Intenta con otro término de búsqueda</small>' : ''}
            </td>
        </tr>
    `;
}

// =========================================
// RENDERIZAR FILAS DE LA TABLA
// =========================================
function renderRows(data) {
    const tbody = document.getElementById('tableBody');
    
    // Validar datos
    if (!data || !Array.isArray(data) || data.length === 0) {
        showEmptyState(tbody, currentSearchTerm !== '');
        return;
    }

    // Limpiar tabla
    tbody.innerHTML = '';

    // Renderizar cada fila con animación
    data.forEach((est, index) => {
        const row = createTableRow(est, index);
        tbody.appendChild(row);
    });
    
    console.log('✅ Tabla renderizada con', data.length, 'registros');
}

// =========================================
// CREAR FILA DE TABLA
// =========================================
function createTableRow(est, index) {
    const tr = document.createElement('tr');
    tr.style.animationDelay = `${index * 0.03}s`;
    
    // Celdas principales
    tr.innerHTML = `
        <td class="fw-bold" style="color: var(--color-primary-indigo); font-family: 'Courier New', monospace;">
            ${escapeHtml(est.ID_EST)}
        </td>
        <td>${escapeHtml(est.NOM_EST)}</td>
        <td>${escapeHtml(est.APE_EST)}</td>
        <td><i class="fas fa-phone-alt me-1 text-muted"></i> ${escapeHtml(est.TEL_EST)}</td>
        <td><i class="fas fa-envelope me-1 text-muted"></i> ${escapeHtml(est.COR_EST)}</td>
        <td><i class="fas fa-map-marker-alt me-1 text-muted"></i> ${escapeHtml(est.DIR_EST)}</td>
        <td><i class="fas fa-calendar me-1 text-muted"></i> ${formatDate(est.FEC_NAC)}</td>
    `;
    
    // Agregar columna de acciones si es SECRETARIO
    if (userRole === 'SECRETARIO') {
        const actionsTd = document.createElement('td');
        actionsTd.className = 'text-end';
        actionsTd.innerHTML = `
            <button class="btn btn-action-edit btn-circle" title="Editar estudiante">
                <i class="fas fa-pen"></i>
            </button>
            <button class="btn btn-action-delete btn-circle" title="Eliminar estudiante">
                <i class="fas fa-trash-alt"></i>
            </button>
        `;
        
        // Event listeners para los botones
        const editBtn = actionsTd.querySelector('.btn-action-edit');
        const deleteBtn = actionsTd.querySelector('.btn-action-delete');
        
        editBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            editUser(est);
        });
        
        deleteBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            destroyUser(est.ID_EST, est.NOM_EST + ' ' + est.APE_EST);
        });
        
        tr.appendChild(actionsTd);
    }
    
    return tr;
}

// =========================================
// UTILIDADES
// =========================================
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(dateStr) {
    if (!dateStr) return 'N/A';
    try {
        const date = new Date(dateStr);
        return date.toLocaleDateString('es-EC', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        });
    } catch (e) {
        return dateStr;
    }
}

// =========================================
// ABRIR MODAL PARA CREAR NUEVO
// =========================================
function openModal(mode) {
    if (userRole !== 'SECRETARIO') {
        showNotification('No tienes permisos para crear estudiantes', 'warning');
        return;
    }
    
    const form = document.getElementById('studentForm');
    form.reset();
    form.classList.remove('was-validated');
    document.getElementById('ID_EST').readOnly = false;
    document.getElementById('modalTitle').innerText = '➕ Nuevo Estudiante';
    currentUrl = 'models/agregar_estudiante.php';
    
    modalInstance.show();
    
    // Focus en el primer campo
    setTimeout(() => {
        document.getElementById('ID_EST').focus();
    }, 300);
    
    console.log('📝 Modal abierto para crear');
}

// =========================================
// EDITAR ESTUDIANTE
// =========================================
function editUser(est) {
    if (userRole !== 'SECRETARIO') {
        showNotification('No tienes permisos para editar estudiantes', 'warning');
        return;
    }
    
    const form = document.getElementById('studentForm');
    form.classList.remove('was-validated');
    
    // Rellenar formulario
    document.getElementById('ID_EST').value = est.ID_EST;
    document.getElementById('ID_EST').readOnly = true; // La cédula es PK, no editable
    document.getElementById('NOM_EST').value = est.NOM_EST;
    document.getElementById('APE_EST').value = est.APE_EST;
    document.getElementById('TEL_EST').value = est.TEL_EST;
    document.getElementById('COR_EST').value = est.COR_EST;
    document.getElementById('DIR_EST').value = est.DIR_EST;
    document.getElementById('FEC_NAC').value = est.FEC_NAC;

    document.getElementById('modalTitle').innerText = '✏️ Editar Estudiante';
    currentUrl = 'models/actualizar_estudiante.php?ID_EST=' + encodeURIComponent(est.ID_EST);
    
    modalInstance.show();
    
    console.log('✏️ Editando estudiante:', est.ID_EST);
}

// =========================================
// GUARDAR CAMBIOS (CREAR O ACTUALIZAR)
// =========================================
function saveUser() {
    const form = document.getElementById('studentForm');

    // Validación HTML5
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }

    // Deshabilitar botón para evitar doble envío
    const saveBtn = document.querySelector('.modal-footer .btn-primary');
    const originalText = saveBtn.innerHTML;
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';

    const formData = new FormData(form);

    console.log('💾 Guardando datos...');
    console.log('URL:', currentUrl);

    fetch(currentUrl, {
        method: 'POST',
        body: formData
    })
    .then(async (response) => {
        console.log('📡 Estado de respuesta:', response.status);

        const resultRaw = await response.text();
        console.log('📥 Respuesta del servidor (raw):', resultRaw);

        let result = {};
        try {
            result = JSON.parse(resultRaw);
        } catch (e) {
            console.error('❌ Error al parsear JSON:', e);
            throw new Error('Respuesta inválida del servidor: ' + resultRaw);
        }

        // Normalizar flags de éxito/ error para distintos endpoints
        const isHttpOk      = response.ok;
        const explicitFail  = result.success === false || result.ok === false;
        const hasErrorMsg   = !!result.errorMsg;
        const isSuccess     = isHttpOk && !explicitFail && !hasErrorMsg;

        if (!isSuccess) {
            const msg = result.errorMsg || result.mensaje || `Error ${response.status}`;
            console.error('❌ Error del servidor:', msg);
            showNotification(msg || 'Error desconocido', 'error');
            return;
        }

        // Éxito: cerrar modal, notificar y recargar tabla
        modalInstance.hide();
        showNotification(result.mensaje || '✅ Datos guardados correctamente', 'success');
        console.log('✅ Operación exitosa');

        setTimeout(() => {
            loadTableData(currentSearchTerm);
        }, 300);
    })
    .catch((error) => {
        console.error('❌ Error de conexión:', error);
        showNotification(error.message || 'Error de conexión con el servidor', 'error');
    })
    .finally(() => {
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalText;
    });
}
let deleteModalInstance = null;

// =========================================
// MODAL DE CONFIRMACIÓN DE ELIMINACIÓN
// =========================================
function showDeleteConfirmModal(id, nombreCompleto) {
    let modalEl = document.getElementById('deleteConfirmModal');

    // Crear el modal dinámicamente si no existe
    if (!modalEl) {
        modalEl = document.createElement('div');
        modalEl.id = 'deleteConfirmModal';
        modalEl.className = 'modal fade';
        modalEl.tabIndex = -1;
        modalEl.setAttribute('aria-hidden', 'true');

        modalEl.innerHTML = `
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header border-0">
                        <h5 class="modal-title text-danger fw-bold">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Eliminar estudiante
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2 delete-confirm-message"></p>
                        <p class="mb-0 text-muted" style="font-size: 0.9rem;">
                            Esta acción no se puede deshacer.
                        </p>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteBtn">
                            <i class="fas fa-trash-alt me-1"></i> Eliminar
                        </button>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modalEl);
    }

    const msgEl = modalEl.querySelector('.delete-confirm-message');
    const confirmBtn = modalEl.querySelector('#confirmDeleteBtn');

    // Mensaje personalizado
    msgEl.textContent = `¿Estás seguro de eliminar al estudiante ${nombreCompleto} (Cédula: ${id})?`;

    // Limpiar handlers anteriores para evitar acumulación
    confirmBtn.onclick = null;

    // Handler de confirmación
    confirmBtn.onclick = function () {
        console.log('🗑️ Eliminando estudiante:', id);

        const originalText = confirmBtn.innerHTML;
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Eliminando...';

        const formData = new FormData();
        formData.append('ID_EST', id);

        fetch('models/eliminar_estudiante.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(resultRaw => {
            console.log('📥 Respuesta eliminar_estudiante:', resultRaw);
            let result = {};
            try {
                result = JSON.parse(resultRaw);
            } catch (e) {
                console.error('❌ Error al parsear JSON:', e);
                throw new Error('Respuesta inválida del servidor: ' + resultRaw);
            }

            const isSuccess = result.success === true || result.ok === true;

            if (isSuccess) {
                showNotification(result.mensaje || '✅ Estudiante eliminado correctamente', 'success');
                console.log('✅ Eliminación exitosa');

                // Ocultar modal de confirmación
                deleteModalInstance.hide();

                // Recargar tabla
                setTimeout(() => {
                    loadTableData(currentSearchTerm);
                }, 300);
            } else {
                const msg = result.errorMsg || result.mensaje || 'Error al eliminar';
                showNotification(msg, 'error');
                console.error('❌ Error al eliminar:', msg);
            }
        })
        .catch(error => {
            console.error('❌ Error de conexión:', error);
            showNotification('Error de conexión con el servidor', 'error');
        })
        .finally(() => {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = originalText;
        });
    };

    // Crear/mostrar instancia de Bootstrap Modal
    if (!deleteModalInstance) {
        deleteModalInstance = new bootstrap.Modal(modalEl, {
            backdrop: 'static',
            keyboard: false
        });
    }

    deleteModalInstance.show();
}

// =========================================
// ELIMINAR ESTUDIANTE
// =========================================
function destroyUser(id, nombreCompleto) {
    if (userRole !== 'SECRETARIO') {
        showNotification('No tienes permisos para eliminar estudiantes', 'warning');
        return;
    }

    showDeleteConfirmModal(id, nombreCompleto);
}

// =========================================
// SISTEMA DE NOTIFICACIONES
// =========================================
function showNotification(message, type = 'info') {
    // Crear notificación toast estilo moderno
    const toast = document.createElement('div');
    toast.className = `notification-toast notification-${type}`;
    
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };
    
    toast.innerHTML = `
        <i class="fas ${icons[type]} me-2"></i>
        <span>${message}</span>
    `;
    
    // Agregar estilos inline
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: white;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        z-index: 9999;
        animation: slideInRight 0.4s ease;
        display: flex;
        align-items: center;
        font-weight: 600;
        min-width: 300px;
        border-left: 4px solid;
    `;
    
    // Colores según tipo
    const colors = {
        success: '#65c68e',
        error: '#ef4444',
        warning: '#e6924d',
        info: '#575cbc'
    };
    
    toast.style.borderLeftColor = colors[type];
    toast.querySelector('i').style.color = colors[type];
    
    document.body.appendChild(toast);
    
    // Auto-eliminar después de 4 segundos
    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.4s ease';
        setTimeout(() => toast.remove(), 400);
    }, 4000);
}

// =========================================
// FUNCIONES EXTRA (REPORTES Y ESTADÍSTICAS)
// =========================================
function generarReporte() {
    showNotification('📄 Generando reporte PDF...', 'info');
    console.log('📄 Generando reporte...');
    
    setTimeout(() => {
        showNotification('Funcionalidad en desarrollo', 'warning');
    }, 1000);
}

function verEstadisticas() {
    showNotification('📊 Abriendo panel de estadísticas...', 'info');
    console.log('📊 Abriendo estadísticas...');
    
    setTimeout(() => {
        showNotification('Funcionalidad en desarrollo', 'warning');
    }, 1000);
}

// =========================================
// ANIMACIONES CSS
// =========================================
const styleSheet = document.createElement('style');
styleSheet.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(styleSheet);

console.log('✅ Sistema Tech Indigo cargado completamente');