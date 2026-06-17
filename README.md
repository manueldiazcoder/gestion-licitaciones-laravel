<!--
  =====================================================================
  Sistema de Gestión de Licitaciones — Laravel 10
  Proyecto de portafolio — PHP FullStack
  =====================================================================
-->

<div align="center">

# 📋 Sistema de Gestión de Licitaciones

### Laravel 10 · PHP 8.2 · MySQL 8.0 · Roles · OAuth 2.0 · 133 Tests

**Aplicación web para administración, búsqueda y reportes de procesos de
contratación pública, con control de acceso basado en roles.**

<p>
  <img src="https://img.shields.io/badge/Laravel-10-FF2D20?logo=laravel&logoColor=white" alt="Laravel 10"/>
  <img src="https://img.shields.io/badge/PHP-8.2-777BB4?logo=php&logoColor=white" alt="PHP 8.2"/>
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white" alt="MySQL 8.0"/>
  <img src="https://img.shields.io/badge/Docker-✓-2496ED?logo=docker&logoColor=white" alt="Docker"/>
  <img src="https://img.shields.io/badge/Tests-133%20passed-13C100?logo=phpunit&logoColor=white" alt="133 tests passing"/>
  <img src="https://img.shields.io/badge/Coverage-289%20assertions-13C100" alt="289 assertions"/>
  <img src="https://img.shields.io/badge/Bootstrap-5.3-7952B3?logo=bootstrap&logoColor=white" alt="Bootstrap 5.3"/>
  <img src="https://img.shields.io/badge/Auth-Roles%20%7C%20OAuth2-blue" alt="Auth + Roles + OAuth2"/>
  <img src="https://img.shields.io/badge/Arquitectura-MVC%20%7C%20Service%20Layer-blue" alt="MVC + Service Layer"/>
</p>

</div>

---

## 📌 Tabla de Contenidos

