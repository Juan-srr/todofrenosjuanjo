<?php include 'views/layouts/header.php'; ?>

<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <div class="logo-container">
                <img src="public/imagenes/proyecto.jpg" alt="Logo Todo Frenos Juanjo" class="login-logo">
            </div>
            <h1 class="login-title">Bienvenido</h1>
            <p class="login-subtitle">Inicia sesión en tu cuenta</p>
            <div class="info-message" style="background: linear-gradient(135deg, #e3f2fd, #bbdefb); color: #1976d2; padding: 15px; border-radius: 10px; margin: 15px 0; border-left: 4px solid #2196f3;">
                <i class="fas fa-shield-alt" style="margin-right: 8px;"></i>
                <strong>Seguridad:</strong> Se enviará un código de verificación a tu correo para completar el inicio de sesión.
            </div>
        </div>
        
        <?php if(isset($_GET['mensaje'])): ?>
            <div class="mensaje-exito">
                <i class="fas fa-check-circle"></i>
                <?php 
                if($_GET['mensaje'] === 'registro_exitoso') {
                    echo 'Registro exitoso. Ahora puedes iniciar sesión.';
                } elseif($_GET['mensaje'] === 'verificacion_exitosa') {
                    echo 'Correo verificado exitosamente. Ahora puedes iniciar sesión.';
                }
                ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['error'])): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <?php 
                if($_GET['error'] === '1') {
                    echo 'Usuario o contraseña incorrectos.';
                } elseif($_GET['error'] === 'mail') {
                    echo 'Error al enviar el correo de verificación. Intenta de nuevo.';
                } elseif($_GET['error'] === 'codigo') {
                    echo 'Error al generar el código de verificación. Intenta de nuevo.';
                } else {
                    echo 'Información incorrecta. Intenta de nuevo.';
                }
                ?>
            </div>
        <?php endif; ?>
        
        <form action="index.php?controller=auth&action=authenticate" method="POST" class="login-form">
            <div class="form-group">
                <div class="input-container">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" name="usuario" placeholder="Usuario" required class="login-input" autocomplete="off">
                    <div class="input-line"></div>
                </div>
            </div>
            
            <div class="form-group">
                <div class="input-container">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="clave" placeholder="Contraseña" required class="login-input" autocomplete="off">
                    <div class="input-line"></div>
                </div>
            </div>
            
            <button type="submit" class="login-button">
                <span class="button-text">Iniciar Sesión</span>
                <i class="fas fa-arrow-right button-icon"></i>
            </button>
        </form>
        
        <div class="login-footer">
            <p>¿No tienes cuenta? <a href="index.php?controller=auth&action=registro" class="register-link">Regístrate aquí</a></p>
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

<?php include 'views/layouts/footer.php'; ?>
