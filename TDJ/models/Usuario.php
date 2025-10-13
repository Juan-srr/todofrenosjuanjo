<?php
require_once 'config/database.php';

class Usuario {
    private $conn;
    private $table = 'usuarios';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // CREATE - Crear usuario
    public function crear($usuario, $correo, $clave, $rol = 'usuario') {
        $hashedPassword = password_hash($clave, PASSWORD_DEFAULT);
        $sql = "INSERT INTO {$this->table} (usuario, correo, clave, rol) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$usuario, $correo, $hashedPassword, $rol]);
    }

    // READ - Verificar login
    public function verificarLogin($usuario, $clave) {
        $sql = "SELECT * FROM {$this->table} WHERE usuario = ?";
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
