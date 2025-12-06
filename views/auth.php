<?php
// This view is now included inside a modal in template.php
?>
<div class="text-center mb-4">
    <i class="bi bi-cpu-fill text-warning" style="font-size: 3rem;"></i>
    <h3 class="fw-bold text-dark-custom mt-2">Ingresar</h3>
    <p class="text-muted small">Accede a los servicios académicos.</p>
</div>

<?php
  if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger text-center small p-2" role="alert">' . $_SESSION['error'] . '</div>';
    // Don't unset here, let it persist until a successful login or page reload without error
  }
?>

<form method="post" action="index.php">
    <input type="hidden" name="referrer" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
    <div class="form-floating mb-3">
        <input type="email" name="email" class="form-control" id="modalEmail" placeholder="Correo Electrónico" required/>
        <label for="modalEmail">Correo Electrónico</label>
    </div>
    <div class="form-floating mb-3">
        <input type="password" name="password" class="form-control" id="modalPassword" placeholder="Contraseña" required/>
        <label for="modalPassword">Contraseña</label>
    </div>
    <button type="submit" class="btn btn-primary w-100 btn-lg">Ingresar</button>
</form>

