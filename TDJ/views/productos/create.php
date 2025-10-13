<?php include 'views/layouts/header.php'; ?>

<main class="crear-producto-container">
    <div class="crear-producto-card">
        <div class="crear-producto-header">
            <h1 class="crear-producto-title">Crear Nuevo Producto</h1>
            <p class="crear-producto-subtitle">Añade un nuevo producto al catálogo de Todo Frenos Juanjo</p>
        </div>
        
        <?php if(isset($_GET['error'])): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                Error al crear el producto. Intenta de nuevo.
            </div>
        <?php endif; ?>
        
        <form method="post" enctype="multipart/form-data" class="crear-producto-form" action="index.php?controller=productos&action=store">
            <div class="form-group">
                <label class="form-label" for="nombre"><i class="fas fa-tag"></i> Nombre del Producto:</label>
                <input type="text" name="nombre" id="nombre" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="descripcion"><i class="fas fa-align-left"></i> Descripción:</label>
                <textarea name="descripcion" id="descripcion" class="form-input form-textarea" required></textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="precio"><i class="fas fa-dollar-sign"></i> Precio:</label>
                <input type="number" name="precio" id="precio" class="form-input" step="0.01" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="cantidad"><i class="fas fa-boxes"></i> Cantidad:</label>
                <input type="number" name="cantidad" id="cantidad" class="form-input" min="0" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="imagen"><i class="fas fa-image"></i> Imagen del Producto:</label>
                <input type="file" name="imagen" id="imagen" class="form-input" accept="image/*" required>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-plus"></i> Crear Producto
                </button>
                <a href="index.php?controller=productos&action=index" class="btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al Catálogo
                </a>
            </div>
        </form>
    </div>
</main>

<?php include 'views/layouts/footer.php'; ?>
