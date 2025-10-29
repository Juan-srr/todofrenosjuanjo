<?php include 'views/layouts/header.php'; ?>

<main class="editar-producto-container">
    <div class="editar-producto-card">
        <div class="editar-producto-header">
            <h1 class="editar-producto-title">Editar Producto</h1>
            <p class="editar-producto-subtitle">Modifica la información del producto seleccionado.</p>
        </div>
        
        <?php if(isset($_GET['error'])): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                Error al actualizar el producto. Intenta de nuevo.
            </div>
        <?php endif; ?>
        
        <form method="post" enctype="multipart/form-data" class="editar-producto-form" action="index.php?controller=productos&action=update&id=<?= $producto['id'] ?>">
            <div class="form-group">
                <label class="form-label" for="nombre"><i class="fas fa-tag"></i> Nombre del Producto:</label>
                <input type="text" name="nombre" id="nombre" class="form-input" value="<?= htmlspecialchars($producto['nombre']) ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="descripcion"><i class="fas fa-align-left"></i> Descripción:</label>
                <textarea name="descripcion" id="descripcion" class="form-input form-textarea" required><?= htmlspecialchars($producto['descripcion']) ?></textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="precio"><i class="fas fa-dollar-sign"></i> Precio:</label>
                <input type="number" name="precio" id="precio" class="form-input" step="0.01" value="<?= $producto['precio'] ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="cantidad"><i class="fas fa-boxes"></i> Cantidad:</label>
                <input type="number" name="cantidad" id="cantidad" class="form-input" value="<?= $producto['cantidad'] ?>" min="0" required>
            </div>
            
            <div class="form-group">
                <label class="form-label"><i class="fas fa-image"></i> Imagen (dejar vacío para mantener la actual):</label>
                <?php if(!empty($producto['imagen'])): ?>
                    <div class="current-image">
                        <img src="public/imagenes/uploads/<?= htmlspecialchars($producto['imagen']) ?>" alt="Imagen actual" style="max-width: 200px; border-radius: 10px; margin: 10px 0;">
                        <p><small>Imagen actual: <?= htmlspecialchars($producto['imagen']) ?></small></p>
                    </div>
                <?php endif; ?>
                <input type="file" name="imagen" id="imagen" class="form-input" accept="image/*">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Actualizar Producto
                </button>
                <a href="index.php?controller=productos&action=index" class="btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al Catálogo
                </a>
            </div>
        </form>
    </div>
</main>

<?php include 'views/layouts/footer.php'; ?>