- [📌 Sobre este proyecto](#-sobre-este-proyecto)
- [🎯 Propósito](#-propósito)
- [✨ Funcionalidades](#-funcionalidades)
- [🧠 Competencias Demostradas](#-competencias-demostradas)
- [🏗 Arquitectura](#-arquitectura)
- [🧠 Decisiones Técnicas](#-decisiones-técnicas)
- [🛠 Stack Tecnológico](#-stack-tecnológico)
- [🚀 Cómo Empezar](#-cómo-empezar)
  - [Opción 1: DevContainer (recomendado)](#opción-1-devcontainer-recomendado)
  - [Opción 2: Solo Docker](#opción-2-solo-docker)
  - [Usuarios de prueba](#usuarios-de-prueba)
  - [Ejecutar tests](#ejecutar-tests)
  - [Configurar OAuth (Google / GitHub)](#configurar-oauth-google--github)
  - [Configurar Resend (recuperación de contraseña)](#configurar-resend-recuperación-de-contraseña)
- [📁 Estructura del Proyecto](#-estructura-del-proyecto)
- [🧪 Testing](#-testing)
- [🐛 Troubleshooting](#-troubleshooting)

---

## 📌 Sobre este proyecto

Sistema fullstack para gestionar el ciclo de vida de licitaciones públicas.
Desde el registro en estado **"Borrador"** hasta la **adjudicación** o **cancelación**,
pasando por publicación y evaluación. Con dos roles de usuario bien diferenciados
y autenticación vía OAuth 2.0 (Google/GitHub).

Construido sobre **Laravel 10** siguiendo buenas prácticas de la industria:

| Concepto | Implementación |
|---|---|
| **Arquitectura** | MVC + Service Layer |
| **ORM** | Eloquent con scopes, accessors, relaciones |
| **Validación** | Form Requests desacoplados |
| **Testing** | PHPUnit 11 con RefreshDatabase + Factories |
| **Autenticación** | Breeze + Socialite (Google/GitHub) |
| **Contenedores** | DevContainer + Docker Compose |

---

## 🎯 Propósito

Muchas organizaciones gestionan sus licitaciones con planillas de cálculo,
correos electrónicos y archivos sueltos. Esto genera falta de trazabilidad,
errores humanos, datos inconsistentes y dificultad para reportar.

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

### 🔐 Autenticación y Control de Acceso

- Inicio y cierre de sesión con contraseñas hasheadas (bcrypt)
- Dos roles: **Administrador** (acceso completo) y **Visor** (solo consulta)
- Middleware de roles por ruta: las rutas de administración son invisibles para visores
- Login con **Google** y **GitHub** via OAuth 2.0 (Socialite)
- Registro de nuevos usuarios con rol `visor` por defecto
- Recuperación de contraseña por correo electrónico via **Resend API**

### 📄 Gestión de Licitaciones

- CRUD completo con ciclo de vida: `Borrador → Publicado → En evaluación → Adjudicado → Cancelado`
- Estados representados con **badges de color** (Bootstrap)
- Búsqueda por ID, objeto/descripción, responsable, estado y rango de fechas
- Validación backend via **Form Requests** con mensajes personalizados en español
- **Scopes de Eloquent** para filtrar, **accesores** para formatear
- Paginación de 10 resultados con query string preservada

### 👤 Gestión de Responsables

- CRUD completo con nombre, teléfono y correo electrónico
- **Protección**: no se puede eliminar un responsable con licitaciones asociadas
- Select dinámico en formularios de licitación, poblado desde la BD

### 📊 Reportes y Exportación

- Dashboard de métricas: total de licitaciones, presupuesto global, presupuesto promedio
- Conteo y presupuesto por estado (incluye estados con 0 registros)
- Distribución por moneda con porcentajes
- Próximos a cerrar (próximos 30 días)
- Exportación contextual a CSV con encoding **UTF-8 BOM** (compatible con Excel)
- Logging estructurado en JSON de cada consulta a reportes y exportaciones

### 🛡 Seguridad y Buenas Prácticas

- **Form Requests** con validación y autorización desacoplada del controlador
- **Prepared statements** vía Eloquent ORM (protección contra SQL Injection)
- **Blade** con escape automático de salida (protección contra XSS)
- Middleware de autenticación y roles (Laravel `auth` + custom `role`)
- Contraseñas hasheadas con bcrypt
- Variables de entorno vía `.env` (sin credenciales hardcodeadas)
- Logging estructurado con canal dedicado `licitaciones` en JSON

---

## 🧠 Competencias Demostradas

> Este proyecto está diseñado como **portafolio** para demostrar competencias
> sólidas en desarrollo Laravel fullstack. Cada sección del código responde
> a una decisión técnica consciente.

### Laravel & PHP

| Competencia | Dónde se ve en el código |
|---|---|
| **Eloquent ORM avanzado** | Scopes reutilizables (`search`, `byEstado`, `byDateRange`), accessors (`presupuesto_formateado`, `rango_fechas`), relaciones `BelongsTo`/`HasMany` |
| **Form Requests** | `StoreLicitacionRequest`, `UpdateLicitacionRequest` — validación y autorización fuera del controlador |
| **Service Layer** | `ReportService` — lógica de reportes extraída del controller (SRP) |
| **Factories + Seeders** | 3 factories con estados personalizados, seeder que genera 10 licitaciones en distintos estados |
| **Testing con PHPUnit** | 133 tests, 289 assertions, RefreshDatabase + SQLite in-memory |
| **Blade templating** | Layout con herencia, componentes Bootstrap, escape automático, lógica condicional por角色 |
| **Middleware personalizado** | Middleware `role` para control de acceso admin/visor |
| **Notificaciones** | `MailResetPasswordNotification` para recovery password |
| **Logging** | Canal personalizado `licitaciones` con formato JSON + rotación diaria |

### Arquitectura de software

| Competencia | Dónde se ve |
|---|---|
| **MVC** | Separación clara Models / Views / Controllers |
| **Responsabilidad única** | Cada clase hace una cosa: Controller recibe request, Service procesa lógica, Model encapsula datos |
| **Inyección de dependencias** | `ReportService` inyectado en el controller via Laravel container |
| **Principio de sustitución de Liskov** | Form Requests extienden `FormRequest` sin romper contratos |
| **Composición sobre herencia** | Traits reutilizables (`HasFactory`, `Notifiable`, `CanResetPassword`) |
| **DTO implícito** | El array asociativo devuelto por `ReportService::generate()` funciona como DTO |

### DevOps

| Competencia | Dónde se ve |
|---|---|
| **Docker + Docker Compose** | Entorno reproducible con PHP 8.2 + Apache + MySQL |
| **DevContainer** | Desarrollo estandarizado en VS Code, postCreateCommand automatizado |
| **Variables de entorno** | `.env` para todas las configuraciones sensibles |
| **Entrypoint con fix de permisos** | Script automatizado que resuelve permisos de `www-data` |

### OAuth 2.0 & Seguridad

| Competencia | Dónde se ve |
|---|---|
| **Authorization Code Grant** | Flujo completo OAuth 2.0 con Google y GitHub via Socialite |
| **Protección CSRF** | State parameter en el flujo OAuth |
| **Auto-registro OAuth** | Usuarios nuevos se crean automáticamente con rol visor |
| **Coexistencia auth tradicional + social** | Login con email/password + Google + GitHub en la misma sesión |
| **Validación de transiciones de estado** | Scopes que encapsulan lógica de negocio |

---

## 🏗 Arquitectura

### Flujo de una petición HTTP

```
Browser (cliente)
     │
     ▼
  routes/web.php ─── Middleware ─── Controller ─── Service (si aplica)
     │                  │                              │
     │               ┌── auth       ┌── LicitacionController  ┌── ReportService
     │               ├── role:admin ├── ResponsableController  │
     │               └── guest      ├── AdminUserController    │
     │                              ├── AuthController         │
     │                              ├── ReportController ──────┘
     │                              └── OAuthController
     │                                   │
     │                                   ▼
     │                               Eloquent Model
     │                                   │
     │                                   ▼
     │                                MySQL DB
     │
     ▼
  Blade View ←── Controller pasa datos con compact()
     │
     ▼
  HTML + CSS + JS ←── Layout base + navbar + sidebar
```

### Flujo de autenticación OAuth 2.0

```
Usuario → Click en "Iniciar sesión con Google/GitHub"
        │
        ▼
OAuthController::redirect()
  └─ Socialite::driver('google')->redirect()
        │
        ▼
Proveedor (Google/GitHub) → Usuario autoriza → Redirige a:
  /auth/{provider}/callback?code=xxx&state=yyy
        │
        ▼
OAuthController::callback()
  ├─ Socialite::driver('google')->user() → {email, name}
  ├─ ¿Email existe en BD?
  │   ├─ Sí → login directo (Auth::login())
  │   └─ No → User::create([...]) + Auth::login()
  └─ Redirige a Dashboard
```

### Flujo de recuperación de contraseña

```
Usuario → "Olvidé mi contraseña" → Ingresa email
        │
        ▼
ForgotPasswordController::sendResetLinkEmail()
  ├─ ¿Email existe en BD? → Genera token via Password::createToken()
  ├─ Envía notificación MailResetPasswordNotification via Resend
  └─ Redirige a login con mensaje de éxito
                           │
                    Usuario recibe email
                           │
                           ▼
                  Enlace con token
                           │
                           ▼
ResetPasswordController::reset()
  ├─ Valida token (Password::reset())
  ├─ Actualiza password (Hash::make())
  └─ Redirige a login
```

---

## 🧠 Decisiones Técnicas

### ¿Por qué Laravel y no un framework más liviano?

Laravel es el framework PHP más adoptado en la industria para aplicaciones
server-rendered. Este proyecto usa Laravel no por inercia, sino porque cada
componente elegido resuelve un problema real:

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

### Patrones y conceptos aplicados

| Patrón | Dónde se usa |
|---|---|
| **MVC** | Laravel: Models (Eloquent), Views (Blade), Controllers |
| **Form Request** | `StoreLicitacionRequest`, `UpdateLicitacionRequest` |
| **Service Layer** | `ReportService` extraído del controller |
| **Factory** | `LicitacionFactory`, `UserFactory`, `ResponsableFactory` |
| **Active Record** | Eloquent Models con lógica de negocio encapsulada |
| **Notification** | `MailResetPasswordNotification` |
| **Middleware** | Auth + role:admin para rutas protegidas |

### ¿Por qué Resend para correos?

PHP `mail()` requiere un servidor SMTP local configurado. En entornos
containerizados no hay servidor SMTP por defecto, y los correos desde IPs
sin reputación son marcados como spam. **Resend** resuelve esto con una API
HTTP que maneja SPF/DKIM/DMARC automáticamente, tracking de entregas, y un
generoso plan gratuito de 100 emails/día.

Ver [documentación oficial](https://resend.com/docs/send-with-laravel).

---

## 🛠 Stack Tecnológico

| Capa | Tecnología | Propósito |
|---|---|---|
| **Framework** | Laravel 10 | MVC, ORM, routing, templating |
| **Lenguaje** | PHP 8.2 | Lógica del servidor |
| **Base de Datos** | MySQL 8.0 | Persistencia |
| **Servidor Web** | Apache 2.4 | Servir la aplicación |
| **Frontend** | Bootstrap 5.3 + Icons + CSS personalizado (#1a365d) | UI responsive |
| **Email** | Resend API | Correos transaccionales |
| **OAuth** | Laravel Socialite | Google + GitHub login |
| **Testing** | PHPUnit 11 (133 tests, 289 assertions) | Tests unitarios y de feature |
| **Contenedores** | Docker + DevContainer | Entorno reproducible |

---

## 🚀 Cómo Empezar

### Requisitos

- [Docker](https://www.docker.com/get-started) (con Docker Compose)
- [VS Code](https://code.visualstudio.com/) + [Dev Containers](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers) (solo para opción DevContainer)

---

### Opción 1: DevContainer (recomendado)

El DevContainer configura automáticamente PHP, Apache, MySQL, Composer, y
corre las migrations + seeders al arrancar.

```bash
git clone <repository-url>
cd gestion-licitaciones-laravel
code .
```

En VS Code: Presioná `F1` → **Dev Containers: Reopen in Container**.

El `postCreateCommand` se encarga automáticamente de:

```
✅ .env.example → .env
✅ composer install
✅ php artisan key:generate
✅ php artisan storage:link
✅ php artisan migrate --seed --force
```

Una vez que el container termine de construirse, la app está disponible en:

➡️ **http://localhost:8080**

---

### Opción 2: Solo Docker

Si no usás VS Code o preferís levantar los containers directamente:

```bash
git clone <repository-url>
cd gestion-licitaciones-laravel

# Copiar y configurar entorno
cp .env.example .env

# Iniciar containers
docker compose -f .devcontainer/docker-compose.yml up -d

# Entrar al container web
docker exec -it gestion-licitaciones-laravel_devcontainer-web-1 bash

# Dentro del container:
composer install
php artisan key:generate
php artisan storage:link
php artisan migrate --seed --force
```

> Si usás Windows, asegurate de compartir el drive en Docker Desktop.

La app queda disponible en:

➡️ **http://localhost:8080**

---

### Usuarios de prueba

| Rol | Email | Contraseña |
|---|---|---|
| **Administrador** | `admin@admin.com` | `admin` |
| **Visor** | `visor@visor.com` | `visor` |

El seeder `DatabaseSeeder` también crea:
- **5 responsables** de prueba
- **10 licitaciones** en distintos estados y monedas

---

### Ejecutar tests

```bash
# Dentro del container:
php artisan test

# Solo tests unitarios
php artisan test --testsuite=Unit

# Solo tests de feature
php artisan test --testsuite=Feature

# Test específico
php artisan test tests/Unit/LicitacionModelTest.php

# Desde afuera del container:
docker exec gestion-licitaciones-laravel_devcontainer-web-1 php artisan test
```

---

### Configurar OAuth (Google / GitHub)

#### Google

1. Andá a [Google Cloud Console → Credenciales](https://console.cloud.google.com/apis/credentials)
2. Creá un proyecto o seleccioná uno existente
3. Configurá la **pantalla de consentimiento OAuth** (tipo *External*)
   - Agregá los scopes: `email`, `profile`
4. Creá una **Credencial → ID de cliente OAuth** (tipo *Aplicación Web*)
   - **URI de redirección autorizada**: `http://localhost:8080/auth/google/callback`
5. Copiá el **Client ID** y **Client Secret** al `.env`:

```bash
GOOGLE_CLIENT_ID=123456789-xxxxx.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-xxxxxxxxxxxxxxxxxxxx
```

6. En la pantalla de login aparecerá el botón **"Iniciar sesión con Google"**

> Si el email de Google ya está registrado, se inicia sesión directamente.
> Si es la primera vez, se crea un usuario nuevo con rol **visor** automáticamente.

#### GitHub

1. Andá a [GitHub Settings → Developer settings → OAuth Apps](https://github.com/settings/developers)
2. Click en **"New OAuth App"**
   - **Application name**: Sistema de Gestión de Licitaciones (dev)
   - **Homepage URL**: `http://localhost:8080`
   - **Authorization callback URL**: `http://localhost:8080/auth/github/callback`
3. Copiá el **Client ID** y generá un **Client Secret**
4. Agregalos al `.env`:

```bash
GITHUB_CLIENT_ID=tu-github-client-id
GITHUB_CLIENT_SECRET=tu-github-client-secret
```

> **Atención**: GitHub no siempre comparte el email del usuario si es privado.
> Asegurate de que el email sea **público** en tu perfil de GitHub
> (Settings → Public profile), o que la app tenga permiso `user:email`.

---

### Configurar Resend (recuperación de contraseña)

1. Registrate en [Resend](https://resend.com) y obtené una API Key
2. Agregala al `.env`:

```bash
RESEND_API_KEY=re_xxxxxxxxx
```

3. El mailer ya está configurado como `resend` en `config/mail.php`
4. Para desarrollo local, el `from` usa `onboarding@resend.dev` (dominio de testing de Resend)

> **En testing** (`phpunit.xml`), el mailer se cambia automáticamente a `array`
> (no envía correos reales, los almacena en memoria).
>
> **En producción**, cambiá `MAIL_FROM_ADDRESS` a un dominio verificado en Resend.

---

## 📁 Estructura del Proyecto

```
gestion-licitaciones-laravel/
│
├── .devcontainer/                          # Docker + DevContainer
│   ├── Dockerfile                          # PHP 8.2 + Apache + extensiones
│   ├── docker-compose.yml                  # Servicios: web (Apache) + mysql
│   ├── entrypoint.sh                       # Fix de permisos automático
│   └── mysql.cnf                           # UTF-8 forzado
│
├── app/
│   ├── Models/
│   │   ├── Licitacion.php                  # Constantes ESTADOS, scopes, accessors, relaciones
│   │   ├── Responsable.php                 # Relación con licitaciones
│   │   └── User.php                        # Roles (esAdmin/esVisor), badge, password reset
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── LicitacionController.php    # CRUD + export CSV
│   │   │   ├── ResponsableController.php   # CRUD de responsables
│   │   │   ├── AdminUserController.php     # Gestión de usuarios (solo admin)
│   │   │   ├── ReportController.php        # Reportes (delega en ReportService)
│   │   │   ├── AuthController.php          # Login/register/logout
│   │   │   ├── OAuthController.php         # Google/GitHub callback
│   │   │   ├── ForgotPasswordController.php
│   │   │   └── ResetPasswordController.php
│   │   │
│   │   └── Requests/
│   │       ├── StoreLicitacionRequest.php   # Validación + autorización crear
│   │       └── UpdateLicitacionRequest.php  # Validación + autorización actualizar
│   │
│   ├── Services/
│   │   └── ReportService.php               # Lógica de reportes extraída del controller
│   │
│   ├── Notifications/
│   │   └── MailResetPasswordNotification.php
│   │
│   └── Logging/
│       └── JsonFormatter.php               # Formato JSON para canal licitaciones
│
├── database/
│   ├── factories/                           # LicitacionFactory, UserFactory, ResponsableFactory
│   ├── migrations/                          # 16 migrations versionadas
│   └── seeders/                             # DatabaseSeeder con datos de prueba
│
├── resources/views/
│   ├── layouts/base.blade.php               # Layout con navbar, sidebar, footer
│   ├── licitaciones/                        # index (consulta), create, edit, show
│   ├── responsables/                        # index, create, edit
│   ├── admin/usuarios/                      # index, edit
│   ├── reportes/index.blade.php             # Dashboard de reportes
│   └── auth/                                # login, register, forgot/reset password
│
├── routes/
│   └── web.php                              # Definición de rutas (web)
│
├── tests/
│   ├── Unit/
│   │   ├── LicitacionModelTest.php          # 20 tests
│   │   ├── UserModelTest.php                # 12 tests
│   │   └── ResponsableModelTest.php         # 7 tests
│   └── Feature/
│       ├── LicitacionControllerTest.php     # 34 tests
│       ├── ResponsableControllerTest.php    # 19 tests
│       ├── AdminUserControllerTest.php      # 17 tests
│       ├── AuthTest.php                     # 10 tests
│       ├── PasswordResetTest.php            # 8 tests
│       └── ReportControllerTest.php         # 5 tests
│
├── config/
│   ├── mail.php                             # Mailer resend configurado
│   ├── logging.php                          # Canal 'licitaciones' con JSON
│   └── resend.php                           # Configuración Resend API
│
└── .devcontainer/                           # (ver arriba)
```

---

## 🧪 Testing

El proyecto tiene **133 tests** con **289 assertions**, ejecutados con
**SQLite en memoria** (`:memory:`) — no tocan la base de datos real.

| Suite | Archivo | Tests | Cobertura |
|---|---|---|---|
| **Unit** | `LicitacionModelTest` | 20 | Constantes, scopes, accessors, relaciones |
| **Unit** | `UserModelTest` | 12 | Roles (esAdmin/esVisor), badge HTML, relaciones |
| **Unit** | `ResponsableModelTest` | 7 | Factory, mass assignment, relaciones, delete |
| **Feature** | `LicitacionControllerTest` | 34 | CRUD admin, permisos visor, validación completa, filtros reales, CSV |
| **Feature** | `ResponsableControllerTest` | 19 | CRUD admin, permisos visor, protección delete con licitaciones |
| **Feature** | `AdminUserControllerTest` | 17 | CRUD admin, permisos visor, auto-protección, validación email único |
| **Feature** | `AuthTest` | 10 | Login, registro, logout, rutas protegidas |
| **Feature** | `PasswordResetTest` | 8 | Forgot + reset flow con Notification::fake() |
| **Feature** | `ReportControllerTest` | 5 | Acceso roles, datos correctos |

### Coverage de lo que importa

| Escenario | Testeado |
|---|---|
| Admin crea licitación | ✅ |
| Admin crea con responsable asignado | ✅ |
| Admin crea con estado explícito | ✅ |
| Admin actualiza estado | ✅ |
| Admin elimina | ✅ |
| Visor ve solo index + show | ✅ |
| Visor recibe 403 en create/edit/update/delete | ✅ |
| Validación campos obligatorios | ✅ |
| Validación moneda inválida | ✅ |
| Validación fecha inicio ≤ fecha cierre | ✅ |
| Validación presupuesto negativo | ✅ |
| Validación estado inválido | ✅ |
| Paginación página 1 (solo Siguiente) | ✅ |
| Paginación página 2 (Anterior + Siguiente) | ✅ |
| Filtro por estado con assertDontSee | ✅ |
| Filtro por search | ✅ |
| Filtro por código (ID) | ✅ |
| Filtros combinados (estado + search) | ✅ |
| Sin resultados muestra mensaje | ✅ |
| Export CSV admin | ✅ |
| Export CSV visor | ✅ |
| Export CSV contenido verificado | ✅ |
| Responsable: CRUD completo | ✅ |
| Responsable: no eliminar con licitaciones | ✅ |
| Usuarios: CRUD admin | ✅ |
| Usuarios: no auto-eliminarse | ✅ |
| Usuarios: no eliminar con licitaciones | ✅ |
| Usuarios: validación email único | ✅ |
| Login válido/inválido | ✅ |
| Registro crea como visor | ✅ |
| Password reset con Notification::fake() | ✅ |
| Reportes: métricas correctas | ✅ |

---

## 🐛 Troubleshooting

### "No se encontraron licitaciones" después de migrar

Los seeders se ejecutan automáticamente con `--force` en el postCreateCommand.
Si no ves datos, ejecutá manualmente:

```bash
php artisan db:seed --force
```

### Error de permisos en `storage/` o `bootstrap/cache/`

El entrypoint del container ya ejecuta `chown -R www-data:www-data` sobre
`storage/` y `bootstrap/cache/`. Si persiste:

```bash
docker exec gestion-licitaciones-laravel_devcontainer-web-1 bash -c "chmod -R 777 storage bootstrap/cache"
```

### Puerto 8080 ocupado

Cambiá el mapeo de puertos en `.devcontainer/docker-compose.yml`:

```yaml
ports:
  - "8081:80"   # en vez de 8080:80
```

### Los tests fallan con "Base de datos no encontrada"

Los tests usan **SQLite en memoria** (`phpunit.xml`), no MySQL. Si ves errores
de conexión, revisá que `phpunit.xml` tenga:

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

### Error "The stream or file could not be opened" en logs

Los logs se escriben en `storage/logs/`. Si el container no tiene permisos:

```bash
docker exec gestion-licitaciones-laravel_devcontainer-web-1 bash -c "mkdir -p storage/logs && chmod 777 storage/logs"
```

### OAuth redirige a localhost pero no funciona

Verificá que las URIs de redirección en Google Cloud / GitHub Apps coincidan
exactamente con:

```
http://localhost:8080/auth/google/callback
http://localhost:8080/auth/github/callback
```

Si cambiás el puerto, actualizá también las URIs en los proveedores OAuth.
