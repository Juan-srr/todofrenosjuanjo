<?php include 'views/layouts/header.php'; ?>

<main>
    <div class="movimiento-header">
        <h2>Registrar Movimiento</h2>
        <a href="index.php?controller=movimientos&action=index" class="boton volver">
            <i class="fas fa-arrow-left"></i> Volver a Movimientos
        </a>
    </div>

    <?php if(isset($_GET['error'])): ?>
        <div class="mensaje-error" style="background:#f8d7da;color:#721c24;padding:10px;border-radius:5px;margin:10px 0;">
            <?php 
            switch($_GET['error']) {
                case '1': echo 'Por favor completa todos los campos obligatorios.'; break;
                case '2': echo 'No hay suficiente stock disponible para esta salida.'; break;
                case '3': echo 'Error al registrar el movimiento. Inténtalo de nuevo.'; break;
            }
            ?>
        </div>
    <?php endif; ?>

    <div class="formulario-movimiento">
        <form action="index.php?controller=movimientos&action=store" method="POST" class="form-movimiento">
            <div class="form-group">
                <label for="producto_id">Producto *</label>
                <select name="producto_id" id="producto_id" required onchange="actualizarStock()">
                    <option value="">Selecciona un producto</option>
                    <?php foreach($productos as $producto): ?>
                        <option value="<?php echo $producto['id']; ?>" 
                                data-stock="<?php echo $producto['cantidad']; ?>"
                                data-precio="<?php echo $producto['precio'] ?? 0; ?>">
                            <?php echo htmlspecialchars($producto['nombre']); ?> 
                            (Stock: <?php echo $producto['cantidad']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <div id="stock-info" class="stock-info" style="display: none;">
                    <span>Stock disponible: <strong id="stock-disponible">0</strong></span>
                </div>
            </div>

            <div class="form-group">
                <label for="tipo">Tipo de Movimiento *</label>
                <select name="tipo" id="tipo" required onchange="actualizarValidacion()">
                    <option value="">Selecciona el tipo</option>
                    <option value="entrada" <?php echo (isset($_GET['tipo']) && $_GET['tipo'] === 'entrada') ? 'selected' : ''; ?>>
                        Entrada
                    </option>
                    <option value="salida" <?php echo (isset($_GET['tipo']) && $_GET['tipo'] === 'salida') ? 'selected' : ''; ?>>
                        Salida
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="cantidad">Cantidad *</label>
                <input type="number" name="cantidad" id="cantidad" min="1" required onchange="validarCantidad()">
                <div id="cantidad-error" class="error-message" style="display: none;">
                    La cantidad no puede ser mayor al stock disponible
                </div>
            </div>

            <div class="form-group">
                <label for="precio_unitario">Precio Unitario *</label>
                <div class="input-group">
                    <span class="input-prefix">$</span>
                    <input type="number" name="precio_unitario" id="precio_unitario" step="0.01" min="0" required>
                </div>
            </div>

            <div class="form-group">
                <label for="referencia">Referencia</label>
                <input type="text" name="referencia" id="referencia" placeholder="Número de factura, orden, etc.">
            </div>

            <div class="form-group">
                <label for="notas">Notas</label>
                <textarea name="notas" id="notas" rows="3" placeholder="Comentarios adicionales sobre el movimiento"></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="boton registrar" id="btn-registrar">
                    <i class="fas fa-save"></i> Registrar Movimiento
                </button>
                <a href="index.php?controller=movimientos&action=index" class="boton cancelar">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
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

.formulario-movimiento {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    max-width: 600px;
    margin: 0 auto;
}

.form-movimiento {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    font-weight: 600;
    margin-bottom: 5px;
    color: #333;
}

.form-group input,
.form-group select,
.form-group textarea {
    padding: 12px;
    border: 2px solid #e1e5e9;
    border-radius: 6px;
    font-size: 16px;
    transition: border-color 0.3s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #007bff;
}

.input-group {
    display: flex;
    align-items: center;
    border: 2px solid #e1e5e9;
    border-radius: 6px;
    overflow: hidden;
}

.input-prefix {
    background: #f8f9fa;
    padding: 12px 15px;
    border-right: 1px solid #e1e5e9;
    color: #666;
    font-weight: 500;
}

.input-group input {
    border: none;
    flex: 1;
    border-radius: 0;
}

.stock-info {
    margin-top: 5px;
    padding: 8px 12px;
    background: #e3f2fd;
    border-radius: 4px;
    font-size: 14px;
    color: #1976d2;
}

.error-message {
    margin-top: 5px;
    padding: 8px 12px;
    background: #ffebee;
    border-radius: 4px;
    font-size: 14px;
    color: #c62828;
}

.form-actions {
    display: flex;
    gap: 15px;
    justify-content: flex-end;
    margin-top: 20px;
    flex-wrap: wrap;
}

.boton.registrar {
    background: #007bff;
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: background-color 0.3s ease;
}

.boton.registrar:hover {
    background: #0056b3;
}

.boton.registrar:disabled {
    background: #6c757d;
    cursor: not-allowed;
}

.boton.cancelar {
    background: #6c757d;
    color: white;
    padding: 12px 24px;
    text-decoration: none;
    border-radius: 6px;
    font-size: 16px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: background-color 0.3s ease;
}

.boton.cancelar:hover {
    background: #545b62;
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

@media (max-width: 768px) {
    .movimiento-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .boton.registrar,
    .boton.cancelar {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
function actualizarStock() {
    const select = document.getElementById('producto_id');
    const stockInfo = document.getElementById('stock-info');
    const stockDisponible = document.getElementById('stock-disponible');
    const precioInput = document.getElementById('precio_unitario');
    
    if (select.value) {
        const option = select.options[select.selectedIndex];
        const stock = option.getAttribute('data-stock');
        const precio = option.getAttribute('data-precio');
        
        stockDisponible.textContent = stock;
        stockInfo.style.display = 'block';
        
        if (precio && precio > 0) {
            precioInput.value = precio;
        }
        
        validarCantidad();
    } else {
        stockInfo.style.display = 'none';
    }
}

function validarCantidad() {
    const select = document.getElementById('producto_id');
    const cantidadInput = document.getElementById('cantidad');
    const tipoSelect = document.getElementById('tipo');
    const errorDiv = document.getElementById('cantidad-error');
    const btnRegistrar = document.getElementById('btn-registrar');
    
    if (select.value && tipoSelect.value === 'salida') {
        const option = select.options[select.selectedIndex];
        const stock = parseInt(option.getAttribute('data-stock'));
        const cantidad = parseInt(cantidadInput.value);
        
        if (cantidad > stock) {
            errorDiv.style.display = 'block';
            btnRegistrar.disabled = true;
        } else {
            errorDiv.style.display = 'none';
            btnRegistrar.disabled = false;
        }
    } else {
        errorDiv.style.display = 'none';
        btnRegistrar.disabled = false;
    }
}

function actualizarValidacion() {
    validarCantidad();
}

// Inicializar validación al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    actualizarStock();
    validarCantidad();
});
</script>

<?php include 'views/layouts/footer.php'; ?>

