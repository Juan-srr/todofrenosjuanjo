<?php include 'views/layouts/header.php'; ?>

<main>
    <h2>Catálogo de Productos</h2>
    
    <?php if(isset($_GET['mensaje'])): ?>
        <div class="error" style="background:#d4edda;color:#155724;padding:10px;border-radius:5px;margin:10px 0;">
            <?php 
            switch($_GET['mensaje']) {
                case 'creado': echo 'Producto creado correctamente.'; break;
                case 'editado': echo 'Producto editado correctamente.'; break;
                case 'eliminado': echo 'Producto eliminado correctamente.'; break;
                case 'actualizado': echo 'Cantidad actualizada correctamente.'; break;
            }
            ?>
        </div>
    <?php endif; ?>

    <div class="catalogo-actions">
        <a href="index.php?controller=movimientos&action=index" class="boton movimientos">
            <i class="fas fa-exchange-alt"></i> Ver Movimientos
        </a>
        <?php if(es_admin()): ?>
            <a href="index.php?controller=productos&action=create" class="boton crear-producto">Crear nuevo producto</a>
        <?php endif; ?>
    </div>

    <div class="catalogo">
        <?php foreach($productos as $row): ?>
            <div class="producto">
                <img src="public/imagenes/uploads/<?php echo htmlspecialchars($row['imagen']); ?>" alt="Imagen producto">
                <h3><?php echo htmlspecialchars($row['nombre']); ?></h3>
                <p><?php echo htmlspecialchars($row['descripcion']); ?></p>
                <p>Precio: <span class="precio">$<?php echo number_format($row['precio'], 2); ?></span></p>
                <p><strong>Cantidad disponible:</strong> <?php echo isset($row['cantidad']) ? htmlspecialchars($row['cantidad']) : 'No especificada'; ?></p>
                <?php if(es_admin()): ?>
                <div class="acciones-producto">
                    <a href="index.php?controller=productos&action=edit&id=<?= $row['id'] ?>" class="boton editar">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <form action="index.php?controller=productos&action=delete&id=<?= $row['id'] ?>" method="POST" onsubmit="return confirmarEliminacion();" style="margin:0;flex:1;">
                        <button type="submit" class="boton eliminar">
                            <i class="fas fa-trash-alt"></i> Eliminar
                        </button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<style>
.catalogo-actions {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.boton.movimientos {
    background: #17a2b8;
    color: white;
    padding: 12px 20px;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: background-color 0.3s ease;
}

.boton.movimientos:hover {
    background: #138496;
}

.boton.crear-producto {
    background: #28a745;
    color: white;
    padding: 12px 20px;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: background-color 0.3s ease;
}

.boton.crear-producto:hover {
    background: #1e7e34;
}

@media (max-width: 768px) {
    .catalogo-actions {
        flex-direction: column;
    }
    
    .boton.movimientos,
    .boton.crear-producto {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
function confirmarEliminacion() {
    return confirm('¿Estás seguro que quieres eliminar este producto?');
}
</script>

<?php include 'views/layouts/footer.php'; ?>
