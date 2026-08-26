# laravel-13

Entorno de desarrollo de Laravel 13 sobre Docker: PHP-CLI (servidor embebido de `artisan serve`) + PostgreSQL 17.

| Servicio | Contenedor | Puerto host | Descripción |
|---|---|---|---|
| `app` | `laravel13-app` | `8000` (`APP_PORT`) | Laravel + Composer + Node/npm (Vite) |
| `db`  | `postgres:17-alpine` | `5432` (`FORWARD_DB_PORT`) | Base de datos |
| Vite  | dentro de `app` | `5173` (`VITE_PORT`) | HMR de assets |

El código vive en `./src` y se monta en `/var/www/html` dentro del contenedor.

## Requisitos

- Docker + Docker Compose v2
- Puertos 8000, 5173 y 5432 libres

## Puesta en marcha

```bash
# 1. Variables del entorno Docker (PHP_VERSION, UID/GID, puertos)
cp .env.example .env   # si no existe;

# 2. Construir la imagen y levantar los servicios
docker compose up -d --build

# 3. Dependencias y clave de la app (primera vez)
docker compose exec app composer install
docker compose exec app cp -n .env.example .env
docker compose exec app php artisan key:generate

# 4. Migraciones
docker compose exec app php artisan migrate

# 5. Assets
docker compose exec app npm install
docker compose exec app npm run build
```

Aplicación disponible en http://localhost:8000

> El `UID`/`GID` del `.env` deben coincidir con los de tu usuario (`id -u` / `id -g`) para que los archivos generados por artisan te pertenezcan.

## Comandos imprescindibles

### Ciclo de vida de los contenedores

```bash
docker compose up -d              # Levantar en segundo plano
docker compose up -d --build      # Levantar reconstruyendo la imagen
docker compose ps                 # Estado de los servicios
docker compose logs -f app        # Ver logs en vivo (Ctrl+C para salir)
docker compose stop               # Parar sin borrar
docker compose down               # Parar y eliminar contenedores
docker compose down -v            # Igual + borra el volumen de PostgreSQL (¡pierdes los datos!)
docker compose restart app        # Reiniciar solo la app
```

### Entrar al contenedor

```bash
docker compose exec app bash      # Shell dentro de la app
docker compose exec db psql -U laravel -d laravel   # Consola de PostgreSQL
```

Dentro del shell puedes ejecutar `php artisan …`, `composer …` y `npm …` sin el prefijo `docker compose exec app`.

### Artisan

```bash
docker compose exec app php artisan list           # Todos los comandos disponibles
docker compose exec app php artisan about          # Info del entorno
docker compose exec app php artisan tinker         # REPL interactivo

# Migraciones y datos
docker compose exec app php artisan migrate
docker compose exec app php artisan migrate:status
docker compose exec app php artisan migrate:rollback
docker compose exec app php artisan migrate:fresh --seed   # Recrea el esquema y siembra
docker compose exec app php artisan db:seed

# Generadores
docker compose exec app php artisan make:model Post -mfc   # modelo + migración + factory + controlador
docker compose exec app php artisan make:controller PostController --resource
docker compose exec app php artisan make:request StorePostRequest
docker compose exec app php artisan make:migration create_posts_table

# Rutas y caché
docker compose exec app php artisan route:list
docker compose exec app php artisan optimize:clear   # Limpia config, rutas, vistas y caché
docker compose exec app php artisan storage:link
```

### Frontend (Vite)

```bash
docker compose exec app npm install
docker compose exec app npm run build                       # Build de producción
docker compose exec app npm run dev -- --host 0.0.0.0       # HMR accesible desde el host
```

> El `--host 0.0.0.0` es necesario: sin él Vite solo escucha dentro del contenedor y el puerto 5173 no responde.

### Composer

```bash
docker compose exec app composer install
docker compose exec app composer update
docker compose exec app composer require vendor/paquete
docker compose exec app composer dump-autoload
```

### Tests y calidad

```bash
docker compose exec app php artisan test              # Suite completa
docker compose exec app php artisan test --filter=NombreDelTest
docker compose exec app ./vendor/bin/pint             # Formatear código (Laravel Pint)
docker compose exec app ./vendor/bin/pint --test      # Solo comprobar, sin modificar
docker compose exec app php artisan pail              # Logs de la app en vivo
```

## Variables de entorno

Hay dos `.env` distintos y cada uno cumple su función:

- **`./.env`** → configura Docker: `PHP_VERSION`, `UID`, `GID`, `APP_PORT`, `VITE_PORT`, `FORWARD_DB_PORT`, `POSTGRES_*`.
- **`./src/.env`** → configura Laravel. La conexión a la base de datos debe apuntar al servicio, no a `localhost`:

```dotenv
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret
```

Si cambias `POSTGRES_*` en `./.env`, actualiza también los `DB_*` de `./src/.env`.

## Problemas frecuentes

| Síntoma | Solución |
|---|---|
| `SQLSTATE[08006] could not connect` | `DB_HOST` debe ser `db`, no `127.0.0.1`. |
| Permisos en `storage/` o `bootstrap/cache` | Ajusta `UID`/`GID` en `./.env` y reconstruye: `docker compose up -d --build`. |
| `http://localhost:5173` no responde | Arranca Vite con `--host 0.0.0.0`. |
| Cambios de config que no se aplican | `docker compose exec app php artisan optimize:clear`. |
| Puerto 8000 ocupado | Cambia `APP_PORT` en `./.env` y `docker compose up -d`. |
