<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
require 'conexao.php';

$id = (int)($_POST['id'] ?? 0);
$usuario_id = $_SESSION['usuario_id'];

if ($id <= 0) {
    die("ID inválido");
}

$stmt = $conn->prepare("
    UPDATE pedidos 
    SET status = 'concluido' 
    WHERE id = ? AND usuario_id = ?
");
$stmt->bind_param("ii", $id, $usuario_id);

if ($stmt->execute()) {
    header("Location: listar_pedidos.php?msg=concluido");
    exit;
} else {
    die("Erro ao concluir pedido");
}