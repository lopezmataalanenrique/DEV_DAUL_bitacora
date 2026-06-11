<?php

namespace Controllers;

use MVC\Router;
use Model\Usuario;

class LoginController{
    public static function login(Router $router) {

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);

            //Validar login
            $alertas = $auth->validarLogin();

            if(empty($alertas)){
                // Comprobar que exista el usuario
                $user = Usuario::where('email',$auth->email);

                if($user) {
                    // Verificar el password
                    if($user->comprobarPasswordAndStatus($auth->password)){
                        session_start();
                        $_SESSION['id'] = $user->id;
                        $_SESSION['name'] = $user->name;
                        $_SESSION['email'] = $user->email;
                        $_SESSION['rol'] = $user->rol;
                        $_SESSION['login'] = true;

                        // Redireccionar según el rol
                        if($user->rol === '1') {
                            header('Location: /crear-usuario');
                        } else {
                            header('Location: /crear-atencion');
                        }
                    }
                } else {
                    Usuario::setAlerta('error', 'Usuario no encontrado');
                }
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/login', [
            'alertas' => $alertas
        ]);
    }

    public static function logout() {
        session_start();
        $_SESSION = [];
        header('Location: /');
    }
}