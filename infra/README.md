# Infraestructura AWS · `eventos-app`

Terraform que aprovisiona la instancia **EC2 + Security Group + IP elástica**. No instala Docker ni
despliega la aplicación: eso se hace a mano por SSH (conectar, instalar Docker, clonar el repo,
configurar `.env` y levantar `docker compose up`) — así el mecanismo de despliegue se ve completo, en
vez de esconderlo detrás de un script.

## Qué crea

| Recurso | Detalle |
|---|---|
| Instancia EC2 | Ubuntu 24.04 LTS (AMI más reciente, resuelta automáticamente), `t3.small` por defecto |
| Security Group | Puerto 22 (SSH) solo desde tu IP · 80 y 443 abiertos al público |
| Key pair | Terraform genera el par de llaves; la privada se guarda local como `eventos-app-key.pem` |
| IP elástica | Para que la IP pública no cambie si la instancia se reinicia |
| Red | VPC y subred **por defecto** de la cuenta/región (no crea VPC propia) |

**No crea:** base de datos gestionada (RDS), dominio, ni nada de Docker/la app — eso es intencional
(ver la decisión de arquitectura en `SESION-13-PLANIFICACION.md`).

## Requisitos

- [Terraform](https://developer.hashicorp.com/terraform/install) >= 1.5
- Una cuenta de AWS con credenciales configuradas localmente, de cualquiera de estas formas:
  - `aws configure` (si tienes el [AWS CLI](https://docs.aws.amazon.com/cli/latest/userguide/getting-started-install.html) instalado), o
  - las variables de entorno `AWS_ACCESS_KEY_ID` / `AWS_SECRET_ACCESS_KEY` (y `AWS_SESSION_TOKEN` si usas credenciales temporales)

Terraform usa la misma cadena de credenciales que el AWS CLI — no hay que pasarle nada aparte.

### ¿Y si tengo varios perfiles de AWS configurados?

Si ya tienes más de una cuenta/rol en `~/.aws/credentials` (perfiles con nombre, no el `default`), hay
dos formas de decirle a Terraform cuál usar — con cualquiera de las dos basta:

**Opción A — variable de entorno** (no requiere tocar ningún archivo `.tf`):
```bash
export AWS_PROFILE=nombre-del-perfil
terraform plan
```

**Opción B — dentro de `terraform.tfvars`** (queda guardado para no repetirlo cada vez):
```hcl
aws_profile = "nombre-del-perfil"
```
`main.tf`/`versions.tf` ya tiene la variable `aws_profile` cableada al provider; si la dejas sin
definir (`null`), usa la cadena de credenciales por defecto (perfil `default`, variables de entorno,
o un rol IAM si corres esto desde una instancia/CI que ya tenga uno asignado).

> Si defines `AWS_PROFILE` **y** `aws_profile` en `terraform.tfvars` al mismo tiempo, gana el valor de
> `terraform.tfvars` (el argumento explícito del provider tiene prioridad sobre la variable de entorno).

¿No tienes ningún perfil creado todavía? `aws configure --profile nombre-del-perfil` te pide el
Access Key ID, el Secret Access Key y la región, y los guarda en `~/.aws/credentials`.

## Uso

```bash
cd infra

# 1. Copiar el archivo de variables y editarlo
cp terraform.tfvars.example terraform.tfvars
# Consigue tu IP publica y pegala en admin_cidr dentro de terraform.tfvars:
curl https://checkip.amazonaws.com

# 2. Descargar los providers (aws, tls, local)
terraform init

# 3. Revisar qué se va a crear (no crea nada todavía)
terraform plan

# 4. Crear la infraestructura de verdad
terraform apply
```

Al terminar, Terraform imprime los `outputs`:

```bash
terraform output                  # ver todos
terraform output -raw ssh_command # solo el comando SSH, listo para copiar/pegar
```

- `ssh_command` — cómo conectarte a la instancia.
- `public_ip` — la IP elástica (para el registro DNS si tienes dominio propio).
- `sslip_domain` — un dominio **gratuito** que ya apunta a tu IP (via [sslip.io](https://sslip.io)),
  útil para el `Caddyfile` del proxy inverso si todavía no compraste un dominio.
- `private_key_path` — dónde quedó guardada la llave privada (`eventos-app-key.pem`, en esta misma
  carpeta, con permisos `0400`).

## Comandos básicos de Terraform

Referencia rápida de los comandos que vas a usar en esta carpeta, más allá del flujo de `## Uso`.

### Flujo de trabajo (en orden)

| Comando | Qué hace |
|---|---|
| `terraform init` | Prepara la carpeta: descarga los providers (`aws`, `tls`, `local`) y configura el backend del estado. Se corre una vez, y de nuevo si cambias `versions.tf` |
| `terraform validate` | Revisa que la sintaxis y los tipos sean correctos, **sin** conectarse a AWS. Rápido, úsalo mientras editas |
| `terraform fmt` | Reordena la indentación y el alineado de los archivos `.tf` a la convención estándar de Terraform |
| `terraform plan` | Calcula y muestra qué va a **crear, cambiar o destruir**, comparando el código contra el estado actual. No modifica nada todavía |
| `terraform apply` | Ejecuta el plan: crea o actualiza los recursos de verdad en AWS. Pide confirmación (escribir `yes`) antes de aplicar |
| `terraform apply -auto-approve` | Igual que arriba, pero sin pedir confirmación. Útil en scripts/CI; evitarlo mientras aprendes, para poder leer el plan antes de aceptar |
| `terraform destroy` | Borra **todos** los recursos que este código gestiona. También pide confirmación |

### Inspeccionar lo que ya existe

| Comando | Qué hace |
|---|---|
| `terraform output` | Imprime todos los valores de `outputs.tf` (`ssh_command`, `public_ip`, `sslip_domain`...) |
| `terraform output -raw <nombre>` | Imprime un solo output, sin comillas — ideal para usarlo dentro de otro comando (`$(terraform output -raw public_ip)`) |
| `terraform state list` | Lista los recursos que Terraform está gestionando ahora mismo (ej. `aws_instance.eventos_app`) |
| `terraform state show <recurso>` | Muestra todos los atributos guardados de un recurso puntual, tal como quedó en AWS |
| `terraform show` | Vuelca el estado completo, con detalle — el equivalente a leer `terraform.tfstate` pero legible |

> 🧠 **La diferencia que más importa al aprender Terraform:** `plan` **nunca** toca AWS -es una
> simulación de lectura; `apply` y `destroy` sí crean o borran recursos reales, que pueden costar
> dinero. Por eso el flujo siempre es `plan` para revisar y **recién después** `apply`.

## Siguiente paso

Con la instancia lista, seguir manualmente por SSH: conectar (`terraform output -raw ssh_command`),
instalar Docker, clonar el repo, configurar `.env` y levantar `docker compose up`.

## Destruir todo

**Importante:** `t3.small` no está en la capa gratuita de 12 meses de AWS (esa cubre
`t2.micro`/`t3.micro`) — revisa el [pricing de EC2](https://aws.amazon.com/ec2/pricing/on-demand/)
antes de dejarla corriendo. Cuando termines el laboratorio:

```bash
terraform destroy
```

Esto borra la instancia, el Security Group, la IP elástica y el key pair — no queda nada facturando.

## Archivos

| Archivo | Contenido |
|---|---|
| `versions.tf` | Versión de Terraform y de los providers (`aws`, `tls`, `local`) |
| `variables.tf` | Variables de entrada (`admin_cidr` es obligatoria, el resto tiene default) |
| `main.tf` | Los recursos: red, AMI, llave SSH, Security Group, instancia, IP elástica |
| `outputs.tf` | Valores que Terraform imprime al terminar (`ssh_command`, `public_ip`, etc.) |
| `terraform.tfvars.example` | Plantilla de variables — copiar a `terraform.tfvars` (que sí está en `.gitignore`) |

`*.tfstate`, `*.tfvars`, `.terraform/` y `*.pem` están en el `.gitignore` de la raíz del repo: nunca se
suben a git, porque el estado de Terraform y la llave privada pueden contener información sensible.
