<?php

namespace Model;

class Usuario extends ActiveRecord
{
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'email', 'password', 'created_date', 'rol', 'status', 'name'];

    public $id;
    public $name;
    public $email;
    public $password;
    public $confirm_password;
    public $created_date;
    public $rol;
    public $status;
    public $rol_nombre; 

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->confirm_password = $args['confirm_password'] ?? '';
        $this->created_date = date('Y-m-d H:i:s');
        $this->rol = $args['rol'] ?? 3; // 3 es el rol por defecto para los analistas
        $this->status = $args['status'] ?? 1; // 1 es el estado por defecto para usuarios activos
        $this->name = $args['name'] ?? '';
        $this->rol_nombre = $args['rol_nombre'] ?? '';
    }

    public function validarLogin()
    {

        if (!$this->email) {
            self::$alertas['error'][] = 'El correo es obligatorio';
        }

        if (!$this->password) {
            self::$alertas['error'][] = 'La contraseña es obligatoria';
        }

        return self::$alertas;
    }

    public function validarNuevaCuenta()
    {

        if (!$this->name) {
            self::$alertas['error'][] = 'El nombre es obligatorio';
        }

        if (!$this->email) {
            self::$alertas['error'][] = 'El correo es obligatorio';
        }

        if (!$this->password) {
            self::$alertas['error'][] = 'La contraseña es obligatoria';
        } else if (strlen($this->password) < 8) {
            self::$alertas['error'][] = 'La contraseña debe tener al menos 8 caracteres';
        } else if (!preg_match('/[A-Z]/', $this->password)) {
            self::$alertas['error'][] = 'La contraseña debe contener al menos una letra mayúscula';
        } else if (!preg_match('/[a-z]/', $this->password)) {
            self::$alertas['error'][] = 'La contraseña debe contener al menos una letra minúscula';
        } else if (!preg_match('/[0-9]/', $this->password)) {
            self::$alertas['error'][] = 'La contraseña debe contener al menos un número';
        } else if (!preg_match('/[\W_]/', $this->password)) {
            self::$alertas['error'][] = 'La contraseña debe contener al menos un carácter especial';
        }

        if ($this->password !== $this->confirm_password) {
            self::$alertas['error'][] = 'Las contraseñas no coinciden';
        }

        return self::$alertas;
    }

    // Revisa que el usuario ya exista
    public function existeUsuario(){
        $query = " SELECT * FROM " . self::$tabla . " WHERE email = '" . $this->email . "' LIMIT 1";
        $result = self::$db->query($query);
        if($result->num_rows) {
            self::$alertas['error'][] = 'El usuario ya está registrado';
        }
        return $result;
    }

    public function hashPassword() {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    public function comprobarPasswordAndStatus($password) {
        $result = password_verify($password, $this->password);

        if(!$result || $this->status === 0) {
            self::$alertas['error'][] = 'Contraseña incorrecta o cuenta inactiva, por favor contacta al administrador';
            return;
        } else {
            return true;
        }

        
    }

    public static function paginarUsuarios($por_pagina, $offset, $filtros = []) {
        // Hacemos JOIN con cat_rol para traer el nombre del perfil
        $query = "SELECT u.*, r.nombre as rol_nombre 
                  FROM usuarios u 
                  LEFT JOIN cat_rol r ON u.rol = r.id 
                  WHERE 1=1";

        // Filtro por Estado (Activo/Inactivo)
        if (isset($filtros['status']) && $filtros['status'] !== '') {
            $query .= " AND u.status = '" . self::$db->escape_string($filtros['status']) . "'";
        }
        // Filtro por Rol
        if (!empty($filtros['rol'])) {
            $query .= " AND u.rol = '" . self::$db->escape_string($filtros['rol']) . "'";
        }
        // Filtro de Búsqueda (Nombre o Correo)
        if (!empty($filtros['busqueda'])) {
            $busqueda = self::$db->escape_string($filtros['busqueda']);
            $query .= " AND (u.name LIKE '%$busqueda%' OR u.email LIKE '%$busqueda%')";
        }

        $query .= " ORDER BY u.id DESC LIMIT {$por_pagina} OFFSET {$offset}";
        return self::consultarSQL($query);
    }

    public static function contarUsuarios($filtros = []) {
        $query = "SELECT COUNT(*) as total FROM usuarios u WHERE 1=1";
        
        if (isset($filtros['status']) && $filtros['status'] !== '') {
            $query .= " AND u.status = '" . self::$db->escape_string($filtros['status']) . "'";
        }
        if (!empty($filtros['rol'])) {
            $query .= " AND u.rol = '" . self::$db->escape_string($filtros['rol']) . "'";
        }
        if (!empty($filtros['busqueda'])) {
            $busqueda = self::$db->escape_string($filtros['busqueda']);
            $query .= " AND (u.name LIKE '%$busqueda%' OR u.email LIKE '%$busqueda%')";
        }

        $resultado = self::$db->query($query);
        return $resultado->fetch_assoc()['total'];
    }

    // Valida los datos específicamente para la pantalla de edición
    public function validarEdicion() {
        if(!$this->name) {
            self::$alertas['error'][] = 'El Nombre es Obligatorio';
        }
        if(!$this->email) {
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }
        
        // Si el administrador escribió algo en el campo de password, lo validamos
        if(!empty($this->password)) {
            if(strlen($this->password) < 8) {
                self::$alertas['error'][] = 'El Password debe contener al menos 8 caracteres';
            }
            if($this->password !== $this->confirm_password) {
                self::$alertas['error'][] = 'Los passwords no coinciden';
            }
        }
        return self::$alertas;
    }

    // Comprueba que si cambia su correo, no choque con el de OTRO usuario diferente
    public function comprobarEmailEdicion() {
        $query = "SELECT * FROM " . self::$tabla . " WHERE email = '" . self::$db->escape_string($this->email) . "' AND id != " . self::$db->escape_string($this->id) . " LIMIT 1";
        $resultado = self::$db->query($query);
        if($resultado->num_rows) {
            self::$alertas['error'][] = 'Este correo electrónico ya está en uso por otro usuario';
        }
        return $resultado;
    }
}
