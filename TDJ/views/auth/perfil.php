<?php include 'views/layouts/header.php'; ?>

<main class="profile-main">
    <div class="profile-container">
        <!-- Header del perfil -->
        <div class="profile-header">
            <div class="profile-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="profile-info">
                <h1 class="profile-name"><?php echo htmlspecialchars($user['usuario']); ?></h1>
                <p class="profile-email">
                    <i class="fas fa-envelope"></i>
                    <?php echo htmlspecialchars($user['correo']); ?>
                </p>
                <div class="profile-role">
                    <span class="role-badge role-<?php echo strtolower($user['rol']); ?>">
                        <i class="fas fa-<?php echo $user['rol'] == 'admin' ? 'crown' : 'user'; ?>"></i>
                        <?php echo htmlspecialchars($user['rol']); ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Gestión de usuarios para administradores -->
        <?php if(es_admin()): ?>
        <div class="admin-section">
            <div class="section-header">
                <h2><i class="fas fa-users-cog"></i> Gestión de Usuarios</h2>
                <p>Administra los usuarios del sistema</p>
            </div>
            
            <!-- Mensajes de estado -->
            <?php if(isset($_GET['mensaje'])): ?>
                <?php if($_GET['mensaje'] == 'permisos_actualizados'): ?>
                    <div class="mensaje mensaje-exito">
                        <i class="fas fa-check-circle"></i> Permisos del usuario actualizados correctamente
                    </div>
                <?php elseif($_GET['mensaje'] == 'usuario_eliminado'): ?>
                    <div class="mensaje mensaje-exito">
                        <i class="fas fa-check-circle"></i> Usuario eliminado correctamente
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if(isset($_GET['error'])): ?>
                <?php if($_GET['error'] == 'propio'): ?>
                    <div class="mensaje mensaje-advertencia">
                        <i class="fas fa-exclamation-triangle"></i> No puedes eliminar tu propio usuario
                    </div>
                <?php elseif($_GET['error'] == 'datos_incompletos'): ?>
                    <div class="mensaje mensaje-error">
                        <i class="fas fa-exclamation-circle"></i> Datos incompletos para procesar la solicitud
                    </div>
                <?php else: ?>
                    <div class="mensaje mensaje-error">
                        <i class="fas fa-exclamation-circle"></i> Error al procesar la solicitud
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            
            <div class="users-table-container">
                <div class="table-header">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Buscar usuarios..." id="userSearch">
                    </div>
                </div>
                
                <div class="table-wrapper">
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-user"></i> Usuario</th>
                                <th><i class="fas fa-envelope"></i> Correo</th>
                                <th><i class="fas fa-user-tag"></i> Rol</th>
                                <th><i class="fas fa-cogs"></i> Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($usuarios as $row): ?>
                            <tr class="user-row" data-usuario="<?= strtolower($row['usuario']) ?>">
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            <?= strtoupper(substr($row['usuario'], 0, 1)) ?>
                                        </div>
                                        <span><?php echo htmlspecialchars($row['usuario']); ?></span>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($row['correo']); ?></td>
                                <td>
                                    <span class="role-badge role-<?php echo strtolower($row['rol']); ?>">
                                        <i class="fas fa-<?php echo $row['rol'] == 'admin' ? 'crown' : 'user'; ?>"></i>
                                        <?php echo htmlspecialchars($row['rol']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="action-btn edit-btn" title="Editar permisos" onclick="editarUsuario(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['usuario']); ?>', '<?php echo htmlspecialchars($row['rol']); ?>')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php if($row['id'] != obtener_user_id()): ?>
                                        <button class="action-btn delete-btn" title="Eliminar usuario" onclick="eliminarUsuario(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['usuario']); ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Modal para editar permisos -->
            <div id="editModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3><i class="fas fa-edit"></i> Editar Permisos de Usuario</h3>
                        <span class="close">&times;</span>
                    </div>
                    <form id="editarPermisosForm" method="post" action="index.php?controller=auth&action=actualizarPermisos">
                        <div class="form-group">
                            <label class="form-label" for="editUsuario">Usuario:</label>
                            <input type="text" id="editUsuario" class="form-input" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="editRol">Rol:</label>
                            <select id="editRol" name="rol" class="form-input" required>
                                <option value="usuario">Usuario</option>
                                <option value="empleado">Empleado</option>
                                <option value="admin">Administrador</option>
                                <option value="dueño">Dueño</option>
                            </select>
                        </div>
                        <input type="hidden" id="editUserId" name="user_id">
                        <div class="form-actions">
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-save"></i> Actualizar Permisos
                            </button>
                            <button type="button" class="btn-secondary" onclick="cerrarModal()">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</main>

<style>
/* Estilos específicos para elementos del perfil que no están en formularios.css */
.profile-avatar {
    font-size: 4rem;
    color: var(--amarillo);
    margin-bottom: 20px;
}

.profile-email {
    color: var(--gris-oscuro);
    font-size: 1.1rem;
    margin-bottom: 15px;
}

.profile-role {
    margin-bottom: 20px;
}

.role-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.role-admin {
    background: linear-gradient(135deg, var(--dorado), var(--naranja-frenos));
    color: var(--negro);
}

.role-user {
    background: linear-gradient(135deg, var(--gris-metalico), var(--plata));
    color: var(--negro);
}

.admin-section {
    margin-top: 40px;
    padding-top: 40px;
    border-top: 2px solid rgba(255, 230, 0, 0.2);
}

.section-header {
    text-align: center;
    margin-bottom: 30px;
}

.section-header h2 {
    font-size: 2rem;
    color: var(--amarillo);
    margin-bottom: 10px;
}

.section-header p {
    color: var(--gris-oscuro);
    font-size: 1.1rem;
}

.users-table-container {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 15px;
    padding: 20px;
    backdrop-filter: blur(10px);
}

.table-header {
    margin-bottom: 20px;
}

.search-box {
    position: relative;
    max-width: 300px;
    margin: 0 auto;
}

.search-box i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gris-oscuro);
}

