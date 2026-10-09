<div class="body-login">
    <main class="new-user-main" style="max-width: 1100px;">
        <h1>Administración de Usuarios</h1>

        <!-- 1. FORMULARIO DE FILTROS -->
        <form method="GET" action="/administrar-usuarios" class="form-filtros" style="display: flex; gap: 15px; margin-bottom: 25px; align-items: flex-end; flex-wrap: wrap; background-color: #f8f9fa; padding: 20px; border-radius: 8px;">
            
            <div class="form-group" style="margin-bottom: 0; flex: 1 1 200px;">
                <label for="busqueda">Buscar por Nombre o Correo:</label>
                <input type="text" id="busqueda" name="busqueda" placeholder="Ej. Juan o juan@ipn.mx" value="<?php echo htmlspecialchars($filtros['busqueda']); ?>" style="width: 100%;">
            </div>

            <div class="form-group" style="margin-bottom: 0; flex: 1 1 150px;">
                <label for="rol">Rol del Sistema:</label>
                <select name="rol" id="rol" style="width: 100%;">
                    <option value="">-- Todos --</option>
                    <?php foreach($roles as $rol): ?>
                        <option value="<?php echo $rol->id; ?>" <?php echo ($filtros['rol'] == $rol->id) ? 'selected' : ''; ?>>
                            <?php echo $rol->nombre; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0; flex: 1 1 150px;">
                <label for="status">Estado:</label>
                <select name="status" id="status" style="width: 100%;">
                    <option value="">-- Todos --</option>
                    <option value="1" <?php echo ($filtros['status'] === '1') ? 'selected' : ''; ?>>Activos</option>
                    <option value="0" <?php echo ($filtros['status'] === '0') ? 'selected' : ''; ?>>Inactivos</option>
                </select>
            </div>

            <div style="flex: 1 1 150px; display: flex; gap: 10px;">
                <input type="submit" class="enviar-btn" value="Buscar" style="margin: 0; width: 100%;">
                <a href="/administrar-usuarios" class="enviar-btn" style="margin: 0; background-color: #6c757d; text-align:center; text-decoration: none; width: 100%;">Limpiar</a>
            </div>
        </form>

        <div style="background-color: #e2e3e5; border-left: 4px solid #6c757d; padding: 10px 15px; border-radius: 4px; margin-bottom: 20px; color: #41464b;">
            Usuarios encontrados: <strong><?php echo $total_registros; ?></strong>
        </div>

        <!-- 2. TABLA DE USUARIOS -->
        <div style="overflow-x: auto;">
            <table class="table table-striped table-hover" style="width: 100%; text-align: left; border-collapse: collapse; min-width: 800px;">
                <thead>
                    <tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px;">Nombre</th>
                        <th style="padding: 12px;">Correo Electrónico</th>
                        <th style="padding: 12px;">Rol</th>
                        <th style="padding: 12px; text-align: center;">Estado</th>
                        <th style="padding: 12px; text-align: center;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($usuarios)): ?>
                        <tr><td colspan="5" style="text-align: center; padding: 20px;">No se encontraron usuarios.</td></tr>
                    <?php else: ?>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr style="border-bottom: 1px solid #dee2e6;">
                                <td style="padding: 12px; font-weight: bold;"><?php echo htmlspecialchars($usuario->name); ?></td>
                                <td style="padding: 12px;"><?php echo htmlspecialchars($usuario->email); ?></td>
                                <td style="padding: 12px;">
                                    <span style="background-color: #e9ecef; padding: 4px 8px; border-radius: 4px; font-size: 0.9em;">
                                        <?php echo htmlspecialchars($usuario->rol_nombre ?? 'Sin Rol'); ?>
                                    </span>
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <?php if ($usuario->status == '1'): ?>
                                        <span style="color: #198754; font-weight: bold;">Activo</span>
                                    <?php else: ?>
                                        <span style="color: #dc3545; font-weight: bold;">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 12px; text-align: center; display: flex; justify-content: center; gap: 8px;">
                                    
                                    
                                    <a href="/editar-usuario?id=<?php echo $usuario->id; ?>" class="enviar-btn" style="background-color: #0d6efd; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 0.9em;">Editar</a>

                                    <!-- Botón Activar/Desactivar -->
                                    <?php if ($usuario->id !== $_SESSION['id']): // Ocultamos el botón si es él mismo ?>
                                        <form method="POST" action="/usuario/cambiar-estado" style="margin: 0;">
                                            <input type="hidden" name="id" value="<?php echo $usuario->id; ?>">
                                            <button type="submit" class="enviar-btn" style="background-color: <?php echo $usuario->status == '1' ? '#dc3545' : '#198754'; ?>; color: white; padding: 6px 12px; border: none; border-radius: 4px; font-size: 0.9em; cursor: pointer;">
                                                <?php echo $usuario->status == '1' ? 'Desactivar' : 'Activar'; ?>
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- 3. PAGINACIÓN INTELIGENTE -->
        <?php if ($total_paginas > 1): ?>
            <div class="paginacion" style="display: flex; justify-content: center; gap: 10px; margin-top: 20px;">
                <?php $query_params = $_GET; ?>
                
                <?php if ($pagina_actual > 1): ?>
                    <?php $query_params['page'] = $pagina_actual - 1; ?>
                    <a href="/administrar-usuarios?<?php echo http_build_query($query_params); ?>" class="btn-secundario" style="padding: 5px 15px; text-decoration: none;">&laquo; Anterior</a>
                <?php endif; ?>

                <span style="padding: 5px 15px; font-weight: bold;">Página <?php echo $pagina_actual; ?> de <?php echo $total_paginas; ?></span>

                <?php if ($pagina_actual < $total_paginas): ?>
                    <?php $query_params['page'] = $pagina_actual + 1; ?>
                    <a href="/administrar-usuarios?<?php echo http_build_query($query_params); ?>" class="btn-secundario" style="padding: 5px 15px; text-decoration: none;">Siguiente &raquo;</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </main>
</div>