<?php
session_start();
require '../includes/conexao.php';

require '../valida_secao.php';


$usuarios = $pdo->query("SELECT * FROM usuarios ORDER BY nome")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Usuários</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap">

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

        /* BOTÃO NOVO*/
        .btn-novo {
            display: inline-block;
            padding: 10px 18px;
            background: rgba(201,168,106,0.25);
            color: #C9A86A;
            border-radius: 6px;
            text-decoration: none;
            border: 1px solid rgba(201,168,106,0.4);
            transition: .2s;
            font-weight: 500;
        }

        .btn-novo:hover {
            background: rgba(201,168,106,0.45);
        }

        /* TABELA */
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            background: #11263E;
            border-radius: 8px;
            overflow: hidden;
        }

        table th {
            background: #1B3B5F;
            padding: 14px;
            font-weight: 600;
            color: #E3ECF5;
        }

        table td {
            padding: 12px;
            text-align: center;
            color: #E3ECF5;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        /* BOTÕES DE AÇÃO */
        .btn-editar, .btn-excluir {
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            transition: .2s;
        }

        .btn-editar {
            background: rgba(60,140,200,0.25);
            border: 1px solid rgba(60,140,200,0.4);
            color: #4DA3D9;
        }
        .btn-editar:hover {
            background: rgba(60,140,200,0.45);
        }

        .btn-excluir {
            background: rgba(225,106,106,0.25);
            border: 1px solid rgba(225,106,106,0.4);
            color: #E16A6A;
        }
        .btn-excluir:hover {
            background: rgba(225,106,106,0.45);
        }
    </style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>ADMINISTRADOR</h2>
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="chaves.php">🔑 Gerenciar Chaves</a>
    <a href="listar_usuarios.php" class="active">👤 Usuários</a>
    <a href="historico.php">📜 Histórico</a>
    <a href="../logout.php" class="logout">🚪 Sair</a>
</div>

<!-- CONTEÚDO -->
<div class="content">
    <h1>Lista de Usuários</h1>

    <a href="usuarios.php" class="btn-novo">+ Cadastrar Usuário</a>

    <table>
        <tr>
            <th>Nome</th>
            <th>Setor</th>
            <th>Ramal</th>
            <th>Nível</th>
            <th>Login</th>
            <th>Ações</th>
        </tr>

        <?php foreach ($usuarios as $u): ?>
            <tr>
                <td><?= $u['nome'] ?></td>
                <td><?= $u['setor'] ?></td>
                <td><?= $u['ramal'] ?></td>
                <td><?= $u['nivel'] ?></td>
                <td><?= $u['login'] ?></td>
                <td>
                    <a class="btn-editar" href="editar_usuario.php?id=<?= $u['id'] ?>">Editar</a>
                    <a class="btn-excluir" href="excluir_usuario.php?id=<?= $u['id'] ?>" onclick="return confirm('Excluir este usuário?')">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

</div>

</body>
</html>
