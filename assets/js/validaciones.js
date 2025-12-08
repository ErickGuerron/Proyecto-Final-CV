/**
 * Sistema de Validación de Formularios
 * Archivo reutilizable para validación de estudiantes, cursos e inscripciones
 */

// =================== VALIDACIONES GENERALES ===================
const Validaciones = {
    /**
     * Valida que un campo no esté vacío
     */
    requerido: function(valor, nombreCampo) {
        if (!valor || valor.trim() === '') {
            return {
                valido: false,
                mensaje: `El campo ${nombreCampo} es requerido`
            };
        }
        return { valido: true };
    },

    /**
     * Valida formato de cédula ecuatoriana (10 dígitos)
     */
    cedula: function(cedula) {
        if (!cedula || cedula.trim() === '') {
            return {
                valido: false,
                mensaje: 'La cédula es requerida'
            };
        }

        // Remover espacios
        cedula = cedula.trim();

        // Validar que solo contenga números
        if (!/^\d+$/.test(cedula)) {
            return {
                valido: false,
                mensaje: 'La cédula solo debe contener números'
            };
        }

        // Validar longitud
        if (cedula.length !== 10) {
            return {
                valido: false,
                mensaje: 'La cédula debe tener 10 dígitos'
            };
        }

        // Validar algoritmo de cédula ecuatoriana
        const provincia = parseInt(cedula.substring(0, 2));
        if (provincia < 1 || provincia > 24) {
            return {
                valido: false,
                mensaje: 'Los dos primeros dígitos de la cédula no corresponden a una provincia válida'
            };
        }

        const tercerDigito = parseInt(cedula.charAt(2));
        if (tercerDigito > 5) {
            return {
                valido: false,
                mensaje: 'El tercer dígito de la cédula no es válido'
            };
        }

        // Validar dígito verificador
        const coeficientes = [2, 1, 2, 1, 2, 1, 2, 1, 2];
        let suma = 0;

        for (let i = 0; i < 9; i++) {
            let valor = parseInt(cedula.charAt(i)) * coeficientes[i];
            if (valor >= 10) {
                valor -= 9;
            }
            suma += valor;
        }

        const digitoVerificador = parseInt(cedula.charAt(9));
        const resultado = suma % 10 === 0 ? 0 : 10 - (suma % 10);

        if (resultado !== digitoVerificador) {
            return {
                valido: false,
                mensaje: 'La cédula ingresada no es válida'
            };
        }

        return { valido: true };
    },

    /**
     * Valida formato de correo electrónico
     */
    email: function(email) {
        if (!email || email.trim() === '') {
            return {
                valido: false,
                mensaje: 'El correo electrónico es requerido'
            };
        }

        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!regex.test(email)) {
            return {
                valido: false,
                mensaje: 'El formato del correo electrónico no es válido'
            };
        }

        return { valido: true };
    },

    /**
     * Valida teléfono (10 dígitos para Ecuador)
     */
    telefono: function(telefono) {
        if (!telefono || telefono.trim() === '') {
            return {
                valido: false,
                mensaje: 'El teléfono es requerido'
            };
        }

        telefono = telefono.trim();

        if (!/^\d+$/.test(telefono)) {
            return {
                valido: false,
                mensaje: 'El teléfono solo debe contener números'
            };
        }

        if (telefono.length !== 10) {
            return {
                valido: false,
                mensaje: 'El teléfono debe tener 10 dígitos'
            };
        }

        // Validar que comience con 0
        if (!telefono.startsWith('0')) {
            return {
                valido: false,
                mensaje: 'El teléfono debe comenzar con 0'
            };
        }

        return { valido: true };
    },

    /**
     * Valida fecha de nacimiento (no puede ser futura y edad razonable)
     */
    fechaNacimiento: function(fecha) {
        if (!fecha || fecha.trim() === '') {
            return {
                valido: false,
                mensaje: 'La fecha de nacimiento es requerida'
            };
        }

        const fechaNac = new Date(fecha);
        const hoy = new Date();

        // Validar que no sea fecha futura
        if (fechaNac > hoy) {
            return {
                valido: false,
                mensaje: 'La fecha de nacimiento no puede ser futura'
            };
        }

        // Calcular edad
        let edad = hoy.getFullYear() - fechaNac.getFullYear();
        const mes = hoy.getMonth() - fechaNac.getMonth();
        if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNac.getDate())) {
            edad--;
        }

        // Validar edad razonable (entre 5 y 100 años)
        if (edad < 5) {
            return {
                valido: false,
                mensaje: 'El estudiante debe tener al menos 5 años'
            };
        }

        if (edad > 100) {
            return {
                valido: false,
                mensaje: 'La fecha de nacimiento no parece ser válida'
            };
        }

        return { valido: true };
    },

    /**
     * Valida longitud mínima
     */
    longitudMinima: function(valor, nombreCampo, minimo) {
        if (!valor || valor.trim().length < minimo) {
            return {
                valido: false,
                mensaje: `El campo ${nombreCampo} debe tener al menos ${minimo} caracteres`
            };
        }
        return { valido: true };
    },

    /**
     * Valida longitud máxima
     */
    longitudMaxima: function(valor, nombreCampo, maximo) {
        if (valor && valor.trim().length > maximo) {
            return {
                valido: false,
                mensaje: `El campo ${nombreCampo} no puede exceder ${maximo} caracteres`
            };
        }
        return { valido: true };
    },

    /**
     * Valida que solo contenga letras y espacios
     */
    soloLetras: function(valor, nombreCampo) {
        if (!valor || valor.trim() === '') {
            return {
                valido: false,
                mensaje: `El campo ${nombreCampo} es requerido`
            };
        }

        const regex = /^[a-záéíóúñA-ZÁÉÍÓÚÑ\s]+$/;
        if (!regex.test(valor)) {
            return {
                valido: false,
                mensaje: `El campo ${nombreCampo} solo debe contener letras`
            };
        }

        return { valido: true };
    }
};

