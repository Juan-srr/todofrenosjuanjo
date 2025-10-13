<?php
// Configuración de correo para Hostinger
// IMPORTANTE: Reemplaza estos valores con los de tu cuenta de Hostinger

// Opción 1: Usar el servidor SMTP de Hostinger (Recomendado)
define('MAIL_HOST', 'smtp.hostinger.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'noreply@tudominio.com'); // ← CAMBIA por tu correo real
define('MAIL_PASSWORD', 'tu_contraseña_real'); // ← CAMBIA por tu contraseña real
define('MAIL_FROM_NAME', 'TodoFrenos - Sistema de Verificación');

// Opción 2: Si la opción 1 no funciona, prueba con estos valores:
// define('MAIL_HOST', 'mail.tudominio.com');
// define('MAIL_PORT', 465);
// define('MAIL_USERNAME', 'noreply@tudominio.com');
// define('MAIL_PASSWORD', 'tu_contraseña_real');

// INSTRUCCIONES PARA CONFIGURAR EN HOSTINGER:
// 1. Ve a tu panel de control de Hostinger
// 2. Busca "Correo" o "Email Accounts"
// 3. Crea una cuenta de correo (ej: noreply@tudominio.com)
// 4. Anota la contraseña que te genere
// 5. Reemplaza los valores de arriba con tus datos reales
?>
