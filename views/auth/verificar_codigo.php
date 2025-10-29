<?php include 'views/layouts/header.php'; ?>

<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <div class="logo-container">
                <img src="public/imagenes/proyecto.jpg" alt="Logo Todo Frenos Juanjo" class="login-logo">
            </div>
            <h1 class="login-title">Verificación de Código</h1>
            <p class="login-subtitle">
                <?php 
                $tipo = $_GET['tipo'] ?? 'registro';
                if ($tipo === 'registro') {
                    echo 'Hemos enviado un código de verificación a tu correo electrónico para completar tu registro.';
                } else {
                    echo 'Hemos enviado un código de verificación a tu correo para completar el inicio de sesión.';
                }
                ?>
            </p>
        </div>
        
        <?php if(isset($_GET['mensaje'])): ?>
            <div class="mensaje-exito">
                <i class="fas fa-check-circle"></i>
                <?php 
                if($_GET['mensaje'] === 'codigo_reenviado') {
                    echo 'Código reenviado exitosamente.';
                } elseif($_GET['mensaje'] === 'verificacion_exitosa') {
                    echo 'Verificación exitosa. Ya puedes iniciar sesión.';
                }
                ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['error'])): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <?php 
                if($_GET['error'] === 'codigo_incorrecto') {
                    echo 'Código incorrecto o expirado. Intenta de nuevo.';
                } elseif($_GET['error'] === 'mail') {
                    echo 'Error al enviar el correo. Intenta de nuevo.';
                } else {
                    echo 'Error en la verificación. Intenta de nuevo.';
                }
                ?>
            </div>
        <?php endif; ?>
        
        <form action="index.php?controller=auth&action=procesarVerificacion" method="POST" class="login-form">
            <input type="hidden" name="tipo" value="<?php echo htmlspecialchars($tipo); ?>">
            
            <div class="form-group">
                <div class="input-container">
                    <i class="fas fa-key input-icon"></i>
                    <input type="text" name="codigo" placeholder="Código de verificación" required 
                           class="login-input" maxlength="6" pattern="[0-9]{6}" 
                           title="Ingresa el código de 6 dígitos" autocomplete="off">
                    <div class="input-line"></div>
                </div>
                <small class="form-text">Ingresa el código de 6 dígitos que enviamos a tu correo</small>
            </div>
            
            <button type="submit" class="login-button">
                <span class="button-text">Verificar Código</span>
                <i class="fas fa-check button-icon"></i>
            </button>
        </form>
        
        <div class="verification-actions">
            <a href="index.php?controller=auth&action=login" class="back-link">
                <i class="fas fa-arrow-left"></i>
                Volver al Login
            </a>
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
// Auto-focus en el campo de código
document.addEventListener('DOMContentLoaded', function() {
    const codigoInput = document.querySelector('input[name="codigo"]');
    if (codigoInput) {
        codigoInput.focus();
    }
    
    // Auto-submit cuando se ingresen 6 dígitos
    codigoInput.addEventListener('input', function() {
        if (this.value.length === 6) {
            this.form.submit();
        }
    });
    
    // Solo permitir números
    codigoInput.addEventListener('keypress', function(e) {
        if (!/[0-9]/.test(e.key) && !['Backspace', 'Delete', 'Tab', 'Enter'].includes(e.key)) {
            e.preventDefault();
        }
    });
    
    // Limpiar campo cuando el usuario sale de la página
    window.addEventListener('beforeunload', function() {
        codigoInput.value = '';
    });
    
    // Limpiar campo cuando se oculta la página
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            codigoInput.value = '';
        }
    });
});
</script>

<?php include 'views/layouts/footer.php'; ?>
