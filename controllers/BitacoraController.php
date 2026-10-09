<?php

namespace Controllers;

use MVC\Router;
use Model\Usuario;
use Model\Atencion;
use Model\CatEscuela;
use Model\CatTipoAtencion;
use Model\CatMotivoAtencion;
use Model\CatMedioAtencion;
use Model\ComunidadPolitecnica;
use Model\CatRol;

class BitacoraController
{
    public static function crearUsuario(Router $router)
    {

        // --- PROTECCIÓN DE RUTA ---
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // SEGURIDAD: Solo Administrador (Rol 1)
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== '1') {
            // Si es un rol menor, lo mandamos a su pantalla principal
            header('Location: /crear-atencion'); 
            exit;
        }

        $alertas = [];

        // Consultamos todos los roles para mostrarlos en el select del formulario
        $roles = CatRol::all();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // TODO: Crear nuevo usuario
            $user = new Usuario($_POST);
            $alertas = $user->validarNuevaCuenta();

            if (empty($alertas)) {
                // Verificar que el usuario no exista
                $result = $user->existeUsuario();

                if ($result->num_rows) {
                    $alertas = Usuario::getAlertas();
                } else {
                    // Quitar el confirm_password antes de guardar
                    unset($user->confirm_password);
                    // Hashear el password
                    $user->hashPassword();

                    $result = $user->guardar();

                    if ($result) {
                        $alertas['exito'][] = 'Usuario creado correctamente';
                    } else {
                        $alertas['error'][] = 'Error al guardar el usuario';
                    }
                }
            }
        }
        $router->render('admin/crear-usuario', [
            'alertas' => $alertas,
            'roles' => $roles
        ], 'layout_app');
    }

    public static function crearAtencion(Router $router)
    {
        // 1. Asegurarnos de que la sesión esté iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // SEGURIDAD: Verificar que el usuario tenga una sesión activa válida
        if (!isset($_SESSION['id'])) {
            header('Location: /'); // Si no ha iniciado sesión, lo mandamos al login
            exit;
        }

        $alertas = [];
        $atencion = new Atencion();

        $escuelas = CatEscuela::all();
        $tipos_atencion = CatTipoAtencion::all();
        $motivos = CatMotivoAtencion::all();
        $medios = CatMedioAtencion::all();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $atencion = new Atencion($_POST);
            $atencion->id_usuario = $_SESSION['id'] ?? null;

            // -- LÓGICA PARA ASPIRANTE Y PÚBLICO EN GENERAL --
            if ($atencion->id_tipo_atencion == '4' || $atencion->id_tipo_atencion == '5') {
                $atencion->id_escuela = 1; // Forzamos "Sin escuela"
                $alertas = $atencion->validarAtencion();

                if (empty($alertas)) {
                    $resultado = $atencion->guardar();
                    if ($resultado) {
                        $alertas['exito'][] = 'Atención registrada correctamente';
                        $atencion = new Atencion();
                    } else {
                        $alertas['error'][] = 'Error al guardar la atención';
                    }
                }
            }
            // -- LÓGICA PARA COMUNIDAD POLITÉCNICA (Alumno, Asesor, Coordinador) --
            else if (in_array($atencion->id_tipo_atencion, ['1', '2', '3'])) {

                $alertas = $atencion->validarAtencion();

                if (empty($alertas)) {
                    // 1. Buscamos si el correo ya existe en nuestra tabla
                    $persona = ComunidadPolitecnica::where('correo', $atencion->correo);

                    if ($persona) {
                        // Si existe: Sobrescribimos sus datos por si el analista los actualizó en el formulario
                        $persona->nombre_completo = $atencion->nombre_completo;
                        $persona->id_escuela = $atencion->id_escuela;
                        $persona->guardar();
                    } else {
                        // Si no existe: Creamos el nuevo registro
                        $nuevaPersona = new ComunidadPolitecnica([
                            'correo' => $atencion->correo,
                            'nombre_completo' => $atencion->nombre_completo,
                            'id_escuela' => $atencion->id_escuela
                        ]);
                        $nuevaPersona->guardar();
                    }

                    // 2. Finalmente, guardamos la atención en la bitácora
                    $resultado = $atencion->guardar();

                    if ($resultado) {
                        $alertas['exito'][] = 'Atención registrada y base de datos actualizada correctamente';
                        $atencion = new Atencion(); // Limpiamos el formulario
                    } else {
                        $alertas['error'][] = 'Error al guardar la atención';
                    }
                }
            } else {
                $alertas['error'][] = 'Perfil no válido.';
            }
        }

        // Renderizamos la vista
        $router->render('bitacora/crear-atencion', [
            'alertas' => $alertas,
            'atencion' => $atencion,
            'escuelas' => $escuelas,
            'tipos_atencion' => $tipos_atencion,
            'motivos' => $motivos,
            'medios' => $medios,
            'name' => $_SESSION['name'] ?? 'Invitado'
        ], 'layout_app');
    }

    public static function buscarComunidad()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Leemos el JSON que nos enviará JavaScript mediante Fetch API
            $datos = json_decode(file_get_contents('php://input'));
            $correo = $datos->correo ?? '';

            if ($correo) {
                // Buscamos en la base de datos usando el modelo que creamos
                $persona = ComunidadPolitecnica::where('correo', $correo);

                if ($persona) {
                    // Si existe, lo devolvemos en formato JSON
                    echo json_encode(['encontrado' => true, 'persona' => $persona]);
                } else {
                    // Si no existe, avisamos que no hay resultados
                    echo json_encode(['encontrado' => false]);
                }
            } else {
                echo json_encode(['error' => 'Correo no proporcionado']);
            }
            exit; // Detenemos la ejecución porque esto es una API, no una vista HTML
        }
    }

    public static function misAtenciones(Router $router)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $id_usuario = $_SESSION['id'] ?? null;

        if (!$id_usuario) {
            header('Location: /');
            exit;
        }

        // Inicializamos el arreglo de alertas
        $alertas = [];
        $fecha_hoy = date('Y-m-d'); // Obtenemos la fecha de hoy (2026-06-23)

        // --- LÓGICA DE FILTROS Y VALIDACIÓN ---
        if (!isset($_GET['inicio']) && !isset($_GET['fin'])) {
            $fecha_inicio = date('Y-m-01');
            $fecha_fin = $fecha_hoy;
        } else {
            $fecha_inicio = $_GET['inicio'];
            $fecha_fin = $_GET['fin'];

            // 1ra Validación: Que inicio no sea mayor que fin
            if ($fecha_inicio && $fecha_fin && $fecha_inicio > $fecha_fin) {
                $alertas['error'][] = 'La fecha de inicio no puede ser mayor a la fecha final.';
            }

            // 2da Validación: Que la fecha final no sea en el futuro
            if ($fecha_fin > $fecha_hoy) {
                $alertas['error'][] = 'La fecha final no puede ser mayor al día de hoy.';
            }

            // Si hubo algún error en las fechas, reiniciamos el filtro al mes actual por seguridad
            if (!empty($alertas)) {
                $fecha_inicio = date('Y-m-01');
                $fecha_fin = $fecha_hoy;
            }
        }

        // 2. Lógica de Paginación
        $pagina_actual = $_GET['page'] ?? 1;
        $pagina_actual = filter_var($pagina_actual, FILTER_VALIDATE_INT);
        if (!$pagina_actual || $pagina_actual < 1) {
            $pagina_actual = 1;
        }

        $registros_por_pagina = 10;
        $offset = ($pagina_actual - 1) * $registros_por_pagina;

        // 3. Consultas a la BD
        $total_registros = Atencion::contarAtenciones($id_usuario, $fecha_inicio, $fecha_fin);
        $total_paginas = ceil($total_registros / $registros_por_pagina);

        $atenciones = Atencion::paginarAtenciones($id_usuario, $registros_por_pagina, $offset, $fecha_inicio, $fecha_fin);

        // 4. Renderizar la vista
        $router->render('bitacora/mis-atenciones', [
            'alertas' => $alertas, // <--- Pasamos las alertas a la vista
            'atenciones' => $atenciones,
            'total_paginas' => $total_paginas,
            'pagina_actual' => $pagina_actual,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'total_registros' => $total_registros
        ], 'layout_app');
    }

    public static function reportes(Router $router)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // SEGURIDAD: Solo Administrador (1) y Supervisor (2)
        if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], ['1', '2'])) {
            header('Location: /mis-atenciones'); // Si no tiene permiso, lo pateamos
            exit;
        }

        $alertas = [];
        $fecha_hoy = date('Y-m-d');
        // --- ESTADÍSTICAS FLEXIBLES ---
        $stat_tipo = $_GET['stat_tipo'] ?? 'mes';
        $stat_valor = $_GET['stat_valor'] ?? '';

        // Si no han elegido nada, calculamos el periodo actual por defecto
        if (empty($stat_valor)) {
            if ($stat_tipo === 'semana') {
                $stat_valor = date('Y-\WW'); // Ej: 2026-W41
            } else if ($stat_tipo === 'trimestre') {
                $stat_valor = date('Y') . '-' . ceil(date('n') / 3); // Ej: 2026-4
            } else {
                $stat_valor = date('Y-m'); // Ej: 2026-10
            }
        }

        $estadisticas = Atencion::obtenerEstadisticas($stat_tipo, $stat_valor);

        // --- MANEJO DE FECHAS (Igual a tus atenciones) ---
        if (!isset($_GET['inicio']) && !isset($_GET['fin'])) {
            $_GET['inicio'] = date('Y-m-01');
            $_GET['fin'] = $fecha_hoy;
        } else {
            if ($_GET['inicio'] && $_GET['fin'] && $_GET['inicio'] > $_GET['fin']) {
                $alertas['error'][] = 'La fecha de inicio no puede ser mayor a la fecha final.';
            }
            if ($_GET['fin'] > $fecha_hoy) {
                $alertas['error'][] = 'La fecha final no puede ser mayor al día de hoy.';
            }
            if (!empty($alertas)) {
                $_GET['inicio'] = date('Y-m-01');
                $_GET['fin'] = $fecha_hoy;
            }
        }

        // Armamos el arreglo de filtros basándonos en la URL limpia
        $filtros = [
            'inicio' => $_GET['inicio'] ?? '',
            'fin' => $_GET['fin'] ?? '',
            'id_tipo_atencion' => $_GET['id_tipo_atencion'] ?? '',
            'id_motivo_atencion' => $_GET['id_motivo_atencion'] ?? '',
            'id_medio_atencion' => $_GET['id_medio_atencion'] ?? '',
            'id_escuela' => $_GET['id_escuela'] ?? '',
            'id_usuario' => $_GET['id_usuario'] ?? ''
        ];

        // --- PAGINACIÓN ---
        $pagina_actual = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT) ?: 1;
        $registros_por_pagina = 10;
        $offset = ($pagina_actual - 1) * $registros_por_pagina;

        // --- CONSULTAS ---
        $total_registros = Atencion::contarReportes($filtros);
        $total_paginas = ceil($total_registros / $registros_por_pagina);
        $atenciones = Atencion::paginarReportes($registros_por_pagina, $offset, $filtros);

        // --- OBTENER CATÁLOGOS PARA LOS SELECTS ---
        $escuelas = CatEscuela::all();
        $tipos_atencion = CatTipoAtencion::all();
        $motivos = CatMotivoAtencion::all();
        $medios = CatMedioAtencion::all();
        $usuarios = Usuario::all(); // Traemos a todos los usuarios registrados

        $router->render('bitacora/reportes', [
            'alertas' => $alertas,
            'atenciones' => $atenciones,
            'total_paginas' => $total_paginas,
            'pagina_actual' => $pagina_actual,
            'filtros' => $filtros,
            'total_registros' => $total_registros,
            'stat_tipo' => $stat_tipo,
            'stat_valor' => $stat_valor,
            'estadisticas' => $estadisticas,
            
            // Catálogos
            'escuelas' => $escuelas,
            'tipos_atencion' => $tipos_atencion,
            'motivos' => $motivos,
            'medios' => $medios,
            'usuarios' => $usuarios
        ], 'layout_app');
    }

    public static function exportarCsv()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // SEGURIDAD: Solo Administrador y Supervisor
        if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], ['1', '2'])) {
            header('Location: /mis-atenciones');
            exit;
        }

        // Recibir fechas por POST
        $fecha_inicio = $_POST['inicio_csv'] ?? date('Y-m-01');
        $fecha_fin = $_POST['fin_csv'] ?? date('Y-m-d');

        // Validar que la fecha final no sea menor a la de inicio
        if ($fecha_inicio > $fecha_fin) {
            $fecha_inicio = date('Y-m-01');
            $fecha_fin = date('Y-m-d');
        }

        // Traer datos
        $atenciones = Atencion::obtenerParaCsv($fecha_inicio, $fecha_fin);

        // Configuramos las cabeceras HTTP para forzar la descarga del archivo
        $nombre_archivo = 'reporte_atenciones_' . $fecha_inicio . '_al_' . $fecha_fin . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $nombre_archivo);

        // Abrir la salida estándar de PHP
        $salida = fopen('php://output', 'w');

        // Truco: Imprimir BOM para que Excel respete el UTF-8 (acentos y ñ)
        fprintf($salida, chr(0xEF).chr(0xBB).chr(0xBF));

        // 1. Escribir los encabezados de las columnas
        fputcsv($salida, ['Fecha', 'Solicitante', 'Correo', 'Perfil', 'Escuela', 'Motivo', 'Medio', 'Atendido por']);

        // 2. Escribir fila por fila
        foreach ($atenciones as $registro) {
            fputcsv($salida, [
                date('d/m/Y H:i', strtotime($registro->fecha_atencion)),
                $registro->nombre_completo,
                $registro->correo,
                $registro->tipo_nombre,
                $registro->escuela_nombre,
                $registro->motivo_nombre,
                $registro->medio_nombre,
                $registro->usuario_nombre ?? 'N/A'
            ]);
        }

        fclose($salida);
        exit; // Detenemos la ejecución para que no renderice HTML al final del archivo
    }

    
}
