<?php
require_once 'models/Producto.php';
require_once 'includes/funciones.php';
require_once 'config/database.php';

class ProductoController {
    private $producto;

    public function __construct() {
        $this->producto = new Producto();
    }

    // CREATE - Mostrar formulario de creación
    public function create() {
        if (!es_admin()) {
            header('Location: index.php?controller=productos&action=index');
            exit();
        }
        include 'views/productos/create.php';
    }

    // CREATE - Procesar creación
    public function store() {
        if (!es_admin()) {
            header('Location: index.php?controller=productos&action=index');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $precio = $_POST['precio'] ?? 0;
            $cantidad = $_POST['cantidad'] ?? 0;

            // Manejo de imagen
            $imagen = '';
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
                $imagen = $this->subirImagen($_FILES['imagen']);
            }

            if ($this->producto->crear($nombre, $descripcion, $precio, $cantidad, $imagen)) {
                header('Location: index.php?controller=productos&action=index&mensaje=creado');
            } else {
                header('Location: index.php?controller=productos&action=create&error=1');
            }
        }
    }

    // READ - Mostrar todos los productos
    public function index() {
        // Todos los usuarios logueados pueden ver el catálogo
        if (!usuario_logueado()) {
            header('Location: index.php?controller=auth&action=login');
            exit();
        }
        $productos = $this->producto->obtenerTodos();
        include 'views/productos/index.php';
    }

    // READ - Mostrar producto específico
    public function show($id) {
        // Todos los usuarios logueados pueden ver productos específicos
        if (!usuario_logueado()) {
            header('Location: index.php?controller=auth&action=login');
            exit();
        }
        $producto = $this->producto->obtenerPorId($id);
        if (!$producto) {
            header('Location: index.php?controller=productos&action=index');
            exit();
        }
        include 'views/productos/show.php';
    }

    // UPDATE - Mostrar formulario de edición
    public function edit($id) {
        if (!es_admin()) {
            header('Location: index.php?controller=productos&action=index');
            exit();
        }

        $producto = $this->producto->obtenerPorId($id);
        if (!$producto) {
            header('Location: index.php?controller=productos&action=index');
            exit();
        }
        include 'views/productos/edit.php';
    }

    // UPDATE - Procesar edición
    public function update($id) {
        if (!es_admin()) {
            header('Location: index.php?controller=productos&action=index');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $precio = $_POST['precio'] ?? 0;
            $cantidad = $_POST['cantidad'] ?? 0;

            // Manejo de imagen
            $imagen = null;
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
                $imagen = $this->subirImagen($_FILES['imagen']);
            }

            if ($this->producto->actualizar($id, $nombre, $descripcion, $precio, $cantidad, $imagen)) {
                header('Location: index.php?controller=productos&action=index&mensaje=editado');
            } else {
                header('Location: index.php?controller=productos&action=edit&id=' . $id . '&error=1');
            }
        }
    }

    // UPDATE - Actualizar cantidad
    public function actualizarCantidad($id) {
        if (!es_admin()) {
            header('Location: index.php?controller=productos&action=index');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cantidad = $_POST['cantidad'] ?? 0;
            if ($this->producto->actualizarCantidad($id, $cantidad)) {
                header('Location: index.php?controller=productos&action=index&mensaje=actualizado');
            } else {
                header('Location: index.php?controller=productos&action=index&error=1');
            }
        }
    }

    // DELETE - Eliminar producto
    public function delete($id) {
        if (!es_admin()) {
            header('Location: index.php?controller=productos&action=index');
            exit();
        }

        if ($this->producto->eliminar($id)) {
            header('Location: index.php?controller=productos&action=index&mensaje=eliminado');
        } else {
            header('Location: index.php?controller=productos&action=index&error=1');
        }
    }

    // Función auxiliar para subir imágenes
    private function subirImagen($file) {
        $uploadDir = 'public/imagenes/uploads/';
        
        // Crear directorio si no existe
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileName = time() . '_' . basename($file['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return $fileName;
        }
        return '';
    }
}
?>
