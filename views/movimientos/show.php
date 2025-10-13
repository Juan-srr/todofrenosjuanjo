<?php include 'views/layouts/header.php'; ?>

<main>
    <div class="movimiento-header">
        <h2>Detalle del Movimiento</h2>
        <div class="movimiento-actions">
            <a href="index.php?controller=movimientos&action=index" class="boton volver">
                <i class="fas fa-arrow-left"></i> Volver a Movimientos
            </a>
            <?php if(es_admin()): ?>
                <a href="index.php?controller=movimientos&action=delete&id=<?php echo $movimiento['id']; ?>" 
                   class="boton eliminar" 
                   onclick="return confirm('¿Estás seguro de eliminar este movimiento?')">
                    <i class="fas fa-trash"></i> Eliminar
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="movimiento-detail">
        <div class="detail-card">
            <div class="detail-header">
                <h3>Información del Movimiento</h3>
                <span class="tipo-movimiento <?php echo $movimiento['tipo']; ?>">
                    <?php echo ucfirst($movimiento['tipo']); ?>
                </span>
            </div>
            
            <div class="detail-content">
                <div class="detail-row">
                    <label>Fecha:</label>
                    <span><?php echo date('d/m/Y H:i:s', strtotime($movimiento['fecha'])); ?></span>
                </div>
                
                <div class="detail-row">
                    <label>Producto:</label>
                    <span><?php echo htmlspecialchars($movimiento['producto_nombre']); ?></span>
                </div>
                
                <div class="detail-row">
                    <label>Cantidad:</label>
                    <span class="cantidad"><?php echo $movimiento['cantidad']; ?> unidades</span>
                </div>
                
                <div class="detail-row">
                    <label>Precio Unitario:</label>
                    <span class="precio">$<?php echo number_format($movimiento['precio_unitario'], 2); ?></span>
                </div>
                
                <div class="detail-row total">
                    <label>Total:</label>
                    <span class="total">$<?php echo number_format($movimiento['cantidad'] * $movimiento['precio_unitario'], 2); ?></span>
                </div>
                
                <div class="detail-row">
                    <label>Usuario:</label>
                    <span><?php echo htmlspecialchars($movimiento['usuario_nombre'] ?? 'N/A'); ?></span>
                </div>
                
                <?php if(!empty($movimiento['referencia'])): ?>
                <div class="detail-row">
                    <label>Referencia:</label>
                    <span><?php echo htmlspecialchars($movimiento['referencia']); ?></span>
                </div>
                <?php endif; ?>
                
                <?php if(!empty($movimiento['notas'])): ?>
                <div class="detail-row">
                    <label>Notas:</label>
                    <span class="notas"><?php echo nl2br(htmlspecialchars($movimiento['notas'])); ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Información del Producto -->
        <div class="producto-info">
            <h3>Información del Producto</h3>
            <p>Este movimiento afectó el inventario del producto <strong><?php echo htmlspecialchars($movimiento['producto_nombre']); ?></strong>.</p>
            
            <?php if($movimiento['tipo'] === 'entrada'): ?>
                <div class="impacto-info entrada">
                    <i class="fas fa-arrow-up"></i>
                    <span>Se agregaron <?php echo $movimiento['cantidad']; ?> unidades al inventario</span>
                </div>
            <?php else: ?>
                <div class="impacto-info salida">
                    <i class="fas fa-arrow-down"></i>
                    <span>Se retiraron <?php echo $movimiento['cantidad']; ?> unidades del inventario</span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<style>
.movimiento-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 15px;
}

.movimiento-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.movimiento-detail {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
}

.detail-card {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.detail-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f8f9fa;
}

.detail-header h3 {
    margin: 0;
    color: #333;
}

.tipo-movimiento {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
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

.detail-content {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #f8f9fa;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-row.total {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 6px;
    margin-top: 10px;
    font-weight: 600;
    font-size: 18px;
}

.detail-row label {
    font-weight: 600;
    color: #666;
    min-width: 120px;
}

.detail-row span {
    color: #333;
    text-align: right;
}

.cantidad {
    font-weight: 600;
    color: #007bff;
}

.precio {
    font-weight: 600;
    color: #28a745;
}

.total {
    color: #dc3545;
    font-size: 20px;
}

.notas {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 4px;
    font-style: italic;
    text-align: left;
    max-width: 300px;
}

.producto-info {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    height: fit-content;
}

.producto-info h3 {
    margin: 0 0 15px 0;
    color: #333;
}

.producto-info p {
    color: #666;
    margin-bottom: 20px;
    line-height: 1.6;
}

.impacto-info {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 15px;
    border-radius: 6px;
    font-weight: 500;
}

.impacto-info.entrada {
    background: #d4edda;
    color: #155724;
}

.impacto-info.salida {
    background: #f8d7da;
    color: #721c24;
}

.impacto-info i {
    font-size: 18px;
}

.boton.volver {
    background: #28a745;
    color: white;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: background-color 0.3s ease;
}

.boton.volver:hover {
    background: #1e7e34;
}

.boton.eliminar {
    background: #dc3545;
    color: white;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: background-color 0.3s ease;
}

.boton.eliminar:hover {
    background: #c82333;
}

@media (max-width: 768px) {
    .movimiento-detail {
        grid-template-columns: 1fr;
    }
    
    .movimiento-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .movimiento-actions {
        width: 100%;
        justify-content: flex-start;
    }
    
    .detail-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
    
    .detail-row span {
        text-align: left;
    }
}
</style>

<?php include 'views/layouts/footer.php'; ?>

