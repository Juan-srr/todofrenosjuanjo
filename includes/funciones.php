<?php

function usuario_logueado() {
    return isset($_SESSION['usuario']) && !empty($_SESSION['usuario']);
}

function es_admin() {
    return isset($_SESSION['rol']) && ($_SESSION['rol'] == 'admin' || $_SESSION['rol'] == 'dueño' || $_SESSION['rol'] == 'administrador');
}

function es_administrador() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] == 'administrador';
}

function es_dueño() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] == 'dueño';
}

function es_empleado() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] == 'empleado';
}

function es_usuario() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] == 'usuario';
}

// Función específica para verificar acceso a movimientos
function tiene_acceso_movimientos() {
    $rol = obtener_rol_usuario();
    return in_array($rol, ['dueño', 'administrador', 'admin', 'empleado']);
}

// Función para verificar si puede crear/editar movimientos
function puede_crear_movimientos() {
    $rol = obtener_rol_usuario();
    return in_array($rol, ['dueño', 'administrador', 'admin', 'empleado']);
}

// Función para verificar si puede eliminar movimientos (solo dueño y administrador)
function puede_eliminar_movimientos() {
    $rol = obtener_rol_usuario();
    return in_array($rol, ['dueño', 'administrador', 'admin']);
}

function obtener_user_id() {
    return $_SESSION['user_id'] ?? null;
}


function obtener_rol_usuario() {
    return $_SESSION['rol'] ?? '';
}
?>
