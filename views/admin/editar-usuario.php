<div class="body-login">
    <main class="new-user-main" style="max-width: 800px; margin: 0 auto;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1>Editar Usuario</h1>
            <a href="/administrar-usuarios" class="enviar-btn" style="width: fit-content; text-align: center; background-color: #6c757d; text-decoration: none;">&laquo; Volver</a>
        </div>

        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <form class="formulario" method="POST" action="/editar-usuario?id=<?php echo $usuario->id; ?>" onsubmit="return confirmarEdicion();" style="background-color: #f8f9fa; padding: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
            
            <div class="form-group">
                <label for="name">Nombre Completo</label>
                <input type="text" id="name" name="name" placeholder="Nombre del Usuario" value="<?php echo htmlspecialchars($usuario->name); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" placeholder="Correo Electrónico" value="<?php echo htmlspecialchars($usuario->email); ?>" required>
            </div>

            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                <div class="form-group" style="flex: 1 1 200px;">
                    <label for="rol">Rol del Sistema</label>
                    <select name="rol" id="rol" required>
                        <option value="" disabled>-- Seleccionar Rol --</option>
                        <?php foreach($roles as $rol): ?>
                            <option value="<?php echo $rol->id; ?>" <?php echo ($usuario->rol == $rol->id) ? 'selected' : ''; ?>>
                                <?php echo $rol->nombre; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="flex: 1 1 200px;">
                    <label for="status">Estado de la Cuenta</label>
                    <select name="status" id="status" required>
                        <option value="1" <?php echo ($usuario->status == '1') ? 'selected' : ''; ?>>Activo</option>
                        <option value="0" <?php echo ($usuario->status == '0') ? 'selected' : ''; ?>>Inactivo</option>
                    </select>
                </div>
            </div>

            <hr style="margin: 20px 0; border: 0; border-top: 1px solid #dee2e6;">

            <div style="background-color: #e9ecef; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                <h4 style="margin-top: 0; color: #495057;">Seguridad</h4>
                <p style="font-size: 0.85em; color: #6c757d; margin-bottom: 10px;">
                    <strong>Instrucciones:</strong> Si NO deseas cambiar la contraseña de este usuario, deja ambos campos en blanco. Si decides cambiarla, debe tener al menos 8 caracteres y contener una mezcla de letras y números para mayor seguridad.
                </p>
                
                <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                    <div class="form-group" style="flex: 1 1 200px; margin-bottom: 0;">
                        <label for="password">Nueva Contraseña (Opcional)</label>
                        <input type="password" id="password" name="password" placeholder="Mínimo 8 caracteres">
                    </div>
                    
                    <div class="form-group" style="flex: 1 1 200px; margin-bottom: 0;">
                        <label for="confirm_password">Confirmar Nueva Contraseña</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Repite la contraseña">
                    </div>
                </div>
            </div>

            <input type="submit" class="enviar-btn" value="Guardar Cambios" style="width: 100%; margin-top: 10px; font-size: 1.1em;">
        </form>
    </main>
</div>

<script>
    // 1. Script para ocultar las alertas a los 5 segundos
    document.addEventListener('DOMContentLoaded', function() {
        const alertas = document.querySelectorAll('.alerta');
        if (alertas.length > 0) {
            alertas.forEach(alerta => {
                setTimeout(() => { alerta.remove(); }, 5000);
            });
        }
    });

    // 2. Script para lanzar la confirmación antes de guardar
    function confirmarEdicion() {
        // La función confirm() nativa lanza el cuadro de "Sí/No" (Aceptar/Cancelar)
        return confirm('¿Estás seguro de que deseas guardar los cambios para este usuario?');
    }
</script>