# Bitácora DEV — Registro de atenciones

Aplicación web en PHP para registrar y consultar las atenciones brindadas a la comunidad politécnica, aspirantes y público en general. Permite a administradores, supervisores y analistas gestionar usuarios, capturar atenciones y generar reportes con filtros.

## Tecnologías

- **PHP** con arquitectura **MVC** propia (`Router`, controladores, modelos y vistas).
- **MySQL** mediante `mysqli`, con un patrón **ActiveRecord** (`models/ActiveRecord.php`).
- **Composer** para dependencias de PHP y autocarga PSR-4 (`vlucas/phpdotenv` para variables de entorno).
- **Node.js + Gulp** para compilar SCSS (Sass), minificar JavaScript (Terser) y optimizar imágenes (Sharp).
- **Bootstrap** y la fuente **Inter** (Google Fonts) cargados desde CDN en los layouts.

## Estructura del proyecto

```
.
├── Router.php              # Enrutador: registra rutas GET/POST y renderiza vistas
├── controllers/            # Lógica de la aplicación
│   ├── LoginController.php     # Inicio y cierre de sesión
│   └── BitacoraController.php  # Usuarios, atenciones, búsqueda y reportes
├── models/                 # Modelos ActiveRecord
│   ├── ActiveRecord.php        # Clase base (CRUD, sanitización, alertas)
│   ├── Usuario.php             # Tabla `usuarios`
│   ├── Atencion.php            # Tabla `atenciones` (paginación y filtros)
│   ├── ComunidadPolitecnica.php# Tabla `comunidad_politecnica`
│   └── Cat*.php                # Catálogos: rol, escuela, medio, motivo y tipo de atención
├── views/                  # Plantillas
│   ├── layout.php              # Layout para la pantalla de inicio de sesión
│   ├── layout_app.php          # Layout de la aplicación (barra de navegación)
│   ├── auth/                   # Vista de inicio de sesión
│   ├── admin/                  # Alta de usuarios
│   ├── bitacora/               # Crear atención, mis atenciones y reportes
│   └── templates/              # Fragmentos reutilizables (alertas)
├── includes/
│   ├── app.php                 # Arranque: autoload, .env, funciones y conexión a BD
│   ├── database.php            # Conexión a MySQL con variables de entorno
│   └── funciones.php           # Utilidades (`debuguear`, `s` para escapar HTML)
├── public/                 # Raíz del servidor web
│   ├── index.php               # Punto de entrada y definición de rutas
│   └── build/                  # CSS, JS e imágenes compilados por Gulp
├── src/                    # Fuentes de estilos, JS e imágenes
│   ├── scss/
│   ├── js/
│   └── img/
└── gulpfile.js             # Tareas de compilación
```

## Requisitos

- PHP 8 o superior con la extensión `mysqli`.
- MySQL o MariaDB.
- Composer.
- Node.js y npm (solo para compilar los recursos de `src/`).

## Instalación

1. Clonar el repositorio:

   ```bash
   git clone https://github.com/lopezmataalanenrique/DEV_DAUL_bitacora.git
   cd DEV_DAUL_bitacora
   ```

2. Instalar las dependencias de PHP:

   ```bash
   composer install
   ```

3. Instalar las dependencias de Node.js:

   ```bash
   npm install
   ```

4. Crear el archivo `includes/.env` (está excluido en `.gitignore`) con los datos de conexión a la base de datos:

   ```env
   DB_HOST=localhost
   DB_USER=usuario
   DB_PASS=contraseña
   DB_NAME=nombre_de_la_base
   ```

5. Crear la base de datos con las tablas que usan los modelos: `usuarios`, `atenciones`, `comunidad_politecnica`, `cat_rol`, `cat_escuela`, `cat_medio_atencion`, `cat_motivo_atencion` y `cat_tipo_atencion`.

## Ejecución

Levantar el servidor integrado de PHP usando `public/` como raíz:

```bash
php -S localhost:3000 -t public
```

Después abrir `http://localhost:3000` en el navegador.

Para compilar los recursos y vigilar cambios en `src/` durante el desarrollo:

```bash
npm run dev
```

Esta tarea ejecuta en serie `js`, `css` e `imagenes`, y después se queda observando los archivos SCSS, JS e imágenes. Los resultados se guardan en `public/build/`.

## Rutas

| Método     | Ruta                     | Acción                                   | Descripción |
|------------|--------------------------|------------------------------------------|-------------|
| GET, POST  | `/`                      | `LoginController::login`                 | Inicio de sesión |
| GET        | `/logout`                | `LoginController::logout`                | Cierre de sesión |
| GET, POST  | `/crear-usuario`         | `BitacoraController::crearUsuario`       | Alta de usuarios (administrador, supervisor o analista) |
| GET, POST  | `/crear-atencion`        | `BitacoraController::crearAtencion`      | Registro de una nueva atención |
| POST       | `/api/buscar-comunidad`  | `BitacoraController::buscarComunidad`    | API JSON para buscar un miembro de la comunidad politécnica por correo |
| GET        | `/mis-atenciones`        | `BitacoraController::misAtenciones`      | Atenciones del usuario en sesión, con filtro por fechas y paginación |
| GET        | `/reportes`              | `BitacoraController::reportes`           | Reportes con filtros avanzados (solo administrador y supervisor) |

## Roles

Los roles se guardan en la tabla `cat_rol` y en la sesión (`$_SESSION['rol']`):

- **1 — Administrador**: al iniciar sesión se redirige a `/crear-usuario`; puede acceder a reportes.
- **2 — Supervisor**: puede acceder a reportes.
- **3 — Analista**: rol por defecto de los nuevos usuarios; registra y consulta sus propias atenciones.

## Funcionamiento general

1. `public/index.php` carga `includes/app.php`, registra las rutas en el `Router` y llama a `comprobarRutas()`.
2. El `Router` inicia la sesión, busca la función asociada a la URL y al método HTTP, y la ejecuta.
3. Los controladores usan los modelos para consultar o guardar información y llaman a `$router->render()` para mostrar una vista dentro de un layout.
4. Los modelos heredan de `ActiveRecord`, que ofrece métodos como `all`, `find`, `where`, `guardar`, `crear`, `actualizar` y `eliminar`, además del manejo de alertas.

## Autor

Alan Enrique López Mata — alopezm1612@alumno.ipn.mx
