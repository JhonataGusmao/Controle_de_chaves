

<?php
session_start();
require '../includes/conexao.php';

require '../valida_secao.php';

// Contagem de chaves
$disp = $pdo->query("SELECT COUNT(*) FROM chaves WHERE status='disponivel'")->fetchColumn();
$ret = $pdo->query("SELECT COUNT(*) FROM chaves WHERE status='retirada'")->fetchColumn();
$total = $pdo->query("SELECT COUNT(*) FROM chaves")->fetchColumn();

// Movimentações recentes
$mov = $pdo->query("
    SELECT m.*, c.numero_sala, u.usuario 
    FROM movimentos m
    JOIN chaves c ON c.id = m.id_chave
    JOIN usuarios u ON u.id = m.id_usuario
    ORDER BY m.data_hora DESC
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="css/admin.css">
    <title>Dashboard - Controle de Chaves</title>
</head>
<body>

<div class="sidebar">
    <h2>ADMINISTRADOR</h2>

    <a href="dashboard.php" class="active">📊 Dashboard</a>
    <a href="chaves.php">🔑 Gerenciar Chaves</a>
    <a href="listar_usuarios.php">👤 Usuários</a>
    <a href="historico.php">📜 Histórico</a>
    <a href="../logout.php" class="logout">🚪 Sair</a>
</div>

<div class="content">

    <h1>Dashboard Administrativo</h1>

    <div class="cards">

        <div class="card azul">
            <h3>Chaves Disponíveis</h3>
            <p><?= $disp ?></p>
        </div>

        <div class="card dourado">
            <h3>Chaves Retiradas</h3>
            <p><?= $ret ?></p>
        </div>

        <div class="card azul_escuro">
            <h3>Total de Chaves Cadastradas</h3>
            <p><?= $total ?></p>
        </div>

    

</body>
</html>
