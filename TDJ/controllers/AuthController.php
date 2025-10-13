<?php
require_once 'config/database.php';
require_once 'models/Usuario.php';
require_once 'includes/funciones.php';

class AuthController {
    private $usuario;
    private $conn;
    
    public function __construct() {
        $this->usuario = new Usuario();
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $usuario = $_POST['usuario'];
            $clave = $_POST['clave'];
            
            $user = $this->usuario->verificarLogin($usuario, $clave);
            if ($user) {
                $_SESSION['usuario'] = $user['usuario'];
                $_SESSION['rol'] = $user['rol'];
                $_SESSION['user_id'] = $user['id'];
                header("Location: index.php?controller=home&action=index");
                exit;
            } else {
                header("Location: index.php?controller=auth&action=login&error=1");
                exit;
            }
        }
    }

    public function login() {
        include 'views/auth/login.php';
    }

    public function registro() {
        include 'views/auth/registro.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $usuario = $_POST['usuario'];
            $correo = $_POST['correo'];
            $clave = $_POST['clave'];

            // Verificar si el usuario ya existe usando el modelo
            $existingUser = $this->usuario->obtenerPorUsuario($usuario);
            $existingEmail = $this->usuario->obtenerPorCorreo($correo);
            
            if ($existingUser || $existingEmail) {
                header("Location: index.php?controller=auth&action=registro&error=exists");
                exit;
            }

            // Crear nuevo usuario usando el modelo
            if ($this->usuario->crear($usuario, $correo, $clave, 'usuario')) {
                header("Location: index.php?controller=auth&action=login&mensaje=registro_exitoso");
                exit;
            } else {
                header("Location: index.php?controller=auth&action=registro&error=1");
                exit;
            }
        }
    }

    public function perfil() {
        // Verificar que el usuario esté logueado
        if (!usuario_logueado()) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        // Obtener datos del usuario actual usando el modelo
        $usuario = $_SESSION['usuario'];
        $user = $this->usuario->obtenerPorUsuario($usuario);

        // Obtener todos los usuarios para administradores
        $usuarios = [];
        if (es_admin()) {
            $usuarios = $this->usuario->obtenerTodos();
        }

        // Incluir la vista del perfil
        include 'views/auth/perfil.php';
    }

    // Método para actualizar permisos de usuario
    public function actualizarPermisos() {
        if (!es_admin()) {
            header('Location: index.php?controller=auth&action=perfil');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_POST['user_id'] ?? '';
            $rol = $_POST['rol'] ?? '';
            
            if ($user_id && $rol) {
                // Usar el modelo para actualizar
                if ($this->usuario->actualizarRol($user_id, $rol)) {
                    header('Location: index.php?controller=auth&action=perfil&mensaje=permisos_actualizados');
                } else {
                    header('Location: index.php?controller=auth&action=perfil&error=1');
                }
            } else {
                header('Location: index.php?controller=auth&action=perfil&error=datos_incompletos');
            }
            exit();
        }
    }

    // Método para eliminar usuario
    public function eliminarUsuario() {
        if (!es_admin()) {
            header('Location: index.php?controller=auth&action=perfil');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_POST['user_id'] ?? '';
            
            if ($user_id) {
                // No permitir eliminar el propio usuario
                if ($user_id == obtener_user_id()) {
                    header('Location: index.php?controller=auth&action=perfil&error=propio');
                    exit();
                }

                // Usar el modelo para eliminar
                if ($this->usuario->eliminar($user_id)) {
                    header('Location: index.php?controller=auth&action=perfil&mensaje=usuario_eliminado');
                } else {
                    header('Location: index.php?controller=auth&action=perfil&error=1');
                }
            } else {
                header('Location: index.php?controller=auth&action=perfil&error=datos_incompletos');
            }
            exit();
        }
    }

    public function logout() {
        session_destroy();
        header("Location: index.php?controller=home&action=index");
        exit;
    }
}
?>
