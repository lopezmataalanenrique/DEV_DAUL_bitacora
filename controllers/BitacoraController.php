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
        // 1. Asegurarnos de que la sesión esté iniciada para leer el id del analista
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
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
}
