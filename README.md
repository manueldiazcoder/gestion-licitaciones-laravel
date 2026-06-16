<div align="center">

# 📋 Sistema de Gestión de Licitaciones

### Laravel 10 · PHP 8.2 · MySQL 8.0 · DevContainer

**Aplicación web para administración, búsqueda y reportes de procesos de
contratación pública, con control de acceso basado en roles.**

<p>
  <img src="https://img.shields.io/badge/Laravel-10-FF2D20?logo=laravel&logoColor=white" alt="Laravel 10"/>
  <img src="https://img.shields.io/badge/PHP-8.2-777BB4?logo=php&logoColor=white" alt="PHP 8.2"/>
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white" alt="MySQL 8.0"/>
  <img src="https://img.shields.io/badge/Docker-✓-2496ED?logo=docker&logoColor=white" alt="Docker"/>
  <img src="https://img.shields.io/badge/Tests-133%20passed-13C100?logo=phpunit&logoColor=white" alt="Tests"/>
  <img src="https://img.shields.io/badge/Bootstrap-5.3-7952B3?logo=bootstrap&logoColor=white" alt="Bootstrap 5.3"/>
  <img src="https://img.shields.io/badge/Auth-Roles%20%7C%20OAuth2-blue" alt="Auth + Roles + OAuth2"/>
</p>

</div>

---

## 📌 Sobre este proyecto

Sistema fullstack para gestionar el ciclo de vida de licitaciones públicas. Desde el registro en estado "Borrador" hasta la adjudicación o cancelación, pasando por publicación y evaluación. Con dos roles de usuario bien diferenciados y autenticación vía OAuth 2.0 (Google/GitHub).

Construido sobre Laravel 10 siguiendo buenas prácticas: **Clean Architecture en las capas del framework**, **Eloquent ORM** con scopes y accesores, **Form Requests** con validación desacoplada, **patrón Service** para lógica de negocio, y **PHPUnit** con factories y tests unitarios + de feature.

---

## 🎯 Propósito

Muchas organizaciones gestionan sus licitaciones con planillas de cálculo, correos electrónicos y archivos sueltos. Esto genera falta de trazabilidad, errores humanos, datos inconsistentes y dificultad para reportar.

El sistema resuelve esto con:

- **Registro estructurado** de licitaciones con presupuesto, cronograma, estado y responsable
- **Búsqueda dinámica** con filtros combinados (ID, objeto, responsable, estado, rango de fechas)
- **Reportes** con datos agregados por estado, moneda y próximos a cerrar
- **Control de acceso** con roles `admin` (CRUD completo) y `visor` (solo consulta)
- **Exportación contextual a CSV** que respeta los filtros activos
- **Autenticación OAuth 2.0** con Google y GitHub
- **Recuperación de contraseña** por correo electrónico via Resend API
- **Entorno 100% containerizado**: un solo comando y el sistema está corriendo

---

## ✨ Funcionalidades

### Autenticación y Control de Acceso
- Inicio y cierre de sesión con contraseñas hasheadas (bcrypt)
- Dos roles: **Administrador** (acceso completo) y **Visor** (solo consulta)
- Middleware de roles por ruta: las rutas de administración son invisibles para visores
- Login con **Google** y **GitHub** via OAuth 2.0 (Socialite)
- Registro de nuevos usuarios con rol `visor` por defecto
- Recuperación de contraseña por correo electrónico via **Resend API**

### Gestión de Licitaciones
- CRUD completo con ciclo de vida: `Borrador → Publicado → En evaluación → Adjudicado → Cancelado`
- Estados representados con badges de color (Bootstrap)
- Búsqueda por ID, objeto/descripción, responsable, estado y rango de fechas
- Validación backend via **Form Requests** con mensajes personalizados en español
- Scopes de Eloquent para filtrar, accesores para formatear
- Paginación de 10 resultados con query string preservada

### Gestión de Responsables
- CRUD completo con nombre, teléfono y correo electrónico
- Protección: no se puede eliminar un responsable con licitaciones asociadas
- Select dinámico en formularios de licitación, poblado desde la BD

### Reportes y Exportación
- Dashboard de métricas: total de licitaciones, presupuesto global, presupuesto promedio
- Conteo y presupuesto por estado (incluye estados con 0 registros)
- Distribución por moneda con porcentajes
- Próximos a cerrar (próximos 30 días)
- Exportación contextual a CSV con encoding UTF-8 BOM (compatible con Excel)
- Logging estructurado en JSON de cada consulta a reportes y exportaciones

### Seguridad y Buenas Prácticas
- **Form Requests** con validación y autorización desacoplada del controlador
- **Prepared statements** vía Eloquent ORM (protección contra SQL Injection)
- **Blade** con escape automático de salida (protección contra XSS)
- Middleware de autenticación y roles (Laravel `auth` + custom `role`)
- Contraseñas hasheadas con bcrypt
- Variables de entorno vía `.env` (sin credenciales hardcodeadas)
- Logging estructurado con canal dedicado `licitaciones` en JSON

