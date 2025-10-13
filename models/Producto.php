<?php
require_once 'config/database.php';

class Producto {
    private $conn;
    private $table = 'productos';

    public function __construct() {
        $this->conn = Database::conectar();
    }

    // CREATE - Crear producto
    public function crear($nombre, $descripcion, $precio, $cantidad, $imagen) {
        $sql = "INSERT INTO {$this->table} (nombre, descripcion, precio, cantidad, imagen) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$nombre, $descripcion, $precio, $cantidad, $imagen]);
    }

    // READ - Obtener todos los productos
    public function obtenerTodos() {
        $sql = "SELECT * FROM {$this->table}";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // READ - Obtener producto por ID
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // UPDATE - Actualizar producto
    public function actualizar($id, $nombre, $descripcion, $precio, $cantidad, $imagen = null) {
        if ($imagen) {
            $sql = "UPDATE {$this->table} SET nombre = ?, descripcion = ?, precio = ?, cantidad = ?, imagen = ? WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$nombre, $descripcion, $precio, $cantidad, $imagen, $id]);
        } else {
            $sql = "UPDATE {$this->table} SET nombre = ?, descripcion = ?, precio = ?, cantidad = ? WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$nombre, $descripcion, $precio, $cantidad, $id]);
        }
    }

    // UPDATE - Actualizar cantidad
    public function actualizarCantidad($id, $cantidad) {
        $sql = "UPDATE {$this->table} SET cantidad = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$cantidad, $id]);
    }

    // DELETE - Eliminar producto
    public function eliminar($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }
}
?>
