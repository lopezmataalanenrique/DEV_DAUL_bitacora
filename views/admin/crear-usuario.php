<div class="body-login">
    <main class="new-user-main">
        <h1>Registrar nueva cuenta</h1>
        <?php
        include_once __DIR__ . '/../templates/alertas.php';
        ?>

        <form method="POST" action="/crear-usuario">
            <div class="form-group">
                <label
                    for="name">Nombre completo</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="Ej. Juan Pérez">
            </div>
            <div class="form-group">
                <label for="email">Correo institucional</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="usuario@ipn.mx"
                    autocomplete="username">
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="********"
                    autocomplete="new-password">
            </div>
            <div>
                <p class="password-requirements">La contraseña debe tener al menos 8 caracteres, incluyendo mayúsculas, minúsculas, números y caracteres especiales.</p>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirmar contraseña</label>
                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    placeholder="********"
                    autocomplete="new-password">
            </div>
            <div class="form-group">
                <label for="rol">Rol</label>
                <select id="rol" name="rol" required>
                    <option value="" disabled selected>-- Selecciona un Rol --</option>
                    <?php foreach ($roles as $rol): ?>
                        <option
                            value="<?php echo $rol->id; ?>"
                            <?php echo (isset($_POST['rol']) && $_POST['rol'] == $rol->id) ? 'selected' : ''; ?>>
                            <?php echo $rol->nombre; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="status">Estado</label>
                <select id="status" name="status">
                    <option value="1" selected>Activo</option>
                    <option value="0">Inactivo</option>
                </select>
            </div>

            <input class="enviar-btn" type="submit" value="Registrar cuenta">
        </form>
    </main>
</div>