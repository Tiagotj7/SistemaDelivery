<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
require 'conexao.php';

$usuario_id = $_SESSION['usuario_id'];

// Busca pedidos do usuário logado
$sql = "
    SELECT p.id, p.quantidade, p.status, p.criado_em,
           pr.nome AS nome_produto
    FROM pedidos p
    JOIN produtos pr ON pr.id = p.produto_id
    WHERE p.usuario_id = ?
    ORDER BY p.criado_em DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meus Pedidos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container container-large">
    <div class="topbar">
        <div>
            <div class="titulo">Meus Pedidos</div>
            <div class="usuario-nome">Visualize e edite seus pedidos.</div>
        </div>
        <div>
            <a href="dashboard.php" class="btn btn-secundario">Voltar ao Dashboard</a>
        </div>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'atualizado'): ?>
        <div class="mensagem sucesso">Pedido atualizado com sucesso.</div>
    <?php endif; ?>

    <?php if ($result && $result->num_rows > 0): ?>
        <table class="tabela">
            <thead>
            <tr>
                <th>ID</th>
                <th>Produto</th>
                <th>Quantidade</th>
                <th>Status</th>
                <th>Data</th>
                <th>Ação</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>#<?= (int)$row['id'] ?></td>
                    <td><?= htmlspecialchars($row['nome_produto']) ?></td>
                    <td><?= (int)$row['quantidade'] ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td><?= htmlspecialchars($row['criado_em']) ?></td>
                    <td>
                        <a href="editar_pedido.php?id=<?= (int)$row['id'] ?>" class="btn btn-primario">
                            Editar
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="mensagem erro">Você ainda não possui pedidos.</div>
    <?php endif; ?>
</div>
</body>
</html>