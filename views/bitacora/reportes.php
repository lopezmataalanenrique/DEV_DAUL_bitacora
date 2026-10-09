<div class="body-login">
    <main class="new-user-main" style="max-width: 1200px;">
        <h1>Reporte General de Atenciones</h1>

        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        

        <!-- 1. FORMULARIO DE FILTROS AVANZADOS -->
        <form method="GET" action="/reportes" class="form-filtros" style="display: flex; gap: 15px; margin-bottom: 20px; align-items: flex-end; flex-wrap: wrap; background-color: #f8f9fa; padding: 20px; border-radius: 8px;">
            
            <div class="form-group" style="margin-bottom: 0; flex: 1 1 150px;">
                <label for="inicio">De:</label>
                <input type="date" id="inicio" name="inicio" value="<?php echo $filtros['inicio']; ?>" style="width: 100%;">
            </div>

            <div class="form-group" style="margin-bottom: 0; flex: 1 1 150px;">
                <label for="fin">Hasta:</label>
                <input type="date" id="fin" name="fin" value="<?php echo $filtros['fin']; ?>" style="width: 100%;">
            </div>

            <div class="form-group" style="margin-bottom: 0; flex: 1 1 200px;">
                <label for="id_tipo_atencion">Perfil</label>
                <select name="id_tipo_atencion" style="width: 100%;">
                    <option value="">-- Todos --</option>
                    <?php foreach($tipos_atencion as $tipo): ?>
                        <option value="<?php echo $tipo->id; ?>" <?php echo ($filtros['id_tipo_atencion'] == $tipo->id) ? 'selected' : ''; ?>><?php echo $tipo->nombre; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0; flex: 1 1 200px;">
                <label for="id_motivo_atencion">Motivo</label>
                <select name="id_motivo_atencion" style="width: 100%;">
                    <option value="">-- Todos --</option>
                    <?php foreach($motivos as $motivo): ?>
                        <option value="<?php echo $motivo->id; ?>" <?php echo ($filtros['id_motivo_atencion'] == $motivo->id) ? 'selected' : ''; ?>><?php echo $motivo->nombre; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0; flex: 1 1 200px;">
                <label for="id_medio_atencion">Medio</label>
                <select name="id_medio_atencion" style="width: 100%;">
                    <option value="">-- Todos --</option>
                    <?php foreach($medios as $medio): ?>
                        <option value="<?php echo $medio->id; ?>" <?php echo ($filtros['id_medio_atencion'] == $medio->id) ? 'selected' : ''; ?>><?php echo $medio->nombre; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0; flex: 1 1 200px;">
                <label for="id_escuela">Escuela</label>
                <select name="id_escuela" style="width: 100%;">
                    <option value="">-- Todas --</option>
                    <?php foreach($escuelas as $escuela): ?>
                        <option value="<?php echo $escuela->id; ?>" <?php echo ($filtros['id_escuela'] == $escuela->id) ? 'selected' : ''; ?>><?php echo $escuela->nombre; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0; flex: 1 1 200px;">
                <label for="id_usuario">Registrado por (Analista)</label>
                <select name="id_usuario" style="width: 100%;">
                    <option value="">-- Todos --</option>
                    <?php foreach($usuarios as $usuario): ?>
                        <option value="<?php echo $usuario->id; ?>" <?php echo ($filtros['id_usuario'] == $usuario->id) ? 'selected' : ''; ?>>
                            <?php echo $usuario->name; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="flex: 1 1 200px; display: flex; gap: 10px; flex-direction: column;">
                <input type="submit" class="enviar-btn" value="Filtrar" style="margin: 0; width: 100%;">
                <a href="/reportes" class="enviar-btn" style="margin: 0; background-color: #6c757d; text-align:center; text-decoration: none; width: 100%;">Limpiar Todo</a>
            </div>
        </form>

        <!-- 2. TOTAL ENCONTRADO -->
        <div style="background-color: #e2e3e5; border-left: 4px solid #6c757d; padding: 10px 15px; border-radius: 4px; margin-bottom: 20px; color: #41464b;">
            Se encontraron <strong><?php echo $total_registros; ?></strong> atención(es) con los filtros actuales.
        </div>

        <!-- 3. TABLA DE DATOS -->
        <div style="overflow-x: auto;">
            <table class="table table-striped table-hover" style="width: 100%; text-align: left; border-collapse: collapse; min-width: 900px;">
                <thead>
                    <tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px;">Fecha</th>
                        <th style="padding: 12px;">Solicitante</th>
                        <th style="padding: 12px;">Escuela</th>
                        <th style="padding: 12px;">Motivo / Medio</th>
                        <th style="padding: 12px; background-color: #e9ecef;">Atendido por</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($atenciones)): ?>
                        <tr><td colspan="5" style="text-align: center; padding: 20px;">No se encontraron registros.</td></tr>
                    <?php else: ?>
                        <?php foreach ($atenciones as $registro): ?>
                            <tr style="border-bottom: 1px solid #dee2e6;">
                                <td style="padding: 12px;"><?php echo date('d/m/Y H:i', strtotime($registro->fecha_atencion)); ?></td>
                                <td style="padding: 12px;">
                                    <strong><?php echo htmlspecialchars($registro->nombre_completo); ?></strong><br>
                                    <span style="color: #6c757d; font-size: 1.2em;"><?php echo htmlspecialchars($registro->tipo_nombre); ?></span>
                                </td>
                                <td style="padding: 12px;"><?php echo htmlspecialchars($registro->escuela_nombre); ?></td>
                                <td style="padding: 12px;">
                                    <?php echo htmlspecialchars($registro->motivo_nombre); ?><br>
                                    <span style="color: #6c757d; font-size: 1.2em;"><?php echo htmlspecialchars($registro->medio_nombre); ?></span>
                                </td>
                                <td style="padding: 12px; font-weight: bold; background-color: #f8f9fa;">
                                    <?php echo htmlspecialchars($registro->usuario_nombre ?? 'N/A'); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- 4. PAGINACIÓN INTELIGENTE -->
        <?php if ($total_paginas > 1): ?>
            <div class="paginacion" style="display: flex; justify-content: center; gap: 10px; margin-top: 20px;">
                <?php 
                    // Truco PHP: Copiamos todos los filtros de la URL actual para no perderlos al cambiar de página
                    $query_params = $_GET; 
                ?>
                
                <?php if ($pagina_actual > 1): ?>
                    <?php $query_params['page'] = $pagina_actual - 1; ?>
                    <a href="/reportes?<?php echo http_build_query($query_params); ?>" class="btn-secundario" style="padding: 5px 15px; text-decoration: none;">&laquo; Anterior</a>
                <?php endif; ?>

                <span style="padding: 5px 15px; font-weight: bold;">Página <?php echo $pagina_actual; ?> de <?php echo $total_paginas; ?></span>

                <?php if ($pagina_actual < $total_paginas): ?>
                    <?php $query_params['page'] = $pagina_actual + 1; ?>
                    <a href="/reportes?<?php echo http_build_query($query_params); ?>" class="btn-secundario" style="padding: 5px 15px; text-decoration: none;">Siguiente &raquo;</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- SECCIÓN DE EXPORTACIÓN A CSV -->
        <div style="background-color: #e6f4ea; border: 1px solid #c3e6cb; padding: 15px; border-radius: 8px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <div>
                <h3 style="margin: 0; font-size: 1.2rem; color: #155724;">Exportar Datos a Excel / CSV</h3>
                <small style="color: #155724;">Selecciona un rango de fechas para descargar los registros tabulados.</small>
            </div>
            
            <form method="POST" action="/reportes/exportar" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap; margin: 0;">
                <div class="form-group" style="margin: 0;">
                    <label for="inicio_csv" style="font-size: 0.9em; font-weight: bold; color: #155724;">Desde:</label>
                    <input type="date" id="inicio_csv" name="inicio_csv" value="<?php echo date('Y-m-01'); ?>" required style="padding: 6px; border: 1px solid #c3e6cb; border-radius: 4px;">
                </div>
                <div class="form-group" style="margin: 0;">
                    <label for="fin_csv" style="font-size: 0.9em; font-weight: bold; color: #155724;">Hasta:</label>
                    <input type="date" id="fin_csv" name="fin_csv" value="<?php echo date('Y-m-d'); ?>" required style="padding: 6px; border: 1px solid #c3e6cb; border-radius: 4px;">
                </div>
                <button type="submit" class="enviar-btn" style="font-size: 1.2rem; margin: 0; background-color: #198754; color: white; border: none; padding: 8px 20px; border-radius: 4px; font-weight: bold; cursor: pointer;">
                    Descargar Archivo
                </button>
            </form>
        </div>

        <!-- SECCIÓN DE ESTADÍSTICAS RÁPIDAS -->
        <div id="seccion-estadisticas" style="background-color: #ffffff; border: 1px solid #dee2e6; padding: 20px; border-radius: 8px; margin-bottom: 25px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f8f9fa; padding-bottom: 15px; margin-bottom: 15px; flex-wrap: wrap; gap: 10px;">
                <h3 style="margin: 0; color: #495057;">Estadísticas del Periodo</h3>
                
                <!-- Selector de periodo inteligente -->
                <form method="GET" action="/reportes" style="display: flex; gap: 10px; align-items: center; margin: 0;">
                    <?php foreach($_GET as $key => $value): ?>
                        <?php if(!in_array($key, ['stat_tipo', 'stat_valor', 'page']) && !empty($value)): ?>
                            <input type="hidden" name="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($value); ?>">
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <select name="stat_tipo" id="stat_tipo" style="font-size: 1.2rem; padding: 5px; border-radius: 4px; border: 1px solid #ced4da;" onchange="toggleStatInputs()">
                        <option value="semana" <?php echo $stat_tipo === 'semana' ? 'selected' : ''; ?>>Semana</option>
                        <option value="mes" <?php echo $stat_tipo === 'mes' ? 'selected' : ''; ?>>Mes</option>
                        <option value="trimestre" <?php echo $stat_tipo === 'trimestre' ? 'selected' : ''; ?>>Trimestre</option>
                    </select>

                    <input type="week" name="stat_valor" id="input_semana" class="stat-input" data-tipo="semana" 
                           value="<?php echo $stat_tipo == 'semana' ? $stat_valor : ''; ?>" 
                           style="font-size: 1.2rem; padding: 4px; border: 1px solid #ced4da; border-radius: 4px; <?php echo $stat_tipo == 'semana' ? '' : 'display:none;'; ?>" 
                           <?php echo $stat_tipo == 'semana' ? '' : 'disabled'; ?>>

                    <input type="month" name="stat_valor" id="input_mes" class="stat-input" data-tipo="mes" 
                           value="<?php echo $stat_tipo == 'mes' ? $stat_valor : ''; ?>" 
                           style="font-size: 1.2rem; padding: 4px; border: 1px solid #ced4da; border-radius: 4px; <?php echo $stat_tipo == 'mes' ? '' : 'display:none;'; ?>" 
                           <?php echo $stat_tipo == 'mes' ? '' : 'disabled'; ?>>

                    <select name="stat_valor" id="input_trimestre" class="stat-input" data-tipo="trimestre" 
                            style="font-size: 1.2rem; padding: 4px; border-radius: 4px; border: 1px solid #ced4da; <?php echo $stat_tipo == 'trimestre' ? '' : 'display:none;'; ?>" 
                            <?php echo $stat_tipo == 'trimestre' ? '' : 'disabled'; ?>>
                        <?php 
                            $anio_actual = date('Y');
                            for ($y = $anio_actual; $y >= $anio_actual - 1; $y--) {
                                for ($q = 4; $q >= 1; $q--) {
                                    $val = "$y-$q";
                                    $selected = ($stat_tipo == 'trimestre' && $stat_valor == $val) ? 'selected' : '';
                                    echo "<option value=\"$val\" $selected>Trimestre $q - $y</option>";
                                }
                            }
                        ?>
                    </select>

                    <button type="submit" class="enviar-btn" style="font-size: 1.2rem; padding: 5px 12px; border-radius: 4px; background-color: #0d6efd; color: white; border: none; font-weight: bold; cursor: pointer;">Actualizar</button>
                </form>
            </div>

            <div style="display: flex; flex-wrap: wrap; gap: 20px;">
                <!-- 1. TOTAL -->
                <div style="flex: 1 1 200px; background-color: #f8f9fa; padding: 15px; border-radius: 6px; text-align: center;">
                    <h4 style="margin: 0 0 10px 0; color: #6c757d; font-size: 1.2rem;">Total de Atenciones</h4>
                    <span style="font-size: 2.5rem; font-weight: bold; color: #212529;"><?php echo $estadisticas['total']; ?></span>
                </div>

                <!-- 2. POR ANALISTA -->
                <div style="flex: 1 1 250px; background-color: #f8f9fa; padding: 15px; border-radius: 6px;">
                    <h4 style="margin: 0 0 10px 0; color: #6c757d; font-size: 1.2rem; border-bottom: 1px solid #dee2e6; padding-bottom: 5px;">Atendido Por</h4>
                    <ul style="margin: 0; padding-left: 0; list-style: none; font-size: 1.2em;">
                        <?php foreach($estadisticas['por_usuario'] as $stat): ?>
                            <li style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                <span><?php echo htmlspecialchars($stat['etiqueta']); ?></span>
                                <strong><?php echo $stat['total']; ?></strong>
                            </li>
                        <?php endforeach; ?>
                        <?php if(empty($estadisticas['por_usuario'])) echo "<li>No hay datos</li>"; ?>
                    </ul>
                </div>

                <!-- 3. POR MOTIVO -->
                <div style="flex: 1 1 250px; background-color: #f8f9fa; padding: 15px; border-radius: 6px;">
                    <h4 style="margin: 0 0 10px 0; color: #6c757d; font-size: 1.2rem; border-bottom: 1px solid #dee2e6; padding-bottom: 5px;">Por Motivo</h4>
                    <ul style="margin: 0; padding-left: 0; list-style: none; font-size: 1.2em;">
                        <?php foreach($estadisticas['por_motivo'] as $stat): ?>
                            <li style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                <span><?php echo htmlspecialchars($stat['etiqueta']); ?></span>
                                <strong><?php echo $stat['total']; ?></strong>
                            </li>
                        <?php endforeach; ?>
                        <?php if(empty($estadisticas['por_motivo'])) echo "<li>No hay datos</li>"; ?>
                    </ul>
                </div>

                <!-- 4. POR MEDIO -->
                <div style="flex: 1 1 250px; background-color: #f8f9fa; padding: 15px; border-radius: 6px;">
                    <h4 style="margin: 0 0 10px 0; color: #6c757d; font-size: 1.2rem; border-bottom: 1px solid #dee2e6; padding-bottom: 5px;">Por Medio</h4>
                    <ul style="margin: 0; padding-left: 0; list-style: none; font-size: 1.2em;">
                        <?php foreach($estadisticas['por_medio'] as $stat): ?>
                            <li style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                <span><?php echo htmlspecialchars($stat['etiqueta']); ?></span>
                                <strong><?php echo $stat['total']; ?></strong>
                            </li>
                        <?php endforeach; ?>
                        <?php if(empty($estadisticas['por_medio'])) echo "<li>No hay datos</li>"; ?>
                    </ul>
                </div>
            </div>
        </div>

    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alertas = document.querySelectorAll('.alerta');
        if (alertas.length > 0) {
            alertas.forEach(alerta => { setTimeout(() => { alerta.remove(); }, 5000); });
        }
    });

    // Script para alternar entre el calendario de semana, mes o el selector de trimestre
    function toggleStatInputs() {
        const tipo = document.getElementById('stat_tipo').value;
        const inputs = document.querySelectorAll('.stat-input');
        
        inputs.forEach(input => {
            if (input.dataset.tipo === tipo) {
                input.style.display = 'inline-block';
                input.disabled = false; // Al habilitarlo, se envía en la URL
            } else {
                input.style.display = 'none';
                input.disabled = true; // Al deshabilitarlo, NO se envía en la URL
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // --- 1. Scroll automático a las estadísticas ---
        const urlParams = new URLSearchParams(window.location.search);
        // Si el usuario acaba de actualizar las estadísticas, bajamos la pantalla
        if(urlParams.has('stat_tipo')) {
            const seccionEstadisticas = document.getElementById('seccion-estadisticas');
            if (seccionEstadisticas) {
                // Hacemos que la pantalla baje suavemente hasta esa sección
                seccionEstadisticas.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    });
</script>