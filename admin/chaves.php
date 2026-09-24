<?php
session_start();
require '../includes/conexao.php';

require '../valida_secao.php';


// Busca as chaves
$sql = $pdo->query("SELECT * FROM chaves ORDER BY numero_sala");
$chaves = $sql->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/admin.css">
    <title>Gerenciar Chaves</title>

    <style>
.btn-add {
    display: inline-block;
    background: #4CC08F;
    padding: 10px 20px;
    color: #0A1A2F;
    font-weight: 600;
    border-radius: 6px;
    text-decoration: none;
    margin-bottom: 20px;
    transition: 0.2s;
}

.btn-add:hover {
    background: #3aa878;
}

.btn-delete {
    padding: 8px 14px;
    border-radius: 6px;
    text-decoration: none;
    color: #E16A6A;
    font-size: 14px;
    font-weight: 500;
    background: rgba(225,106,106,0.25);
    border: 1px solid rgba(225,106,106,0.4);
    transition: .2s ease;
}

.btn-delete:hover {
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
    <a href="historico.php">📜 Histórico</a>
    <a href="../logout.php" class="logout">🚪 Sair</a>
</div>

<div class="content">
    <h1>Gerenciar Chaves</h1>

    <a href="nova_chave.php" class="btn-add">➕ Adicionar Chave</a>


    <table>
    <tr>
        <th>Nome da Chave</th>
        <th>Setor</th>
        <th>Autorizados</th>
        <th>Ações</th>
    </tr>

    <?php foreach ($chaves as $c): ?>
        <tr>
            <td><?= htmlspecialchars($c['numero_sala']) ?></td>
            <td><?= htmlspecialchars($c['descricao']) ?></td>
            <td>
                <a href="editar_autorizados.php?id=<?= $c['id'] ?>" class="btn-info">Ver Autorizados</a>
            </td>
            <td>
                <a href="editar_chave.php?id=<?= $c['id'] ?>" class="btn-editar">Editar</a>
                <a href="excluir_chave.php?id=<?= $c['id'] ?>" class="btn-delete" onclick="return confirm('Excluir esta chave? Esta ação removerá também movimentos e autorizações relacionados.')">Excluir</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

</div>

</body>
</html>
