<?php
session_start();
$baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Delivery - Início</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <div class="logo-simples">
        <span>Delivery</span> Online
    </div>
    <p class="subtitulo">
        Gerencie usuários, produtos e pedidos em um painel simples e moderno.
    </p>

    <div style="display:flex; flex-direction:column; gap:10px; margin-top:10px;">
        <a href="<?= $baseUrl ?>/login.php" class="btn btn-primario btn-block">Fazer Login</a>
        <a href="<?= $baseUrl ?>/cadastro.php" class="btn btn-secundario btn-block">Cadastrar Novo Usuário</a>
    </div>
</div>
</body>
</html>