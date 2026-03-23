<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
require 'conexao.php';

$usuario_id = $_SESSION['usuario_id'];
$status = "erro";
$mensagem = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produto_id = (int)($_POST['produto_id'] ?? 0);
    $quantidade = (int)($_POST['quantidade'] ?? 1);

    if ($produto_id <= 0 || $quantidade <= 0) {
        $mensagem = "Dados inválidos para pedido.";
    } else {
        // Verifica estoque
        $stmt = $conn->prepare("SELECT quantidade, nome FROM produtos WHERE id = ?");
        $stmt->bind_param("i", $produto_id);
        $stmt->execute();
        $stmt->bind_result($estoque, $nome_produto);
        if ($stmt->fetch()) {
            $stmt->close();
            if ($estoque >= $quantidade) {
                // Cria pedido
                $stmt = $conn->prepare(
                    "INSERT INTO pedidos (usuario_id, produto_id, quantidade, status)
                     VALUES (?, ?, ?, 'sucesso')"
                );
                $stmt->bind_param("iii", $usuario_id, $produto_id, $quantidade);
                if ($stmt->execute()) {
                    // Atualiza estoque
                    $stmt->close();
                    $stmt = $conn->prepare(
                        "UPDATE produtos SET quantidade = quantidade - ? WHERE id = ?"
                    );
                    $stmt->bind_param("ii", $quantidade, $produto_id);
                    $stmt->execute();
                    $stmt->close();

                    $status = "sucesso";
                    $mensagem = "Pedido do produto '{$nome_produto}' realizado com sucesso.";
                } else {
                    $mensagem = "Falha ao registrar pedido.";
                }
            } else {
                $mensagem = "Estoque insuficiente para o produto selecionado.";
            }
        } else {
            $mensagem = "Produto não encontrado.";
        }
    }
} else {
    $mensagem = "Método inválido.";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Resultado do Pedido</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Resultado do Pedido</h1>
    <p class="subtitulo">Veja abaixo o status da sua solicitação.</p>

    <div class="mensagem <?= $status === 'sucesso' ? 'sucesso' : 'erro' ?>">
        <strong>Status:</strong> <?= htmlspecialchars($status) ?><br>
        <?= htmlspecialchars($mensagem) ?>
    </div>

    <div style="display:flex; flex-direction:column; gap:8px; margin-top:8px;">
        <a href="produtos.php" class="btn btn-primario btn-block">Voltar à lista de produtos</a>
        <a href="dashboard.php" class="btn btn-secundario btn-block">Ir para o Dashboard</a>
    </div>
</div>
</body>
</html>