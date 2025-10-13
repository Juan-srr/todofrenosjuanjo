<?php
require_once 'config/database.php';

class HomeController {
    private $conn;

    public function __construct() {
        // Usar la conexión de la clase Database
        $this->conn = Database::conectar();
    }

    public function index() {
        include 'views/home/index.php';
    }
}
?>
