<?php
namespace Model;

class CatMedioAtencion extends ActiveRecord {
    protected static $tabla = 'cat_medio_atencion';
    protected static $columnasDB = ['id', 'nombre'];

    public $id;
    public $nombre;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
    }
}