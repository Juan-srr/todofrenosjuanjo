<?php include 'views/layouts/header.php'; ?>

<main>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo-container">
                    <img src="public/imagenes/proyecto.jpg" alt="Logo Todo Frenos Juanjo" class="login-logo">
                </div>
                <h1 class="login-title">Crear Cuenta</h1>
                <p class="login-subtitle">Únete a Todo Frenos Juanjo</p>
            </div>
            
            <?php if(isset($_GET['error'])): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?php 
                    if($_GET['error'] === 'exists') {
                        echo 'El usuario o correo ya existe. Intenta con otros datos.';
                    } elseif($_GET['error'] === 'mail') {
                        echo 'Error al enviar el correo de verificación. Intenta de nuevo.';
                    } elseif($_GET['error'] === 'codigo') {
                        echo 'Error al generar el código de verificación. Intenta de nuevo.';
                    } elseif($_GET['error'] === '1') {
                        echo 'Error al crear la cuenta. Intenta de nuevo.';
                    } else {
                        echo 'Error al crear la cuenta. Intenta de nuevo.';
                    }
                    ?>
                </div>
            <?php endif; ?>
            
            <form action="index.php?controller=auth&action=store" method="POST" class="login-form">
                <div class="form-group">
                    <div class="input-container">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" name="usuario" placeholder="Usuario" required class="login-input">
                        <div class="input-line"></div>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="input-container">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" name="correo" placeholder="Correo electrónico" required class="login-input">
                        <div class="input-line"></div>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="input-container">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="clave" placeholder="Contraseña" required class="login-input">
                        <div class="input-line"></div>
                    </div>
                </div>
                
                <button type="submit" class="login-button">
                    <span class="button-text">Crear Cuenta</span>
                    <i class="fas fa-arrow-right button-icon"></i>
                </button>
            </form>
            
            <div class="login-footer">
                <p>¿Ya tienes cuenta? <a href="index.php?controller=auth&action=login" class="register-link">Iniciar sesión</a></p>
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
</main>

<?php include 'views/layouts/footer.php'; ?>
