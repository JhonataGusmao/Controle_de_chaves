<?php
session_start();
require '../includes/conexao.php';

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['nivel'] !== 'admin') {
    header("Location: ../logout.php");
    exit;
}




$mensagem = "";

// Se enviou o formulário
if (!empty($_POST['numero_sala']) && !empty($_POST['descricao']) && !empty($_POST['autorizados'])) {

    $numero = $_POST['numero_sala'];
    $setor = $_POST['descricao'];
    $autorizados = $_POST['autorizados']; // texto simples por enquanto

    $sql = $pdo->prepare("INSERT INTO chaves (numero_sala, descricao, autorizados) VALUES (?, ?, ?)");
    
    if ($sql->execute([$numero, $setor, $autorizados])) {
        $mensagem = "Chave adicionada com sucesso!";
    } else {
        $mensagem = "Erro ao adicionar!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/admin.css">
    <title>Nova Chave</title>

    <style>
        .form-box {
            background: #11263E;
            padding: 25px;
            border-radius: 8px;
            width: 400px;
            margin-top: 20px;
        }

        .form-box label {
            display: block;
            margin-top: 10px;
            font-weight: 600;
        }

        .form-box input,
        .form-box textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border-radius: 6px;
            border: none;
        }

        .btn-salvar {
            margin-top: 15px;
            background: #C9A86A;
            color: #0A1A2F;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            border: none;
            transition: .2s;
        }

        .btn-salvar:hover {
            background: #b49058;
        }

        .mensagem {
            margin-top: 10px;
            color: #4CC08F;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>ADMIN</h2>
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="gerenciar_chaves.php">🔑 Gerenciar Chaves</a>
    <a href="listar_usuarios.php">👤 Usuários</a>
    <a href="../logout.php" class="logout">🚪 Sair</a>
</div>

<div class="content">
    <h1>Adicionar Nova Chave</h1>

    <?php if ($mensagem != ""): ?>
        <p class="mensagem"><?= $mensagem ?></p>
    <?php endif; ?>

    <div class="form-box">
        <form method="post">

            <label>Nome da Chave / Número da Sala:</label>
            <input type="text" name="numero_sala" required>

            <label>Setor:</label>
            <input type="text" name="descricao" required>

            <label>Pessoas Autorizadas (separe por vírgula):</label>
            <textarea name="autorizados" rows="3" required></textarea>

            <button class="btn-salvar" type="submit">Salvar</button>

        </form>
    </div>

</div>

</body>
</html>
