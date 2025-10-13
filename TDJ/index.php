<?php
session_start();

// Incluir archivos necesarios
require_once 'config/database.php';
require_once 'includes/funciones.php';
require_once 'controllers/ProductoController.php';
require_once 'controllers/AuthController.php';
require_once 'controllers/HomeController.php';

// Obtener parámetros de la URL
$controller = $_GET['controller'] ?? 'home';
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

// Router simple y limpio
try {
    switch ($controller) {
        case 'productos':
            $productoController = new ProductoController();
            if ($id) {
                switch ($action) {
                    case 'edit':
                        $productoController->edit($id);
                        break;
                    case 'update':
                        $productoController->update($id);
                        break;
                    case 'delete':
                        $productoController->delete($id);
                        break;
                    case 'actualizarCantidad':
                        $productoController->actualizarCantidad($id);
                        break;
                    default:
                        $productoController->show($id);
                        break;
                }
            } else {
                switch ($action) {
                    case 'create':
                        $productoController->create();
                        break;
                    case 'store':
                        $productoController->store();
                        break;
                    default:
                        $productoController->index();
                        break;
                }
            }
            break;
            
        case 'auth':
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
                default:
                    $authController->login();
                    break;
            }
            break;
            
        case 'home':
        default:
            $homeController = new HomeController();
            $homeController->index();
            break;
            
    }
} catch (Exception $e) {
    // Manejo de errores
    echo "Error: " . $e->getMessage();
}
?>
