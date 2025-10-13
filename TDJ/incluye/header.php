<?php
// Iniciamos sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once 'utilidades.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo Frenos Juanjo</title>
    <link rel="stylesheet" href="publico/estilos/estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>
<body>
<header>
    <nav class="navbar" role="navigation" aria-label="Navegación principal">
        <a href="index.php" class="logo-link" aria-label="Ir al inicio">
            <img src="fotos/proyecto.jpg" alt="Logo Todo Frenos Juanjo" class="logo-navbar">
        </a>
        
        <input type="checkbox" id="menu-toggle" class="menu-toggle" aria-label="Alternar menú de navegación" />
        <label for="menu-toggle" class="menu-icon" role="button" tabindex="0" aria-expanded="false" aria-controls="nav-menu">
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
        </label>
        
        <ul class="nav-links" id="nav-menu" role="menubar">
            <li role="none"><a href="index.php" role="menuitem" aria-current="page">Inicio</a></li>
            <li role="none"><a href="productos.php" role="menuitem">Productos</a></li>
            <?php if(usuario_logueado()): ?>
                <li role="none"><a href="miCuenta.php" role="menuitem">Mi Cuenta</a></li>
                <li role="none"><a href="cerrarSesion.php" role="menuitem">Cerrar sesión</a></li>
            <?php else: ?>
                <li role="none"><a href="registrarse.php" role="menuitem">Registrarse</a></li>
                <li role="none"><a href="iniciarSesion.php" role="menuitem">Iniciar sesión</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <div class="menu-overlay" aria-hidden="true"></div>
</header> 