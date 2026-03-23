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

// busca pedido
$stmt = $conn->prepare("
    SELECT produto_id, quantidade 
    FROM pedidos 
    WHERE id = ? AND usuario_id = ?
");
$stmt->bind_param("ii", $id, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$pedido = $result->fetch_assoc();
$stmt->close();

if (!$pedido) {
    die("Pedido não encontrado");
}

// devolve estoque
$stmt = $conn->prepare("
    UPDATE produtos 
    SET quantidade = quantidade + ? 
    WHERE id = ?
");
$stmt->bind_param("ii", $pedido['quantidade'], $pedido['produto_id']);
$stmt->execute();
$stmt->close();

// exclui
$stmt = $conn->prepare("DELETE FROM pedidos WHERE id = ? AND usuario_id = ?");
$stmt->bind_param("ii", $id, $usuario_id);

if ($stmt->execute()) {
    header("Location: listar_pedidos.php?msg=excluido");
    exit;
} else {
    die("Erro ao excluir");
}