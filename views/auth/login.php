<div class="body-login">
    <div class="login-container">
        <aside class="login-sidebar">
            <h2>Bitácora de atenciones</h2>
        </aside>
        <main class="login-main">
            <h1>Iniciar sesión</h1>
            <p class="mensaje-informativo">Acceso exclusivo para Analistas y Supervisores autorizados.</p>

            <?php
                include_once __DIR__ . '/../templates/alertas.php';
            ?>

            <form method="POST" action="/">
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
                        autocomplete="current-password">
                </div>

                <input class="enviar-btn" type="submit" value="Iniciar sesión">                

        </main>
    </div>
</div>