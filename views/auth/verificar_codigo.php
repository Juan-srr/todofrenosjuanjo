<?php include 'views/layouts/header.php'; ?>

<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <div class="logo-container">
                <img src="public/imagenes/proyecto.jpg" alt="Logo Todo Frenos Juanjo" class="login-logo">
            </div>
            <h1 class="login-title">
                <?php if (isset($_SESSION['tipo_verificacion']) && $_SESSION['tipo_verificacion'] === 'registro'): ?>
                    Verificar Correo
                <?php else: ?>
                    Verificar Sesión
                <?php endif; ?>
            </h1>
            <p class="login-subtitle">
                <?php if (isset($_SESSION['tipo_verificacion']) && $_SESSION['tipo_verificacion'] === 'registro'): ?>
                    Completa tu registro
                <?php else: ?>
                    Confirma tu identidad
                <?php endif; ?>
            </p>
        </div>
        
        <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'registro_exitoso'): ?>
            <div class="mensaje-exito">
                <i class="fas fa-check-circle"></i>
                Registro exitoso😀. Hemos enviado un código de verificación a tu correo electrónico.
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'codigo_reenviado'): ?>
            <div class="mensaje-exito">
                <i class="fas fa-paper-plane"></i>
                Código de verificación reenviado exitosamente.
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error']) && $_GET['error'] === 'codigo_invalido'): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                El código ingresado no es válido o ha expirado. Intenta de nuevo.
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error']) && $_GET['error'] === 'mail'): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                Error al enviar el correo. Intenta de nuevo.
            </div>
        <?php endif; ?>
        
        <div class="verification-info">
            <p>
                <?php if (isset($_SESSION['tipo_verificacion']) && $_SESSION['tipo_verificacion'] === 'registro'): ?>
                    Para completar tu registro, ingresa el código de 6 dígitos que hemos enviado a tu correo.
                <?php else: ?>
                    Para continuar con el inicio de sesión, ingresa el código de 6 dígitos que hemos enviado a tu correo.
                <?php endif; ?>
            </p>
        </div>
        
        <form action="index.php?controller=auth&action=procesarVerificacion" method="POST" class="login-form">
            <div class="form-group">
                <div class="input-container">
                    <i class="fas fa-key input-icon"></i>
                    <input type="text" 
                           name="codigo" 
                           placeholder="Código de verificación" 
                           maxlength="6" 
                           pattern="[0-9]{6}" 
                           required 
                           autocomplete="off"
                           class="login-input codigo-input"
                           style="letter-spacing: 5px; font-size: 24px; text-align: center;">
                    <div class="input-line"></div>
                </div>
                <small class="form-text text-muted">
                    Ingresa el código de 6 dígitos enviado a tu correo
                </small>
            </div>
            
            <button type="submit" class="login-button">
                <span class="button-text">Verificar Código</span>
                <i class="fas fa-check button-icon"></i>
            </button>
        </form>
        
        <div class="verification-actions">
            <p class="text-center">
                ¿No recibiste el código? 
                <a href="#" onclick="reenviarCodigo()" class="reenviar-link">Reenviar código</a>
            </p>
            <p class="text-center">
                <a href="index.php?controller=auth&action=login" class="volver-link">Volver al login</a>
            </p>
        </div>
    </div>
    
    <!-- Elementos decorativos animados -->
    <div class="floating-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
        <div class="shape shape-4"></div>
    </div>
    
    <!-- Partículas de frenos -->
    <div class="brake-particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>
</div>

<script>
function reenviarCodigo() {
    if (confirm('¿Deseas reenviar el código de verificación?')) {
        // Crear formulario temporal para reenvío
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'index.php?controller=auth&action=reenviarCodigo';
        
        // Añadir token CSRF si existe
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'csrf_token';
            input.value = csrfToken.getAttribute('content');
            form.appendChild(input);
        }
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Auto-focus en el campo de código
document.addEventListener('DOMContentLoaded', function() {
    const codigoInput = document.querySelector('.codigo-input');
    if (codigoInput) {
        codigoInput.focus();
    }
});

// Validación en tiempo real del código
document.querySelector('.codigo-input').addEventListener('input', function(e) {
    // Solo permitir números
    e.target.value = e.target.value.replace(/[^0-9]/g, '');
    
    // Auto-submit cuando se complete el código
    if (e.target.value.length === 6) {
        setTimeout(() => {
            e.target.form.submit();
        }, 500);
    }
});
</script>

<?php include 'views/layouts/footer.php'; ?>
