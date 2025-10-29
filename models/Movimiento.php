<?php
require_once 'config/database.php';

class Movimiento {
    private $conn;
    private $table = 'movimientos';

    public function __construct() {
        $this->conn = Database::conectar();
    }

    // CREATE - Crear movimiento
    public function crear($producto_id, $tipo, $cantidad, $precio_unitario, $referencia = '', $notas = '') {
        try {
            $this->conn->beginTransaction();
            
            // Insertar el movimiento
            // NOTA: El trigger 'actualizar_cantidad_producto' en la BD actualiza el stock automáticamente
            $sql = "INSERT INTO {$this->table} (producto_id, tipo, cantidad, precio_unitario, referencia, notas, fecha, usuario_id) VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)";
            $stmt = $this->conn->prepare($sql);
            $usuario_id = obtener_user_id() ?? 1; // ID del usuario logueado
            $stmt->execute([$producto_id, $tipo, $cantidad, $precio_unitario, $referencia, $notas, $usuario_id]);
            
            // El stock se actualiza automáticamente mediante el trigger de la base de datos
            // Ya no llamamos a actualizarStock() para evitar la doble actualización
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    // Verificar si la tabla usuarios existe
    private function tablaUsuariosExiste() {
        try {
            $sql = "SHOW TABLES LIKE 'usuarios'";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetch() !== false;
        } catch (Exception $e) {
            return false;
        }
    }

    // Obtener nombre de usuario de forma segura
    private function obtenerNombreUsuario($usuario_id) {
        if (!$this->tablaUsuariosExiste()) {
            return "Usuario " . $usuario_id;
        }

        try {
            $sql = "SELECT usuario, correo FROM usuarios WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$usuario_id]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($usuario) {
                return $usuario['usuario'] ?? $usuario['correo'] ?? "Usuario " . $usuario_id;
            }
            return "Usuario " . $usuario_id;
        } catch (Exception $e) {
            return "Usuario " . $usuario_id;
        }
    }

    // READ - Obtener todos los movimientos
    public function obtenerTodos($limit = 50) {
        $sql = "SELECT m.*, p.nombre as producto_nombre 
                FROM {$this->table} m 
                LEFT JOIN productos p ON m.producto_id = p.id 
                ORDER BY m.fecha DESC 
                LIMIT " . intval($limit);
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $movimientos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Agregar nombre de usuario a cada movimiento
        foreach ($movimientos as &$mov) {
            $mov['usuario_nombre'] = $this->obtenerNombreUsuario($mov['usuario_id']);
        }
        
        return $movimientos;
    }

    // READ - Obtener movimientos por producto
    public function obtenerPorProducto($producto_id) {
        $sql = "SELECT m.*, p.nombre as producto_nombre 
                FROM {$this->table} m 
                LEFT JOIN productos p ON m.producto_id = p.id 
                WHERE m.producto_id = ? 
                ORDER BY m.fecha DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$producto_id]);
        $movimientos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Agregar nombre de usuario a cada movimiento
        foreach ($movimientos as &$mov) {
            $mov['usuario_nombre'] = $this->obtenerNombreUsuario($mov['usuario_id']);
        }
        
        return $movimientos;
    }

    // READ - Obtener estadísticas de movimientos
    public function obtenerEstadisticas() {
        $sql = "SELECT 
                    COUNT(*) as total_movimientos,
                    SUM(CASE WHEN tipo = 'entrada' THEN 1 ELSE 0 END) as total_entradas,
                    SUM(CASE WHEN tipo = 'salida' THEN 1 ELSE 0 END) as total_salidas
                FROM {$this->table}";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // READ - Obtener movimientos recientes (últimos 30 días)
    public function obtenerMovimientosRecientes() {
        $sql = "SELECT m.*, p.nombre as producto_nombre 
                FROM {$this->table} m 
                LEFT JOIN productos p ON m.producto_id = p.id 
                WHERE m.fecha >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                ORDER BY m.fecha DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $movimientos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Agregar nombre de usuario a cada movimiento
        foreach ($movimientos as &$mov) {
            $mov['usuario_nombre'] = $this->obtenerNombreUsuario($mov['usuario_id']);
        }
        
        return $movimientos;
    }

    // READ - Obtener todos los productos para el dropdown
    public function obtenerProductos() {
        $sql = "SELECT id, nombre, cantidad FROM productos ORDER BY nombre";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // READ - Obtener movimiento por ID
    public function obtenerPorId($id) {
        $sql = "SELECT m.*, p.nombre as producto_nombre 
                FROM {$this->table} m 
                LEFT JOIN productos p ON m.producto_id = p.id 
                WHERE m.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        $movimiento = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($movimiento) {
            $movimiento['usuario_nombre'] = $this->obtenerNombreUsuario($movimiento['usuario_id']);
        }
        
        return $movimiento;
    }

    // DELETE - Eliminar movimiento (solo admin)
    public function eliminar($id) {
        try {
            $this->conn->beginTransaction();
            
            // Obtener el movimiento para revertir el stock
            $movimiento = $this->obtenerPorId($id);
            if (!$movimiento) {
                return false;
            }
            
            // Revertir el stock manualmente
            // Si era una entrada, restamos. Si era una salida, sumamos.
            if ($movimiento['tipo'] === 'entrada') {
                $sql = "UPDATE productos SET cantidad = cantidad - ? WHERE id = ?";
            } else {
                $sql = "UPDATE productos SET cantidad = cantidad + ? WHERE id = ?";
            }
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$movimiento['cantidad'], $movimiento['producto_id']]);
            
            // Eliminar el movimiento
            $sql = "DELETE FROM {$this->table} WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id]);
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }
}
?>