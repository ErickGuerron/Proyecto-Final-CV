<?php
// This view is now included inside a modal in template.php
?>
<div class="text-center mb-4">
    <i class="bi bi-cpu-fill text-warning" style="font-size: 3rem;"></i>
    <h3 class="fw-bold text-dark-custom mt-2">Ingresar</h3>
    <p class="text-muted small">Accede a los servicios académicos.</p>
</div>

<div id="login-error-message" class="alert alert-danger text-center small p-2" role="alert" style="display: none;"></div>

<form id="loginForm" method="post" action="index.php" novalidate>
    <input type="hidden" name="referrer" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
    <div class="form-floating mb-3">
        <input type="email" name="email" class="form-control" id="modalEmail" placeholder="Correo Electrónico" required/>
        <label for="modalEmail">Correo Electrónico</label>
        <div class="invalid-feedback">
            Por favor, ingrese un correo electrónico válido.
        </div>
    </div>
    <div class="form-floating mb-3">
        <input type="password" name="password" class="form-control" id="modalPassword" placeholder="Contraseña" required/>
        <label for="modalPassword">Contraseña</label>
    </div>
    <button type="submit" class="btn btn-primary w-100 btn-lg">Ingresar</button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('loginForm');
    const emailInput = document.getElementById('modalEmail');
    const errorMessageDiv = document.getElementById('login-error-message');

    const validateEmail = () => {
        const isValid = emailInput.value && /^\S+@\S+\.\S+$/.test(emailInput.value);
        if (!isValid) {
            emailInput.classList.add('is-invalid');
        } else {
            emailInput.classList.remove('is-invalid');
        }
        return isValid;
    };

    if (emailInput) {
        emailInput.addEventListener('blur', validateEmail);
        emailInput.addEventListener('input', () => {
            if (emailInput.classList.contains('is-invalid')) {
                validateEmail();
            }
        });
    }

    if (loginForm) {
        loginForm.addEventListener('submit', function (event) {
            event.preventDefault();
            event.stopPropagation();

            // Hide previous server errors
            errorMessageDiv.style.display = 'none';

            if (!validateEmail()) {
                return;
            }

            const formData = new FormData(loginForm);

            fetch('index.php', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    errorMessageDiv.textContent = data.message;
                    errorMessageDiv.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                errorMessageDiv.textContent = 'Ocurrió un error inesperado. Por favor, intente de nuevo.';
                errorMessageDiv.style.display = 'block';
            });
        });
    }
});
</script>
