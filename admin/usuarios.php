<?php
session_start();
require '../includes/conexao.php';

require '../valida_secao.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $setor = $_POST['setor'];
    $ramal = $_POST['ramal'];
    $nivel = $_POST['nivel'];
    $login = $_POST['login'];
    $senha = $_POST['senha']; // sem criptografia

    $sql = $pdo->prepare("INSERT INTO usuarios (nome, setor, ramal, nivel, login, senha)
                          VALUES (:nome, :setor, :ramal, :nivel, :login, :senha)");

    $sql->execute([
        ':nome' => $nome,
        ':setor' => $setor,
        ':ramal' => $ramal,
        ':nivel' => $nivel,
        ':login' => $login,
        ':senha' => $senha
    ]);

    header("Location: listar_usuarios.php?msg=success");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Cadastrar Usuário</title>

<style>
    body {
        margin: 0;
        font-family: 'Inter', Arial, sans-serif;
        background: #0A1A2F;
        color: #E3ECF5;
    }

    /* SIDEBAR */
    .sidebar {
        position: fixed;
        left: 0;
        top: 0;
        width: 230px;
        height: 100%;
        background: #1B3B5F;
        padding: 20px;
        box-shadow: 4px 0 20px rgba(0,0,0,0.4);
    }

    .sidebar h2 {
        color: #ffffff;
        text-align: center;
        margin-bottom: 30px;
        font-weight: 600;
        letter-spacing: 1px;
    }

    .sidebar a {
        display: block;
        padding: 12px;
        margin-bottom: 10px;
        color: #E3ECF5;
        background: rgba(255,255,255,0.05);
        text-decoration: none;
        border-radius: 6px;
        transition: .2s ease;
    }

    .sidebar a:hover,
    .sidebar a.active {
        background: rgba(201,168,106,0.25);
    }

    .sidebar .logout {
        background: rgba(225,106,106,0.25);
        color: #E16A6A;
    }

    /* CONTEÚDO */
    .content {
        margin-left: 260px;
        padding: 30px;
    }

    h1 {
        font-size: 28px;
        color: #ffffff;
        font-weight: 600;
    }

    form {
        margin-top: 20px;
        background: #11263E;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0,0,0,0.3);
        width: 450px;
    }

    label {
        font-weight: 500;
    }

    input, select {
        width: 100%;
        padding: 10px;
        border-radius: 6px;
        border: none;
        margin-top: 5px;
        margin-bottom: 15px;
        background: #1B3B5F;
        color: #E3ECF5;
    }

    button {
        width: 100%;
        padding: 12px;
        background: rgba(201,168,106,0.25);
        border: 1px solid rgba(201,168,106,0.5);
        color: #C9A86A;
        font-size: 16px;
        border-radius: 6px;
        cursor: pointer;
        transition: .2s;
    }

    button:hover {
        background: rgba(201,168,106,0.45);
    }

</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>ADMINISTRADOR</h2>
    <a href="./dashboard.php">📊 Dashboard</a>
    <a href="chaves.php">🔑 Gerenciar Chaves</a>
    <a href="listar_usuarios.php">👤 Usuários</a>
    <a href="historico.php">📜 Histórico</a>
    <a href="../logout.php" class="logout">🚪 Sair</a>
</div>

<!-- CONTEÚDO -->
<div class="content">
    <h1>Cadastrar Usuário</h1>

    <form method="POST">

        <label>Nome</label>
        <input type="text" name="nome" required>

        <label>Setor</label>
        <input type="text" name="setor" required>

        <label>Ramal</label>
        <input type="text" name="ramal">

        <label>Nível</label>
        <select name="nivel" required>
            <option value="usuario">Usuário</option>
            <option value="admin">Administrador</option>
            <option value="sargento">Sargento</option>
        </select>

        <label>Login</label>
        <input type="text" name="login" required>

        <label>Senha</label>
        <input type="text" name="senha" required>

        <button type="submit">Cadastrar</button>
    </form>
</div>

</body>
</html>
