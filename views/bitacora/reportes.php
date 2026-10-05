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
                                    <span style="color: #6c757d; font-size: 0.9em;"><?php echo htmlspecialchars($registro->tipo_nombre); ?></span>
                                </td>
                                <td style="padding: 12px;"><?php echo htmlspecialchars($registro->escuela_nombre); ?></td>
                                <td style="padding: 12px;">
                                    <?php echo htmlspecialchars($registro->motivo_nombre); ?><br>
                                    <span style="color: #6c757d; font-size: 0.9em;"><?php echo htmlspecialchars($registro->medio_nombre); ?></span>
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

    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alertas = document.querySelectorAll('.alerta');
        if (alertas.length > 0) {
            alertas.forEach(alerta => { setTimeout(() => { alerta.remove(); }, 5000); });
        }
    });
</script>