// =================== VALIDADORES POR ENTIDAD ===================

/**
 * Validador para formulario de Estudiante
 */
const ValidadorEstudiante = {
    validar: function(datosFormulario) {
        const errores = [];

        // Validar cédula
        const resultCedula = Validaciones.cedula(datosFormulario.id_est);
        if (!resultCedula.valido) {
            errores.push(resultCedula.mensaje);
        }

        // Validar nombre
        const resultNombre = Validaciones.soloLetras(datosFormulario.nom_est, 'Nombre');
        if (!resultNombre.valido) {
            errores.push(resultNombre.mensaje);
        } else {
            const resultNombreMin = Validaciones.longitudMinima(datosFormulario.nom_est, 'Nombre', 2);
            if (!resultNombreMin.valido) {
                errores.push(resultNombreMin.mensaje);
            }
        }

        // Validar apellido
        const resultApellido = Validaciones.soloLetras(datosFormulario.ape_est, 'Apellido');
        if (!resultApellido.valido) {
            errores.push(resultApellido.mensaje);
        } else {
            const resultApellidoMin = Validaciones.longitudMinima(datosFormulario.ape_est, 'Apellido', 2);
            if (!resultApellidoMin.valido) {
                errores.push(resultApellidoMin.mensaje);
            }
        }

        // Validar dirección
        const resultDir = Validaciones.requerido(datosFormulario.dir_est, 'Dirección');
        if (!resultDir.valido) {
            errores.push(resultDir.mensaje);
        } else {
            const resultDirMin = Validaciones.longitudMinima(datosFormulario.dir_est, 'Dirección', 5);
            if (!resultDirMin.valido) {
                errores.push(resultDirMin.mensaje);
            }
        }

        // Validar teléfono
        const resultTel = Validaciones.telefono(datosFormulario.tel_est);
        if (!resultTel.valido) {
            errores.push(resultTel.mensaje);
        }

        return {
            valido: errores.length === 0,
            errores: errores
        };
    }
};

