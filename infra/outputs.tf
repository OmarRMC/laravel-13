output "instance_id" {
  description = "ID de la instancia EC2."
  value       = aws_instance.eventos_app.id
}

output "public_ip" {
  description = "IP publica elastica de la instancia (no cambia al reiniciar)."
  value       = aws_eip.eventos_app.public_ip
}

output "ssh_command" {
  description = "Comando listo para conectarte por SSH."
  value       = "ssh -i ${local_sensitive_file.private_key.filename} ubuntu@${aws_eip.eventos_app.public_ip}"
}

output "sslip_domain" {
  description = <<-EOT
    Dominio gratuito (sslip.io) que ya resuelve a tu IP elastica, sin comprar nada.
  EOT
  value = "${replace(aws_eip.eventos_app.public_ip, ".", "-")}.sslip.io"
}

output "private_key_path" {
  description = "Ruta local del archivo .pem generado."
  value       = local_sensitive_file.private_key.filename
}
