<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'u659361960_todofrenos');
define('DB_USER', 'root');
define('DB_PASS', '');

class Database {
    private static $conn = null;
    
    public static function conectar() {
        if (self::$conn === null) {
            try {
                self::$conn = new PDO(
                    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
                    DB_USER,
                    DB_PASS
                );
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch(PDOException $e) {
                die("Error de conexión: " . $e->getMessage());
            }
        }
        return self::$conn;
    }
    
    public function getConnection() {
        return self::conectar();
    }
}

// Crear conexión PDO para compatibilidad con código existente
try {
    $conn = Database::conectar();
} catch(PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
