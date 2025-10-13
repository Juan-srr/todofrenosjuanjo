<?php
require_once 'models/Movimiento.php';
require_once 'includes/funciones.php';

class MovimientoController {
    private $movimiento;

    public function __construct() {
        $this->movimiento = new Movimiento();
    }

    // READ - Mostrar todos los movimientos
    public function index() {
        try {
            if (!usuario_logueado()) {
                header('Location: index.php?controller=auth&action=login');
                exit();
            }

            // Verificar acceso a movimientos según el rol
            if (!tiene_acceso_movimientos()) {
                header('Location: index.php?controller=home&action=index&error=sin_permisos');
                exit();
            }

            $movimientos = $this->movimiento->obtenerMovimientosRecientes();
            $estadisticas = $this->movimiento->obtenerEstadisticas();
            
            // Debug: Verificar datos
            if (empty($movimientos)) {
                $movimientos = [];
            }
            if (empty($estadisticas)) {
                $estadisticas = [
                    'total_movimientos' => 0,
                    'total_entradas' => 0,
                    'total_salidas' => 0
                ];
            }
            
            include 'views/movimientos/index.php';
        } catch (Exception $e) {
            // Mostrar error en lugar de página en blanco
            echo "<h2>Error en el Sistema de Movimientos</h2>";
            echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<p>Archivo: " . htmlspecialchars($e->getFile()) . "</p>";
            echo "<p>Línea: " . $e->getLine() . "</p>";
            echo "<p><a href='index.php?controller=productos&action=index'>Volver al catálogo</a></p>";
        }
    }

    // CREATE - Mostrar formulario de creación
    public function create() {
        try {
            if (!usuario_logueado()) {
                header('Location: index.php?controller=auth&action=login');
                exit();
            }

            // Verificar acceso a movimientos según el rol
            if (!tiene_acceso_movimientos()) {
                header('Location: index.php?controller=home&action=index&error=sin_permisos');
                exit();
            }

            $productos = $this->movimiento->obtenerProductos();
            
            if (empty($productos)) {
                echo "<h2>No hay productos disponibles</h2>";
                echo "<p>Debes crear productos antes de registrar movimientos.</p>";
                echo "<p><a href='index.php?controller=productos&action=create'>Crear producto</a></p>";
                return;
            }
            
            include 'views/movimientos/create.php';
        } catch (Exception $e) {
            echo "<h2>Error al cargar formulario de movimientos</h2>";
            echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<p><a href='index.php?controller=movimientos&action=index'>Volver a movimientos</a></p>";
        }
    }

    // CREATE - Procesar creación
    public function store() {
        try {
            if (!usuario_logueado()) {
                header('Location: index.php?controller=auth&action=login');
                exit();
            }

            // Verificar acceso a movimientos según el rol
            if (!tiene_acceso_movimientos()) {
                header('Location: index.php?controller=home&action=index&error=sin_permisos');
                exit();
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $producto_id = $_POST['producto_id'] ?? '';
                $tipo = $_POST['tipo'] ?? '';
                $cantidad = $_POST['cantidad'] ?? 0;
                $precio_unitario = $_POST['precio_unitario'] ?? 0;
                $referencia = $_POST['referencia'] ?? '';
                $notas = $_POST['notas'] ?? '';

                // Validaciones
                if (empty($producto_id) || empty($tipo) || $cantidad <= 0 || $precio_unitario <= 0) {
                    header('Location: index.php?controller=movimientos&action=create&error=1');
                    exit();
                }

                // Verificar que hay suficiente stock para salidas
                if ($tipo === 'salida') {
                    $productos = $this->movimiento->obtenerProductos();
                    $producto_actual = null;
                    foreach ($productos as $p) {
                        if ($p['id'] == $producto_id) {
                            $producto_actual = $p;
                            break;
                        }
                    }
                    
                    if (!$producto_actual || $producto_actual['cantidad'] < $cantidad) {
                        header('Location: index.php?controller=movimientos&action=create&error=2');
                        exit();
                    }
                }

                if ($this->movimiento->crear($producto_id, $tipo, $cantidad, $precio_unitario, $referencia, $notas)) {
                    header('Location: index.php?controller=movimientos&action=index&mensaje=creado');
                } else {
                    header('Location: index.php?controller=movimientos&action=create&error=3');
                }
            }
        } catch (Exception $e) {
            header('Location: index.php?controller=movimientos&action=create&error=4');
        }
    }

    // READ - Mostrar movimiento específico
    public function show($id) {
        try {
            if (!usuario_logueado()) {
                header('Location: index.php?controller=auth&action=login');
                exit();
            }

            // Verificar acceso a movimientos según el rol
            if (!tiene_acceso_movimientos()) {
                header('Location: index.php?controller=home&action=index&error=sin_permisos');
                exit();
            }

            $movimiento = $this->movimiento->obtenerPorId($id);
            if (!$movimiento) {
                header('Location: index.php?controller=movimientos&action=index');
                exit();
            }
            include 'views/movimientos/show.php';
        } catch (Exception $e) {
            echo "<h2>Error al mostrar movimiento</h2>";
            echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<p><a href='index.php?controller=movimientos&action=index'>Volver a movimientos</a></p>";
        }
    }

    // DELETE - Eliminar movimiento
    public function delete($id) {
        try {
            if (!usuario_logueado()) {
                header('Location: index.php?controller=auth&action=login');
                exit();
            }

            // Solo dueño y administrador pueden eliminar movimientos
            if (!puede_eliminar_movimientos()) {
                header('Location: index.php?controller=movimientos&action=index&error=sin_permisos_eliminar');
                exit();
            }

            if ($this->movimiento->eliminar($id)) {
                header('Location: index.php?controller=movimientos&action=index&mensaje=eliminado');
            } else {
                header('Location: index.php?controller=movimientos&action=index&error=1');
            }
        } catch (Exception $e) {
            header('Location: index.php?controller=movimientos&action=index&error=1');
        }
    }

    // Exportar movimientos
    public function exportar() {
        try {
            if (!usuario_logueado()) {
                header('Location: index.php?controller=auth&action=login');
                exit();
            }

            // Verificar acceso a movimientos según el rol
            if (!tiene_acceso_movimientos()) {
                header('Location: index.php?controller=home&action=index&error=sin_permisos');
                exit();
            }

            $formato = $_GET['formato'] ?? 'excel';
            $movimientos = $this->movimiento->obtenerTodos(1000);
            
            // Cargar librerías de exportación
            require_once 'includes/ExcelGenerator.php';
            require_once 'includes/PDFGenerator.php';
            
            switch ($formato) {
                case 'excel':
                    ExcelGenerator::generarXLSX($movimientos, 'movimientos_' . date('Y-m-d') . '.xlsx');
                    break;
                case 'pdf':
                    PDFGenerator::generarPDF($movimientos, 'movimientos_' . date('Y-m-d') . '.pdf');
                    break;
                default:
                    ExcelGenerator::generarXLSX($movimientos, 'movimientos_' . date('Y-m-d') . '.xlsx');
                    break;
            }
        } catch (Exception $e) {
            echo "Error al exportar: " . $e->getMessage();
        }
    }

}
?>