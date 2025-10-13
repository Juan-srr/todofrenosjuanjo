<?php
// Función para verificar si el usuario está logueado
function usuario_logueado() {
    return isset($_SESSION['usuario']) && !empty($_SESSION['usuario']);
}

// Función para verificar si es administrador o dueño
function es_admin() {
    return isset($_SESSION['rol']) && ($_SESSION['rol'] == 'admin' || $_SESSION['rol'] == 'dueño');
}

// Función para verificar si es administrador específicamente
function es_administrador() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] == 'admin';
}

// Función para verificar si es dueño específicamente
function es_dueño() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] == 'dueño';
}

// Función para verificar si es empleado
function es_empleado() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] == 'empleado';
}

// Función para verificar si es usuario regular
function es_usuario() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] == 'user';
}

// Función para obtener el ID del usuario logueado
function obtener_user_id() {
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        return null;
    }
    return $_SESSION['user_id'];
}

// Función para obtener el nombre del usuario logueado
function obtener_nombre_usuario() {
    return $_SESSION['usuario'] ?? '';
}

// Función para obtener el rol del usuario logueado
function obtener_rol_usuario() {
    return $_SESSION['rol'] ?? '';
}
?>
