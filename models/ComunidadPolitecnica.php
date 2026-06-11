<?php

namespace Model;

class ComunidadPolitecnica extends ActiveRecord {
    protected static $tabla = 'comunidad_politecnica';
    protected static $columnasDB = ['id', 'correo', 'nombre_completo', 'id_escuela'];

    public $id;
    public $correo;
    public $nombre_completo;
    public $id_escuela;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->correo = $args['correo'] ?? '';
        $this->nombre_completo = $args['nombre_completo'] ?? '';
        $this->id_escuela = $args['id_escuela'] ?? '';
    }
}