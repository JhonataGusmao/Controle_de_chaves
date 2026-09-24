<?php
session_start();
require '../includes/conexao.php';
require '../valida_secao.php';

/* ============================================================
   MAPA DE NOMES → carrega cpf → nome da tabela autorizados_chave
============================================================ */
$mapaNomes = [];
$buscaNomes = $pdo->query("SELECT cpf, nome FROM autorizados_chave");
foreach ($buscaNomes as $p) {
    $mapaNomes[$p['cpf']] = $p['nome'];
}

/* ============================================================
   FUNÇÃO PARA EXIBIR CPF + NOME
============================================================ */
function mostrarNome($cpf, $mapaNomes)
{
    if (!$cpf) return null;
    $nome = $mapaNomes[$cpf] ?? null;
    return $nome ? "$cpf - $nome" : $cpf;
}

/* ============================================================
   FILTROS DE BUSCA
============================================================ */
$filtro = "";
$params = [];

if (!empty($_GET['buscar'])) {
    $filtro .= " AND (entregue_por LIKE :buscar OR pego_por LIKE :buscar OR devolvido_por LIKE :buscar)";
    $params[':buscar'] = "%" . $_GET['buscar'] . "%";
}

if (!empty($_GET['data_inicio'])) {
    $filtro .= " AND horario_saida >= :data_inicio";
    $params[':data_inicio'] = $_GET['data_inicio'] . " 00:00:00";
}

if (!empty($_GET['data_fim'])) {
    $filtro .= " AND horario_saida <= :data_fim";
    $params[':data_fim'] = $_GET['data_fim'] . " 23:59:59";
}

/* ============================================================
   CONSULTA DO HISTÓRICO
============================================================ */
$sql = $pdo->prepare("
    SELECT *
    FROM historico_chaves
    WHERE 1=1 $filtro
    ORDER BY horario_saida DESC
");

$sql->execute($params);
$historico = $sql->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Histórico de Movimentações</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

<style>
    body {
        margin: 0;
        font-family: "Inter", sans-serif;
        background: #0A1A2F;
        color: #E3ECF5;
    }

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
        text-align: center;
        color: white;
        margin-bottom: 30px;
    }

    .sidebar a {
        display: block;
        padding: 12px;
        margin-bottom: 10px;
        text-decoration: none;
        background: rgba(255,255,255,0.05);
        border-radius: 6px;
        color: #E3ECF5;
        transition: .2s;
    }

    .sidebar a:hover,
    .sidebar .active {
        background: rgba(201,168,106,0.25);
    }

    .sidebar .logout {
        background: rgba(225,106,106,0.25);
        color: #E16A6A;
    }

    .content {
        margin-left: 260px;
        padding: 30px;
    }

    h1 {
        color: white;
        font-size: 28px;
        margin-bottom: 10px;
    }

    .filtro {
        background: #11263E;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .filtro input {
        padding: 10px;
        border-radius: 6px;
        border: none;
        width: 200px;
        margin-right: 10px;
    }

    .btn-filtrar {
        padding: 10px 16px;
        background: #C9A86A;
        border: none;
        color: #1B1B1B;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
    }

    .btn-filtrar.active-filter {
        background: rgba(201,168,106,0.45);
        border: 1px solid rgba(201,168,106,0.6);
        color: #000;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: #11263E;
        border-radius: 8px;
        overflow: hidden;
        margin-top: 10px;
    }

    th {
        background: #1B3B5F;
        padding: 14px;
        font-weight: 600;
        color: #E3ECF5;
    }

    td {
        padding: 12px;
        text-align: center;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }

    .pendente {
        color: #E16A6A;
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
    <a href="historico.php" class="active">📜 Histórico</a>
    <a href="../logout.php" class="logout">🚪 Sair</a>
</div>

<div class="content">

    <h1>Histórico de Movimentações</h1>

    <div class="filtro">
        <form method="GET">
            <input type="text" name="buscar" placeholder="Buscar usuário..." value="<?= $_GET['buscar'] ?? '' ?>">
            <input type="date" name="data_inicio" value="<?= $_GET['data_inicio'] ?? '' ?>">
            <input type="date" name="data_fim" value="<?= $_GET['data_fim'] ?? '' ?>">

            <?php
            $filtroAtivo = !empty($_GET['buscar']) || !empty($_GET['data_inicio']) || !empty($_GET['data_fim']);
            ?>

            <button class="btn-filtrar <?= $filtroAtivo ? 'active-filter' : '' ?>">Filtrar</button>
        </form>
    </div>

    <table>
        <tr>
            <th>Retirado por</th>
            <th>Login</th>
            <th>Horário Saída</th>
            <th>Devolvido Por</th>
            <th>Horário Devolução</th>
            <th>Observação Retirada</th>
            <th>Observação Devolução</th>
        </tr>

        <?php foreach ($historico as $h): ?>
        <tr>
            <td><?= htmlspecialchars(mostrarNome($h['pego_por'], $mapaNomes)) ?></td>
            <td><?= htmlspecialchars($h['entregue_por']) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($h['horario_saida'])) ?></td>
            <td>
                <?= $h['devolvido_por']
                    ? htmlspecialchars(mostrarNome($h['devolvido_por'], $mapaNomes))
                    : "<span class='pendente'>Pendente</span>" ?>
            </td>
            <td>
                <?= $h['horario_devolucao']
                    ? date('d/m/Y H:i', strtotime($h['horario_devolucao']))
                    : "<span class='pendente'>--</span>" ?>
            </td>
            <td>
            <?= !empty($h['observacao']) 
                ? htmlspecialchars($h['observacao']) 
                : '--' ?>
            </td>

            <td>
            <?= !empty($h['observacao_devolucao']) 
                ? htmlspecialchars($h['observacao_devolucao']) 
                : '--' ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

</div>

</body>
</html>
