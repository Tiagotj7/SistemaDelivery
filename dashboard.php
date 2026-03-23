<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$nome = $_SESSION['usuario_nome'] ?? 'Usuário';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container container-large">
    <div class="topbar">
        <div>
            <div class="titulo">Painel de Delivery</div>
            <div class="usuario-nome">Bem-vindo, <?= htmlspecialchars($nome) ?></div>
        </div>
        <div>
            <a href="logout.php" class="btn btn-secundario">Sair</a>
        </div>
    </div>

<div class="nav-links">
    <a href="cadastro_de_produto.php" class="btn btn-primario">Cadastrar Produto</a>
    <a href="produtos.php" class="btn btn-secundario">Listar Produtos / Fazer Pedido</a>
    <a href="listar_pedidos.php" class="btn btn-secundario">Gerenciar Pedidos</a>
</div>

    <p class="subtitulo">
        Use o menu acima para gerenciar produtos e simular pedidos em tempo real.
    </p>
</div>
</body>
</html>