/**
 * Validador para formulario de Curso (si se necesita en el futuro)
 */
const ValidadorCurso = {
    validar: function(datosFormulario) {
        const errores = [];

        // Validar nombre del curso
        const resultNombre = Validaciones.requerido(datosFormulario.NOM_CUR, 'Nombre del Curso');
        if (!resultNombre.valido) {
            errores.push(resultNombre.mensaje);
        } else {
            const resultNombreMin = Validaciones.longitudMinima(datosFormulario.NOM_CUR, 'Nombre del Curso', 3);
            if (!resultNombreMin.valido) {
                errores.push(resultNombreMin.mensaje);
            }
        }

        // Validar descripción
        const resultDesc = Validaciones.requerido(datosFormulario.DES_CUR, 'Descripción');
        if (!resultDesc.valido) {
            errores.push(resultDesc.mensaje);
        } else {
            const resultDescMin = Validaciones.longitudMinima(datosFormulario.DES_CUR, 'Descripción', 10);
            if (!resultDescMin.valido) {
                errores.push(resultDescMin.mensaje);
            }
        }

        return {
            valido: errores.length === 0,
            errores: errores
        };
    }
};

/**
 * Validador para formulario de Inscripción (si se necesita en el futuro)
 */
const ValidadorInscripcion = {
    validar: function(datosFormulario) {
        const errores = [];

        // Validar que se haya seleccionado estudiante
        const resultEst = Validaciones.requerido(datosFormulario.ID_EST_INS, 'Estudiante');
        if (!resultEst.valido) {
            errores.push(resultEst.mensaje);
        }

        // Validar que se haya seleccionado curso
        const resultCur = Validaciones.requerido(datosFormulario.ID_CUR_INS, 'Curso');
        if (!resultCur.valido) {
            errores.push(resultCur.mensaje);
        }

        return {
            valido: errores.length === 0,
            errores: errores
        };
    }
};

// =================== UTILIDADES ===================

/**
 * Muestra mensajes de error en la interfaz
 */
function mostrarErrores(errores) {
    let mensaje = '<ul style="text-align:left; padding-left:20px;">';
    errores.forEach(function(error) {
        mensaje += '<li>' + error + '</li>';
    });
    mensaje += '</ul>';

    $.messager.alert({
        title: 'Errores de Validación',
        msg: mensaje,
        icon: 'error',
        width: 450
    });
}

/**
 * Obtiene los datos de un formulario jQuery EasyUI
 */
function obtenerDatosFormulario(idFormulario) {
    var form = $(idFormulario);
    var data = {};
    
    // Obtener todos los campos del formulario
    form.find('input[name]').each(function() {
        var input = $(this);
        var name = input.attr('name');
        var value = '';
        
        // Verificar si es un textbox de EasyUI
        if (input.hasClass('easyui-textbox')) {
            value = input.textbox('getValue');
        } 
        // Verificar si es un datebox de EasyUI
        else if (input.hasClass('easyui-datebox')) {
            value = input.datebox('getValue');
        }
        // Verificar si es un combobox de EasyUI
        else if (input.hasClass('easyui-combobox')) {
            value = input.combobox('getValue');
        }
        // Campo normal
        else {
            value = input.val();
        }
        
        data[name] = value;
    });
    
    return data;
}

// Exportar funciones para uso global
window.Validaciones = Validaciones;
window.ValidadorEstudiante = ValidadorEstudiante;
window.ValidadorCurso = ValidadorCurso;
window.ValidadorInscripcion = ValidadorInscripcion;
window.mostrarErrores = mostrarErrores;
window.obtenerDatosFormulario = obtenerDatosFormulario;