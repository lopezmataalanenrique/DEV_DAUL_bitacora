<div class="body-login">
   <main class="new-user-main" style="max-width: 1000px;">
      <h1>Mis atenciones registradas</h1>
      <?php include_once __DIR__ . '/../templates/alertas.php'; ?>
      <form method="GET" action="/mis-atenciones" class="form-filtros" style="display: flex; gap: 15px; margin-bottom: 20px; align-items: flex-end;">
         <div class="form-group" style="margin-bottom: 0; flex: 1;">
            <label for="inicio">Fecha Inicio</label>
            <input type="date" id="inicio" name="inicio" value="<?php echo $fecha_inicio; ?>">
         </div>

         <div class="form-group" style="margin-bottom: 0; flex: 1;">
            <label for="fin">Fecha Fin</label>
            <input type="date" id="fin" name="fin" value="<?php echo $fecha_fin; ?>">
         </div>

         <div style="flex: 1; display: flex; gap: 10px;">
            <input type="submit" class="enviar-btn" value="Aplicar Filtros" style="margin: 0;">
            <a href="/mis-atenciones" class="enviar-btn" style="margin: 0; background-color: #6c757d; text-align:center; text-decoration: none;">Limpiar</a>
         </div>
      </form>

      <?php if (!empty($fecha_inicio) || !empty($fecha_fin)): ?>
         <div style="background-color: #e2e3e5; border-left: 4px solid #6c757d; padding: 10px 15px; border-radius: 4px; margin-bottom: 20px; color: #41464b;">
            <strong>Filtros aplicados:</strong> Se encontraron <strong><?php echo $total_registros; ?></strong> atención(es).
         </div>
      <?php endif; ?>

      <div style="overflow-x: auto;">
         <table class="table table-striped table-hover" style="width: 100%; text-align: left; border-collapse: collapse;">
            <thead>
               <tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                  <th style="padding: 12px;">Fecha</th>
                  <th style="padding: 12px;">Solicitante</th>
                  <th style="padding: 12px;">Perfil</th>
                  <th style="padding: 12px;">Motivo</th>
                  <th style="padding: 12px;">Medio</th>
               </tr>
            </thead>
            <tbody>
               <?php if (empty($atenciones)): ?>
                  <tr>
                     <td colspan="5" style="text-align: center; padding: 20px;">No se encontraron registros.</td>
                  </tr>
               <?php else: ?>
                  <?php foreach ($atenciones as $registro): ?>
                     <tr style="border-bottom: 1px solid #dee2e6;">
                        <td style="padding: 12px;"><?php echo date('d/m/Y H:i', strtotime($registro->fecha_atencion)); ?></td>
                        <td style="padding: 12px;">
                           <strong><?php echo htmlspecialchars($registro->nombre_completo); ?></strong><br>
                           <small style="color: #6c757d;"><?php echo htmlspecialchars($registro->correo); ?></small>
                        </td>
                        <td style="padding: 12px;"><?php echo htmlspecialchars($registro->tipo_nombre); ?></td>
                        <td style="padding: 12px;"><?php echo htmlspecialchars($registro->motivo_nombre); ?></td>
                        <td style="padding: 12px;"><?php echo htmlspecialchars($registro->medio_nombre); ?></td>
                     </tr>
                  <?php endforeach; ?>
               <?php endif; ?>
            </tbody>
         </table>
      </div>

      <?php if ($total_paginas > 1): ?>
         <div class="paginacion" style="display: flex; justify-content: center; gap: 10px; margin-top: 20px;">
            <?php if ($pagina_actual > 1): ?>
               <a href="/mis-atenciones?page=<?php echo $pagina_actual - 1; ?>&inicio=<?php echo $fecha_inicio; ?>&fin=<?php echo $fecha_fin; ?>"
                  class="btn-secundario" style="padding: 5px 15px; text-decoration: none;">&laquo; Anterior</a>
            <?php endif; ?>

            <span style="padding: 5px 15px; font-weight: bold;">
               Página <?php echo $pagina_actual; ?> de <?php echo $total_paginas; ?>
            </span>

            <?php if ($pagina_actual < $total_paginas): ?>
               <a href="/mis-atenciones?page=<?php echo $pagina_actual + 1; ?>&inicio=<?php echo $fecha_inicio; ?>&fin=<?php echo $fecha_fin; ?>"
                  class="btn-secundario" style="padding: 5px 15px; text-decoration: none;">Siguiente &raquo;</a>
            <?php endif; ?>
         </div>
      <?php endif; ?>

   </main>
</div>

<script>
   document.addEventListener('DOMContentLoaded', function() {
      // Seleccionamos todas las alertas que existan en la pantalla
      const alertas = document.querySelectorAll('.alerta');

      // Si hay alertas, empezamos el contador
      if (alertas.length > 0) {
         alertas.forEach(alerta => {
            setTimeout(() => {
               // Después de 5000 milisegundos (5s), la borramos del HTML
               alerta.remove();
            }, 5000);
         });
      }
   });
</script>