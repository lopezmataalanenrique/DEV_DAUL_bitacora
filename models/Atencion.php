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

    // Propiedades virtuales para los JOINS
    public $escuela_nombre;
    public $motivo_nombre;
    public $medio_nombre;
    public $tipo_nombre;

    // Propiedad virtual para el nombre del usuario (no se almacena en la base de datos)
    public $usuario_nombre;

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

        $this->escuela_nombre = $args['escuela_nombre'] ?? '';
        $this->motivo_nombre = $args['motivo_nombre'] ?? '';
        $this->medio_nombre = $args['medio_nombre'] ?? '';
        $this->tipo_nombre = $args['tipo_nombre'] ?? '';

        // Inicializar la propiedad virtual para el nombre del usuario
        $this->usuario_nombre = $args['usuario_nombre'] ?? '';
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

    // Método para paginar con filtros de rango de fechas
    public static function paginarAtenciones($id_usuario, $por_pagina, $offset, $fecha_inicio = '', $fecha_fin = '')
    {
        $query = "SELECT a.*, 
                  e.nombre as escuela_nombre, 
                  m.nombre as motivo_nombre, 
                  med.nombre as medio_nombre, 
                  t.nombre as tipo_nombre 
                  FROM atenciones a 
                  LEFT JOIN cat_escuela e ON a.id_escuela = e.id 
                  LEFT JOIN cat_motivo_atencion m ON a.id_motivo_atencion = m.id 
                  LEFT JOIN cat_medio_atencion med ON a.id_medio_atencion = med.id 
                  LEFT JOIN cat_tipo_atencion t ON a.id_tipo_atencion = t.id 
                  WHERE a.id_usuario = " . self::$db->escape_string($id_usuario);

        // Lógica inteligente para el rango de fechas
        if ($fecha_inicio && $fecha_fin) {
            $query .= " AND DATE(a.fecha_atencion) BETWEEN '" . self::$db->escape_string($fecha_inicio) . "' AND '" . self::$db->escape_string($fecha_fin) . "'";
        } else if ($fecha_inicio) {
            $query .= " AND DATE(a.fecha_atencion) >= '" . self::$db->escape_string($fecha_inicio) . "'";
        } else if ($fecha_fin) {
            $query .= " AND DATE(a.fecha_atencion) <= '" . self::$db->escape_string($fecha_fin) . "'";
        }

        $query .= " ORDER BY a.fecha_atencion DESC LIMIT {$por_pagina} OFFSET {$offset}";

        return self::consultarSQL($query);
    }

    // Método para contar (mismas reglas de fechas)
    public static function contarAtenciones($id_usuario, $fecha_inicio = '', $fecha_fin = '')
    {
        $query = "SELECT COUNT(*) as total FROM atenciones WHERE id_usuario = " . self::$db->escape_string($id_usuario);

        if ($fecha_inicio && $fecha_fin) {
            $query .= " AND DATE(fecha_atencion) BETWEEN '" . self::$db->escape_string($fecha_inicio) . "' AND '" . self::$db->escape_string($fecha_fin) . "'";
        } else if ($fecha_inicio) {
            $query .= " AND DATE(fecha_atencion) >= '" . self::$db->escape_string($fecha_inicio) . "'";
        } else if ($fecha_fin) {
            $query .= " AND DATE(fecha_atencion) <= '" . self::$db->escape_string($fecha_fin) . "'";
        }

        $resultado = self::$db->query($query);
        $fila = $resultado->fetch_assoc();
        return $fila['total'];
    }

    public static function paginarReportes($por_pagina, $offset, $filtros = []) {
        $query = "SELECT a.*, 
                  e.nombre as escuela_nombre, 
                  m.nombre as motivo_nombre, 
                  med.nombre as medio_nombre, 
                  t.nombre as tipo_nombre,
                  u.name as usuario_nombre 
                  FROM atenciones a 
                  LEFT JOIN cat_escuela e ON a.id_escuela = e.id 
                  LEFT JOIN cat_motivo_atencion m ON a.id_motivo_atencion = m.id 
                  LEFT JOIN cat_medio_atencion med ON a.id_medio_atencion = med.id 
                  LEFT JOIN cat_tipo_atencion t ON a.id_tipo_atencion = t.id 
                  LEFT JOIN usuarios u ON a.id_usuario = u.id 
                  WHERE 1=1"; // WHERE 1=1 es un truco para concatenar los AND fácilmente

        // Fechas
        if(!empty($filtros['inicio']) && !empty($filtros['fin'])) {
            $query .= " AND DATE(a.fecha_atencion) BETWEEN '" . self::$db->escape_string($filtros['inicio']) . "' AND '" . self::$db->escape_string($filtros['fin']) . "'";
        }
        // Catálogos
        if(!empty($filtros['id_tipo_atencion'])) $query .= " AND a.id_tipo_atencion = '" . self::$db->escape_string($filtros['id_tipo_atencion']) . "'";
        if(!empty($filtros['id_motivo_atencion'])) $query .= " AND a.id_motivo_atencion = '" . self::$db->escape_string($filtros['id_motivo_atencion']) . "'";
        if(!empty($filtros['id_medio_atencion'])) $query .= " AND a.id_medio_atencion = '" . self::$db->escape_string($filtros['id_medio_atencion']) . "'";
        if(!empty($filtros['id_escuela'])) $query .= " AND a.id_escuela = '" . self::$db->escape_string($filtros['id_escuela']) . "'";
        if(!empty($filtros['id_usuario'])) $query .= " AND a.id_usuario = '" . self::$db->escape_string($filtros['id_usuario']) . "'";

        $query .= " ORDER BY a.fecha_atencion DESC LIMIT {$por_pagina} OFFSET {$offset}";
        return self::consultarSQL($query);
    }

    public static function contarReportes($filtros = []) {
        $query = "SELECT COUNT(*) as total FROM atenciones a WHERE 1=1";
        
        if(!empty($filtros['inicio']) && !empty($filtros['fin'])) {
            $query .= " AND DATE(a.fecha_atencion) BETWEEN '" . self::$db->escape_string($filtros['inicio']) . "' AND '" . self::$db->escape_string($filtros['fin']) . "'";
        }
        if(!empty($filtros['id_tipo_atencion'])) $query .= " AND a.id_tipo_atencion = '" . self::$db->escape_string($filtros['id_tipo_atencion']) . "'";
        if(!empty($filtros['id_motivo_atencion'])) $query .= " AND a.id_motivo_atencion = '" . self::$db->escape_string($filtros['id_motivo_atencion']) . "'";
        if(!empty($filtros['id_medio_atencion'])) $query .= " AND a.id_medio_atencion = '" . self::$db->escape_string($filtros['id_medio_atencion']) . "'";
        if(!empty($filtros['id_escuela'])) $query .= " AND a.id_escuela = '" . self::$db->escape_string($filtros['id_escuela']) . "'";
        if(!empty($filtros['id_usuario'])) $query .= " AND a.id_usuario = '" . self::$db->escape_string($filtros['id_usuario']) . "'";

        $resultado = self::$db->query($query);
        $fila = $resultado->fetch_assoc();
        return $fila['total'];
    }

    public static function obtenerParaCsv($fecha_inicio, $fecha_fin) {
        $query = "SELECT a.*, 
                  e.nombre as escuela_nombre, 
                  m.nombre as motivo_nombre, 
                  med.nombre as medio_nombre, 
                  t.nombre as tipo_nombre,
                  u.name as usuario_nombre 
                  FROM atenciones a 
                  LEFT JOIN cat_escuela e ON a.id_escuela = e.id 
                  LEFT JOIN cat_motivo_atencion m ON a.id_motivo_atencion = m.id 
                  LEFT JOIN cat_medio_atencion med ON a.id_medio_atencion = med.id 
                  LEFT JOIN cat_tipo_atencion t ON a.id_tipo_atencion = t.id 
                  LEFT JOIN usuarios u ON a.id_usuario = u.id 
                  WHERE 1=1";

        if ($fecha_inicio && $fecha_fin) {
            $query .= " AND DATE(a.fecha_atencion) BETWEEN '" . self::$db->escape_string($fecha_inicio) . "' AND '" . self::$db->escape_string($fecha_fin) . "'";
        }

        $query .= " ORDER BY a.fecha_atencion DESC";
        
        return self::consultarSQL($query);
    }

    public static function obtenerEstadisticas($tipo = 'mes', $valor = '') {
        $stats = [
            'total' => 0,
            'por_usuario' => [],
            'por_motivo' => [],
            'por_medio' => []
        ];

        $where = "";

        if ($tipo === 'semana') {
            // Formato que envía HTML5: "2026-W41"
            if ($valor && preg_match('/^(\d{4})-W(\d{2})$/', $valor, $matches)) {
                $where = "YEARWEEK(fecha_atencion, 1) = '" . $matches[1] . $matches[2] . "'";
            } else {
                $where = "YEARWEEK(fecha_atencion, 1) = YEARWEEK(CURDATE(), 1)";
            }
        } else if ($tipo === 'trimestre') {
            // Formato que crearemos: "2026-3" (Año-Trimestre)
            if ($valor && preg_match('/^(\d{4})-(\d)$/', $valor, $matches)) {
                $where = "YEAR(fecha_atencion) = '" . $matches[1] . "' AND QUARTER(fecha_atencion) = '" . $matches[2] . "'";
            } else {
                $where = "YEAR(fecha_atencion) = YEAR(CURDATE()) AND QUARTER(fecha_atencion) = QUARTER(CURDATE())";
            }
        } else {
            // Mes (por defecto). Formato HTML5: "2026-10"
            if ($valor && preg_match('/^(\d{4})-(\d{2})$/', $valor, $matches)) {
                $where = "YEAR(fecha_atencion) = '" . $matches[1] . "' AND MONTH(fecha_atencion) = '" . $matches[2] . "'";
            } else {
                $where = "YEAR(fecha_atencion) = YEAR(CURDATE()) AND MONTH(fecha_atencion) = MONTH(CURDATE())";
            }
        }

        // Consultas
        $res = self::$db->query("SELECT COUNT(*) as total FROM atenciones WHERE $where");
        if($res) $stats['total'] = $res->fetch_assoc()['total'];

        $res = self::$db->query("SELECT u.name as etiqueta, COUNT(a.id) as total FROM atenciones a INNER JOIN usuarios u ON a.id_usuario = u.id WHERE $where GROUP BY u.id ORDER BY total DESC");
        if($res) while($row = $res->fetch_assoc()) $stats['por_usuario'][] = $row;

        $res = self::$db->query("SELECT m.nombre as etiqueta, COUNT(a.id) as total FROM atenciones a INNER JOIN cat_motivo_atencion m ON a.id_motivo_atencion = m.id WHERE $where GROUP BY m.id ORDER BY total DESC");
        if($res) while($row = $res->fetch_assoc()) $stats['por_motivo'][] = $row;

        $res = self::$db->query("SELECT m.nombre as etiqueta, COUNT(a.id) as total FROM atenciones a INNER JOIN cat_medio_atencion m ON a.id_medio_atencion = m.id WHERE $where GROUP BY m.id ORDER BY total DESC");
        if($res) while($row = $res->fetch_assoc()) $stats['por_medio'][] = $row;

        return $stats;
    }
}
