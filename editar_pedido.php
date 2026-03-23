<?php
// MOSTRAR ERROS PARA DEBUG
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
require 'conexao.php';

$usuario_id = $_SESSION['usuario_id'];
$mensagem = "";
$erro = false;
$pedido = null;

/*
 * 1) PRIMEIRO CASO: ACESSO VIA GET (ABRIR TELA)
 */
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($id <= 0) {
        $erro = true;
        $mensagem = "ID de pedido inválido.";
    } else {
        $sql = "
            SELECT p.id, p.quantidade, p.status,
                   pr.nome AS nome_produto
            FROM pedidos p
            JOIN produtos pr ON pr.id = p.produto_id
            WHERE p.id = ? AND p.usuario_id = ?
        ";

        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Erro ao preparar a query: " . $conn->error);
        }

        $stmt->bind_param("ii", $id, $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $pedido = $result->fetch_assoc();
        $stmt->close();

        if (!$pedido) {
            $erro = true;
            $mensagem = "Pedido não encontrado ou não pertence ao usuário.";
        }
    }
}

/*
 * 2) SEGUNDO CASO: ACESSO VIA POST (SALVAR ALTERAÇÕES)
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $nova_quantidade = isset($_POST['quantidade']) ? (int)$_POST['quantidade'] : 0;

    if ($id <= 0 || $nova_quantidade <= 0) {
        $erro = true;
        $mensagem = "Dados inválidos.";
    } else {
        // Confere se o pedido pertence ao usuário logado
        $sql = "SELECT id FROM pedidos WHERE id = ? AND usuario_id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Erro ao preparar a query de verificação: " . $conn->error);
        }

        $stmt->bind_param("ii", $id, $usuario_id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 0) {
            $erro = true;
            $mensagem = "Pedido não encontrado ou não pertence ao usuário.";
        }
        $stmt->close();

        if (!$erro) {
            // Atualiza somente a quantidade
            $sql = "UPDATE pedidos SET quantidade = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                die("Erro ao preparar a query de atualização: " . $conn->error);
            }

            $stmt->bind_param("ii", $nova_quantidade, $id);
            if ($stmt->execute()) {
                $stmt->close();
                header("Location: listar_pedidos.php?msg=atualizado");
                exit;
            } else {
                $erro = true;
                $mensagem = "Erro ao atualizar pedido.";
            }
            $stmt->close();
        }
    }

    // Se deu erro na atualização, recarrega o pedido para exibir no formulário
    $sql = "
        SELECT p.id, p.quantidade, p.status,
               pr.nome AS nome_produto
        FROM pedidos p
        JOIN produtos pr ON pr.id = p.produto_id
        WHERE p.id = ? AND p.usuario_id = ?
    ";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Erro ao preparar a query para recarregar o pedido: " . $conn->error);
    }

    $stmt->bind_param("ii", $id, $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $pedido = $result->fetch_assoc();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Pedido</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Editar Pedido</h1>
    <p class="subtitulo">Altere a quantidade do seu pedido.</p>

    <?php if ($mensagem): ?>
        <div class="mensagem <?= $erro ? 'erro' : 'sucesso' ?>">
            <?= htmlspecialchars($mensagem) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($pedido)): ?>
        <form method="post" action="editar_pedido.php">
            <input type="hidden" name="id" value="<?= (int)$pedido['id'] ?>">

            <div class="grupo-campo">
                <label>Produto</label>
                <input type="text" value="<?= htmlspecialchars($pedido['nome_produto']) ?>" disabled>
            </div>

            <div class="grupo-campo">
                <label>Quantidade</label>
                <input type="number" name="quantidade"
                       value="<?= (int)$pedido['quantidade'] ?>" min="1">
            </div>

            <button type="submit" class="btn btn-primario btn-block">Salvar Alterações</button>
        </form>
    <?php elseif ($erro): ?>
        <p style="margin-top:10px;">
            <a href="listar_pedidos.php" class="link-acao">Voltar à lista de pedidos</a>
        </p>
    <?php endif; ?>

    <p style="margin-top:14px; font-size:14px;">
        <a href="listar_pedidos.php" class="link-acao">Voltar à lista de pedidos</a>
    </p>
</div>
</body>
</html>