---

## 🧠 Decisiones Técnicas

### Arquitectura

| Patrón / Concepto | Dónde se usa |
|---|---|
| **MVC** | Laravel: Models (Eloquent), Views (Blade), Controllers |
| **Form Request** | `StoreLicitacionRequest`, `UpdateLicitacionRequest` — validación y autorización desacoplada |
| **Eloquent Scopes** | `search()`, `byEstado()`, `byMoneda()`, `byDateRange()`, etc. |
| **Eloquent Accessors** | `presupuesto_formateado`, `rango_fechas` |
| **Factory Pattern** | `LicitacionFactory`, `UserFactory`, `ResponsableFactory` para tests y seeders |
| **Service Layer** | `ReportService` extraído del controlador para lógica de reportes |
| **Notificaciones** | `MailResetPasswordNotification` para recovery password |
| **Logging canal dedicado** | Canal `licitaciones` con formato JSON + rotación diaria |

### ¿Por qué Laravel y no un framework más liviano?

Laravel es el framework PHP más adoptado en la industria para aplicaciones server-rendered. Este proyecto usa Laravel no por inercia, sino porque cada componente elegido resuelve un problema real:

| Necesidad | Solución Laravel |
|---|---|
| ORM con relaciones | Eloquent: `BelongsTo`, `HasMany`, scopes, accessors |
| Validación desacoplada | Form Requests con reglas y mensajes en español |
| Autenticación | Breeze scaffold + Socialite para OAuth |
| Templates | Blade con herencia de layouts, escape automático |
| Migraciones | Versionado del schema de base de datos |
| Seeders | Datos de prueba reproducibles |
| Testing | PHPUnit + RefreshDatabase + Factories |
| Contenedores | DevContainer listo para IDE |

### ¿Por qué Resend para correos?

PHP `mail()` requiere un servidor SMTP local configurado. En entornos containerizados no hay servidor SMTP por defecto, y los correos desde IPs sin reputación son marcados como spam. Resend resuelve esto con una API HTTP que maneja SPF/DKIM/DMARC automáticamente, tracking de entregas, y un generoso plan gratuito de 100 emails/día.

