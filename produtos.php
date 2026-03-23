<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
require 'conexao.php';

$sql = "SELECT id, nome, preco_venda, detalhes, desconto, quantidade
        FROM produtos
        ORDER BY criado_em DESC
        LIMIT 5";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Produtos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container container-large">
    <div class="topbar">
        <div>
            <div class="titulo">Produtos Disponíveis</div>
            <div class="usuario-nome">Selecione um item para fazer um pedido.</div>
        </div>
        <div>
            <a href="dashboard.php" class="btn btn-secundario">Voltar ao Dashboard</a>
        </div>
    </div>

    <?php if ($result && $result->num_rows > 0): ?>
        <table class="tabela">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Preço</th>
                    <th>Desconto</th>
                    <th>Estoque</th>
                    <th>Detalhes</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php
                    $estoque = (int)$row['quantidade'];
                    $badgeClasse = $estoque > 5 ? 'ok' : 'baixo';
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['nome']) ?></td>
                    <td>R$ <?= number_format($row['preco_venda'], 2, ',', '.') ?></td>
                    <td><?= number_format($row['desconto'], 2, ',', '.') ?>%</td>
                    <td>
                        <span class="badge-estoque <?= $badgeClasse ?>">
                            <?= $estoque ?> unid.
                        </span>
                    </td>
                    <td style="max-width:260px;">
                        <?= nl2br(htmlspecialchars($row['detalhes'])) ?>
                    </td>
                    <td>
                        <form method="post" action="pedido.php" style="display:flex; gap:6px; align-items:center;">
                            <input type="hidden" name="produto_id" value="<?= (int)$row['id'] ?>">
                            <input type="number" name="quantidade" value="1" min="1" style="width:60px;">
                            <button type="submit" class="btn btn-primario">Fazer Pedido</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="mensagem erro">Nenhum produto encontrado. Cadastre um produto primeiro.</div>
    <?php endif; ?>

</div>
</body>
</html>