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

    <!-- Estilos para modal custom de reportes (sin EasyUI) -->
    <style>
        .report-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.35);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 24px;
            z-index: 1050;
            backdrop-filter: blur(2px);
        }
        .report-modal-backdrop.show {
            display: flex;
        }
        .report-modal {
            background: #fff;
            width: 90vw;
            max-width: 1100px;
            height: 80vh;
            max-height: 850px;
            min-height: 480px;
            border-radius: 16px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.18);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        .report-modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 18px;
            border-bottom: 1px solid #e2e8f0;
            background: linear-gradient(135deg, rgba(87, 92, 188, 0.05), rgba(101, 198, 142, 0.04));
        }
        .report-modal-title {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 700;
            color: #575cbc;
        }
        .report-modal-close {
            border: none;
            background: transparent;
            font-size: 1.3rem;
            line-height: 1;
            color: #4a5568;
            cursor: pointer;
            padding: 6px;
        }
        .report-modal-close:hover {
            color: #2d3748;
        }
        .report-modal-body {
            flex: 1;
            background: #f7fafc;
        }
        .report-modal-body iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
    </style>
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
                        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-tip" plain="true"
                            onclick="generarReporteListado()">Reporte Estudiantes</a>
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
            <div id="dlg" class="easyui-dialog" style="width:520px"
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

            <!-- Modal sencillo para previsualizar reportes (custom, sin EasyUI) -->
            <div id="reportModal" class="report-modal-backdrop" aria-hidden="true">
                <div class="report-modal" role="dialog" aria-labelledby="reportModalTitle">
                    <div class="report-modal-header">
                        <h5 class="report-modal-title" id="reportModalTitle">Reporte</h5>
                        <button type="button" class="report-modal-close" id="reportModalClose" aria-label="Cerrar">&times;</button>
                    </div>
                    <div class="report-modal-body">
                        <iframe id="reportFrame" src="" title="Vista previa de reporte"></iframe>
                    </div>
                </div>
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

                    // Inicializa modal custom de reportes al cargar la página
                    setupReportModal();
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


                // --- FUNCIONES DE REPORTE (modal custom sin EasyUI) ---
                function setupReportModal() {
                    var backdrop = document.getElementById('reportModal');
                    var closeBtn = document.getElementById('reportModalClose');

                    if (closeBtn) {
                        closeBtn.addEventListener('click', closeReportModal);
                    }

                    if (backdrop) {
                        backdrop.addEventListener('click', function (e) {
                            if (e.target === backdrop) {
                                closeReportModal();
                            }
                        });
                    }

                    document.addEventListener('keydown', function (e) {
                        if (e.key === 'Escape') {
                            closeReportModal();
                        }
                    });
                }

                function openReportModal(url, title) {
                    var backdrop = document.getElementById('reportModal');
                    var frame = document.getElementById('reportFrame');
                    var titleEl = document.getElementById('reportModalTitle');

                    if (!backdrop || !frame) return;

                    if (titleEl) {
                        titleEl.textContent = title || 'Reporte';
                    }

                    frame.src = url;
                    backdrop.classList.add('show');
                    backdrop.setAttribute('aria-hidden', 'false');
                }

                function closeReportModal() {
                    var backdrop = document.getElementById('reportModal');
                    var frame = document.getElementById('reportFrame');

                    if (frame) {
                        frame.src = '';
                    }

                    if (backdrop) {
                        backdrop.classList.remove('show');
                        backdrop.setAttribute('aria-hidden', 'true');
                    }
                }

                function generarReporte() {
                    var filtro = $('#searchIdEst').textbox('getValue').trim();
                    var hasFiltro = filtro !== '';

                    var urlReporte = hasFiltro
                        ? 'views/report_estudiante.php?id=' + encodeURIComponent(filtro)
                        : 'views/report_estudiantes_registros.php';

                    var titulo = hasFiltro ? 'Reporte de estudiante' : 'Reporte general';

                    openReportModal(urlReporte, titulo);
                }

                function generarReporteListado() {
                    openReportModal('views/report_estudiantes.php', 'Reporte de estudiantes');
                }

                function verEstadisticas() {
                    openReportModal('views/report_grafico_cursos.php', 'Estadísticas de cursos');
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
