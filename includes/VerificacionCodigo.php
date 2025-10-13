<?php
require_once 'config/database.php';

class VerificacionCodigo {
    private $conn;
    private $table = 'codigos_verificacion';
    
    public function __construct() {
        $this->conn = Database::conectar();
        $this->crearTablaSiNoExiste();
    }
    
    private function crearTablaSiNoExiste() {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            correo VARCHAR(255) NOT NULL,
            codigo VARCHAR(6) NOT NULL,
            tipo ENUM('registro', 'login') NOT NULL,
            expira_en TIMESTAMP NOT NULL,
            usado BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_usuario_tipo (usuario_id, tipo),
            INDEX idx_correo_codigo (correo, codigo),
            INDEX idx_expira (expira_en)
        )";
        
        try {
            $this->conn->exec($sql);
        } catch (PDOException $e) {
            error_log("Error creando tabla de códigos: " . $e->getMessage());
        }
    }
    
    public function generarCodigo($usuarioId, $correo, $tipo = 'registro') {
        
        $this->limpiarCodigosExpirados();
        
        
        $codigo = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        
        $minutos = ($tipo === 'registro') ? 10 : 5;
        $expiraEn = date('Y-m-d H:i:s', strtotime("+{$minutos} minutes"));
        
        
        $sql = "INSERT INTO {$this->table} (usuario_id, correo, codigo, tipo, expira_en) 
                VALUES (?, ?, ?, ?, ?)";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$usuarioId, $correo, $codigo, $tipo, $expiraEn]);
            return $codigo;
        } catch (PDOException $e) {
            error_log("Error generando código: " . $e->getMessage());
            return false;
        }
    }
    
    public function verificarCodigo($correo, $codigo, $tipo = 'registro') {
        $sql = "SELECT * FROM {$this->table} 
                WHERE correo = ? AND codigo = ? AND tipo = ? 
                AND usado = FALSE AND expira_en > NOW()
                ORDER BY created_at DESC LIMIT 1";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$correo, $codigo, $tipo]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                
                $this->marcarCodigoComoUsado($resultado['id']);
                return true;
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Error verificando código: " . $e->getMessage());
            return false;
        }
    }
    
    private function marcarCodigoComoUsado($codigoId) {
        $sql = "UPDATE {$this->table} SET usado = TRUE WHERE id = ?";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$codigoId]);
        } catch (PDOException $e) {
            error_log("Error marcando código como usado: " . $e->getMessage());
        }
    }
    
    private function limpiarCodigosExpirados() {
        $sql = "DELETE FROM {$this->table} WHERE expira_en < NOW()";
        try {
            $this->conn->exec($sql);
        } catch (PDOException $e) {
            error_log("Error limpiando códigos expirados: " . $e->getMessage());
        }
    }
    
    public function obtenerCodigoActivo($usuarioId, $tipo) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE usuario_id = ? AND tipo = ? 
                AND usado = FALSE AND expira_en > NOW()
                ORDER BY created_at DESC LIMIT 1";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$usuarioId, $tipo]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error obteniendo código activo: " . $e->getMessage());
            return false;
        }
    }
}
?>
