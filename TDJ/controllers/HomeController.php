<?php
require_once 'config/database.php';

class HomeController {
    private $conn;

    public function __construct() {
        // Crear la conexión a la base de datos
        $this->conn = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
            DB_USER,
            DB_PASS
        );
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function index() {
        include 'views/home/index.php';
    }
}
?>
