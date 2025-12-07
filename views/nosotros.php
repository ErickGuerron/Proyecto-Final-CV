<div class="container my-4">
    <div class="card p-4 shadow-sm">
        <?php if ($_SESSION['user']['ROL_USU'] === 'ADMIN' || $_SESSION['user']['ROL_USU'] === 'SECRETARIO'): ?>
            <h1 class="mb-3 text-primary">Reportes sobre Nosotros</h1>
            <p class="lead">Aquí se mostrarán los reportes relacionados con la institución.</p>
            <p>Funcionalidad de reportes pendiente de implementación.</p>
        <?php else: ?>
            <h1 class="mb-3 text-primary">Página sobre Nosotros</h1>
            <p class="lead">Conoce más sobre Tech Indigo Académico, nuestra misión y visión.</p>
            <p>Aquí se incluirá información detallada sobre la institución. (Contenido de marcador de posición).</p>
        <?php endif; ?>
    </div>
    <?php if ($_SESSION['user']['ROL_USU'] === 'SECRETARIO'): ?>
    <div class="card p-4 shadow-sm mt-4">
        <h2 class="mb-3 text-secondary">Agregar Estudiante</h2>
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <form action="index.php?action=nosotros" method="POST">
            <div class="mb-3">
                <label for="nom_est" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nom_est" name="nom_est" required>
            </div>
            <div class="mb-3">
                <label for="ape_est" class="form-label">Apellido</label>
                <input type="text" class="form-control" id="ape_est" name="ape_est" required>
            </div>
            <div class="mb-3">
                <label for="tel_est" class="form-label">Teléfono</label>
                <input type="text" class="form-control" id="tel_est" name="tel_est" required>
            </div>
            <div class="mb-3">
                <label for="cor_est" class="form-label">Correo</label>
                <input type="email" class="form-control" id="cor_est" name="cor_est" required>
            </div>
            <div class="mb-3">
                <label for="dir_est" class="form-label">Dirección</label>
                <input type="text" class="form-control" id="dir_est" name="dir_est" required>
            </div>
            <div class="mb-3">
                <label for="fec_nac" class="form-label">Fecha de Nacimiento</label>
                <input type="date" class="form-control" id="fec_nac" name="fec_nac" required>
            </div>
            <button type="submit" class="btn btn-primary">Agregar Estudiante</button>
        </form>
    </div>
    <?php endif; ?>
</div>
