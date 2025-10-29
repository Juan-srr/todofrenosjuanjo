<?php
require_once 'config/database.php';

class CodigoVerificacion {
    private $conn;
    private $table = 'codigos_verificacion';

    public function __construct() {
        $this->conn = Database::conectar();
    }

    // Generar código de verificación
    public function generarCodigo($usuario_id, $correo, $tipo = 'registro') {
        // Eliminar códigos anteriores no usados del mismo usuario
        $this->limpiarCodigosAnteriores($usuario_id, $tipo);
        
        // Generar código de 6 dígitos
        $codigo = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Calcular tiempo de expiración (10 minutos para registro, 5 para login)
        $minutos = ($tipo === 'registro') ? 10 : 5;
        $expira_en = date('Y-m-d H:i:s', strtotime("+{$minutos} minutes"));
        
        $sql = "INSERT INTO {$this->table} (usuario_id, correo, codigo, tipo, expira_en) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt->execute([$usuario_id, $correo, $codigo, $tipo, $expira_en])) {
            return $codigo;
        }
        
        return false;
    }

    // Verificar código
    public function verificarCodigo($correo, $codigo, $tipo = 'registro') {
        $sql = "SELECT * FROM {$this->table} 
                WHERE correo = ? AND codigo = ? AND tipo = ? AND usado = 0 AND expira_en > NOW()";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$correo, $codigo, $tipo]);
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($resultado) {
            // Marcar código como usado
            $this->marcarComoUsado($resultado['id']);
            return $resultado;
        }
        
        return false;
    }

    // Marcar código como usado
    private function marcarComoUsado($id) {
        $sql = "UPDATE {$this->table} SET usado = 1 WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
    }

    // Limpiar códigos anteriores no usados
    private function limpiarCodigosAnteriores($usuario_id, $tipo) {
        $sql = "DELETE FROM {$this->table} 
                WHERE usuario_id = ? AND tipo = ? AND (usado = 1 OR expira_en < NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$usuario_id, $tipo]);
    }

    // Limpiar códigos expirados (para ejecutar periódicamente)
    public function limpiarCodigosExpirados() {
        $sql = "DELETE FROM {$this->table} WHERE expira_en < NOW()";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute();
    }
}
?>
