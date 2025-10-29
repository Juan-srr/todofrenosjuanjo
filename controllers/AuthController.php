<?php
require_once 'config/database.php';
require_once 'models/Usuario.php';
require_once 'models/CodigoVerificacion.php';
require_once 'includes/funciones.php';
require_once 'includes/Mailer.php';

class AuthController {
    private $usuario;
    private $codigoVerificacion;
    private $mailer;
    private $conn;
    
    public function __construct() {
        $this->usuario = new Usuario();
        $this->codigoVerificacion = new CodigoVerificacion();
        $this->mailer = new Mailer();
        $this->conn = Database::conectar();
    }
    
    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = $_POST['usuario'];
            $clave = $_POST['clave'];
            
            $user = $this->usuario->obtenerPorUsuario($usuario);
            if ($user && password_verify($clave, $user['clave'])) {
                // Verificar si el correo está verificado
                if (!$user['correo_verificado']) {
                    // Generar y enviar código de verificación
                    $codigo = $this->codigoVerificacion->generarCodigo($user['id'], $user['correo'], 'login');
                    if ($codigo && $this->mailer->enviarCodigoVerificacion($user['correo'], $codigo, 'login')) {
                        $_SESSION['usuario_pendiente'] = $user;
                        header("Location: index.php?controller=auth&action=verificarCodigo&tipo=login");
                        exit;
                    } else {
                        header("Location: index.php?controller=auth&action=login&error=mail");
                        exit;
                    }
                } else {
                    // Login exitoso
                    $_SESSION['usuario'] = $user['usuario'];
                    $_SESSION['rol'] = $user['rol'];
                    $_SESSION['user_id'] = $user['id'];
                    header("Location: index.php?controller=home&action=index");
                    exit;
                }
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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = $_POST['usuario'];
            $correo = $_POST['correo'];
            $clave = $_POST['clave'];

            // Verificar si el usuario ya existe
            $existingUser = $this->usuario->obtenerPorUsuario($usuario);
            $existingEmail = $this->usuario->obtenerPorCorreo($correo);
            
            if ($existingUser || $existingEmail) {
                header("Location: index.php?controller=auth&action=registro&error=exists");
                exit;
            }

            // Crear nuevo usuario (sin verificar correo inicialmente)
            if ($this->usuario->crear($usuario, $correo, $clave, 'usuario')) {
                // Obtener el usuario recién creado
                $user = $this->usuario->obtenerPorUsuario($usuario);
                
                // Generar y enviar código de verificación
                $codigo = $this->codigoVerificacion->generarCodigo($user['id'], $correo, 'registro');
                if ($codigo && $this->mailer->enviarCodigoVerificacion($correo, $codigo, 'registro')) {
                    $_SESSION['usuario_pendiente'] = $user;
                    header("Location: index.php?controller=auth&action=verificarCodigo&tipo=registro");
                    exit;
                } else {
                    header("Location: index.php?controller=auth&action=registro&error=mail");
                    exit;
                }
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


    // Mostrar formulario de verificación de código
    public function verificarCodigo() {
        if (!isset($_SESSION['usuario_pendiente'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }
        
        $tipo = $_GET['tipo'] ?? 'registro';
        include 'views/auth/verificar_codigo.php';
    }

    // Procesar verificación de código
    public function procesarVerificacion() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $codigo = $_POST['codigo'];
            $tipo = $_POST['tipo'];
            
            if (!isset($_SESSION['usuario_pendiente'])) {
                header("Location: index.php?controller=auth&action=login");
                exit;
            }
            
            $usuario = $_SESSION['usuario_pendiente'];
            $correo = $usuario['correo'];
            
            // Verificar el código
            $verificacion = $this->codigoVerificacion->verificarCodigo($correo, $codigo, $tipo);
            
            if ($verificacion) {
                // Marcar correo como verificado
                $this->usuario->marcarCorreoVerificado($correo);
                
                // Limpiar sesión pendiente
                unset($_SESSION['usuario_pendiente']);
                
                if ($tipo === 'registro') {
                    // Para registro, redirigir al login con mensaje de éxito
                    header("Location: index.php?controller=auth&action=login&mensaje=verificacion_exitosa");
                } else {
                    // Para login, iniciar sesión directamente
                    $_SESSION['usuario'] = $usuario['usuario'];
                    $_SESSION['rol'] = $usuario['rol'];
                    $_SESSION['user_id'] = $usuario['id'];
                    header("Location: index.php?controller=home&action=index");
                }
                exit;
            } else {
                header("Location: index.php?controller=auth&action=verificarCodigo&tipo={$tipo}&error=codigo_incorrecto");
                exit;
            }
        }
    }

    // Reenviar código de verificación
    public function reenviarCodigo() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipo = $_POST['tipo'];
            
            if (!isset($_SESSION['usuario_pendiente'])) {
                header("Location: index.php?controller=auth&action=login");
                exit;
            }
            
            $usuario = $_SESSION['usuario_pendiente'];
            
            // Generar nuevo código
            $codigo = $this->codigoVerificacion->generarCodigo($usuario['id'], $usuario['correo'], $tipo);
            
            if ($codigo && $this->mailer->enviarCodigoVerificacion($usuario['correo'], $codigo, $tipo)) {
                header("Location: index.php?controller=auth&action=verificarCodigo&tipo={$tipo}&mensaje=codigo_reenviado");
            } else {
                header("Location: index.php?controller=auth&action=verificarCodigo&tipo={$tipo}&error=mail");
            }
            exit;
        }
    }

    public function logout() {
        session_destroy();
        header("Location: index.php?controller=home&action=index");
        exit;
    }
}
?>
