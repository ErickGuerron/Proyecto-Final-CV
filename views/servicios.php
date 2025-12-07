<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Gestión Académica - Estudiantes</title>
    <!-- Usamos CDNs para asegurar que las librerías carguen correctamente -->
    <link rel="stylesheet" type="text/css" href="../assets/jquery/themes/default/easyui.css">
    <link rel="stylesheet" type="text/css" href="../assets/jquery/themes/icon.css">
    <link rel="stylesheet" type="text/css" href="../assets/jquery/themes/color.css">

    <!-- Cargamos jQuery primero -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Luego cargamos EasyUI -->
    <script type="text/javascript" src="../assets/jquery/jquery.easyui.min.js"></script>
</head>

<body>
    <div class="container my-4">
        <!-- Definimos el rol en una variable para facilitar las condiciones más abajo -->
        <?php $rol = $_SESSION['user']['ROL_USU']; ?>

        <div class="card p-4 shadow-sm">
            <?php if ($rol === 'ADMIN' || $rol === 'SECRETARIO'): ?>
                <h1 class="mb-3 text-primary">Gestión de Estudiantes</h1>
                <p class="lead">
                    <?php echo ($rol === 'SECRETARIO') ? 'Administración completa de registros.' : 'Visualización y generación de reportes.'; ?>
                </p>
            <?php else: ?>
                <h1 class="mb-3 text-primary">Página de Servicios</h1>
                <p class="lead">Explora la amplia gama de servicios académicos que ofrecemos en Tech Indigo Académico.</p>
            <?php endif; ?>
        </div>

        <?php if ($rol === 'SECRETARIO' || $rol === 'ADMIN'): ?>

            <div style="margin-top: 20px;">
                <table id="dg" title="Listado de Estudiantes" class="easyui-datagrid" style="width:100%;height:400px"
                    url="models/obtener_estudiante.php" toolbar="#toolbar" pagination="true" rownumbers="true"
                    fitColumns="true" singleSelect="true">
                    <thead>
                        <tr>
                            <th field="ID_EST" width="50">Cedula</th>
                            <th field="NOM_EST" width="50">Nombre</th>
                            <th field="APE_EST" width="50">Apellido</th>
                            <th field="TEL_EST" width="50">Telefono</th>
                            <th field="COR_EST" width="50">Correo</th>
                            <th field="DIR_EST" width="50">Direccion</th>
                            <th field="FEC_NAC" width="80">Fecha de Nacimiento</th>
                        </tr>
                    </thead>
                </table>

                <div id="toolbar" style="padding:5px; display:flex; justify-content:space-between; align-items:center;">

                    <div style="display: flex; align-items: center;">
                        <?php if ($rol === 'SECRETARIO'): ?>
                            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true"
                                onclick="newUser()">Nuevo</a>
                            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true"
                                onclick="editUser()">Editar</a>
                            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true"
                                onclick="destroyUser()">Eliminar</a>
                            <!-- Separador visual -->
                            <span class="datagrid-btn-separator"
                                style="vertical-align: middle; height: 15px; display:inline-block; margin: 0 10px;"></span>
                        <?php endif; ?>

                        <!-- GRUPO 2: REPORTES (Visible para ADMIN y SECRETARIO porque "secretaria tiene todo") -->
                        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-print" plain="true"
                            onclick="generarReporte()">Reporte General</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-large-picture" plain="true"
                            onclick="verEstadisticas()">Estadísticas</a>
                    </div>

                    <!-- GRUPO 3: BUSCADOR (Visible para ambos para poder filtrar la tabla/reportes) -->
                    <div>
                        <span style="font-weight:bold;">Buscar Cédula:</span>
                        <input id="searchIdEst" class="easyui-textbox" style="width:150px" prompt="Escriba para filtrar...">
                    </div>
                </div>
            </div>

            <!-- Diálogo CRUD (Solo útil para SECRETARIO, pero el HTML puede estar presente oculto) -->
            <div id="dlg" class="easyui-dialog" style="width:400px"
                data-options="closed:true,modal:true,border:'thin',buttons:'#dlg-buttons'">
                <form id="fm" method="post" novalidate style="margin:0;padding:20px 50px">
                    <h3>Informacion del Estudiante</h3>
                    <div style="margin-bottom:10px">
                        <input name="ID_EST" class="easyui-textbox" required="true" label="Cedula:" style="width:100%">
                    </div>
                    <div style="margin-bottom:10px">
                        <input name="NOM_EST" class="easyui-textbox" required="true" label="Nombre:" style="width:100%">
                    </div>
                    <div style="margin-bottom:10px">
                        <input name="APE_EST" class="easyui-textbox" required="true" label="Apellido:" style="width:100%">
                    </div>
                    <div style="margin-bottom:10px">
                        <input name="TEL_EST" class="easyui-textbox" required="true" label="Telefono:" style="width:100%">
                    </div>
                    <div style="margin-bottom:10px">
                        <input name="COR_EST" class="easyui-textbox" required="true" validType="email" label="Email:"
                            style="width:100%">
                    </div>
                    <div style="margin-bottom:10px">
                        <input name="DIR_EST" class="easyui-textbox" required="true" label="Direccion:" style="width:100%">
                    </div>
                    <div style="margin-bottom:10px">
                        <input name="FEC_NAC" class="easyui-textbox" required="true" type="date" label="FechaNac:"
                            style="width:100%">
                    </div>
                </form>
            </div>
            <div id="dlg-buttons">
                <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveUser()"
                    style="width:90px">Guardar</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel"
                    onclick="javascript:$('#dlg').dialog('close')" style="width:90px">Cancelar</a>
            </div>

            <script type="text/javascript">
                var url;
                var searchTimer;

                $(function () {
                    $('#searchIdEst').textbox('textbox').on('keyup', function (e) {
                        clearTimeout(searchTimer);

                        var valor = this.value.trim();


                        searchTimer = setTimeout(function () {
                            doSearch(valor);
                        }, 250);
                    });
                });

                function doSearch(valorDesdeKeyup) {
                    var $input = $('#searchIdEst').textbox('textbox');

                    var searchValue = (typeof valorDesdeKeyup === 'string')
                        ? valorDesdeKeyup.trim()
                        : $input.val().trim();

                    if (searchValue === "") {
                        $('#dg').datagrid('options').url = 'models/obtener_estudiante.php';
                    } else {
                        $('#dg').datagrid('options').url =
                            'models/obtener_estudiante_id.php?ID_EST=' + encodeURIComponent(searchValue);
                    }

                    $('#dg').datagrid('reload');

                    $input.focus();
                    var len = $input.val().length;
                    if ($input[0].setSelectionRange) {
                        $input[0].setSelectionRange(len, len);
                    }
                }


                // --- FUNCIONES DE REPORTE ---
                function generarReporte() {
                    // Ejemplo: Usar el valor del buscador para generar un reporte filtrado
                    var filtro = $('#searchIdEst').textbox('getValue');
                    alert("Generando reporte. Filtro aplicado: " + (filtro ? filtro : "Ninguno"));
                    // window.open('models/generar_pdf.php?filtro=' + filtro, '_blank');
                }

                function verEstadisticas() {
                    alert("Mostrando estadísticas generales...");
                }

                // --- FUNCIONES CRUD (Solo funcionarán si el usuario tiene permisos en el backend también) ---
                function newUser() {
                    $('#dlg').dialog('open').dialog('center').dialog('setTitle', 'Nuevo Estudiante');
                    $('#fm').form('clear');
                    url = 'models/agregar_estudiante.php';
                }
                function editUser() {
                    var row = $('#dg').datagrid('getSelected');
                    if (row) {
                        $('#dlg').dialog('open').dialog('center').dialog('setTitle', 'Editar Estudiante');
                        $('#fm').form('load', row);
                        url = 'models/actualizar_estudiante.php?ID_EST=' + row.ID_EST;
                    }
                }
                function saveUser() {
                    $('#fm').form('submit', {
                        url: url,
                        iframe: false,
                        onSubmit: function () {
                            return $(this).form('validate');
                        },
                        success: function (result) {
                            var result = eval('(' + result + ')');
                            if (result.errorMsg) {
                                $.messager.show({
                                    title: 'Error',
                                    msg: result.errorMsg
                                });
                            } else {
                                $('#dlg').dialog('close');
                                $('#dg').datagrid('reload');
                            }
                        }
                    });
                }
                function destroyUser() {
                    var row = $('#dg').datagrid('getSelected');
                    if (row) {
                        $.messager.confirm('Confirmar', '¿Está seguro de eliminar este usuario?', function (r) {
                            if (r) {
                                $.post('models/eliminar_estudiante.php', { ID_EST: row.ID_EST }, function (result) {
                                    if (result.success) {
                                        $('#dg').datagrid('reload');
                                    } else {
                                        $.messager.show({
                                            title: 'Error',
                                            msg: result.errorMsg
                                        });
                                    }
                                }, 'json');
                            }
                        });
                    }
                }
            </script>
        <?php endif; ?>
    </div>
</body>

</html>