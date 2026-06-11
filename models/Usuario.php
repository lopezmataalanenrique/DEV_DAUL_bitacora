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
}
