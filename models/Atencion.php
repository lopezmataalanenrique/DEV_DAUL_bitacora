<?php

namespace Model;

class Atencion extends ActiveRecord
{
    // Base de datos (Solo las columnas que realmente existen en la tabla atenciones)
    protected static $tabla = 'atenciones';
    protected static $columnasDB = [
        'id', 
        'id_tipo_atencion', 
        'id_motivo_atencion', 
        'id_medio_atencion', 
        'id_usuario', 
        'id_escuela', 
        'fecha_atencion',
        'nombre_completo',
        'correo',          
    ];

    public $id;
    public $id_tipo_atencion;
    public $id_motivo_atencion;
    public $id_medio_atencion;
    public $id_usuario;
    public $id_escuela;
    public $fecha_atencion;
    public $correo;
    public $nombre_completo;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null; 
        
        // IDs de los catálogos
        $this->id_tipo_atencion = $args['id_tipo_atencion'] ?? '';
        $this->id_motivo_atencion = $args['id_motivo_atencion'] ?? '';
        $this->id_medio_atencion = $args['id_medio_atencion'] ?? '';
        $this->id_escuela = $args['id_escuela'] ?? '';
        
        // Llave foránea del usuario
        $this->id_usuario = $args['id_usuario'] ?? '';
        
        $this->fecha_atencion = $args['fecha_atencion'] ?? date('Y-m-d H:i:s');

        // Datos virtuales para mantenerlos en el formulario si hay error
        $this->correo = $args['correo'] ?? '';
        $this->nombre_completo = $args['nombre_completo'] ?? '';
    }

    // Validación actualizada
    public function validarAtencion()
    {
        if (!$this->id_tipo_atencion) {
            self::$alertas['error'][] = 'El tipo de perfil es obligatorio';
        }
        if (!$this->correo) {
            self::$alertas['error'][] = 'El correo electrónico es obligatorio';
        }
        if (!$this->nombre_completo) {
            self::$alertas['error'][] = 'El nombre es obligatorio';
        }
        if (!$this->id_escuela) {
            self::$alertas['error'][] = 'La escuela es obligatoria';
        }
        if (!$this->id_motivo_atencion) {
            self::$alertas['error'][] = 'El motivo de solicitud es obligatorio';
        }
        if (!$this->id_medio_atencion) {
            self::$alertas['error'][] = 'El medio de contacto es obligatorio';
        }

        return self::$alertas;
    }
}