<!-- =========================================================================================
     ESTILOS CSS PERSONALIZADOS (TECH INDIGO THEME)
========================================================================================== -->
<style>
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
    .panel-title { font-weight: bold; font-size: 16px; color: white; }
    .panel-tool-close { background: url('assets/jquery/themes/icons/cancel.png') no-repeat center center; filter: brightness(0) invert(1); }

    /* Botones */
    .l-btn {
        background: #f0f0f0; border: 1px solid #ccc; border-radius: 20px;
        padding: 5px 15px; margin: 0 5px; transition: all 0.3s;
    }
    .l-btn:hover { background: #e0e0e0; border-color: #bbb; }
    .c6, .l-btn-primary { background: #65c68e !important; color: white !important; border: none !important; }
    .c6:hover { background: #57b07d !important; }
    .l-btn-text { font-size: 14px; line-height: 20px; }

    /* Formularios */
    .fitem { margin-bottom: 20px; }
    .fitem label { display: inline-block; width: 120px; font-weight: bold; color: #575cbc; }
    .textbox .textbox-text { padding: 5px; border-radius: 4px; }
    
    /* Alertas */
    .messager-body { padding: 20px !important; font-size: 14px; }
    .messager-icon { margin-right: 10px; }
</style>

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
            <?php if ($rol == 'SECRETARIO' || $rol == 'ADMIN'): ?>
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="newStudent()">Nuevo</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="editStudent()">Editar</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="destroyStudent()">Eliminar</a>
                <span style="border-left: 1px solid #ccc; margin: 0 10px;"></span>
            <?php endif; ?>
            <span>Buscar Cédula:</span>
            <input id="search-est" class="easyui-searchbox" style="width:250px" data-options="searcher:doSearchEst,prompt:'Escribe la cédula...'">
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
            <?php if ($rol == 'SECRETARIO' || $rol == 'ADMIN'): ?>
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="newCourse()">Nuevo Curso</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="editCourse()">Editar</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="destroyCourse()">Eliminar</a>
                <span style="border-left: 1px solid #ccc; margin: 0 10px;"></span>
            <?php endif; ?>
            <span>Buscar ID:</span>
            <input id="search-cur" class="easyui-searchbox" style="width:250px" data-options="searcher:doSearchCur,prompt:'ID Curso...'">
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
            <?php if ($rol == 'SECRETARIO' || $rol == 'ADMIN'): ?>
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="newEnrollment()">Inscribir</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="editEnrollment()">Editar</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="destroyEnrollment()">Eliminar</a>
                <span style="border-left: 1px solid #ccc; margin: 0 10px;"></span>
            <?php endif; ?>
            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-reload" plain="true" onclick="$('#dg-ins').datagrid('reload')">Recargar</a>
        </div>
    </div>
</div>

<!-- ================= MODALES (DIALOGS) ================= -->

<!-- Modal Estudiante -->
<div id="dlg-est" class="easyui-dialog" style="width:500px" data-options="closed:true,modal:true,border:'thin',buttons:'#dlg-buttons-est'">
    <form id="fm-est" method="post" novalidate style="margin:0;padding:20px 50px">
        <h3 style="color:#575cbc; border-bottom:1px solid #ddd; padding-bottom:10px;">Datos Estudiante</h3>
        <div class="fitem"><label>Cédula:</label><input name="ID_EST" class="easyui-textbox" required="true" id="input-id-est"></div>
        <div class="fitem"><label>Nombre:</label><input name="NOM_EST" class="easyui-textbox" required="true"></div>
        <div class="fitem"><label>Apellido:</label><input name="APE_EST" class="easyui-textbox" required="true"></div>
        <div class="fitem"><label>Teléfono:</label><input name="TEL_EST" class="easyui-textbox" required="true"></div>
        <div class="fitem"><label>Correo:</label><input name="COR_EST" class="easyui-textbox" required="true" validType="email"></div>
        <div class="fitem"><label>Dirección:</label><input name="DIR_EST" class="easyui-textbox" required="true"></div>
        <div class="fitem"><label>Fec. Nac:</label><input name="FEC_NAC" class="easyui-datebox" required="true" data-options="formatter:myformatter,parser:myparser"></div>
    </form>
</div>
<div id="dlg-buttons-est">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveStudent()" style="width:120px; font-weight:bold;">Guardar</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg-est').dialog('close')" style="width:120px">Cancelar</a>
</div>

<!-- Modal Curso -->
<div id="dlg-cur" class="easyui-dialog" style="width:500px" data-options="closed:true,modal:true,border:'thin',buttons:'#dlg-buttons-cur'">
    <form id="fm-cur" method="post" novalidate style="margin:0;padding:20px 50px">
        <h3 style="color:#575cbc; border-bottom:1px solid #ddd; padding-bottom:10px;">Datos Curso</h3>
        
        <!-- Campo ID Oculto al crear -->
        <div class="fitem" id="div-id-cur">
            <label>ID Curso:</label>
            <input name="ID_CUR" class="easyui-textbox" id="input-id-cur">
        </div>

        <div class="fitem"><label>Nombre:</label><input name="NOM_CUR" class="easyui-textbox" required="true"></div>
        <div class="fitem"><label>Descripción:</label><input name="DES_CUR" class="easyui-textbox" required="true" style="height:60px" data-options="multiline:true"></div>
    </form>
</div>
<div id="dlg-buttons-cur">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveCourse()" style="width:120px; font-weight:bold;">Guardar</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg-cur').dialog('close')" style="width:120px">Cancelar</a>
</div>

<!-- Modal Inscripción -->
<div id="dlg-ins" class="easyui-dialog" style="width:500px" data-options="closed:true,modal:true,border:'thin',buttons:'#dlg-buttons-ins'">
    <form id="fm-ins" method="post" novalidate style="margin:0;padding:20px 50px">
        <h3 style="color:#575cbc; border-bottom:1px solid #ddd; padding-bottom:10px;">Datos Inscripción</h3>
        <input type="hidden" name="ID_INS">
        <div class="fitem">
            <label>Estudiante:</label>
            <input name="ID_EST_INS" class="easyui-combobox" required="true" style="width:260px;"
                data-options="valueField:'ID_EST',textField:'NOM_EST',url:'models/obtener_estudiante.php',
                formatter: function(row){ return row.ID_EST + ' - ' + row.NOM_EST + ' ' + row.APE_EST; }">
        </div>
        <div class="fitem">
            <label>Curso:</label>
            <input name="ID_CUR_INS" class="easyui-combobox" required="true" style="width:260px;"
                data-options="valueField:'ID_CUR',textField:'NOM_CUR',url:'models/obtener_cursos.php',
                formatter: function(row){ return row.ID_CUR + ' - ' + row.NOM_CUR; }">
        </div>
    </form>
</div>
<div id="dlg-buttons-ins">
    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveEnrollment()" style="width:120px; font-weight:bold;">Guardar</a>
    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg-ins').dialog('close')" style="width:120px">Cancelar</a>
</div>

<!-- ================= LOGICA JAVASCRIPT ================= -->
<script type="text/javascript">
    var url;
    
    // -- UTILS --
    function myformatter(date){
        var y = date.getFullYear(); var m = date.getMonth()+1; var d = date.getDate();
        return y+'-'+(m<10?('0'+m):m)+'-'+(d<10?('0'+d):d);
    }
    function myparser(s){
        if (!s) return new Date();
        var ss = (s.split('-'));
        return new Date(parseInt(ss[0],10),parseInt(ss[1],10)-1,parseInt(ss[2],10));
    }
    function formatStudentName(val, row){ return row.NOM_EST + ' ' + row.APE_EST; }

    // --- MANEJO CENTRALIZADO DE RESPUESTAS ---
    function handleFormResponse(result, dialogId, gridId, successMsg) {
        try {
            var data = JSON.parse(result);
            // Validamos si el backend devuelve 'error', 'errorMsg' o 'success':false
            if (data.error || data.errorMsg || data.success === false) {
                $.messager.alert({
                    title: 'Error de Validación',
                    msg: '<span style="color:red; font-weight:bold">' + (data.error || data.errorMsg || "Error desconocido") + '</span>',
                    icon: 'error',
                    width: 400
                });
            } else {
                // Éxito
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
            // Si la respuesta no es JSON válido (ej: error fatal de PHP)
            console.error(result);
            $.messager.alert({
                title: 'Error del Sistema',
                msg: 'Ocurrió un error inesperado. Revise la consola.',
                icon: 'error'
            });
        }
    }

    // -- ESTUDIANTE --
    function doSearchEst(value){
        if(value == '') $('#dg-est').datagrid('load', 'models/obtener_estudiante.php');
        else $('#dg-est').datagrid({url: 'models/obtener_estudiante_id.php?ID_EST='+value});
    }
    function newStudent(){
        $('#dlg-est').dialog('open').dialog('setTitle','Nuevo Estudiante');
        $('#fm-est').form('clear');
        $('#input-id-est').textbox('readonly', false);
        url = 'models/agregar_estudiante.php';
    }
    function editStudent(){
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
    function saveStudent(){
        $('#fm-est').form('submit',{
            url: url,
            onSubmit: function(){ return $(this).form('validate'); },
            success: function(result){
                handleFormResponse(result, '#dlg-est', '#dg-est', 'Estudiante guardado correctamente');
            }
        });
    }
    function destroyStudent(){
        var row = $('#dg-est').datagrid('getSelected');
        if (row){
            $.messager.confirm({title:'Confirmar', msg:'¿Eliminar estudiante?', fn:function(r){
                if (r){
                    $.post('models/eliminar_estudiante.php',{ID_EST:row.ID_EST},function(result){
                        if (result.success || result.ok) {
                            $('#dg-est').datagrid('reload');
                            $.messager.show({title:'Éxito', msg:'Estudiante eliminado'});
                        } else {
                            $.messager.alert({title:'Error', msg: result.errorMsg || 'No se pudo eliminar', icon:'error'});
                        }
                    },'json');
                }
            }});
        } else {
            $.messager.alert({title:'Aviso', msg:'Seleccione un estudiante.', icon:'info'});
        }
    }

    // -- CURSO --
    function doSearchCur(value){
        if(value == '') $('#dg-cur').datagrid('load', 'models/obtener_cursos.php');
        else $('#dg-cur').datagrid({url: 'models/obtener_curso_id.php?ID_CUR='+value});
    }
    function newCourse(){
        $('#dlg-cur').dialog('open').dialog('setTitle','Nuevo Curso');
        $('#fm-cur').form('clear');
        $('#div-id-cur').hide(); // Ocultar ID al crear
        $('#input-id-cur').textbox('disableValidation');
        url = 'models/agregar_curso.php';
    }
    function editCourse(){
        var row = $('#dg-cur').datagrid('getSelected');
        if (row){
            $('#dlg-cur').dialog('open').dialog('setTitle','Editar Curso');
            $('#fm-cur').form('load',row);
            $('#div-id-cur').show(); // Mostrar ID al editar
            $('#input-id-cur').textbox('readonly', true);
            url = 'models/actualizar_curso.php';
        } else {
            $.messager.alert({title:'Aviso', msg:'Seleccione un curso.', icon:'info'});
        }
    }
    function saveCourse(){
        $('#fm-cur').form('submit',{
            url: url,
            onSubmit: function(){ return $(this).form('validate'); },
            success: function(result){
                handleFormResponse(result, '#dlg-cur', '#dg-cur', 'Curso guardado correctamente');
            }
        });
    }
    function destroyCourse(){
        var row = $('#dg-cur').datagrid('getSelected');
        if (row){
            $.messager.confirm({title:'Confirmar', msg:'¿Eliminar curso?', fn:function(r){
                if (r){
                    $.post('models/eliminar_curso.php',{ID_CUR:row.ID_CUR},function(result){
                        if (result.success) {
                            $('#dg-cur').datagrid('reload');
                            $.messager.show({title:'Éxito', msg:'Curso eliminado'});
                        } else {
                            $.messager.alert({title:'Error', msg: result.errorMsg || 'No se pudo eliminar', icon:'error'});
                        }
                    },'json');
                }
            }});
        } else {
            $.messager.alert({title:'Aviso', msg:'Seleccione un curso.', icon:'info'});
        }
    }

    // -- INSCRIPCION --
    function newEnrollment(){
        $('#dlg-ins').dialog('open').dialog('setTitle','Nueva Inscripción');
        $('#fm-ins').form('clear');
        url = 'models/agregar_inscripcion.php';
    }
    function editEnrollment(){
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
    function saveEnrollment(){
        $('#fm-ins').form('submit',{
            url: url,
            onSubmit: function(){ return $(this).form('validate'); },
            success: function(result){
                handleFormResponse(result, '#dlg-ins', '#dg-ins', 'Inscripción procesada correctamente');
            }
        });
    }
    function destroyEnrollment(){
        var row = $('#dg-ins').datagrid('getSelected');
        if (row){
            $.messager.confirm({title:'Confirmar', msg:'¿Eliminar inscripción?', fn:function(r){
                if (r){
                    $.post('models/eliminar_inscripcion.php',{ID_INS:row.ID_INS},function(result){
                        if (result.success) {
                            $('#dg-ins').datagrid('reload');
                            $.messager.show({title:'Éxito', msg:'Inscripción eliminada'});
                        } else {
                            $.messager.alert({title:'Error', msg: result.errorMsg || 'No se pudo eliminar', icon:'error'});
                        }
                    },'json');
                }
            }});
        } else {
            $.messager.alert({title:'Aviso', msg:'Seleccione una inscripción.', icon:'info'});
        }
    }
</script>