Ver [documentación oficial](https://resend.com/docs/send-with-laravel).

---

## 🛠 Stack Tecnológico

| Capa | Tecnología | Propósito |
|---|---|---|
| **Framework** | Laravel 10 | MVC, ORM, routing, templating |
| **Lenguaje** | PHP 8.2 | Lógica del servidor |
| **Base de Datos** | MySQL 8.0 | Persistencia |
| **Servidor Web** | Apache 2.4 (DevContainer) | Servir la aplicación |
| **Frontend** | Bootstrap 5.3 + Icons + CSS personalizado | UI responsive |
| **Email** | Resend API | Correos transaccionales |
| **OAuth** | Laravel Socialite | Google + GitHub login |
| **Testing** | PHPUnit 11 (133 tests) | Tests unitarios y de feature |
| **Contenedores** | Docker + DevContainer | Entorno reproducible |

---

## 🚀 Cómo Empezar

### Requisitos

- [Docker Desktop](https://www.docker.com/products/docker-desktop/)
- [VS Code](https://code.visualstudio.com/) + extensión [Dev Containers](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers)

### Inicio rápido (DevContainer)

```bash
git clone <repository-url>
cd gestion-licitaciones-laravel
code .
```

En VS Code: `F1` → **Dev Containers: Reopen in Container**.

El `postCreateCommand` se encarga automáticamente de:
- Copiar `.env.example` → `.env`
- `composer install`
- `php artisan key:generate`
- `php artisan storage:link`
- `php artisan migrate --seed --force`

### Acceso

| Rol | Email | Contraseña |
|---|---|---|
| **Administrador** | `admin@admin.com` | `admin` |
| **Visor** | `visor@visor.com` | `visor` |

App disponible en [http://localhost:8080](http://localhost:8080)

### Usuarios de prueba (seeders)

El seeder `DatabaseSeeder` crea:
- 1 admin (`admin@admin.com`)
- 1 visor (`visor@visor.com`)
- 5 responsables
- 10 licitaciones en distintos estados y monedas

### Ejecutar tests

```bash
# Dentro del container:
php artisan test

# O desde afuera:
docker exec gestion-licitaciones-laravel_devcontainer-web-1 php artisan test
```

### Configurar OAuth (Google / GitHub)

Ver las secciones correspondientes en el `.env.example` para las credenciales de OAuth. Los endpoints de callback son:

- Google: `http://localhost:8080/auth/google/callback`
- GitHub: `http://localhost:8080/auth/github/callback`

### Configurar Resend (recuperación de contraseña)

1. Registrate en [Resend](https://resend.com) y obtené una API Key
2. Agregala al `.env`: `RESEND_API_KEY=re_xxxxxxxxx`
3. El mailer ya está configurado como `resend` en `config/mail.php`
4. Para desarrollo local, el `from` usa `onboarding@resend.dev`

> En testing, el mailer se cambia automáticamente a `array` (no envía correos reales).

---

## 📁 Estructura del Proyecto

```
gestion-licitaciones-laravel/
├── .devcontainer/                      # Docker + DevContainer
│   ├── Dockerfile                      # PHP 8.2 + Apache
│   ├── docker-compose.yml              # web + mysql
│   └── entrypoint.sh                   # Fix permisos automático
├── app/
│   ├── Models/
│   │   ├── Licitacion.php              # Constantes ESTADOS, scopes, accessors, relaciones
│   │   ├── Responsable.php             # Relación con licitaciones
│   │   └── User.php                    # Roles (esAdmin/esVisor), badge, password reset
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── LicitacionController.php   # CRUD + export CSV
│   │   │   ├── ResponsableController.php  # CRUD de responsables
│   │   │   ├── AdminUserController.php    # Gestión de usuarios (solo admin)
│   │   │   ├── ReportController.php       # Reportes (delega en ReportService)
│   │   │   ├── AuthController.php         # Login/register/logout
│   │   │   ├── OAuthController.php        # Google/GitHub callback
│   │   │   ├── ForgotPasswordController.php
│   │   │   └── ResetPasswordController.php
│   │   └── Requests/
│   │       ├── StoreLicitacionRequest.php    # Validación + autorización
│   │       └── UpdateLicitacionRequest.php   # Validación + autorización
│   ├── Services/
│   │   └── ReportService.php            # Lógica de reportes (extraída del controller)
│   ├── Notifications/
│   │   └── MailResetPasswordNotification.php
│   └── Logging/
│       └── JsonFormatter.php           # Formato JSON para logs
├── database/
│   ├── factories/                      # LicitacionFactory, UserFactory, ResponsableFactory
│   ├── migrations/                     # 16 migrations versionadas
│   └── seeders/                        # Datos de prueba
├── resources/views/
│   ├── layouts/base.blade.php          # Layout con navbar, sidebar, footer
│   ├── licitaciones/                   # index (consulta), create, edit, show
│   ├── responsables/                   # index, create, edit
│   ├── admin/usuarios/                 # index, edit
│   ├── reportes/index.blade.php        # Dashboard de reportes
│   └── auth/                           # login, register, forgot/reset password
├── routes/
│   └── web.php                         # Definición de rutas
├── tests/
│   ├── Unit/
│   │   ├── LicitacionModelTest.php     # 20 tests: constantes, scopes, accessors, relaciones
│   │   ├── UserModelTest.php           # 12 tests: roles, badge, relaciones
│   │   └── ResponsableModelTest.php    # 7 tests: factory, fillable, relaciones
│   └── Feature/
│       ├── LicitacionControllerTest.php  # 34 tests: CRUD, roles, validación, filtros, CSV
│       ├── ResponsableControllerTest.php # 19 tests: CRUD, roles, validación
│       ├── AdminUserControllerTest.php   # 17 tests: CRUD, roles, auto-protección
│       ├── AuthTest.php                  # 10 tests: login, register, logout
│       ├── PasswordResetTest.php         # 8 tests: forgot + reset flow
│       └── ReportControllerTest.php      # 5 tests: acceso y datos
└── config/
    ├── mail.php                        # Mailer resend configurado
    ├── logging.php                     # Canal 'licitaciones' con JSON
    └── resend.php                      # Configuración Resend API
```

---

## 🧪 Testing

El proyecto tiene **133 tests** con **289 assertions**, organizados en:

| Suite | Archivo | Tests | Cobertura |
|---|---|---|---|
| **Unit** | `LicitacionModelTest` | 20 | Constantes, scopes, accessors, relaciones |
| **Unit** | `UserModelTest` | 12 | Roles, badge, relación licitaciones |
| **Unit** | `ResponsableModelTest` | 7 | Factory, fillable, relación, delete |
| **Feature** | `LicitacionControllerTest` | 34 | CRUD admin, permisos visor, validación, filtros, export CSV |
| **Feature** | `ResponsableControllerTest` | 19 | CRUD admin, permisos visor, validación, protección delete |
| **Feature** | `AdminUserControllerTest` | 17 | CRUD admin, permisos visor, auto-protección, validación |
| **Feature** | `AuthTest` | 10 | Login, registro, logout, rutas protegidas |
| **Feature** | `PasswordResetTest` | 8 | Forgot + reset flow con Notification::fake() |
| **Feature** | `ReportControllerTest` | 5 | Acceso roles, datos correctos |

### Ejecución

```bash
# Todos los tests
php artisan test

# Solo unitarios
php artisan test --testsuite=Unit

# Solo feature
php artisan test --testsuite=Feature

# Archivo específico
php artisan test tests/Unit/LicitacionModelTest.php
```

Los tests usan **SQLite en memoria** (`:memory:`) configurado en `phpunit.xml`, no tocan la base de datos real.
