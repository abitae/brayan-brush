# Brayan Brush

Sistema de **logística y transporte terrestre** que integra sitio web corporativo, panel de administración y API de operaciones (encomiendas, rastreo, facturación electrónica).

## Estructura del repositorio

| Carpeta | Descripción | Stack |
|--------|-------------|--------|
| **frontend_brayan** | Sitio web público (Brayan Brush) + panel admin. Páginas: inicio, servicios, rastreo, cotizador, contacto, reclamos. Admin: branding, servicios, precios por ruta, prohibiciones, cotizaciones, asistente IA. | Laravel 12, Inertia.js, React, TypeScript, Tailwind CSS, Fortify, reCAPTCHA v3 |
| **system_brayan_v1** | Sistema operativo (versión 1). Gestión de encomiendas, caja, facturación, reportes. | Laravel, Livewire |
| **system_brayan_v2** | Sistema operativo (versión 2). Encomiendas, facturación electrónica (Greenter), API REST (JWT), Excel, PDF. | Laravel 12, Livewire, Volt, Flux, Greenter, JWT, Spatie Permission |

El **frontend_brayan** consume la API de rastreo del sistema (por ejemplo `system_brayan` / `system_brayan_v2`) configurando la URL en el panel admin (ej. `https://system_brayan.test/api/frontend/tracking`).

---

## Requisitos previos

- **PHP** >= 8.2  
- **Composer**  
- **Node.js** >= 18 y **npm**  
- **Base de datos**: MySQL >= 8.2 o PostgreSQL (según el proyecto)  
- **Extensiones PHP**: pdo, xml, curl, zip, mbstring, etc.

---

## frontend_brayan (sitio web + admin)

Sitio corporativo con Inertia + React. Incluye rastreo por código (con reCAPTCHA v3), cotizador, formularios de contacto/cotización y panel de administración.

### Instalación

```bash
cd frontend_brayan
cp .env.example .env
php artisan key:generate
```

Configura en `.env` la base de datos y, si usas rastreo externo y/o asistente IA:

- `TRACKING_API_URL` o la URL de rastreo desde el panel admin  
- Para reCAPTCHA (formulario de rastreo): `RECAPTCHA_SITE_KEY`, `RECAPTCHA_SECRET_KEY`  
- Para asistente IA: `GEMINI_API_KEY` y/o `OPENAI_API_KEY` (según config en admin)

```bash
composer install
npm install
php artisan migrate
npm run build
```

### Desarrollo

```bash
# Terminal 1
php artisan serve

# Terminal 2
npm run dev
```

Acceso: `http://localhost:8000`. Admin: `/admin` (requiere login). Tras el login se redirige a `/admin`.

---

## system_brayan_v1 / system_brayan_v2

Sistemas internos de operaciones. Consulta el **README.md** dentro de cada carpeta para requisitos, instalación y comandos específicos.

```bash
cd system_brayan_v1   # o system_brayan_v2
cp .env.example .env
php artisan key:generate
composer install
# Configurar DB y variables en .env
php artisan migrate --seed
php artisan serve
```

La API de rastreo para el frontend suele estar en una ruta como:

- `GET /api/frontend/tracking?code=CODIGO`

Respuesta esperada (entre otros): `code`, `estado_encomienda`, `name_origen`, `name_destino`, `lugar_origen`, `lugar_destino`, `direccion_envio`, `isHome`, fechas.

---

## Licencia

Los proyectos pueden tener licencia MIT u otra según cada `composer.json`. Revisa el archivo en cada carpeta.
