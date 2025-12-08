<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Estudiantes - UTA</title>
    
    <!-- Fuentes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- jQuery EasyUI -->
    <link rel="stylesheet" type="text/css" href="jquery/themes/default/easyui.css">
    <link rel="stylesheet" type="text/css" href="jquery/themes/icon.css">
    <link rel="stylesheet" type="text/css" href="jquery/themes/color.css">

    <!-- Hoja de estilos personalizada -->
    <link rel="stylesheet" type="text/css" href="assets/css/tabla.css">

    <!-- FontAwesome (para el ícono del PDF en el modal de reportes) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- jQuery y EasyUI -->
    <script type="text/javascript" src="jquery/jquery.min.js"></script>
    <script type="text/javascript" src="jquery/jquery.easyui.min.js"></script>

    <!-- Validaciones reutilizables -->
    <script type="text/javascript" src="assets/js/validaciones.js"></script>

    <!-- =========================================================================================
         ESTILOS CSS PERSONALIZADOS (TECH INDIGO THEME)
    ========================================================================================== -->
    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f3f4f6;
            margin: 0;
            padding: 0;
        }

        /* Ventanas (Dialogs) */
        .window {
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            border: 1px solid #ddd;
        }
        .window-header {
            background: #575cbc; /* Tech Indigo Purple */
            color: white;
            padding: 10px;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        .panel-title { 
            font-weight: bold; 
            font-size: 16px; 
            color: white; 
        }
        .panel-tool-close { 
            background: url('assets/jquery/themes/icons/cancel.png') no-repeat center center; 
            filter: brightness(0) invert(1); 
        }

        /* Botones EasyUI */
        .l-btn {
            background: #f0f0f0; 
            border: 1px solid #ccc; 
            border-radius: 20px;
            padding: 5px 15px; 
            margin: 0 5px; 
            transition: all 0.3s;
        }
        .l-btn:hover { 
            background: #e0e0e0; 
            border-color: #bbb; 
        }
        .c6, .l-btn-primary { 
            background: #65c68e !important; 
            color: white !important; 
            border: none !important; 
        }
        .c6:hover { 
            background: #57b07d !important; 
        }
        .l-btn-text { 
            font-size: 14px; 
            line-height: 20px; 
        }

        /* Formularios */
        .fitem { 
            margin-bottom: 20px; 
        }
        .fitem label { 
            display: inline-block; 
            width: 120px; 
            font-weight: bold; 
            color: #575cbc; 
        }
        .textbox .textbox-text { 
            padding: 5px; 
            border-radius: 4px; 
        }
        
        /* Alertas */
        .messager-body { 
            padding: 20px !important; 
            font-size: 14px; 
        }
        .messager-icon { 
            margin-right: 10px; 
        }

        /* Contenedor principal */
        .app-wrapper {
            max-width: 1200px;
            margin: 25px auto;
            background: #ffffff;
            border-radius: 12px;
            padding: 16px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12);
        }

        .app-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        .app-header__icon {
            width: 40px;
            height: 40px;
            border-radius: 999px;
            background: radial-gradient(circle at 30% 20%, #818cf8, #4f46e5);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .app-header__icon svg {
            width: 24px;
            height: 24px;
        }

        .app-header__text h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
            color: #111827;
        }

        .app-header__text p {
            margin: 0;
            font-size: 13px;
            color: #6b7280;
        }

        .easyui-tabs {
            border-radius: 8px;
            overflow: hidden;
        }

        /* ================== MODAL DE REPORTES (SOLO ESTUDIANTES) ================== */
        .report-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.65);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            backdrop-filter: blur(3px);
        }
        .report-modal-overlay.activo {
            display: flex;
        }
        .report-modal {
            background: #fff;
            border-radius: 16px;
            width: 90%;
            max-width: 1100px;
            height: 85vh;
            max-height: 850px;
            min-height: 500px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: modalSlideIn 0.3s ease-out;
        }
        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: scale(0.95) translateY(-20px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
        .report-modal__header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: linear-gradient(135deg, #575cbc 0%, #4a4fa8 100%);
            color: #fff;
            padding: 16px 24px;
            border-bottom: 3px solid rgba(255,255,255,0.1);
        }
        .report-modal__titulo {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .report-modal__titulo i {
            font-size: 20px;
        }
        .report-modal__cerrar {
            background: rgba(255,255,255,0.15);
            border: none;
            color: #fff;
            font-size: 24px;
            cursor: pointer;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .report-modal__cerrar:hover {
            background: rgba(255,255,255,0.25);
            transform: rotate(90deg);
        }
        .report-modal__body {
            flex: 1;
            background: #f6f7fb;
            padding: 0;
            overflow: hidden;
        }
        #report-iframe {
            width: 100%;
            height: 100%;
            border: none;
            background: #fff;
        }
        .report-modal__footer {
            padding: 14px 24px;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            background: #fff;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <?php 
        if(!isset($_SESSION)) { session_start(); }
        $rol = isset($_SESSION['user']['ROL_USU']) ? $_SESSION['user']['ROL_USU'] : 'GUEST'; 
    ?>

    <div class="app-wrapper">
        <header class="app-header">
            <div class="app-header__icon">
                <!-- Ícono simple de "graduación" -->
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="M12 3L2 8.5L12 14L22 8.5L12 3Z" fill="currentColor"/>
                    <path d="M6 11V16L12 20L18 16V11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="app-header__text">
                <h2>Sistema de Gestión de Estudiantes</h2>
                <p>Administre estudiantes, cursos e inscripciones desde un solo panel</p>
            </div>
        </header>

        <div class="easyui-tabs" style="width:100%; height:600px;">
            
            <!-- ======================= 1. PESTAÑA ESTUDIANTES ======================= -->
            <div title="Estudiantes" style="padding:10px">
                <table id="dg-est" class="easyui-datagrid" style="width:100%;height:100%"
                       url="models/obtener_estudiante.php"
                       toolbar="#toolbar-est" pagination="true"
                       rownumbers="true" fitColumns="true" singleSelect="true">
                    <thead>
                        <tr>
                            <th field="ID_EST" width="50">Cédula</th>
                            <th field="NOM_EST" width="50">Nombre</th>
                            <th field="APE_EST" width="50">Apellido</th>
                            <th field="TEL_EST" width="50">Teléfono</th>
                            <th field="COR_EST" width="80">Correo</th>
                            <th field="FEC_NAC" width="50">Fec. Nac.</th>
                        </tr>
                    </thead>
                </table>
                
                <div id="toolbar-est" style="padding: 8px; background-color: #f8f9fa;">
                    <?php if ($rol === 'SECRETARIO'): ?>
                        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="newStudent()">Nuevo</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="editStudent()">Editar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="destroyStudent()">Eliminar</a>
                        <span style="border-left: 1px solid #ccc; margin: 0 10px;"></span>
                    <?php endif; ?>
                    
                    <span>Buscar Cédula:</span>
                    <input id="search-est"
                           type="text"
                           style="width:200px; padding:5px; border:1px solid #ccc; border-radius:4px;"
                           placeholder="Escribe la cédula..."
                           onkeyup="doSearchEstDynamic()">
                    
                    <!-- ===== Botones de REPORTES SOLO PARA ESTUDIANTES ===== -->
                    <span style="border-left: 1px solid #ccc; margin: 0 10px;"></span>
                    <a href="javascript:void(0)"
                       class="easyui-linkbutton"
                       iconCls="icon-print"
                       plain="true"
                       onclick="openReportEstudianteIndividual()">
                        Reporte Estudiante
                    </a>
                    <a href="javascript:void(0)"
                       class="easyui-linkbutton"
                       iconCls="icon-save"
                       plain="true"
                       onclick="openReportModal('cursos')">
                        Estudiantes y Cursos
                    </a>
                    <a href="javascript:void(0)"
                       class="easyui-linkbutton"
                       iconCls="icon-chart"
                       plain="true"
                       onclick="openReportModal('grafico')">
                        Gráfico Cursos
                    </a>
                </div>
            </div>

            <!-- ======================= 2. PESTAÑA CURSOS ======================= -->
            <div title="Cursos" style="padding:10px">
                <table id="dg-cur" class="easyui-datagrid" style="width:100%;height:100%"
                       url="models/obtener_cursos.php"
                       toolbar="#toolbar-cur" pagination="true"
                       rownumbers="true" fitColumns="true" singleSelect="true">
                    <thead>
                        <tr>
                            <th field="ID_CUR" width="40">ID</th>
                            <th field="NOM_CUR" width="100">Curso</th>
                            <th field="DES_CUR" width="200">Descripción</th>
                            <th field="FEC_CRE" width="50">Fec. Creación</th>
                        </tr>
                    </thead>
                </table>

                <div id="toolbar-cur" style="padding: 8px; background-color: #f8f9fa;">
                    <?php if ($rol === 'SECRETARIO'): ?>
                        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="newCourse()">Nuevo Curso</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="editCourse()">Editar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="destroyCourse()">Eliminar</a>
                        <span style="border-left: 1px solid #ccc; margin: 0 10px;"></span>
                    <?php endif; ?>
                    <span>Buscar ID:</span>
                    <input id="search-cur"
                           type="text"
                           style="width:200px; padding:5px; border:1px solid #ccc; border-radius:4px;"
                           placeholder="ID Curso..."
                           onkeyup="doSearchCurDynamic()">
                </div>
            </div>

            <!-- ======================= 3. PESTAÑA INSCRIPCIONES ======================= -->
            <div title="Inscripciones" style="padding:10px">
                <table id="dg-ins" class="easyui-datagrid" style="width:100%;height:100%"
                       url="models/obtener_inscripciones.php"
                       toolbar="#toolbar-ins" pagination="true"
                       rownumbers="true" fitColumns="true" singleSelect="true">
                    <thead>
                        <tr>
                            <th field="ID_INS" width="30">ID</th>
                            <th field="NOM_EST" width="100" formatter="formatStudentName">Estudiante</th>
                            <th field="NOM_CUR" width="100">Curso</th>
                            <th field="FEC_INS" width="50">Fec. Inscripción</th>
                        </tr>
                    </thead>
                </table>

                <div id="toolbar-ins" style="padding: 8px; background-color: #f8f9fa;">
                    <?php if ($rol === 'SECRETARIO'): ?>
                        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="newEnrollment()">Inscribir</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="editEnrollment()">Editar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="destroyEnrollment()">Eliminar</a>
                        <span style="border-left: 1px solid #ccc; margin: 0 10px;"></span>
                    <?php endif; ?>
                    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-reload" plain="true" onclick="$('#dg-ins').datagrid('reload')">Recargar</a>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= MODALES (DIALOGS) ================= -->

    <!-- Modal Estudiante -->
    <div id="dlg-est" class="easyui-dialog" style="width:500px"
         data-options="closed:true,modal:true,border:'thin',buttons:'#dlg-buttons-est'">
        <form id="fm-est" method="post" novalidate style="margin:0;padding:20px 50px">
            <h3 style="color:#575cbc; border-bottom:1px solid #ddd; padding-bottom:10px;">Datos Estudiante</h3>
            <div class="fitem">
                <label>Cédula:</label>
                <input name="ID_EST" class="easyui-textbox" required="true" id="input-id-est">
            </div>
            <div class="fitem">
                <label>Nombre:</label>
                <input name="NOM_EST" class="easyui-textbox" required="true">
            </div>
            <div class="fitem">
                <label>Apellido:</label>
                <input name="APE_EST" class="easyui-textbox" required="true">
            </div>
            <div class="fitem">
                <label>Teléfono:</label>
                <input name="TEL_EST" class="easyui-textbox" required="true">
            </div>
            <div class="fitem">
                <label>Correo:</label>
                <input name="COR_EST" class="easyui-textbox" required="true" validType="email">
            </div>
            <div class="fitem">
                <label>Dirección:</label>
                <input name="DIR_EST" class="easyui-textbox" required="true">
            </div>
            <div class="fitem">
                <label>Fec. Nac:</label>
                <input name="FEC_NAC" class="easyui-datebox" required="true" data-options="formatter:myformatter,parser:myparser">
            </div>
        </form>
    </div>
    <div id="dlg-buttons-est">
        <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveStudent()" style="width:120px; font-weight:bold;">Guardar</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg-est').dialog('close')" style="width:120px">Cancelar</a>
    </div>

    <!-- Modal Curso -->
    <div id="dlg-cur" class="easyui-dialog" style="width:500px"
         data-options="closed:true,modal:true,border:'thin',buttons:'#dlg-buttons-cur'">
        <form id="fm-cur" method="post" novalidate style="margin:0;padding:20px 50px">
            <h3 style="color:#575cbc; border-bottom:1px solid #ddd; padding-bottom:10px;">Datos Curso</h3>
            
            <!-- Campo ID Oculto al crear -->
            <div class="fitem" id="div-id-cur">
                <label>ID Curso:</label>
                <input name="ID_CUR" class="easyui-textbox" id="input-id-cur">
            </div>

            <div class="fitem">
                <label>Nombre:</label>
                <input name="NOM_CUR" class="easyui-textbox" required="true">
            </div>
            <div class="fitem">
                <label>Descripción:</label>
                <input name="DES_CUR" class="easyui-textbox" required="true" style="height:60px" data-options="multiline:true">
            </div>
        </form>
    </div>
    <div id="dlg-buttons-cur">
        <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveCourse()" style="width:120px; font-weight:bold;">Guardar</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg-cur').dialog('close')" style="width:120px">Cancelar</a>
    </div>

    <!-- Modal Inscripción -->
    <div id="dlg-ins" class="easyui-dialog" style="width:500px"
         data-options="closed:true,modal:true,border:'thin',buttons:'#dlg-buttons-ins'">
        <form id="fm-ins" method="post" novalidate style="margin:0;padding:20px 50px">
            <h3 style="color:#575cbc; border-bottom:1px solid #ddd; padding-bottom:10px;">Datos Inscripción</h3>
            <input type="hidden" name="ID_INS">
            <div class="fitem">
                <label>Estudiante:</label>
                <input name="ID_EST_INS" class="easyui-combobox" required="true" style="width:260px;"
                    data-options="
                        valueField:'ID_EST',
                        textField:'NOM_EST',
                        url:'models/obtener_estudiante.php',
                        editable:false,
                        formatter:function(row){
                            return row.ID_EST + ' - ' + row.NOM_EST + ' ' + row.APE_EST;
                        }">
            </div>
            <div class="fitem">
                <label>Curso:</label>
                <input name="ID_CUR_INS" class="easyui-combobox" required="true" style="width:260px;"
                    data-options="
                        valueField:'ID_CUR',
                        textField:'NOM_CUR',
                        url:'models/obtener_cursos.php',
                        editable:false,
                        formatter:function(row){
                            return row.ID_CUR + ' - ' + row.NOM_CUR;
                        }">
            </div>
        </form>
    </div>
    <div id="dlg-buttons-ins">
        <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveEnrollment()" style="width:120px; font-weight:bold;">Guardar</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg-ins').dialog('close')" style="width:120px">Cancelar</a>
    </div>

    <!-- ================= MODAL DE REPORTES (SOLO ESTUDIANTES) ================= -->
    <div id="report-modal" class="report-modal-overlay" onclick="closeReportModal(event)">
        <div class="report-modal" onclick="event.stopPropagation()">
            <div class="report-modal__header">
                <h3 id="report-modal-title" class="report-modal__titulo">
                    <i class="fas fa-file-pdf"></i>
                    <span>Reporte de Estudiantes</span>
                </h3>
                <button type="button"
                        class="report-modal__cerrar"
                        onclick="closeReportModal()">
                    &times;
                </button>
            </div>
            <div class="report-modal__body">
                <iframe id="report-iframe" src=""></iframe>
            </div>
            <div class="report-modal__footer">
                <a href="javascript:void(0)"
                   class="easyui-linkbutton"
                   iconCls="icon-print"
                   onclick="printReport()">
                    Imprimir
                </a>
                <a href="javascript:void(0)"
                   class="easyui-linkbutton"
                   iconCls="icon-save"
                   onclick="downloadReport()">
                    Descargar
                </a>
            </div>
        </div>
    </div>

    <!-- ================= LÓGICA JAVASCRIPT ================= -->
    <script type="text/javascript">
        var url;
        var currentReportUrl = '';
        var currentRole = '<?php echo $rol; ?>';
        var searchTimeoutEst = null;
        var searchTimeoutCur = null;

        // ================= GESTIÓN DE PERMISOS =================
        function canModifyData() {
            return currentRole === 'SECRETARIO';
        }

        function ensureCanModifyData() {
            if (!canModifyData()) {
                $.messager.alert({
                    title: 'Permiso denegado',
                    msg: 'Su rol no está autorizado para crear, editar o eliminar registros. Solo puede consultar datos y generar reportes.',
                    icon: 'warning'
                });
                return false;
            }
            return true;
        }

        // -- UTILS --
        function myformatter(date){
            var y = date.getFullYear(); 
            var m = date.getMonth()+1; 
            var d = date.getDate();
            return y+'-'+(m<10?('0'+m):m)+'-'+(d<10?('0'+d):d);
        }
        function myparser(s){
            if (!s) return new Date();
            var ss = (s.split('-'));
            return new Date(parseInt(ss[0],10),parseInt(ss[1],10)-1,parseInt(ss[2],10));
        }
        function formatStudentName(val, row){ 
            return row.NOM_EST + ' ' + row.APE_EST; 
        }

        // --- MANEJO CENTRALIZADO DE RESPUESTAS ---
        function handleFormResponse(result, dialogId, gridId, successMsg) {
            try {
                var data = JSON.parse(result);
                if (data.error || data.errorMsg || data.success === false || data.ok === false) {
                    $.messager.alert({
                        title: 'Error de Validación',
                        msg: '<span style="color:red; font-weight:bold">' + (data.error || data.errorMsg || data.mensaje || "Error desconocido") + '</span>',
                        icon: 'error',
                        width: 400
                    });
                } else {
                    $(dialogId).dialog('close');
                    $(gridId).datagrid('reload');
                    $.messager.show({
                        title: 'Éxito',
                        msg: successMsg || 'Operación realizada correctamente',
                        timeout: 3000,
                        showType: 'slide'
                    });
                }
            } catch(e) {
                console.error(result);
                $.messager.alert({
                    title: 'Error del Sistema',
                    msg: 'Ocurrió un error inesperado. Revise la consola.',
                    icon: 'error'
                });
            }
        }

        // ========================= BÚSQUEDA DINÁMICA ESTUDIANTES =========================
        function doSearchEstDynamic() {
            if (searchTimeoutEst) {
                clearTimeout(searchTimeoutEst);
            }

            searchTimeoutEst = setTimeout(function() {
                var searchValue = document.getElementById('search-est').value.trim();
                
                if (searchValue === '') {
                    $('#dg-est').datagrid({
                        url: 'models/obtener_estudiante.php'
                    });
                } else {
                    $('#dg-est').datagrid({
                        url: 'models/obtener_estudiante_id.php?ID_EST=' + encodeURIComponent(searchValue)
                    });
                }
            }, 500);
        }

        // ========================= BÚSQUEDA DINÁMICA CURSOS =========================
        function doSearchCurDynamic() {
            if (searchTimeoutCur) {
                clearTimeout(searchTimeoutCur);
            }

            searchTimeoutCur = setTimeout(function() {
                var searchValue = document.getElementById('search-cur').value.trim();
                
                if (searchValue === '') {
                    $('#dg-cur').datagrid({
                        url: 'models/obtener_cursos.php'
                    });
                } else {
                    $('#dg-cur').datagrid({
                        url: 'models/obtener_curso_id.php?ID_CUR=' + encodeURIComponent(searchValue)
                    });
                }
            }, 500);
        }

        // ========================= ESTUDIANTE =========================
        function newStudent(){
            if (!ensureCanModifyData()) return;
            $('#dlg-est').dialog('open').dialog('setTitle','Nuevo Estudiante');
            $('#fm-est').form('clear');
            $('#input-id-est').textbox('readonly', false);
            url = 'models/agregar_estudiante.php';
        }

        function editStudent(){
            if (!ensureCanModifyData()) return;
            var row = $('#dg-est').datagrid('getSelected');
            if (row){
                $('#dlg-est').dialog('open').dialog('setTitle','Editar Estudiante');
                $('#fm-est').form('load',row);
                $('#input-id-est').textbox('readonly', true);
                url = 'models/actualizar_estudiante.php';
            } else {
                $.messager.alert({title:'Aviso', msg:'Seleccione un estudiante.', icon:'info'});
            }
        }

        // ======= saveStudent con validaciones.js =======
        function saveStudent() {
            if (!ensureCanModifyData()) return;

            // VALIDACIÓN PERSONALIZADA ANTES DE ENVIAR
            if (window.obtenerDatosFormulario && window.ValidadorEstudiante && window.Validaciones && window.mostrarErrores) {

                var datosRaw = obtenerDatosFormulario('#fm-est');

                // Adaptar nombres a lo que espera ValidadorEstudiante
                var datosEstudiante = {
                    id_est: datosRaw.ID_EST || '',
                    nom_est: datosRaw.NOM_EST || '',
                    ape_est: datosRaw.APE_EST || '',
                    dir_est: datosRaw.DIR_EST || '',
                    tel_est: datosRaw.TEL_EST || ''
                };

                var errores = [];

                // 1. Validación de Estudiante
                var resultadoEst = ValidadorEstudiante.validar(datosEstudiante);
                if (!resultadoEst.valido) {
                    errores = errores.concat(resultadoEst.errores);
                }

                // 2. Correo
                var correo = datosRaw.COR_EST || '';
                var resEmail = Validaciones.email(correo);
                if (!resEmail.valido) {
                    errores.push(resEmail.mensaje);
                }

                // 3. Fecha de nacimiento
                var fecNac = datosRaw.FEC_NAC || '';
                var resFecha = Validaciones.fechaNacimiento(fecNac);
                if (!resFecha.valido) {
                    errores.push(resFecha.mensaje);
                }

                // 4. Si hay errores, no se envía
                if (errores.length > 0) {
                    mostrarErrores(errores);
                    return;
                }
            } else {
                console.warn('validaciones.js no está disponible, se usa solo la validación de EasyUI.');
            }

            // ENVÍO DEL FORMULARIO (EasyUI)
            $('#fm-est').form('submit', {
                url: url,
                onSubmit: function () {
                    return $(this).form('validate');
                },
                success: function (result) {
                    handleFormResponse(result, '#dlg-est', '#dg-est', 'Estudiante guardado correctamente');
                }
            });
        }

        function destroyStudent(){
            if (!ensureCanModifyData()) return;
            var row = $('#dg-est').datagrid('getSelected');
            if (row){
                $.messager.confirm({
                    title:'Confirmar', 
                    msg:'¿Eliminar estudiante?', 
                    fn:function(r){
                        if (r){
                            $.post(
                                'models/eliminar_estudiante.php',
                                {ID_EST:row.ID_EST},
                                function(result){
                                    if (result.success || result.ok) {
                                        $('#dg-est').datagrid('reload');
                                        $.messager.show({title:'Éxito', msg:'Estudiante eliminado'});
                                    } else {
                                        $.messager.alert({
                                            title:'Error', 
                                            msg: result.errorMsg || result.error || 'No se pudo eliminar', 
                                            icon:'error'
                                        });
                                    }
                                },
                                'json'
                            );
                        }
                    }
                });
            } else {
                $.messager.alert({title:'Aviso', msg:'Seleccione un estudiante.', icon:'info'});
            }
        }

        // ========================= CURSO =========================
        function newCourse(){
            if (!ensureCanModifyData()) return;
            $('#dlg-cur').dialog('open').dialog('setTitle','Nuevo Curso');
            $('#fm-cur').form('clear');
            $('#div-id-cur').hide();
            $('#input-id-cur').textbox('disableValidation');
            url = 'models/agregar_curso.php';
        }

        function editCourse(){
            if (!ensureCanModifyData()) return;
            var row = $('#dg-cur').datagrid('getSelected');
            if (row){
                $('#dlg-cur').dialog('open').dialog('setTitle','Editar Curso');
                $('#fm-cur').form('load',row);
                $('#div-id-cur').show();
                $('#input-id-cur').textbox('readonly', true);
                url = 'models/actualizar_curso.php';
            } else {
                $.messager.alert({title:'Aviso', msg:'Seleccione un curso.', icon:'info'});
            }
        }

        // ======= saveCourse con validaciones.js =======
        function saveCourse(){
            if (!ensureCanModifyData()) return;

            if (window.obtenerDatosFormulario && window.ValidadorCurso && window.mostrarErrores) {

                var datos = obtenerDatosFormulario('#fm-cur');

                var resultado = ValidadorCurso.validar(datos);
                if (!resultado.valido) {
                    mostrarErrores(resultado.errores);
                    return;
                }
            } else {
                console.warn('validaciones.js no está disponible para cursos, se usa solo la validación de EasyUI.');
            }

            $('#fm-cur').form('submit', {
                url: url,
                onSubmit: function () {
                    return $(this).form('validate');
                },
                success: function(result){
                    handleFormResponse(result, '#dlg-cur', '#dg-cur', 'Curso guardado correctamente');
                }
            });
        }

        function destroyCourse(){
            if (!ensureCanModifyData()) return;
            var row = $('#dg-cur').datagrid('getSelected');
            if (row){
                $.messager.confirm({
                    title:'Confirmar', 
                    msg:'¿Eliminar curso?', 
                    fn:function(r){
                        if (r){
                            $.post(
                                'models/eliminar_curso.php',
                                {ID_CUR:row.ID_CUR},
                                function(result){
                                    if (result.success || result.ok) {
                                        $('#dg-cur').datagrid('reload');
                                        $.messager.show({title:'Éxito', msg:'Curso eliminado'});
                                    } else {
                                        $.messager.alert({
                                            title:'Error', 
                                            msg: result.errorMsg || result.error || 'No se pudo eliminar', 
                                            icon:'error'
                                        });
                                    }
                                },
                                'json'
                            );
                        }
                    }
                });
            } else {
                $.messager.alert({title:'Aviso', msg:'Seleccione un curso.', icon:'info'});
            }
        }

        // ========================= INSCRIPCIÓN =========================
        function newEnrollment(){
            if (!ensureCanModifyData()) return;
            $('#dlg-ins').dialog('open').dialog('setTitle','Nueva Inscripción');
            $('#fm-ins').form('clear');
            url = 'models/agregar_inscripcion.php';
        }

        function editEnrollment(){
            if (!ensureCanModifyData()) return;
            var row = $('#dg-ins').datagrid('getSelected');
            if (row){
                $('#dlg-ins').dialog('open').dialog('setTitle','Editar Inscripción');
                $('#fm-ins').form('load',row);
                $('#fm-ins').form('load', {
                    ID_EST_INS: row.ID_EST_INS,
                    ID_CUR_INS: row.ID_CUR_INS
                });
                url = 'models/actualizar_inscripcion.php';
            } else {
                $.messager.alert({title:'Aviso', msg:'Seleccione una inscripción.', icon:'info'});
            }
        }

        // ======= saveEnrollment con validaciones.js =======
        function saveEnrollment(){
            if (!ensureCanModifyData()) return;

            if (window.obtenerDatosFormulario && window.ValidadorInscripcion && window.mostrarErrores) {

                var datos = obtenerDatosFormulario('#fm-ins');

                var resultado = ValidadorInscripcion.validar(datos);
                if (!resultado.valido) {
                    mostrarErrores(resultado.errores);
                    return;
                }
            } else {
                console.warn('validaciones.js no está disponible para inscripciones, se usa solo la validación de EasyUI.');
            }

            $('#fm-ins').form('submit',{
                url: url,
                onSubmit: function(){ 
                    return $(this).form('validate'); 
                },
                success: function(result){
                    console.log("Success callback for saveEnrollment fired. Result:", result);
                    try {
                        var data = JSON.parse(result);
                        if (data.ok || data.success) {
                            $('#dlg-ins').dialog('close');
                            $('#dg-ins').datagrid('reload');
                            $.messager.show({
                                title: 'Éxito',
                                msg: 'Inscripción procesada correctamente',
                                timeout: 3000,
                                showType: 'slide'
                            });
                        } else {
                            $.messager.alert({
                                title: 'Error de Validación',
                                msg: '<span style="color:red; font-weight:bold">' + (data.mensaje || data.error || data.errorMsg || "Error desconocido") + '</span>',
                                icon: 'error',
                                width: 400
                            });
                        }
                    } catch(e) {
                        console.error("Error al parsear JSON o en la lógica de success:", e);
                        $.messager.alert({
                            title: 'Error del Sistema',
                            msg: 'Ocurrió un error inesperado al procesar la respuesta. Revise la consola.',
                            icon: 'error'
                        });
                    }
                }
            });
        }

        function destroyEnrollment(){
            if (!ensureCanModifyData()) return;
            var row = $('#dg-ins').datagrid('getSelected');
            if (row){
                $.messager.confirm({
                    title:'Confirmar', 
                    msg:'¿Eliminar inscripción?', 
                    fn:function(r){
                        if (r){
                            $.post(
                                'models/eliminar_inscripcion.php',
                                {ID_INS:row.ID_INS},
                                function(result){
                                    if (result.success || result.ok) {
                                        $('#dg-ins').datagrid('reload');
                                        $.messager.show({title:'Éxito', msg:'Inscripción eliminada'});
                                    } else {
                                        $.messager.alert({
                                            title:'Error', 
                                            msg: result.errorMsg || result.error || 'No se pudo eliminar', 
                                            icon:'error'
                                        });
                                    }
                                },
                                'json'
                            );
                        }
                    }
                });
            } else {
                $.messager.alert({title:'Aviso', msg:'Seleccione una inscripción.', icon:'info'});
            }
        }

        // =================== LÓGICA DE REPORTES (SOLO ESTUDIANTES) ===================
        function openReportEstudianteIndividual() {
            var row = $('#dg-est').datagrid('getSelected');
            
            if (!row) {
                $.messager.alert({
                    title: 'Selección Requerida',
                    msg: 'Por favor, seleccione un estudiante de la tabla para generar el reporte.',
                    icon: 'warning'
                });
                return;
            }

            var modal = document.getElementById('report-modal');
            var iframe = document.getElementById('report-iframe');
            var titleElement = document.querySelector('#report-modal-title span');

            // Enviar siempre id_est (en minúsculas) al PHP
            currentReportUrl = 'views/report_estudiante.php?id_est=' + encodeURIComponent(row.ID_EST);
            titleElement.textContent = 'Reporte: ' + row.NOM_EST + ' ' + row.APE_EST;

            iframe.src = currentReportUrl;
            modal.classList.add('activo');
            document.body.style.overflow = 'hidden';
        }

        function openReportModal(type) {
            var modal = document.getElementById('report-modal');
            var iframe = document.getElementById('report-iframe');
            var titleElement = document.querySelector('#report-modal-title span');

            if (type === 'cursos') {
                currentReportUrl = 'views/report_estudiantes_cursos.php';
                titleElement.textContent = 'Estudiantes y Cursos';
            } else if (type === 'grafico') {
                currentReportUrl = 'views/report_grafico_cursos.php';
                titleElement.textContent = 'Gráfico de Cursos';
            }

            iframe.src = currentReportUrl;
            modal.classList.add('activo');
            document.body.style.overflow = 'hidden';
        }

        function closeReportModal(event) {
            if (event && event.target && event.currentTarget && event.target !== event.currentTarget) {
                return;
            }

            var modal = document.getElementById('report-modal');
            var iframe = document.getElementById('report-iframe');

            modal.classList.remove('activo');
            document.body.style.overflow = '';

            setTimeout(function () {
                iframe.src = '';
            }, 200);
        }

        function printReport() {
            var iframe = document.getElementById('report-iframe');
            if (iframe && iframe.contentWindow) {
                iframe.contentWindow.print();
            } else {
                $.messager.alert('Aviso', 'No se pudo acceder al contenido del reporte.', 'warning');
            }
        }

        // ======= downloadReport con soporte de parámetros existentes =======
        function downloadReport() {
            if (currentReportUrl) {
                var url = currentReportUrl;
                // Si ya hay parámetros, agregamos con &, si no, con ?
                url += (url.indexOf('?') === -1 ? '?download=1' : '&download=1');
                window.open(url, '_blank');
            } else {
                $.messager.alert('Aviso', 'No hay un reporte cargado para descargar.', 'warning');
            }
        }

        // Cerrar modal con ESC
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeReportModal();
            }
        });
    </script>

</body>
</html>
