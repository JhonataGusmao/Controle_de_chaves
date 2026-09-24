<?php
session_start();
require '../includes/conexao.php';

require '../valida_secao.php';



// Verifica se recebeu um ID
if (!isset($_GET['id'])) {
    header("Location: chaves.php");
    exit;
}

$id = $_GET['id'];

// Busca a chave atual
$sql = $pdo->prepare("SELECT * FROM chaves WHERE id = ?");
$sql->execute([$id]);
$chave = $sql->fetch();

if (!$chave) {
    echo "Chave não encontrada!";
    exit;
}

// Atualizar chave
if (!empty($_POST)) {

    $nome = $_POST['nome'];
    $setor = $_POST['setor'];

    $up = $pdo->prepare("UPDATE chaves SET numero_sala=?, descricao=? WHERE id=?");
    $up->execute([$nome, $setor, $id]);

    header("Location: editar_chave.php?ok=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/admin.css">
    <title>Editar Chave</title>
</head>
<body>

<div class="sidebar">
    <h2>ADMINISTRADOR</h2>
    <a href="dashboard.php">📊 Dashboard</a>
    <a class="active" href="gerenciar_chaves.php">🔑 Gerenciar Chaves</a>
    <a href="listar_usuarios.php">👤 Usuários</a>
    <a href="../logout.php" class="logout">🚪 Sair</a>
</div>

<div class="content">
    <h1>Editar Chave</h1>

    <form method="post" class="form-cad">
        <label>Nome da Chave:</label>
        <input type="text" name="nome" value="<?= htmlspecialchars($chave['numero_sala']) ?>" required>

        <label>Setor:</label>
        <input type="text" name="setor" value="<?= htmlspecialchars($chave['descricao']) ?>" required>

        <button type="submit" class="btn-save">Salvar Alterações</button>
    </form>

</div>

</body>
</html>
