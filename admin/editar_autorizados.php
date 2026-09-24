<?php
session_start();
require '../includes/conexao.php';

require '../valida_secao.php';


// Verifica ID da chave
if (!isset($_GET['id'])) {
    header("Location: chaves.php");
    exit;
}

$id_chave = $_GET['id'];

/* ------------------------------
   BUSCA INFORMAÇÕES DA CHAVE
--------------------------------*/
$chave = $pdo->prepare("SELECT * FROM chaves WHERE id=?");
$chave->execute([$id_chave]);
$dadosChave = $chave->fetch();

/* ------------------------------
   BUSCA AUTORIZADOS POR CHAVE
--------------------------------*/
$aut = $pdo->prepare("
    SELECT id, cpf, nome
    FROM autorizados_chave
    WHERE id_chave = ?
");
$aut->execute([$id_chave]);
$autorizados = $aut->fetchAll();

/* ------------------------------
      ADICIONAR AUTORIZADO
--------------------------------*/
if (!empty($_POST['cpf'])) {

    // limpa CPF
    $cpf = preg_replace('/\D/', '', $_POST['cpf']);
    $nome = trim($_POST['nome']);

    if (strlen($cpf) != 11) {
        die("<script>alert('CPF inválido. Digite apenas números.'); history.back();</script>");
    }

    if (strlen($nome) < 3) {
        die("<script>alert('Nome inválido.'); history.back();</script>");
    }

    // Verifica duplicidade
    $check = $pdo->prepare("
        SELECT id FROM autorizados_chave
        WHERE id_chave = ? AND cpf = ?
    ");
    $check->execute([$id_chave, $cpf]);

    if ($check->rowCount() > 0) {
        die("<script>alert('Este CPF já está autorizado para esta chave!'); history.back();</script>");
    }

    // Insere registro
    $ins = $pdo->prepare("
        INSERT INTO autorizados_chave (id_chave, cpf, nome)
        VALUES (?, ?, ?)
    ");
    $ins->execute([$id_chave, $cpf, $nome]);

    header("Location: editar_autorizados.php?id=$id_chave");
    exit;
}

/* ------------------------------
      REMOVER AUTORIZADO
--------------------------------*/
if (isset($_GET['remove'])) {
    $uid = $_GET['remove'];

    $del = $pdo->prepare("
        DELETE FROM autorizados_chave
        WHERE id=? AND id_chave=?
    ");
    $del->execute([$uid, $id_chave]);

    header("Location: editar_autorizados.php?id=$id_chave");
    exit;
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/admin.css">
    <title>Autorizados - <?= htmlspecialchars($dadosChave['descricao']) ?> / Sala <?= htmlspecialchars($dadosChave['numero_sala']) ?></title>


    <style>
/* Caixa principal do formulário */
.form-box {
    background: #11263E;
    padding: 20px;
    border-radius: 8px;
    width: 420px;
    margin-top: 20px;
}

/* Labels */
.form-box label {
    font-weight: 600;
    display: block;
    margin-bottom: 5px;
}

/* Inputs */
.form-box input {
    width: 100%;
    padding: 10px;
    border-radius: 6px;
    border: none;
    background: #1B3B5F;
    color: #E3ECF5;
    margin-bottom: 15px;
}

/* Botão Salvar */
.btn-save {
    background: #C9A86A;
    color: #0A1A2F;
    font-weight: 600;
    padding: 10px 16px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: .2s;
}

.btn-save:hover {
    background: #b49058;
}

/* Botão remover */
.btn-del {
    background: rgba(225,106,106,0.25);
    color: #E16A6A;
    padding: 8px 12px;
    border-radius: 6px;
    text-decoration: none;
    border: 1px solid rgba(225,106,106,0.4);
}

.btn-del:hover {
    background: rgba(225,106,106,0.45);
}
</style>

</head>
<body>

<div class="sidebar">
    <h2>ADMINISTRADOR</h2>
    <a href="dashboard.php">📊 Dashboard</a>
    <a class="active" href="chaves.php">🔑 Gerenciar Chaves</a>
    <a href="listar_usuarios.php">👤 Usuários</a>
    <a href="../logout.php" class="logout">🚪 Sair</a>
</div>

<div class="content">

    <h1>Autorizados – Sala <?= htmlspecialchars($dadosChave['numero_sala']) ?></h1>

    <h3>Pessoas Autorizadas</h3>

    <table>
        <tr>
            <th>CPF</th>
            <th>Nome</th>
            <th>Ação</th>
        </tr>

        <?php foreach ($autorizados as $a): ?>
        <tr>
            <td><?= htmlspecialchars($a['cpf']) ?></td>
            <td><?= htmlspecialchars($a['nome']) ?></td>
            <td>
                <a href="editar_autorizados.php?id=<?= $id_chave ?>&remove=<?= $a['id'] ?>" class="btn-del">
                    ❌ Remover
                </a>
            </td>
        </tr>
        <?php endforeach; ?>

    </table>

    <h3>Adicionar Autorizado</h3>

<div class="form-box">
    <form method="post">

        <label>CPF do Autorizado</label>
        <input type="text" name="cpf" placeholder="Digite o CPF (apenas números)" required>

        <label>Nome do Autorizado</label>
        <input type="text" name="nome" placeholder="Digite o nome completo" required>

        <button type="submit" class="btn-save">Adicionar</button>
    </form>
</div>


</div>

</body>
</html>
