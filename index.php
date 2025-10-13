<?php
session_start();

// Detectar si estamos en Hostinger o entorno local
$is_hostinger = (strpos($_SERVER['HTTP_HOST'], 'hostinger') !== false || 
                 strpos($_SERVER['HTTP_HOST'], '.com') !== false ||
                 strpos($_SERVER['HTTP_HOST'], '.net') !== false);

// Cargar configuración según el entorno
if ($is_hostinger) {
    require_once 'config/database_hostinger.php';
    require_once 'config/mail_hostinger.php';
} else {
    require_once 'config/database.php';
    require_once 'config/mail.php';
}
require_once 'includes/funciones.php';
require_once 'controllers/ProductoController.php';
require_once 'controllers/AuthController.php';
require_once 'controllers/HomeController.php';
require_once 'controllers/MovimientoController.php';


$controller = $_GET['controller'] ?? 'home';
$action = $_GET['action'] ?? 'index';


if ($controller === 'auth') {
    $authController = new AuthController();
    
    switch ($action) {
        case 'login':
            $authController->login();
            break;
        case 'authenticate':
            $authController->authenticate();
            break;
        case 'registro':
            $authController->registro();
            break;
        case 'store':
            $authController->store();
            break;
        case 'perfil':
            $authController->perfil();
            break;
        case 'actualizarPermisos':
            $authController->actualizarPermisos();
            break;
        case 'eliminarUsuario':
            $authController->eliminarUsuario();
            break;
        case 'logout':
            $authController->logout();
            break;
        
        case 'verificarCodigo':
            $authController->verificarCodigo();
            break;
        case 'procesarVerificacion':
            $authController->procesarVerificacion();
            break;
        default:
            $authController->login();
            break;
    }
} elseif ($controller === 'home') {
    $homeController = new HomeController();
    $homeController->index();
} elseif ($controller === 'productos') {
    $productoController = new ProductoController();
    
    switch ($action) {
        case 'index':
            $productoController->index();
            break;
        case 'create':
            $productoController->create();
            break;
        case 'store':
            $productoController->store();
            break;
        case 'show':
            $id = $_GET['id'] ?? null;
            if ($id) {
                $productoController->show($id);
            } else {
                header('Location: index.php?controller=productos&action=index');
            }
            break;
        case 'edit':
            $id = $_GET['id'] ?? null;
            if ($id) {
                $productoController->edit($id);
            } else {
                header('Location: index.php?controller=productos&action=index');
            }
            break;
        case 'update':
            $id = $_GET['id'] ?? null;
            if ($id) {
                $productoController->update($id);
            } else {
                header('Location: index.php?controller=productos&action=index');
            }
            break;
        case 'delete':
            $id = $_GET['id'] ?? null;
            if ($id) {
                $productoController->delete($id);
            } else {
                header('Location: index.php?controller=productos&action=index');
            }
            break;
        default:
            $productoController->index();
            break;
    }
} elseif ($controller === 'movimientos') {
    $movimientoController = new MovimientoController();
    
    switch ($action) {
        case 'index':
            $movimientoController->index();
            break;
        case 'create':
            $movimientoController->create();
            break;
        case 'store':
            $movimientoController->store();
            break;
        case 'show':
            $id = $_GET['id'] ?? null;
            if ($id) {
                $movimientoController->show($id);
            } else {
                header('Location: index.php?controller=movimientos&action=index');
            }
            break;
        case 'delete':
            $id = $_GET['id'] ?? null;
            if ($id) {
                $movimientoController->delete($id);
            } else {
                header('Location: index.php?controller=movimientos&action=index');
            }
            break;
        case 'exportar':
            $movimientoController->exportar();
            break;
        default:
            $movimientoController->index();
            break;
    }
} else {
    
    $homeController = new HomeController();
    $homeController->index();
}
?>
