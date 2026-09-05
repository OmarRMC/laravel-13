# 📝 Tarea - Llenar la base de datos con factories y seeders

> **Proyecto:** *Agenda de Eventos e Inscripciones*
> **Tablas:** Usuarios, roles, perfiles, categorías, eventos

---

## 📑 Índice

| S | Sección |
|---|---|
| 0 | [Cómo entregar la tarea](#0-cómo-entregar-la-tarea) |
| 1 | [Enunciado](#1-enunciado) |
| 2 | [Reglas de la tarea](#2-reglas-de-la-tarea) |
| 3 | [Qué se debe tener al terminar](#3-qué-se-debe-tener-al-terminar) |
| 4 | [Comprobación](#4-comprobación) |
| 5 | [Casos que deben poder probarse](#5-casos-que-deben-poder-probarse) |

---

## 0. Cómo entregar la tarea

La entrega es por **Pull Request**, no por archivo suelto ni por commit directo a `main`. Pasos:

1. **Crear una rama** nueva a partir de `main`, con un nombre que diga de qué trata:
   ```bash
   git checkout main
   git pull
   git checkout -b tarea-seed-completo
   ```
2. **Escribir el código** de la tarea (factories y seeders) en esa rama.
3. **Comprobar** que `php artisan migrate:fresh --seed` funciona desde cero y que los números de la
   [sección 4](#4-comprobación) cuadran.
4. **Confirmar y subir** los cambios:
   ```bash
   git add .
   git commit -m "Añade factories y seeders de la tarea"
   git push -u origin tarea-seed-completo
   ```
5. **Crear el Pull Request** de `tarea-seed-completo` hacia `main` (desde GitHub o con `gh pr create`),
   describiendo brevemente qué se sembró.

> 💡 No hace falta nada más elaborado: una rama, sus commits, y un PR que se pueda revisar.

---

## 1. Enunciado

La aplicación gestiona **eventos**: unos usuarios los **organizan** y otros los consultan. Hay tres
roles (`admin`, `organizador`, `participante`) y cada evento tiene un **estado** (`borrador`,
`publicado`, `cerrado`, `cancelado`), que decide si se ve en público o no.

Ahora mismo, para probar cualquier pantalla hay que crear los datos a mano, y hay que repetirlo cada
vez que se borra la base.

**Tu tarea:** escribir los *factories* y *seeders* necesarios para que, con un solo comando,

```bash
php artisan migrate:fresh --seed
```

la base quede con **pocos datos, pero bien elegidos**: los justos para poder iniciar sesión con cada
rol y reproducir **todos los casos del S5** sin crear nada a mano.

> 🧠 **La idea.** No se trata de generar muchas filas, sino de que **cada situación importante tenga
> al menos una fila que la represente**: un evento publicado, otro en borrador, una cuenta
> desactivada, una categoría vacía. Con eso, la aplicación entera se puede recorrer.

---

## 2. Reglas de la tarea

**Se puede:** crear los factories y seeders que hagan falta, con los *states* que necesites, y usar
datos inventados para lo que no importa (títulos, descripciones, nombres).

**No se puede:**

- ❌ Cambiar el esquema de la base de datos.
- ❌ Cambiar el comportamiento de la aplicación (rutas, permisos, pantallas). Esta tarea **solo
  llena** la base.
- ❌ Insertar filas con SQL directo: todo pasa por modelos y relaciones.

**Obligatorio:**

- ✅ La siembra funciona **desde cero, de una vez y sin errores**.
- ✅ Todas las cuentas usan la contraseña **`password`**.

> 💡 **Fijo o inventado.** Si necesitas escribir el dato para probar (el email con el que inicias
> sesión, la categoría de una URL), va **fijo en un seeder**. Si solo tiene que existir y parecer
> real (un título, una descripción), lo genera una **factory**.

---

## 3. Qué se debe tener al terminar

### 3.1 Roles - 3

`admin`, `organizador` y `participante`. Los nombres son fijos: el programa pregunta por ellos
literalmente.

### 3.2 Usuarios - 6

Cuatro con **email fijo**, porque son con los que se inicia sesión:

| Email | Rol | Cuenta activa | Para qué sirve |
|---|---|---|---|
| `admin@socef.test` | `admin` | ✅ | Zona de administración |
| `organizador@socef.test` | `organizador` | ✅ | Panel; es el dueño de todos los eventos |
| `participante@socef.test` | `participante` | ✅ | Ver solo lo público |
| `baja@socef.test` | `participante` | ❌ **desactivada** | Comprobar que no puede entrar |

Y **2 participantes de relleno**, con nombre y email inventados, para que el listado de usuarios no
sea solo los cuatro de arriba.

### 3.3 Perfiles - 2

El perfil (teléfono, institución, biografía) es opcional y **como mucho uno por usuario**. Créalo
solo para el **administrador** y el **organizador**: así se comprueba que las pantallas también
aguantan a los usuarios que **no** tienen perfil.

### 3.4 Categorías - 3

Con nombre y enlace fijos, porque se usan para filtrar el catálogo:

`tecnologia` · `negocios` · `arte`

Cada una con su color. **Una de las tres se queda sin eventos** - hace falta para el caso 12.

### 3.5 Eventos - 6

Todos del mismo organizador (`organizador@socef.test`):

| Enlace | Estado | Fecha | Detalle |
|---|---|---|---|
| `laravel-13-desde-cero` | `publicado` | futura | gratuito, presencial |
| `curso-de-arte` | `publicado` | futura | **de pago**, precio 149,99 |
| `webinar-virtual` | `publicado` | futura | modalidad virtual, **sin hora de fin** |
| `jornada-pasada` | `publicado` | **pasada** | ya se celebró: no sale en el catálogo |
| `borrador-secreto` | `borrador` | futura | no debe verse en público |
| `cancelado-por-lluvia` | `cancelado` | futura | anulado |

Son **3 publicados y futuros** - los únicos que aparecen en el catálogo -, 1 publicado ya celebrado,
1 en borrador y 1 cancelado.

> ⚠️ **Dos detalles del esquema.** El enlace (*slug*) de cada evento es **único**, así que si generas
> títulos inventados tendrás que asegurarte de que no se repitan. Y la hora de fin, cuando exista,
> debe ser **posterior** a la de inicio: derívala de ella, no la generes por separado.

---

## 4. Comprobación

Siembra desde cero y comprueba estos números. Si alguno no cuadra, la tarea no está terminada.

| Consulta | Resultado |
|---|---|
| Usuarios | 6 |
| Roles | 3 |
| Perfiles | 2 |
| Categorías | 3 |
| Eventos | 6 |
| Eventos publicados que aún no han empezado | 3 |
| Eventos en borrador | 1 |
| Categorías sin ningún evento | 1 |
| Usuarios con la cuenta desactivada | 1 |
| Usuarios sin perfil | 4 |

---

## 5. Casos que deben poder probarse

Esta es la lista que de verdad se corrige. Cada fila es algo que hay que poder reproducir
**entrando en la aplicación**, sin tocar la base de datos. Si un caso no se puede montar, faltan
datos.

### Sin iniciar sesión

| # | Caso | Qué debe ocurrir |
|---|---|---|
| 1 | Ver el catálogo | Aparecen los **3** eventos publicados que aún no han empezado |
| 2 | Filtrar por una categoría con eventos | Solo los suyos |
| 3 | Filtrar por la categoría vacía | Listado vacío, sin error |
| 4 | Abrir la ficha de un evento publicado | Título, categoría, organizador y fechas |
| 5 | Abrir `borrador-secreto` | **404** - un borrador no es público |
| 6 | Abrir `cancelado-por-lluvia` | Se muestra como **cancelado** |
| 7 | Abrir `curso-de-arte` | Muestra el **precio**, no "Gratuito" |
| 8 | Abrir `webinar-virtual` | Se ve bien **sin hora de fin** |

### Con sesión iniciada

| # | Caso | Qué debe ocurrir |
|---|---|---|
| 9 | Entrar como `organizador@socef.test` a su panel | Sus **6** eventos, **incluido el borrador** |
| 10 | Entrar como `admin@socef.test` al listado de usuarios | Los 6, con sus roles |
| 11 | Cambiar los roles de un usuario | Se guardan los marcados y se quitan los demás |
| 12 | Borrar la categoría **sin eventos** | Se borra |
| 13 | Borrar la categoría `tecnologia` (**con eventos**) | Se **niega**, con un aviso |
| 14 | Entrar con `baja@socef.test` | Lo expulsa al inicio de sesión con un aviso |
| 15 | Entrar como `participante@socef.test` a la administración | **403** |
