#!/bin/bash
set -e

# Modo "all": levanta el worker de colas y el scheduler de fondo, y el
# servidor HTTP al frente (PID 1 del contenedor, recibe las senales de Docker).
# El trap mata los procesos de fondo cuando Docker para el contenedor
# (sin esto quedan huerfanos, como paso durante las pruebas de reintentos).
iniciar_todo() {
    # Sin "exec" aqui: el trap necesita que bash siga siendo el proceso (PID 1)
    # para poder atraparlo. Con "exec" bash se reemplaza a si mismo y el
    # trap nunca se ejecuta (asi termino matando el contenedor a la fuerza).
    trap 'kill $(jobs -p) 2>/dev/null; exit 0' TERM INT

    php artisan queue:work --tries=3 &
    php artisan schedule:work &
    php artisan serve --host=0.0.0.0 --port=8000 &

    wait
}

case "$1" in
    all)
        iniciar_todo
        ;;
    queue)
        exec php artisan queue:work --tries=3
        ;;
    scheduler)
        exec php artisan schedule:work
        ;;
    app | *)
        exec php artisan serve --host=0.0.0.0 --port=8000
        ;;
esac
