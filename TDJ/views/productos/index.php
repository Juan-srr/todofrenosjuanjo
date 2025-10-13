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

    <?php if(es_admin()): ?>
        <a href="index.php?controller=productos&action=create" class="boton crear-producto">Crear nuevo producto</a>
    <?php endif; ?>

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

<script>
function confirmarEliminacion() {
    return confirm('¿Estás seguro que quieres eliminar este producto?');
}
</script>

<?php include 'views/layouts/footer.php'; ?>
