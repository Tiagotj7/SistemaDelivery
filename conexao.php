<?php
$host = "localhost:3307";
$usuario = "root";      // ajuste se usar outro usuário
$senha = "";            // ajuste se tiver senha
$banco = "delivery";

$conn = new mysqli($host, $usuario, $senha, $banco);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>