.search-box input {
    width: 100%;
    padding: 12px 15px 12px 45px;
    border: none;
    border-radius: 25px;
    background: rgba(255, 255, 255, 0.9);
    font-size: 14px;
}

.users-table {
    width: 100%;
    border-collapse: collapse;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 15px;
    overflow: hidden;
}

.users-table th,
.users-table td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
}

.users-table th {
    background: linear-gradient(135deg, var(--amarillo), var(--dorado));
    color: var(--negro);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.user-avatar {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, var(--amarillo), var(--dorado));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--negro);
    font-weight: 700;
    font-size: 1.2rem;
}

.action-buttons {
    display: flex;
    gap: 8px;
}

.action-btn {
    width: 35px;
    height: 35px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.edit-btn {
    background: linear-gradient(135deg, var(--azul-acero), #2980b9);
    color: white;
}

.delete-btn {
    background: linear-gradient(135deg, var(--rojo-frenos), #c0392b);
    color: white;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.mensaje {
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
}

.mensaje-exito {
    background: linear-gradient(135deg, #d4edda, #c3e6cb);
    color: #155724;
    border-left: 4px solid #28a745;
}

.mensaje-error {
    background: linear-gradient(135deg, #fed7d7, #feb2b2);
    color: #c53030;
    border-left: 4px solid #e53e3e;
}

.mensaje-advertencia {
    background: linear-gradient(135deg, #fef5e7, #fed7aa);
    color: #d97706;
    border-left: 4px solid #f59e0b;
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(5px);
}

.modal-content {
    background: linear-gradient(135deg, 
        rgba(176, 176, 176, 0.95) 0%, 
        rgba(192, 192, 192, 0.9) 25%, 
        rgba(208, 208, 208, 0.85) 50%, 
        rgba(224, 224, 224, 0.9) 75%, 
        rgba(176, 176, 176, 0.95) 100%);
    margin: 5% auto;
    padding: 30px;
    border-radius: 20px;
    width: 90%;
    max-width: 500px;
    backdrop-filter: blur(20px);
    border: 2px solid rgba(255, 230, 0, 0.5);
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid rgba(255, 230, 0, 0.3);
}

.modal-header h3 {
    color: var(--negro);
    margin: 0;
}

.close {
    color: var(--gris-oscuro);
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    transition: color 0.3s ease;
}

.close:hover {
    color: var(--rojo-frenos);
}

/* Responsive */
@media (max-width: 768px) {
    .profile-container {
        padding: 20px;
        margin: 10px;
    }
    
    .profile-header {
        flex-direction: column;
        text-align: center;
        gap: 20px;
    }
    
    .profile-info {
        text-align: center;
    }
    
    .profile-name {
        font-size: 2rem;
    }
    
    .profile-email {
        font-size: 1rem;
    }
    
    .users-table-container {
        padding: 15px;
        overflow-x: auto;
    }
    
    .users-table {
        font-size: 12px;
        min-width: 600px;
    }
    
    .users-table th,
    .users-table td {
        padding: 8px 6px;
        white-space: nowrap;
    }
    
    .user-info {
        gap: 8px;
    }
    
    .user-avatar {
        width: 30px;
        height: 30px;
        font-size: 1rem;
    }
    
    .action-buttons {
        flex-direction: column;
        gap: 4px;
    }
    
    .action-btn {
        width: 30px;
        height: 30px;
        font-size: 12px;
    }
    
    .role-badge {
        padding: 4px 8px;
        font-size: 0.8rem;
    }
    
    .search-box {
        max-width: 100%;
    }
    
    .search-box input {
        padding: 10px 15px 10px 40px;
        font-size: 14px;
    }
    
    .modal-content {
        margin: 5% auto;
        padding: 20px;
        width: 95%;
    }
    
    .section-header h2 {
        font-size: 1.5rem;
    }
    
    .section-header p {
        font-size: 1rem;
    }
}

@media (max-width: 480px) {
    .profile-container {
        padding: 15px;
        margin: 5px;
    }
    
    .profile-name {
        font-size: 1.8rem;
    }
    
    .profile-avatar {
        font-size: 3rem;
    }
    
    .users-table {
        font-size: 11px;
        min-width: 500px;
    }
    
    .users-table th,
    .users-table td {
        padding: 6px 4px;
    }
    
    .user-avatar {
        width: 25px;
        height: 25px;
        font-size: 0.9rem;
    }
    
    .action-btn {
        width: 25px;
        height: 25px;
        font-size: 10px;
    }
    
    .role-badge {
        padding: 3px 6px;
        font-size: 0.7rem;
    }
    
    .search-box input {
        padding: 8px 12px 8px 35px;
        font-size: 13px;
    }
    
    .modal-content {
        margin: 10% auto;
        padding: 15px;
    }
    
    .section-header h2 {
        font-size: 1.3rem;
    }
    
    .section-header p {
        font-size: 0.9rem;
    }
    
    .mensaje {
        padding: 12px 15px;
        font-size: 13px;
    }
}

@media (max-width: 360px) {
    .users-table {
        min-width: 400px;
        font-size: 10px;
    }
    
    .users-table th,
    .users-table td {
        padding: 4px 2px;
    }
    
    .user-avatar {
        width: 20px;
        height: 20px;
        font-size: 0.8rem;
    }
    
    .action-btn {
        width: 20px;
        height: 20px;
        font-size: 9px;
    }
    
    .role-badge {
        padding: 2px 4px;
        font-size: 0.6rem;
    }
}

/* Mejoras para el scroll horizontal de la tabla */
.table-wrapper {
    overflow-x: auto;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.table-wrapper::-webkit-scrollbar {
    height: 8px;
}

.table-wrapper::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 4px;
}

.table-wrapper::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, var(--amarillo), var(--dorado));
    border-radius: 4px;
}

.table-wrapper::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, var(--dorado), var(--amarillo));
}
</style>

<script>
// Funcionalidad del modal y gestión de usuarios
const modal = document.getElementById('editModal');
const closeBtn = document.querySelector('.close');

closeBtn.onclick = function() {
    modal.style.display = "none";
}

window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

function editarUsuario(userId, usuario, rol) {
    document.getElementById('editUserId').value = userId;
    document.getElementById('editUsuario').value = usuario;
    document.getElementById('editRol').value = rol;
    modal.style.display = "block";
}

function cerrarModal() {
    modal.style.display = "none";
}

function eliminarUsuario(userId, usuario) {
    if (confirm(`¿Estás seguro de que quieres eliminar al usuario "${usuario}"?`)) {
        // Crear un formulario temporal para enviar la solicitud POST
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'index.php?controller=auth&action=eliminarUsuario';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'user_id';
        input.value = userId;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}

// Búsqueda de usuarios
document.getElementById('userSearch').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('.user-row');
    
    rows.forEach(row => {
        const usuario = row.getAttribute('data-usuario');
        if (usuario.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Reset del formulario al cerrar el modal
document.querySelector('.close').addEventListener('click', function() {
    document.getElementById('editarPermisosForm').reset();
});
</script>

<?php include 'views/layouts/footer.php'; ?>
