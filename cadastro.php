<?php
require 'conexao.php';

$mensagem = ""; // IMPORTANTE: inicializa a variável

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $data_nascimento = $_POST['data_nascimento'] ?? null;
    $sexo  = $_POST['sexo'] ?? null;

    if ($nome === "" || $email === "" || $senha === "") {
        $mensagem = "Preencha nome, e-mail e senha.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $mensagem = "E-mail já cadastrado.";
        } else {
            $stmt->close();
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

            $stmt = $conn->prepare(
                "INSERT INTO usuarios (nome, email, senha, data_nascimento, sexo)
                 VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("sssss", $nome, $email, $senha_hash, $data_nascimento, $sexo);

            if ($stmt->execute()) {
                $stmt->close();
                header("Location: login.php?cadastro=sucesso");
                exit;
            } else {
                $mensagem = "Erro ao cadastrar usuário.";
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Usuário</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Novo Usuário</h1>
    <p class="subtitulo">Crie sua conta para acessar o painel de delivery.</p>

    <?php if ($mensagem): ?>
        <div class="mensagem erro"><?= htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>

    <form method="post" action="cadastro.php">
        <div class="grupo-campo">
            <label>Nome</label>
            <input type="text" name="nome" required>
        </div>

        <div class="grupo-campo">
            <label>E-mail</label>
            <input type="email" name="email" required>
        </div>

        <div class="grupo-campo">
            <label>Senha</label>
            <input type="password" name="senha" required>
        </div>

        <div class="grupo-campo">
            <label>Data de Nascimento</label>
            <input type="date" name="data_nascimento">
        </div>

        <div class="grupo-campo">
            <label>Sexo</label>
            <select name="sexo">
                <option value="">Selecione</option>
                <option value="M">Masculino</option>
                <option value="F">Feminino</option>
                <option value="O">Outro</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primario btn-block">Cadastrar</button>
    </form>

    <p style="margin-top:14px; font-size:14px;">
        Já possui conta?
        <a href="login.php" class="link-acao">Fazer login</a>
    </p>
</div>
</body>
</html>