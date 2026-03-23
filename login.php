<?php
session_start();
require 'conexao.php';

$mensagem = ""; // IMPORTANTE: inicializa a variável

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($email === "" || $senha === "") {
        $mensagem = "Informe e-mail e senha.";
    } else {
        $stmt = $conn->prepare("SELECT id, nome, senha FROM usuarios WHERE email = ?");
        if ($stmt === false) {
            die("Erro na preparação da query: " . $conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($id, $nome, $senha_hash);

        if ($stmt->fetch()) {
            if (password_verify($senha, $senha_hash)) {
                $_SESSION['usuario_id']  = $id;
                $_SESSION['usuario_nome'] = $nome;
                $stmt->close();
                header("Location: dashboard.php");
                exit;
            } else {
                $mensagem = "Credenciais inválidas.";
            }
        } else {
            $mensagem = "Credenciais inválidas.";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Entrar</h1>
    <p class="subtitulo">Acesse o sistema de delivery com seu e-mail e senha.</p>

    <?php if (isset($_GET['cadastro']) && $_GET['cadastro'] === 'sucesso'): ?>
        <div class="mensagem sucesso">Cadastro realizado com sucesso. Faça login.</div>
    <?php endif; ?>

    <?php if ($mensagem): ?>
        <div class="mensagem erro"><?= htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>

    <form method="post" action="login.php">
        <div class="grupo-campo">
            <label>E-mail</label>
            <input type="email" name="email" required>
        </div>

        <div class="grupo-campo">
            <label>Senha</label>
            <input type="password" name="senha" required>
        </div>

        <button type="submit" class="btn btn-primario btn-block">Entrar</button>
    </form>

    <p style="margin-top:14px; font-size:14px;">
        Não possui conta?
        <a href="cadastro.php" class="link-acao">Cadastre-se</a>
    </p>
</div>
</body>
</html>