<!DOCTYPE html>
<html lang="es-MX">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bitácora DEV</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <!-- Hoja de estilo -->
    <link rel="stylesheet" href="build/css/app.css">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>

<body>

    <?php
    // Obtenemos la ruta actual (ej. "/crear-usuario" o "/crear-atencion")
    // parse_url limpia la ruta por si tiene parámetros extra en la URL
    $rutaActual = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    ?>

    <header class="barra-navegacion">
        <nav class="navbar navbar-dark fixed-top px-5">
            <div class="container-fluid">
                <p class="navbar-brand fs-2">Hola, <span class="navbar-name"><?php echo $_SESSION['name'] ?? 'Invitado'; ?></span></p>
                <button class="navbar-toggler fs-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title fs-1" id="offcanvasNavbarLabel">Bitácora</h5>
                        <button type="button" class="btn-close fs-2" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                        <ul class="navbar-nav justify-content-end flex-grow-1">

                            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === '1'): ?>
                                <li class="nav-item">
                                    <a class="nav-link fs-2 <?php echo ($rutaActual === '/crear-usuario') ? 'active' : ''; ?>"
                                        <?php echo ($rutaActual === '/crear-usuario') ? 'aria-current="page"' : ''; ?>
                                        href="/crear-usuario">Crear Usuario</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link fs-2 <?php echo ($rutaActual === '/reportes') ? 'active' : ''; ?>"
                                        <?php echo ($rutaActual === '/reportes') ? 'aria-current="page"' : ''; ?>
                                        href="/reportes">Reportes</a>
                                </li>
                            <?php endif; ?>

                            <li class="nav-item">
                                <a class="nav-link fs-2 <?php echo ($rutaActual === '/crear-atencion') ? 'active' : ''; ?>"
                                    <?php echo ($rutaActual === '/crear-atencion') ? 'aria-current="page"' : ''; ?>
                                    href="/crear-atencion">Registrar Atención</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link fs-2 <?php echo ($rutaActual === '/mis-atenciones') ? 'active' : ''; ?>"
                                    <?php echo ($rutaActual === '/mis-atenciones') ? 'aria-current="page"' : ''; ?>
                                    href="/mis-atenciones">Mis Registros</a>
                            </li>


                            <li class="nav-item mt-4 border-top pt-2">
                                <a class="nav-link text-danger fs-2" href="/logout">Cerrar Sesión</a>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <?php echo $contenido; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>


</body>

</html>