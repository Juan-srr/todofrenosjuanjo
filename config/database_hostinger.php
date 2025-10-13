<?php
// Configuración de base de datos para Hostinger
class Database {
    // Configuración de la base de datos para Hostinger
    private static $DB_HOST = 'localhost'; // O el host que te proporcione Hostinger
    private static $DB_NAME = 'u659361960_todofrenos'; // Tu base de datos en Hostinger
    private static $DB_USER = 'u659361960_tu_usuario'; // Tu usuario de Hostinger
    private static $DB_PASS = 'tu_contraseña_de_hostinger'; // Tu contraseña de Hostinger
    
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
