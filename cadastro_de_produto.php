<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
require 'conexao.php';

$mensagem = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome        = trim($_POST['nome'] ?? '');
    $preco_venda = $_POST['preco_venda'] ?? '';
    $detalhes    = trim($_POST['detalhes'] ?? '');
    $desconto    = $_POST['desconto'] ?? 0;
    $quantidade  = $_POST['quantidade'] ?? 0;

    if ($nome === "" || $preco_venda === "") {
        $mensagem = "Nome e preço de venda são obrigatórios.";
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO produtos (nome, preco_venda, detalhes, desconto, quantidade)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sdsdi", $nome, $preco_venda, $detalhes, $desconto, $quantidade);

        if ($stmt->execute()) {
            $mensagem = "Produto cadastrado com sucesso.";
        } else {
            $mensagem = "Erro ao cadastrar produto.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Produto</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container container-large">
    <div class="topbar">
        <div>
            <div class="titulo">Cadastro de Produto</div>
            <div class="usuario-nome">Adicione novos itens ao catálogo.</div>
        </div>
        <div>
            <a href="dashboard.php" class="btn btn-secundario">Voltar ao Dashboard</a>
        </div>
    </div>

    <?php if ($mensagem): ?>
        <div class="mensagem <?= ($mensagem === 'Produto cadastrado com sucesso.') ? 'sucesso' : 'erro' ?>">
            <?= htmlspecialchars($mensagem) ?>
        </div>
    <?php endif; ?>

    <form method="post" action="cadastro_de_produto.php">
        <div class="grupo-campo">
            <label>Nome do Produto</label>
            <input type="text" name="nome" required>
        </div>

        <div class="grupo-campo">
            <label>Preço de Venda (R$)</label>
            <input type="number" step="0.01" name="preco_venda" required>
        </div>

        <div class="grupo-campo">
            <label>Detalhes</label>
            <textarea name="detalhes" placeholder="Ex: tamanho da porção, ingredientes, etc."></textarea>
        </div>

        <div class="grupo-campo">
            <label>Desconto (%)</label>
            <input type="number" step="0.01" name="desconto" value="0">
        </div>

        <div class="grupo-campo">
            <label>Quantidade em estoque</label>
            <input type="number" name="quantidade" value="0">
        </div>

        <button type="submit" class="btn btn-primario">Salvar Produto</button>
    </form>
</div>
</body>
</html>