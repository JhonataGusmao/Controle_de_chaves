<?php
session_start();
require '../includes/conexao.php';

require '../valida_secao.php';

$id = $_GET['id'];

$sql = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
$sql->execute([':id' => $id]);
$usuario = $sql->fetch();

if (!$usuario) {
    die("Usuário não encontrado!");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $setor = $_POST['setor'];
    $ramal = $_POST['ramal'];
    $nivel = $_POST['nivel'];
    $login = $_POST['login'];
    $senha = $_POST['senha'];

    $update = $pdo->prepare("UPDATE usuarios 
                             SET nome=:nome, setor=:setor, ramal=:ramal, nivel=:nivel,
                                 login=:login, senha=:senha
                             WHERE id=:id");

    $update->execute([
        ':nome' => $nome,
        ':setor' => $setor,
        ':ramal' => $ramal,
        ':nivel' => $nivel,
        ':login' => $login,
        ':senha' => $senha,
        ':id' => $id
    ]);

    header("Location: listar_usuarios.php?msg=updated");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Usuário</title>

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
        }

        .sidebar a {
            display: block;
            padding: 12px;
            margin-bottom: 10px;
            color: #E3ECF5;
            background: rgba(255,255,255,0.05);
            text-decoration: none;
            border-radius: 6px;
            transition: .2s;
        }

        .sidebar a:hover {
            background: rgba(201,168,106,0.25);
        }

        /* CONTEÚDO */
        .content {
            margin-left: 260px;
            padding: 30px;
        }

        h2 {
            font-size: 26px;
            margin-bottom: 20px;
        }

        /* FORM */
        form {
            background: #11263E;
            padding: 25px;
            width: 450px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.3);
        }

        label {
            font-size: 15px;
            color: #E3ECF5;
        }

        input, select {
            width: 100%;
            padding: 12px;
            margin-top: 6px;
            margin-bottom: 18px;
            border: none;
            border-radius: 6px;
            background: #1B3B5F;
            color: #fff;
            font-size: 15px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: rgba(201,168,106,0.35);
            color: #C9A86A;
            border: 1px solid rgba(201,168,106,0.5);
            font-size: 16px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            transition: .2s;
        }

        button:hover {
            background: rgba(201,168,106,0.55);
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <h2>Menu</h2>
        <a href="listar_usuarios.php">👤Usuários</a>
        <a href="../logout.php">🚪 Sair</a>
    </div>

    <div class="content">
        <h2>Editar Usuário</h2>

        <form method="POST">
            <label>Nome:</label>
            <input type="text" name="nome" value="<?= $usuario['nome'] ?>" required>

            <label>Setor:</label>
            <input type="text" name="setor" value="<?= $usuario['setor'] ?>" required>

            <label>Ramal:</label>
            <input type="text" name="ramal" value="<?= $usuario['ramal'] ?>">

            <label>Nível:</label>
            <select name="nivel">
                <option value="usuario" <?= $usuario['nivel']=='usuario'?'selected':'' ?>>Usuário</option>
                <option value="admin" <?= $usuario['nivel']=='admin'?'selected':'' ?>>Administrador</option>
                <option value="sargento" <?= $usuario['nivel']=='sargento'?'selected':'' ?>>Sargento</option>
            </select>

            <label>Login:</label>
            <input type="text" name="login" value="<?= $usuario['login'] ?>" required>

            <label>Senha:</label>
            <input type="text" name="senha" value="<?= $usuario['senha'] ?>" required>

            <button type="submit">Salvar Alterações</button>
        </form>
    </div>

</body>
</html>
