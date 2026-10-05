<?php 

require_once __DIR__ . '/../includes/app.php';

use Controllers\BitacoraController;
use Controllers\LoginController;
use MVC\Router;

$router = new Router();

// Iniciar sesión
$router->get('/', [LoginController::class, 'login']);
$router->post('/', [LoginController::class, 'login']);
// Cerrar sesión
$router->get('/logout', [LoginController::class, 'logout']);
// Nuevo registro de usuario (Administrador, supervisor o analista)
$router->get('/crear-usuario', [BitacoraController::class, 'crearUsuario']);
$router->post('/crear-usuario', [BitacoraController::class, 'crearUsuario']);
// Crear nueva atención
$router->get('/crear-atencion', [BitacoraController::class, 'crearAtencion']);
$router->post('/crear-atencion', [BitacoraController::class, 'crearAtencion']);
// Ruta para la API de búsqueda (Asegúrate de importar el controlador si es necesario)
$router->post('/api/buscar-comunidad', [Controllers\BitacoraController::class, 'buscarComunidad']);
// Mis atenciones
$router->get('/mis-atenciones', [BitacoraController::class, 'misAtenciones']);
// Reportes
$router->get('/reportes', [Controllers\BitacoraController::class, 'reportes']);
// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();