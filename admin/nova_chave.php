<?php
session_start();
require '../includes/conexao.php';

require '../valida_secao.php';


$mensagem = "";

// Se enviou o formulário
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!empty($_POST['numero_sala']) && !empty($_POST['descricao']) && !empty($_POST['autorizados'])) {

        $numero = trim($_POST['numero_sala']);
        $setor = trim($_POST['descricao']);
        $raw_autorizados = trim($_POST['autorizados']);

        // 1. INSERE A CHAVE
        $sql = $pdo->prepare("INSERT INTO chaves (numero_sala, descricao) VALUES (?, ?)");

        if ($sql->execute([$numero, $setor])) {

            $id_chave = $pdo->lastInsertId();

            // 2. PROCESSA AUTORIZADOS
            $linhas = explode("\n", $raw_autorizados);

            foreach ($linhas as $linha) {

                $linha = trim($linha);
                if ($linha === "") continue;

                if (!strpos($linha, "-")) continue;

                list($cpf, $nome) = array_map('trim', explode("-", $linha));

                $cpf = preg_replace('/\D/', '', $cpf);

                if (strlen($cpf) !== 11) continue;
                if (empty($nome)) continue;

                $ins = $pdo->prepare("
                    INSERT INTO autorizados_chave (id_chave, cpf, nome)
                    VALUES (?, ?, ?)
                ");
                $ins->execute([$id_chave, $cpf, $nome]);
            }

            $mensagem = "Chave cadastrada com sucesso!";

        } else {
            $mensagem = "Erro ao cadastrar a chave!";
        }
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
    <h2>ADMINISTRADOR</h2>
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="chaves.php">🔑 Gerenciar Chaves</a>
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

            <label>Nome da Chave</label>
            <input type="text" name="numero_sala" required>

            <label>Setor</label>
            <input type="text" name="descricao" required>

            <label>Pessoas autorizadas
            <br><small>(uma por linha — CPF - Nome)</small></label>
            <textarea name="autorizados" required></textarea>

            <button class="btn-salvar" type="submit">Salvar</button>

        </form>
    </div>

</div>

</body>
</html>
