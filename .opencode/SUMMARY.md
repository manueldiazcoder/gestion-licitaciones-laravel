# Summary - Gestion Licitaciones Laravel

## Goal
- Hacer que la app Laravel sea idéntica al proyecto PHP vanilla (UI, estados, roles, CRUDs) y que funcione sin errores al clonar/levantar.

## Constraints & Preferences
- Estados exactos del vanilla: `Borrador`, `Publicado`, `En evaluación`, `Adjudicado`, `Cancelado` (con tildes y mayúsculas).
- Roles exactos del vanilla: solo `admin` (Administrador) y `visor` (Visor).
- Columnas de responsables: `nombre_completo`, `numero_telefono`, `correo_electronico` (los 3 NOT NULL).
- Solución de permisos permanente, segura y automática para cualquier persona que clone el proyecto.
- El entorno real es DevContainer (no Laravel Sail).
- Consultar página debe tener filtros ID, Objeto, Responsable, Estado + exportación contextual a CSV.
- No mostrar columnas presupuesto/moneda ni filtro de moneda en Consultar.

## Progress
### Done
- CSS custom con paleta #1a365d (navbar, dashboard, tablas, forms, flash alerts).
- Override de Bootstrap CSS vars (`--bs-primary-rgb: 26, 54, 93`, `--bs-success-rgb: 39, 103, 73`, `.btn-primary`, `.btn-success`) para que `bg-primary`, `btn-primary`, `text-primary` usen la paleta de la app.
- Layout base con navbar estilo original (brand, nav items con iconos, user dropdown).
- Dashboard sin métricas de estado — solo bienvenida + cards de acceso directo.
- Reportes con desglose por estado dentro de la card "Total procesos" (Publicados, En evaluación, Adjudicados) con indicadores de color.
- Tablas con thead azul oscuro (`table-licitaciones`), filas hover, botones de acción.
- CRUD Responsables completo: migration (`nombre_completo`, `numero_telefono`, `correo_electronico`), model, controller, views (index, create, edit). Solo admin.
- CRUD Admin Usuarios: controller, views (index, edit). Roles solo `admin`/`visor`.
- Columna `estado` en procesos con valores exactos del vanilla: `Borrador`, `Publicado`, `En evaluación`, `Adjudicado`, `Cancelado`.
- Selects de Estado y Responsable en formularios create/edit de procesos.
- Seeders: `ResponsableSeeder` (5 responsables), `ProcesoSeeder` (10 procesos con todos los estados y monedas).
- DevContainer entrypoint personalizado (`.devcontainer/entrypoint.sh`) que corrige permisos en `storage/` y `bootstrap/cache/` en cada inicio.
- DevContainer Dockerfile con `ENTRYPOINT ["custom-entrypoint"]`.
- `devcontainer.json` con `postCreateCommand` incluye `npm run build` + `chown public/build`.
- Eliminados archivos muertos: `docker-compose.yml` (Sail), `docker/`, `routes/api.php`, `routes/channels.php`, `routes/console.php`.
- Actualizados: `.env.example` (`DB_HOST=mysql`), `.env` (sin Sail vars), `README.md` (instrucciones DevContainer), `composer.json` (sin Sail en description/keywords).
- Fix `RouteServiceProvider`: eliminada carga de `routes/api.php`.
- Register y Reset-password usan `bg-primary`/`btn-primary` en vez de `bg-success`/`btn-success`.
- Vite assets rebuilt con overrides CSS.
- Auth forms unificados con azul oscuro (`#1a365d`) en card headers, botones e íconos.
- **Página Consultar** completa:
  - Filtros: ID, Objeto/Descripción, Responsable (select desde DB), Estado (select), fecha desde/hasta.
  - Tabla sin columnas Presupuesto/Moneda.
  - Columnas añadidas: Estado (badge colorido con `COLORES_ESTADO`), Responsable (nombre completo).
  - Exportación CSV contextual con BOM Excel (`/procesos/exportar-csv`) que respeta los filtros activos.
  - Scopes `byId` y `byResponsable` en el modelo Proceso.
- Rutas: export CSV para procesos, reportes y CRUD completos.

### In Progress
- (ninguno — todos los features core están completos)

### Blocked
- (ninguno)

## Key Decisions
- Override de Bootstrap CSS vars en `:root` en vez de modificar cada vista: `bg-primary`, `btn-primary`, `text-primary`, `border-primary` etc. automáticamente usan `#1a365d`.
- Register usa `bg-primary`/`btn-primary` (no verde) porque el verde está fuera de la identidad visual de la app.
- Dashboard sin métricas: se movieron a Reportes como desglose dentro de la card Total procesos.
- `public/build/` en `.gitignore`: assets se compilan con `npm run build` en `postCreateCommand`.
- `npm run build` seguido de `chown -R www-data:www-data public/build` para evitar archivos root-owned.
- Ruta de export CSV de procesos ANTES del resource route para evitar conflictos de ruteo.
- El filtro `codigo` usa `input type="number"` porque `codigo_proceso` es integer auto-increment.

## Next Steps
- Verificar que los permisos de admin funcionan en todos los controllers (middleware `role:admin`).
- Prueba exhaustiva: crear/editar/eliminar procesos, responsables, usuarios.
- Verificar que la exportación CSV descarga el archivo correcto con los filtros aplicados.
- Revisar vista `show` de procesos (detail view).
- Considerar si se necesita paginación infinita o la actual está bien.

## Critical Context
- El proyecto usa DevContainer (`.devcontainer/docker-compose.yml` + `Dockerfile`), no Laravel Sail.
- PHP corre como `www-data` con UID 1000 (mapeado al host).
- Puerto: `localhost:8080` (web), MySQL en `mysql:3306`, expuesto en `3307`.
- Base de datos: `prueba_laravel`, usuario `sail`, password `password`.
- Credenciales seed: admin `admin@admin.com`/`admin`, visor `visor@visor.com`/`visor`.
- Para clonar desde cero: abrir en VS Code → "Reopen in Container" → esperar postCreateCommand.
- El vanilla `SearchController` filtra por: `codigo_licitacion`, `objeto`, `responsable`, `estado`.
- El vanilla `ReportController` exporta a CSV respetando `$_SESSION['export_filtros']`.

## Relevant Files
- `.devcontainer/`: Dockerfile, docker-compose.yml, devcontainer.json, entrypoint.sh, mysql.cnf, init.sql.
- `resources/css/app.css`: CSS custom + overrides Bootstrap (`--bs-primary-rgb`, `.btn-primary`, `.btn-success`).
- `resources/views/auth/`: login, register, forgot-password, reset-password (todos usan `bg-primary`/`btn-primary`).
- `resources/views/home.blade.php`: dashboard sin métricas, solo navegación.
- `resources/views/reportes/index.blade.php`: card Total procesos con desglose por estado.
- `resources/views/procesos/index.blade.php`: página Consultar con filtros ID/Responsable/Estado + export CSV.
- `app/Http/Controllers/ProcesoController.php`: index con scopes `search()`, `byId()`, `byResponsable()`, `byEstado()`, `byDateRange()` + `exportCsv()`.
- `app/Http/Controllers/ReportController.php`: exportCsv, consulta publicados/enEvaluacion/adjudicados.
- `app/Models/Proceso.php`: constantes `ESTADOS`, `COLORES_ESTADO`, scopes `search()`, `byId()`, `byResponsable()`, `byMoneda()`, `byDateRange()`.
- `app/Providers/RouteServiceProvider.php`: limpio, solo carga `routes/web.php`.
- `routes/web.php`: rutas de auth, procesos (incluyendo export), responsables, reportes, admin usuarios.
