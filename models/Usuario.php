<?php
require_once 'config/database.php';

class Usuario {
    private $conn;
    private $table = 'usuarios';

    public function __construct() {
        $this->conn = Database::conectar();
        $this->crearTablaSiNoExiste();
    }
    
    private function crearTablaSiNoExiste() {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario VARCHAR(50) UNIQUE NOT NULL,
            correo VARCHAR(255) UNIQUE NOT NULL,
            clave VARCHAR(255) NOT NULL,
            rol ENUM('usuario', 'admin', 'dueño', 'administrador', 'empleado') DEFAULT 'usuario',
            correo_verificado BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        try {
            $this->conn->exec($sql);
        } catch (PDOException $e) {
            error_log("Error creando tabla usuarios: " . $e->getMessage());
        }
    }

    // CREATE - Crear usuario (sin verificar correo)
    public function crear($usuario, $correo, $clave, $rol = 'usuario') {
        $hashedPassword = password_hash($clave, PASSWORD_DEFAULT);
        $sql = "INSERT INTO {$this->table} (usuario, correo, clave, rol) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$usuario, $correo, $hashedPassword, $rol]);
    }
    
    // Marcar correo como verificado
    public function marcarCorreoVerificado($correo) {
        $sql = "UPDATE {$this->table} SET correo_verificado = TRUE WHERE correo = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$correo]);
    }
    
    // Verificar si el correo está verificado
    public function correoVerificado($correo) {
        $sql = "SELECT correo_verificado FROM {$this->table} WHERE correo = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$correo]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ? (bool)$resultado['correo_verificado'] : false;
    }

    // READ - Verificar login (solo si correo está verificado)
    public function verificarLogin($usuario, $clave) {
        $sql = "SELECT * FROM {$this->table} WHERE usuario = ? AND correo_verificado = TRUE";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$usuario]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($clave, $user['clave'])) {
            return $user;
        }
        return false;
    }

    // READ - Obtener usuario por ID
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // READ - Obtener usuario por nombre de usuario
    public function obtenerPorUsuario($usuario) {
        $sql = "SELECT * FROM {$this->table} WHERE usuario = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // READ - Obtener usuario por correo
    public function obtenerPorCorreo($correo) {
        $sql = "SELECT * FROM {$this->table} WHERE correo = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$correo]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // READ - Obtener todos los usuarios
    public function obtenerTodos() {
        $sql = "SELECT id, usuario, correo, rol FROM {$this->table} ORDER BY usuario";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // UPDATE - Actualizar rol de usuario
    public function actualizarRol($id, $rol) {
        $sql = "UPDATE {$this->table} SET rol = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$rol, $id]);
    }

    // DELETE - Eliminar usuario
    public function eliminar($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }
}
?>
