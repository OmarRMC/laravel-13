# Arquitectura y funcionalidades — Agenda de Eventos e Inscripciones

## 1. Qué es

Aplicación de gestión de eventos e inscripciones. Alguien publica un evento, otras personas se
inscriben, y el organizador pasa lista y emite certificados. Monolito Laravel (PHP 8.3+) con Blade +
Tailwind + Alpine.js en el front (sin framework SPA), PostgreSQL como base de datos, Redis como backend
de colas, todo corriendo en Docker Compose en local y en una instancia EC2 en producción.

## 2. Vista general de la arquitectura

```
Cliente (navegador)              Cliente API/MCP (Postman, asistente de IA, etc.)
      │ sesión/cookie                    │ Bearer token
      ▼                                  ▼
  Rutas web (Blade)                Rutas API (/api/v1)  /  Rutas MCP
      │                                  │                    │
      ▼                                  ▼                    ▼
  Controladores web          Controladores API          Servidor MCP
  (Blade + validación)       (JSON + Sanctum)      (Tools / Resources / Prompts)
      └──────────────┬───────────────────┴────────────────────┘
                      ▼
         Modelos Eloquent ── Policies ── Middleware
                      │
      ┌───────────────┼─────────────────┬───────────────────┐
      ▼               ▼                 ▼                   ▼
  PostgreSQL        Redis         Colas / Correo      Almacenamiento
  (datos)        (colas/caché)   (envío asíncrono)   (afiches, S3-compatible)
```

## 3. Dominios funcionales

### Eventos y catálogo
- Listado público filtrable por categoría; ficha de evento accesible por un slug legible (no por id
  numérico).
- CRUD completo para quien organiza, restringido a sus propios eventos (un administrador puede
  gestionar cualquiera).
- Las reglas de negocio para poder inscribirse (evento publicado, fecha no vencida, cupo disponible)
  están centralizadas en un único método del modelo, reutilizado tanto por el middleware que protege
  las rutas HTTP como por la herramienta de inscripción del servidor MCP — evita tener la misma regla
  duplicada en dos sitios que podrían desincronizarse.

### Inscripciones
- Alta y cancelación de inscripción por parte del usuario, con un listado de "mis inscripciones".
- Panel del organizador: lista de inscritos por evento y marcado de asistencia (con verificación de
  que la persona marcada esté realmente inscrita en ese evento concreto).
- Certificado de participación en PDF, que solo se genera si hay asistencia registrada.

### Administración
- Gestión de categorías y de usuarios, en una zona aparte protegida por permisos de administrador.

### Autenticación y usuarios
- Registro, login, verificación de email y recuperación de contraseña.
- Roles propios (no un paquete externo) con una tabla de roles y una tabla puente usuario-rol.
- Los usuarios pueden desactivarse; un middleware bloquea a los usuarios inactivos en cada request
  protegida.
- Perfil extendido de usuario, además del perfil básico de cuenta.
- Cada usuario puede emitir y revocar, desde su propio perfil, un token de API que también sirve para
  autenticarse contra el servidor MCP.

### Reportes y exportación
- Reporte de inscritos por evento en PDF.
- Reporte de inscritos por evento en Excel.

### Correo y procesos en segundo plano
- Correo de confirmación al inscribirse y correo de recordatorio antes del evento.
- Un job en cola que envía los recordatorios de forma asíncrona (Redis como backend de colas).
- Dos tareas programadas: una diaria que dispara los recordatorios, y otra horaria que cierra
  automáticamente los eventos cuya fecha ya pasó.

### API REST
- Endpoints públicos de solo lectura para eventos y categorías.
- Endpoints protegidos con token (login, logout, mis inscripciones, inscribirse a un evento), sujetos
  a las mismas reglas de negocio y al mismo filtro de "usuario activo" que la parte web.

### Integración con IA (servidor MCP)
- Un servidor expone la agenda de eventos como herramientas para un asistente de IA: buscar eventos,
  listar categorías, consultar las propias inscripciones e inscribirse a un evento.
- Expone además un recurso (el programa de un evento) y un prompt guiado (invitar a un amigo a un
  evento).
- Accesible en dos modos: local (para un cliente de línea de comandos o un inspector) y por HTTP,
  protegido con el mismo sistema de tokens que la API REST.
- Diseñado para no filtrar datos entre usuarios: "mis inscripciones" siempre se resuelve a partir del
  usuario dueño del token de la petición, nunca de un parámetro que el cliente pudiera manipular.

### Internacionalización
- Selector de idioma y detección del idioma activo por request, con las cadenas de texto traducidas.

## 4. Modelo de datos (resumen)

- **Usuario** 1—1 **Perfil** (datos extendidos).
- **Usuario** 1—N **Evento** (como organizador).
- **Usuario** N—M **Evento** a través de una tabla de inscripciones, que además guarda datos propios de
  la relación: un código de inscripción, el estado de la inscripción y si hubo asistencia.
- **Usuario** N—M **Rol** a través de una tabla puente simple.
- **Evento** N—1 **Categoría**.
- Colas y caché resueltas en tablas propias (trabajos en cola, caché) además de Redis.

## 5. Capas de permisos

1. **Middleware de sesión**: exige estar autenticado y con la cuenta activa para cualquier zona
   privada.
2. **Autorización por acción** (policy del evento): un administrador puede todo; el resto solo puede
   editar/gestionar los eventos que organiza.
3. **Reglas de negocio del propio dominio**: un evento puede rechazar una inscripción aunque el usuario
   tenga permiso de sobra, si ya no está publicado, si ya empezó o si no quedan cupos.

## 6. Infraestructura

- **Local**: Docker Compose con tres servicios — la aplicación (PHP), la base de datos (PostgreSQL) y
  Redis. Un script de arranque soporta distintos modos (solo servidor web, solo worker de colas, solo
  planificador de tareas, o los tres juntos).
- **Producción**: una instancia en la nube (AWS EC2) aprovisionada con Terraform (infraestructura como
  código) — crea la instancia, el grupo de seguridad con los puertos estrictamente necesarios y una IP
  pública fija. La instalación de Docker y el despliegue de la aplicación dentro de esa instancia se
  hacen aparte, por conexión remota segura.
- **Almacenamiento**: compatible con almacenamiento de objetos tipo S3 para archivos (afiches, PDFs
  generados).

## 7. Integraciones y librerías externas relevantes

| Pieza | Rol |
|---|---|
| PostgreSQL | Base de datos principal |
| Redis | Backend de colas y caché |
| Sistema de tokens de API | Autenticación de la API REST y del servidor MCP |
| Librería de generación de PDF | Certificados y reportes |
| Librería de exportación a Excel | Reporte de inscritos |
| Librería de almacenamiento compatible S3 | Archivos subidos por los usuarios |
| Docker Compose | Orquestación del entorno de ejecución |
| Terraform | Aprovisionamiento de la infraestructura en la nube |

## 8. Pendientes / posibles siguientes pasos

- Ampliar la cobertura de pruebas automatizadas más allá de lo mínimo.
- Confirmar si todos los correos deberían enviarse por cola (algunos podrían estar síncronos todavía).
- Backend remoto para el estado de Terraform si en algún momento más de una persona administra la
  infraestructura.
- Base de datos gestionada aparte de la instancia, si el proyecto necesita alta disponibilidad o
  backups automáticos sin gestionarlos a mano.
