<div class="body-login">
    <main class="new-user-main">
        <h1>Registrar nueva atención</h1>
        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <form method="POST" action="/crear-atencion" id="form-atencion">

            <div class="form-group">
                <fieldset>
                    <legend>Selecciona tu perfil:</legend>
                    <?php foreach ($tipos_atencion as $tipo): ?>
                        <div>
                            <input
                                class="radio-perfil"
                                type="radio"
                                id="perfil_<?php echo $tipo->id; ?>"
                                name="id_tipo_atencion"
                                value="<?php echo $tipo->id; ?>"
                                <?php echo ($atencion->id_tipo_atencion == $tipo->id || (empty($atencion->id_tipo_atencion) && $tipo->id == 5)) ? 'checked' : ''; ?> />
                            <label for="perfil_<?php echo $tipo->id; ?>"><?php echo $tipo->nombre; ?></label>
                        </div>
                    <?php endforeach; ?>
                </fieldset>
            </div>

            <div class="form-group">
                <label for="correo">Correo electrónico</label>

                <input
                    type="email"
                    id="correo"
                    name="correo"
                    placeholder="Ej. juan.perez@gmail.com"
                    value="<?php echo $atencion->correo ?? ''; ?>"
                    required>
                <button type="button" id="btn-consultar" class="enviar-btn">
                    Consultar
                </button>
                <div id="alerta-busqueda" style="margin-top: 1rem;"></div>
            </div>

            <div class="form-group">
                <label for="nombre">Nombre completo</label>
                <input
                    type="text"
                    id="nombre"
                    name="nombre_completo"
                    placeholder="Ej. Juan Pérez García"
                    value="<?php echo $atencion->nombre_completo ?? ''; ?>"
                    required>
            </div>

            <div class="form-group" id="grupo-escuela">
                <label for="escuela">Escuela / Centro de Estudios</label>
                <select id="escuela" name="id_escuela" required>
                    <?php foreach ($escuelas as $escuela): ?>
                        <option
                            value="<?php echo $escuela->id; ?>"
                            <?php echo ($atencion->id_escuela == $escuela->id) ? 'selected' : ''; ?>>
                            <?php echo $escuela->nombre; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="solicitud">Tipo de solicitud (Motivo)</label>
                <select id="solicitud" name="id_motivo_atencion" required>
                    <option value="" disabled <?php echo empty($atencion->id_motivo_atencion) ? 'selected' : ''; ?>>
                        Selecciona una opción...
                    </option>
                    <?php foreach ($motivos as $motivo): ?>
                        <option
                            value="<?php echo $motivo->id; ?>"
                            <?php echo ($atencion->id_motivo_atencion == $motivo->id) ? 'selected' : ''; ?>>
                            <?php echo $motivo->nombre; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="medio">Medio de contacto</label>
                <select id="medio" name="id_medio_atencion" required>
                    <option value="" disabled <?php echo empty($atencion->id_medio_atencion) ? 'selected' : ''; ?>>
                        Selecciona una opción...
                    </option>
                    <?php foreach ($medios as $medio): ?>
                        <option
                            value="<?php echo $medio->id; ?>"
                            <?php echo ($atencion->id_medio_atencion == $medio->id) ? 'selected' : ''; ?>>
                            <?php echo $medio->nombre; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Fecha de atención</label>
                <?php
                $fechaActual = date('d/m/Y');
                echo "<p class='fecha-atencion'>$fechaActual</p>";
                ?>
            </div>

            <input class="enviar-btn" type="submit" value="Registrar atención">
        </form>
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const radiosPerfil = document.querySelectorAll('.radio-perfil');
        const selectEscuela = document.querySelector('#escuela');
        const btnConsultar = document.querySelector('#btn-consultar');
        const formAtencion = document.querySelector('#form-atencion');
        const inputCorreo = document.querySelector('#correo');
        const inputNombre = document.querySelector('#nombre');
        
        // Atrapamos el nuevo contenedor
        const divAlertaBusqueda = document.querySelector('#alerta-busqueda');

        // -- Función simplificada para alertas --
        function mostrarAlerta(mensaje, tipo) {
            // Le asignamos tus clases CSS de golpe (alerta + exito o error)
            divAlertaBusqueda.className = `alerta ${tipo}`;
            divAlertaBusqueda.textContent = mensaje;

            // Lo limpiamos después de 4 segundos
            setTimeout(() => {
                divAlertaBusqueda.className = '';
                divAlertaBusqueda.textContent = '';
            }, 4000);
        }

        // -- 1. Lógica para ocultar/mostrar escuela según el perfil --
        function evaluarPerfil() {
            const perfilSeleccionado = document.querySelector('.radio-perfil:checked');
            if (perfilSeleccionado) {
                const idPerfil = parseInt(perfilSeleccionado.value);

                if (idPerfil === 4 || idPerfil === 5) { // Aspirante o Público
                    selectEscuela.value = "1";
                    selectEscuela.setAttribute('disabled', 'disabled');
                    btnConsultar.style.display = 'none';
                } else { // Alumno, Asesor, Coordinador
                    selectEscuela.removeAttribute('disabled');
                    btnConsultar.style.display = 'block';
                }
            }
        }

        evaluarPerfil();

        radiosPerfil.forEach(radio => {
            radio.addEventListener('change', evaluarPerfil);
        });

        if (formAtencion) {
            formAtencion.addEventListener('submit', function() {
                selectEscuela.removeAttribute('disabled');
            });
        }

        // -- 2. Lógica del botón Consultar (Fetch API) --
        if (btnConsultar) {
            btnConsultar.addEventListener('click', async function() {
                const correoValue = inputCorreo.value.trim();

                if (!correoValue) {
                    mostrarAlerta('Por favor, ingresa un correo electrónico antes de consultar.', 'error');
                    return;
                }

                try {
                    // Hacemos la petición silenciosa al backend
                    const respuesta = await fetch('/api/buscar-comunidad', {
                        method: 'POST',
                        body: JSON.stringify({
                            correo: correoValue
                        }),
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    });

                    const resultado = await respuesta.json();

                    if (resultado.encontrado) {
                        // Rellenamos los campos
                        inputNombre.value = resultado.persona.nombre_completo;
                        selectEscuela.value = resultado.persona.id_escuela;

                        // Alerta de éxito en el HTML
                        mostrarAlerta('Usuario encontrado. Datos cargados.', 'exito');
                    } else {
                        // Limpiamos los campos
                        inputNombre.value = '';
                        selectEscuela.value = '';
                        
                        // Alerta de error en el HTML
                        mostrarAlerta('Usuario no encontrado. Por favor llena sus datos.', 'error');
                    }

                } catch (error) {
                    console.error('Error al consultar:', error);
                    mostrarAlerta('Hubo un error al conectar con el servidor.', 'error');
                }
            });
        }
    });

    // -- Desaparecer las alertas de PHP automáticamente --
        const alertasPHP = document.querySelectorAll('.alerta');
        alertasPHP.forEach(alerta => {
            setTimeout(() => {
                alerta.remove();
            }, 4000); // 4000 milisegundos = 4 segundos
        });
</script>