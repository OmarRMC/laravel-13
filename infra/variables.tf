variable "aws_region" {
  description = "Region de AWS donde se crean los recursos. Afecta que AMI de Ubuntu esta disponible."
  type        = string
  default     = "us-east-1"
}

variable "aws_profile" {
  description = <<-EOT
    Nombre del perfil de AWS CLI a usar (el que configuraste con "aws configure --profile nombre",
    guardado en ~/.aws/credentials). Dejar en null usa la cadena de credenciales por defecto:
    variable de entorno AWS_PROFILE, perfil "default", o un rol IAM si corres esto desde una
    instancia/CI que ya tiene uno asignado.
  EOT
  type    = string
  default = null
}

variable "project_name" {
  description = "Prefijo usado para nombrar los recursos (Security Group, key pair, tags). No debe llevar espacios."
  type        = string
  default     = "eventos-app"
}

variable "instance_type" {
  description = <<-EOT
    Tipo de instancia EC2. t3.small es el recomendado por la Sesion 13: app + base de datos + worker
    de colas corren en la MISMA maquina, y t3.micro se queda corto de RAM para los tres a la vez.
    Importante: t3.small NO esta en la capa gratuita de 12 meses de AWS (esa cubre t2.micro/t3.micro).
  EOT
  type    = string
  default = "t3.micro"
}

variable "root_volume_size_gb" {
  description = "Tamano del disco raiz (EBS) en GB. Ahi vive tambien el volumen de Postgres (S16 del guion)."
  type        = number
  default     = 20
}

variable "admin_cidr" {
  description = <<-EOT
    Tu IP publica en formato CIDR (ej. "203.0.113.10/32"), para restringir el puerto 22 (SSH) a
    solo tu maquina -nunca 0.0.0.0/0. Consiguela con: curl https://checkip.amazonaws.com
  EOT
  type = string

  validation {
    condition     = can(cidrhost(var.admin_cidr, 0))
    error_message = "admin_cidr debe ser un bloque CIDR valido, ej. 203.0.113.10/32 (nota el /32 al final)."
  }
}
