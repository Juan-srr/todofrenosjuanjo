<?php include 'views/layouts/header.php'; ?>

<main>
    <div class="movimientos-header">
        <h2>Gestión de Movimientos</h2>
        <div class="movimientos-actions">
            <?php if(tiene_acceso_movimientos()): ?>
                <div class="exportar-dropdown">
                    <button class="boton exportar dropdown-toggle">
                        <i class="fas fa-download"></i> Exportar <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu">
                        <a href="index.php?controller=movimientos&action=exportar&formato=excel" class="dropdown-item">
                            <i class="fas fa-file-excel"></i> Excel
                        </a>
                        <a href="index.php?controller=movimientos&action=exportar&formato=pdf" class="dropdown-item">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a>
                    </div>
                </div>
                <?php if(puede_crear_movimientos()): ?>
                    <a href="index.php?controller=movimientos&action=create" class="boton crear-movimiento">
                        <i class="fas fa-plus"></i> Registrar Movimiento
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php if(isset($_GET['mensaje'])): ?>
        <div class="mensaje-exito" style="background:#d4edda;color:#155724;padding:10px;border-radius:5px;margin:10px 0;">
            <?php 
            switch($_GET['mensaje']) {
                case 'creado': echo 'Movimiento registrado correctamente.'; break;
                case 'eliminado': echo 'Movimiento eliminado correctamente.'; break;
            }
            ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_GET['error'])): ?>
        <div class="mensaje-error" style="background:#f8d7da;color:#721c24;padding:10px;border-radius:5px;margin:10px 0;">
            <?php 
            switch($_GET['error']) {
                case '1': echo 'Error al procesar la solicitud.'; break;
                case 'sin_permisos': echo 'No tienes permisos para acceder a esta sección.'; break;
                case 'sin_permisos_eliminar': echo 'No tienes permisos para eliminar movimientos.'; break;
                case 'sin_datos': echo 'No hay datos para exportar.'; break;
                case 'exportar_fallo': echo 'Error al generar el archivo de exportación. Intenta de nuevo.'; break;
            }
            ?>
        </div>
    <?php endif; ?>

    <!-- Estadísticas -->
    <div class="estadisticas-movimientos">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $estadisticas['total_movimientos'] ?? 0; ?></h3>
                <p>Total Movimientos</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon entrada">
                <i class="fas fa-arrow-up"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $estadisticas['total_entradas'] ?? 0; ?></h3>
                <p># Entradas</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon salida">
                <i class="fas fa-arrow-down"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $estadisticas['total_salidas'] ?? 0; ?></h3>
                <p># Salidas</p>
            </div>
        </div>
    </div>

    <!-- Movimientos Recientes -->
    <div class="movimientos-recientes">
        <h3>Movimientos Recientes (Últimos 30 días)</h3>
        
        <?php if(empty($movimientos)): ?>
            <div class="sin-movimientos">
                <i class="fas fa-inbox"></i>
                <p>No hay movimientos registrados</p>
                <a href="index.php?controller=movimientos&action=create" class="boton">Registrar Primer Movimiento</a>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table class="tabla-movimientos">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Producto</th>
                            <th>Tipo</th>
                            <th>Cantidad</th>
                            <th>Precio Unit.</th>
                            <th>Total</th>
                            <th>Usuario</th>
                            <th>Referencia</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($movimientos as $mov): ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($mov['fecha'])); ?></td>
                                <td><?php echo htmlspecialchars($mov['producto_nombre']); ?></td>
                                <td>
                                    <span class="tipo-movimiento <?php echo $mov['tipo']; ?>">
                                        <?php echo ucfirst($mov['tipo']); ?>
                                    </span>
                                </td>
                                <td><?php echo $mov['cantidad']; ?></td>
                                <td>$<?php echo number_format($mov['precio_unitario'], 2); ?></td>
                                <td>$<?php echo number_format($mov['cantidad'] * $mov['precio_unitario'], 2); ?></td>
                                <td><?php echo htmlspecialchars($mov['usuario_nombre'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($mov['referencia']); ?></td>
                                <td>
                                    <a href="index.php?controller=movimientos&action=show&id=<?php echo $mov['id']; ?>" class="boton-accion ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if(puede_eliminar_movimientos()): ?>
                                        <a href="index.php?controller=movimientos&action=delete&id=<?php echo $mov['id']; ?>" 
                                           class="boton-accion eliminar" 
                                           onclick="return confirm('¿Estás seguro de eliminar este movimiento?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Acciones Rápidas -->
    <?php if(tiene_acceso_movimientos()): ?>
        <div class="acciones-rapidas">
            <h3>Acciones Rápidas</h3>
            <div class="botones-rapidos">
                <?php if(puede_crear_movimientos()): ?>
                    <a href="index.php?controller=movimientos&action=create&tipo=entrada" class="boton rapido entrada">
                        <i class="fas fa-plus"></i> Nueva Entrada
                    </a>
                    <a href="index.php?controller=movimientos&action=create&tipo=salida" class="boton rapido salida">
                        <i class="fas fa-minus"></i> Registrar Salida
                    </a>
                <?php endif; ?>
                <a href="index.php?controller=productos&action=index" class="boton rapido productos">
                    <i class="fas fa-box"></i> Ver Productos
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="sin-permisos">
            <div class="mensaje-sin-permisos">
                <i class="fas fa-lock"></i>
                <h3>Acceso Restringido</h3>
                <p>No tienes permisos para acceder al módulo de movimientos.</p>
                <p>Contacta al administrador si necesitas acceso.</p>
                <a href="index.php?controller=home&action=index" class="boton">Volver al Inicio</a>
            </div>
        </div>
    <?php endif; ?>
</main>

<style>
.movimientos-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 15px;
}

.movimientos-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.estadisticas-movimientos {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 15px;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
    background: #007bff;
}

.stat-icon.entrada {
    background: #28a745;
}

.stat-icon.salida {
    background: #dc3545;
}

.stat-content h3 {
    margin: 0;
    font-size: 24px;
    font-weight: bold;
    color: #333;
}

.stat-content p {
    margin: 5px 0 0 0;
    color: #666;
    font-size: 14px;
}

.movimientos-recientes {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.sin-movimientos {
    text-align: center;
    padding: 40px;
    color: #666;
}

.sin-movimientos i {
    font-size: 48px;
    margin-bottom: 15px;
    color: #ccc;
}

.table-container {
    overflow-x: auto;
}

.tabla-movimientos {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

.tabla-movimientos th,
.tabla-movimientos td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.tabla-movimientos th {
    background: #f8f9fa;
    font-weight: 600;
    color: #333;
}

.tipo-movimiento {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
    text-transform: uppercase;
}

.tipo-movimiento.entrada {
    background: #d4edda;
    color: #155724;
}

.tipo-movimiento.salida {
    background: #f8d7da;
    color: #721c24;
}

.boton-accion {
    display: inline-block;
    padding: 6px 10px;
    margin: 0 2px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 12px;
}

.boton-accion.ver {
    background: #17a2b8;
    color: white;
}

.boton-accion.eliminar {
    background: #dc3545;
    color: white;
}

.acciones-rapidas {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.botones-rapidos {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    margin-top: 15px;
}

.boton.rapido {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.boton.rapido.entrada {
    background: #28a745;
    color: white;
}

.boton.rapido.salida {
    background: #dc3545;
    color: white;
}

.boton.rapido.productos {
    background: #6c757d;
    color: white;
}

.boton.rapido:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* Estilos para mensaje de sin permisos */
.sin-permisos {
    background: white;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    text-align: center;
}

.mensaje-sin-permisos {
    max-width: 400px;
    margin: 0 auto;
}

.mensaje-sin-permisos i {
    font-size: 64px;
    color: #dc3545;
    margin-bottom: 20px;
}

.mensaje-sin-permisos h3 {
    color: #333;
    margin-bottom: 15px;
}

.mensaje-sin-permisos p {
    color: #666;
    margin-bottom: 10px;
    line-height: 1.5;
}

.mensaje-sin-permisos .boton {
    margin-top: 20px;
    background: #007bff;
    color: white;
    padding: 12px 24px;
    text-decoration: none;
    border-radius: 6px;
    display: inline-block;
    transition: background-color 0.3s ease;
}

.mensaje-sin-permisos .boton:hover {
    background: #0056b3;
    color: white;
}

/* Estilos para el dropdown de exportación */
.exportar-dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-toggle {
    background: #17a2b8;
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 6px;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: background-color 0.3s ease;
}

.dropdown-toggle:hover {
    background: #138496;
}

.dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    background: white;
    border: 1px solid #ddd;
    border-radius: 6px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    min-width: 150px;
    z-index: 1000;
    display: none;
}

.dropdown-menu.show {
    display: block;
}

.dropdown-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 15px;
    color: #333;
    text-decoration: none;
    transition: background-color 0.3s ease;
}

.dropdown-item:hover {
    background: #f8f9fa;
    color: #333;
}

.dropdown-item i {
    width: 16px;
    text-align: center;
}

@media (max-width: 768px) {
    .movimientos-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .estadisticas-movimientos {
        grid-template-columns: 1fr;
    }
    
    .botones-rapidos {
        flex-direction: column;
    }
    
    .tabla-movimientos {
        font-size: 14px;
    }
    
    .dropdown-menu {
        position: static;
        box-shadow: none;
        border: none;
        margin-top: 10px;
    }
}
</style>

<script>
// Funcionalidad del dropdown de exportación
document.addEventListener('DOMContentLoaded', function() {
    const dropdownToggle = document.querySelector('.dropdown-toggle');
    const dropdownMenu = document.querySelector('.dropdown-menu');
    
    if (dropdownToggle && dropdownMenu) {
        dropdownToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
        });
        
        // Cerrar dropdown al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!dropdownToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('show');
            }
        });
    }
});
</script>

<?php include 'views/layouts/footer.php'; ?>

