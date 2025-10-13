<?php
require_once 'config/database.php';
require_once 'models/Usuario.php';
require_once 'includes/funciones.php';
require_once 'includes/Mailer.php';
require_once 'includes/VerificacionCodigo.php';

class AuthController {
    private $usuario;
    private $conn;
    private $mailer;
    private $verificacion;
    
    public function __construct() {
        $this->usuario = new Usuario();
        $this->conn = Database::conectar();
        $this->mailer = new Mailer();
        $this->verificacion = new VerificacionCodigo();
    }
    
    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = $_POST['usuario'];
            $clave = $_POST['clave'];
            
            $user = $this->usuario->verificarLogin($usuario, $clave);
            if ($user) {
                // Generar código de verificación para el login
                $codigo = $this->verificacion->generarCodigo($user['id'], $user['correo'], 'login');
                
                if ($codigo) {
                    // Enviar código por correo
                    if ($this->mailer->enviarCodigoVerificacion($user['correo'], $codigo, 'login')) {
                        // Guardar datos en sesión temporal
                        $_SESSION['temp_user'] = $user;
                        $_SESSION['verificacion_pendiente'] = true;
                        $_SESSION['tipo_verificacion'] = 'login';
                        
                        header("Location: index.php?controller=auth&action=verificarCodigo");
                        exit;
                    } else {
                        header("Location: index.php?controller=auth&action=login&error=mail");
                        exit;
                    }
                } else {
                    header("Location: index.php?controller=auth&action=login&error=codigo");
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

            // Crear nuevo usuario (por defecto como 'usuario')
            if ($this->usuario->crear($usuario, $correo, $clave, 'usuario')) {
                // Obtener el ID del usuario creado
                $user = $this->usuario->obtenerPorUsuario($usuario);
                
                // Generar código de verificación
                $codigo = $this->verificacion->generarCodigo($user['id'], $correo, 'registro');
                
                if ($codigo) {
                    // Enviar código por correo
                    if ($this->mailer->enviarCodigoVerificacion($correo, $codigo, 'registro')) {
                        // Guardar datos en sesión temporal
                        $_SESSION['temp_user'] = $user;
                        $_SESSION['verificacion_pendiente'] = true;
                        $_SESSION['tipo_verificacion'] = 'registro';
                        
                        header("Location: index.php?controller=auth&action=verificarCodigo&mensaje=registro_exitoso");
                        exit;
                    } else {
                        // Si falla el envío, eliminar usuario y mostrar error
                        $this->usuario->eliminar($user['id']);
                        header("Location: index.php?controller=auth&action=registro&error=mail");
                        exit;
                    }
                } else {
                    // Si falla la generación del código, eliminar usuario y mostrar error
                    $this->usuario->eliminar($user['id']);
                    header("Location: index.php?controller=auth&action=registro&error=codigo");
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

    public function verificarCodigo() {
        if (!isset($_SESSION['verificacion_pendiente'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }
        include 'views/auth/verificar_codigo.php';
    }
    
    public function procesarVerificacion() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $codigo = $_POST['codigo'];
            $tipo = $_SESSION['tipo_verificacion'] ?? 'login';
            $user = $_SESSION['temp_user'] ?? null;
            
            if (!$user) {
                header("Location: index.php?controller=auth&action=login");
                exit;
            }
            
            if ($this->verificacion->verificarCodigo($user['correo'], $codigo, $tipo)) {
                // Código válido
                if ($tipo === 'registro') {
                    // Marcar correo como verificado
                    $this->usuario->marcarCorreoVerificado($user['correo']);
                    unset($_SESSION['temp_user'], $_SESSION['verificacion_pendiente'], $_SESSION['tipo_verificacion']);
                    
                    header("Location: index.php?controller=auth&action=login&mensaje=correo_verificado");
                    exit;
                } else {
                    // Login exitoso
                    $_SESSION['usuario'] = $user['usuario'];
                    $_SESSION['rol'] = $user['rol'];
                    $_SESSION['user_id'] = $user['id'];
                    unset($_SESSION['temp_user'], $_SESSION['verificacion_pendiente'], $_SESSION['tipo_verificacion']);
                    
                    header("Location: index.php?controller=home&action=index");
                    exit;
                }
            } else {
                // Código inválido
                header("Location: index.php?controller=auth&action=verificarCodigo&error=codigo_invalido");
                exit;
            }
        }
    }

    public function reenviarCodigo() {
        if (!isset($_SESSION['verificacion_pendiente'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }
        
        $user = $_SESSION['temp_user'] ?? null;
        $tipo = $_SESSION['tipo_verificacion'] ?? 'login';
        
        if (!$user) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }
        
        // Generar nuevo código
        $codigo = $this->verificacion->generarCodigo($user['id'], $user['correo'], $tipo);
        
        if ($codigo) {
            // Enviar código por correo
            if ($this->mailer->enviarCodigoVerificacion($user['correo'], $codigo, $tipo)) {
                header("Location: index.php?controller=auth&action=verificarCodigo&mensaje=codigo_reenviado");
                exit;
            } else {
                header("Location: index.php?controller=auth&action=verificarCodigo&error=mail");
                exit;
            }
        } else {
            header("Location: index.php?controller=auth&action=verificarCodigo&error=codigo");
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
