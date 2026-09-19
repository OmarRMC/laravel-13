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

### Servidor, cola y scheduler juntos

El contenedor `app` levanta de una vez el servidor HTTP, el worker de colas (`queue:work`) y el scheduler (`schedule:work`) — ver `docker/start.sh`. Con el `docker compose up -d` de arriba ya alcanza: `compose.yml` lo arranca con `command: ["docker/start.sh", "${APP_ROLE:-all}"]`, y por defecto `APP_ROLE` es `all`.

El script acepta otros modos, por si se necesita correr una sola pieza (por ejemplo, separando cada proceso en su propio contenedor en un `compose.yml` de producción):

```bash
docker/start.sh app          # Solo el servidor HTTP
docker/start.sh queue        # Solo el worker de colas
docker/start.sh scheduler    # Solo el scheduler
docker/start.sh all          # Los tres juntos (el default)
```

Para levantar el contenedor `app` en uno de esos modos en vez del default, sin tocar `compose.yml`, se sobrescribe `APP_ROLE`:

```bash
APP_ROLE=queue docker compose up -d app        # Es un "docker compose up -d" normal, pero solo con el worker
APP_ROLE=scheduler docker compose up -d app    # o solo con el scheduler
docker compose up -d app                       # Sin la variable, vuelve al default (all)
```

> Ojo: `APP_ROLE=queue docker compose up -d app` **recrea** el contenedor `app` reemplazando el que esté corriendo (no lo suma) — sirve para fijar qué corre ese contenedor, no para levantar un worker extra en paralelo. Para eso último, con el contenedor `all` ya corriendo, se le puede pedir un segundo proceso encima con `docker compose exec app docker/start.sh queue` (por ejemplo, para escalar el worker de colas).

**Consideraciones:**
- Todo corre en un solo contenedor: si el `queue:work` de fondo se cae, el contenedor sigue "sano" (el servidor sigue respondiendo) y Docker no lo reinicia solo — revisa `docker compose logs app` si sospechas que dejó de procesar la cola. Para producción real, donde cada proceso deba reiniciarse por su cuenta, conviene separar `queue`/`scheduler` en servicios propios dentro de `compose.yml`, reusando la misma imagen y solo cambiando el `command`.
- Como `compose.yml` monta `./src` sobre `/var/www/html`, el permiso de ejecución de `docker/start.sh` depende del archivo en el host, no de lo que haga el `Dockerfile`. Si lo editas y deja de funcionar con `Permission denied`, corre `chmod +x src/docker/start.sh`.
- El modo `all` evita `exec` a propósito: si el servidor reemplazara al proceso de `bash` con `exec`, el `trap` que limpia `queue:work`/`schedule:work` al parar el contenedor dejaría de ejecutarse y `docker compose stop`/`down` tendrían que forzar el cierre (`SIGKILL`) tras 10s en vez de cerrar limpio.

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
