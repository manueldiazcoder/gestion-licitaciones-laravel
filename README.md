# Sistema de Gestión de Licitaciones

### Laravel 10 · PHP 8.2 · MySQL · DevContainer · OAuth 2.0 · Roles

Sistema para la gestión de procesos de contratación pública con roles
**Administrador** y **Visor**, autenticación vía OAuth 2.0 (Google/GitHub),
dashboard con métricas por estado, CRUD completo de procesos y responsables.

## Requisitos

- [Docker Desktop](https://www.docker.com/products/docker-desktop/)
- [Visual Studio Code](https://code.visualstudio.com/)
- Extensión: [Dev Containers](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers)

## Inicio rápido

```bash
git clone git@github.com:manueldiazcoder/gestion-licitaciones-laravel.git
cd gestion-licitaciones-laravel
code .
```

En VS Code presioná `F1` → **Dev Containers: Reopen in Container**.

El `postCreateCommand` se encarga automáticamente de:

- Copiar `.env.example` → `.env`
- `composer install`
- `php artisan key:generate`
- `php artisan storage:link`
- `php artisan migrate --seed --force`

### Acceso

| Rol          | Email             | Password |
|--------------|-------------------|----------|
| Administrador | admin@admin.com   | admin    |
| Visor         | visor@visor.com   | visor    |

App disponible en [http://localhost:8080](http://localhost:8080)

## Stack

- **Backend**: Laravel 10 + PHP 8.2
- **Base de datos**: MySQL 8.0
- **Frontend**: Blade + CSS personalizado + Vite
- **Autenticación**: Laravel Breeze + Socialite (Google/GitHub)
- **Contenedores**: DevContainer (Docker)

## Estructura

```
.
├── .devcontainer/          # Configuración DevContainer
│   ├── Dockerfile          # Imagen PHP 8.2 Apache
│   ├── docker-compose.yml  # Servicios web + mysql
│   ├── entrypoint.sh       # Fix de permisos automático
│   └── mysql.cnf           # UTF-8 forzado
├── app/
│   ├── Models/
│   │   ├── Proceso.php     # Estados: Borrador, Publicado, etc.
│   │   ├── Responsable.php # nombre_completo, telefono, email
│   │   └── User.php        # Roles: admin, visor
│   └── Http/Controllers/
│       ├── AuthController.php
│       ├── OAuthController.php
│       ├── ProcesoController.php
│       ├── ResponsableController.php
│       ├── AdminUserController.php
│       └── ReportController.php
├── resources/views/        # Vistas Blade
└── routes/web.php          # Definición de rutas
```

## Licencia

MIT
