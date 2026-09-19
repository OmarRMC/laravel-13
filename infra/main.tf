locals {
  tags = {
    Project   = var.project_name
    ManagedBy = "terraform"
    Curso     = "laravel-13-sesion-13"
  }
}

# Red: se reutiliza la VPC por defecto de la cuenta/region
data "aws_vpc" "default" {
  default = true
}

data "aws_subnets" "default" {
  filter {
    name   = "vpc-id"
    values = [data.aws_vpc.default.id]
  }
}


# AMI: la Ubuntu 24.04 LTS mas reciente, resuelta en cada apply
data "aws_ami" "ubuntu" {
  most_recent = true
  owners      = ["099720109477"] # Canonical

  filter {
    name   = "name"
    values = ["ubuntu/images/hvm-ssd-gp3/ubuntu-noble-24.04-amd64-server-*"]
  }

  filter {
    name   = "virtualization-type"
    values = ["hvm"]
  }
}


# Llave SSH: Terraform la genera y sube solo la parte publica a AWS. La
# privada se guarda en disco local (nunca en git, ver .gitignore).
resource "tls_private_key" "ssh" {
  algorithm = "ED25519"
}

resource "aws_key_pair" "eventos_app" {
  key_name   = "${var.project_name}-key"
  public_key = tls_private_key.ssh.public_key_openssh

  tags = local.tags
}

resource "local_sensitive_file" "private_key" {
  content         = tls_private_key.ssh.private_key_openssh
  filename        = "${path.module}/${var.project_name}-key.pem"
  file_permission = "0400"
}


# Security Group
resource "aws_security_group" "eventos_app" {
  name        = "${var.project_name}-sg"
  description = "SSH restringido a admin_cidr; HTTP/HTTPS publicos para ${var.project_name}"
  vpc_id      = data.aws_vpc.default.id

  ingress {
    description = "SSH - solo desde tu IP"
    from_port   = 22
    to_port     = 22
    protocol    = "tcp"
    cidr_blocks = [var.admin_cidr]
  }

  ingress {
    description = "HTTP - Caddy redirige a HTTPS"
    from_port   = 80
    to_port     = 80
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    description = "HTTPS - trafico real de la app"
    from_port   = 443
    to_port     = 443
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  egress {
    description = "Salida libre (apt, docker pull, composer, SMTP...)"
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = local.tags
}


# Instancia: Ubuntu 24.04, sin user_data -Docker/clonar/desplegar se hace a
# mano por SSH siguiendo
resource "aws_instance" "eventos_app" {
  ami                         = data.aws_ami.ubuntu.id
  instance_type               = var.instance_type
  subnet_id                   = data.aws_subnets.default.ids[0]
  vpc_security_group_ids      = [aws_security_group.eventos_app.id]
  key_name                    = aws_key_pair.eventos_app.key_name
  associate_public_ip_address = true

  root_block_device {
    volume_type            = "gp3"
    volume_size            = var.root_volume_size_gb
    delete_on_termination  = true
  }

  tags = merge(local.tags, { Name = var.project_name })
}


# IP elastica: para que la IP publica no cambie si la instancia se reinicia
resource "aws_eip" "eventos_app" {
  instance = aws_instance.eventos_app.id
  domain   = "vpc"

  tags = local.tags
}
