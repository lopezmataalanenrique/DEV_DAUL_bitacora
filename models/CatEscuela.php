<?php
namespace Model;

class CatEscuela extends ActiveRecord {
    protected static $tabla = 'cat_escuela';
    protected static $columnasDB = ['id', 'nombre'];

    public $id;
    public $nombre;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
    }
}