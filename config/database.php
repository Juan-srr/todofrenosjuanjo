<?php
class Database {
    // Configuración de la base de datos
    private static $DB_HOST = 'localhost';
    private static $DB_NAME = 'todofrenos';
    private static $DB_USER = 'root';
    private static $DB_PASS = '';
    
    private static $conn = null;
    
    public static function conectar() {
        if (self::$conn === null) {
            try {
                self::$conn = new PDO(
                    "mysql:host=" . self::$DB_HOST . ";dbname=" . self::$DB_NAME . ";charset=utf8",
                    self::$DB_USER,
                    self::$DB_PASS
                );
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch(PDOException $e) {
                die("Error de conexión: " . $e->getMessage());
            }
        }
        return self::$conn;
    }
}
?>
