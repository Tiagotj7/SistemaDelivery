<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
require 'conexao.php';

$usuario_id = $_SESSION['usuario_id'];
$statusFiltro = $_GET['status'] ?? "";

// QUERY BASE
$sql = "
    SELECT p.id, p.quantidade, p.status, p.criado_em,
           pr.nome AS nome_produto
    FROM pedidos p
    JOIN produtos pr ON pr.id = p.produto_id
    WHERE p.usuario_id = ?
";

// FILTRO
if ($statusFiltro) {
    $sql .= " AND p.status = ?";
}

$sql .= " ORDER BY p.criado_em DESC";

// PREPARE
$stmt = $conn->prepare($sql);

if ($statusFiltro) {
    $stmt->bind_param("is", $usuario_id, $statusFiltro);
} else {
    $stmt->bind_param("i", $usuario_id);
}

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
            <div class="usuario-nome">Gerencie seus pedidos.</div>
        </div>
        <div>
            <a href="dashboard.php" class="btn btn-secundario">Voltar</a>
        </div>
    </div>

    <!-- FILTRO -->
    <form method="get" style="margin-bottom:10px;">
        <select name="status">
            <option value="">Todos</option>
            <option value="pendente" <?= $statusFiltro == 'pendente' ? 'selected' : '' ?>>Pendente</option>
            <option value="concluido" <?= $statusFiltro == 'concluido' ? 'selected' : '' ?>>Concluído</option>
        </select>
        <button class="btn btn-primario">Filtrar</button>
    </form>

    <?php if (isset($_GET['msg'])): ?>
        <div class="mensagem sucesso">
            <?=
                $_GET['msg'] === 'concluido' ? 'Pedido concluído!' :
                ($_GET['msg'] === 'excluido' ? 'Pedido excluído!' : '')
            ?>
        </div>
    <?php endif; ?>

    <?php if ($result && $result->num_rows > 0): ?>
        <table class="tabela">
            <thead>
            <tr>
                <th>ID</th>
                <th>Produto</th>
                <th>Qtd</th>
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

                        <!-- CONCLUIR -->
                        <?php if ($row['status'] !== 'concluido'): ?>
                        <form method="post" action="concluir_pedido.php" style="display:inline;">
                            <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                            <button class="btn btn-secundario">Concluir</button>
                        </form>
                        <?php endif; ?>

                        <!-- EXCLUIR -->
                        <form method="post" action="excluir_pedido.php" style="display:inline;"
                              onsubmit="return confirm('Excluir pedido?')">
                            <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                            <button class="btn btn-secundario">Excluir</button>
                        </form>

                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="mensagem erro">Nenhum pedido encontrado.</div>
    <?php endif; ?>
</div>
</body>
</html>
