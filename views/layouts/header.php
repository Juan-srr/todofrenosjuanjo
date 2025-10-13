<?php
// Incluir funciones de autenticación
require_once 'includes/funciones.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo Frenos Juanjo</title>
    <link rel="stylesheet" href="public/css/estilos.css">
    <link rel="stylesheet" href="public/css/formularios.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <script type="text/javascript">window.$crisp=[];window.CRISP_WEBSITE_ID="92f39e0a-0988-49e3-b174-ee315ba28152";(function(){d=document;s=d.createElement("script");s.src="https://client.crisp.chat/l.js";s.async=1;d.getElementsByTagName("head")[0].appendChild(s);})();</script>
</head>
<body>
<header>
    <nav class="navbar" role="navigation" aria-label="Navegación principal">
        <a href="index.php?controller=home&action=index" class="logo-link" aria-label="Ir al inicio">
            <img src="public/imagenes/proyecto.jpg" alt="Logo Todo Frenos Juanjo" class="logo-navbar">
        </a>
        
        <input type="checkbox" id="menu-toggle" class="menu-toggle" aria-label="Alternar menú de navegación" />
        <label for="menu-toggle" class="menu-icon" role="button" tabindex="0" aria-expanded="false" aria-controls="nav-menu">
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
        </label>
        
        <ul class="nav-links" id="nav-menu" role="menubar">
            <li role="none"><a href="index.php?controller=home&action=index" role="menuitem" aria-current="page">Inicio</a></li>
            <li role="none"><a href="index.php?controller=productos&action=index" role="menuitem">Catálogo</a></li>
            <?php if(usuario_logueado()): ?>
                <li role="none"><a href="index.php?controller=auth&action=perfil" role="menuitem">Perfil</a></li>
                <li role="none"><a href="index.php?controller=auth&action=logout" role="menuitem">Cerrar sesión</a></li>
            <?php else: ?>
                <li role="none"><a href="index.php?controller=auth&action=registro" role="menuitem">Registrarse</a></li>
                <li role="none"><a href="index.php?controller=auth&action=login" role="menuitem">Iniciar sesión</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <div class="menu-overlay" aria-hidden="true"></div>
</header>
      
