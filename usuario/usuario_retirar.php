<?php
session_start();
require '../includes/conexao.php';
require '../valida_secao_usuario.php';

$chave_id        = $_POST['chave_id'];
$nome_autorizado = trim($_POST['autorizado']);
$observacao      = isset($_POST['observacao']) ? trim($_POST['observacao']) : null;

// SE FOR OUTROS, OBSERVAÇÃO É OBRIGATÓRIA
if (strtoupper($nome_autorizado) === 'OUTROS' && empty($observacao)) {
    die("<script>alert('Informe a observação para OUTROS.'); window.location='dashboard.php';</script>");
}

if (strtoupper($nome_autorizado) !== 'OUTROS') {

    // VALIDA AUTORIZADO NORMAL
    $sql = $pdo->prepare("
        SELECT * FROM autorizados_chave 
        WHERE id_chave = ? AND LOWER(nome) = LOWER(?)
    ");
    $sql->execute([$chave_id, $nome_autorizado]);
    $aut = $sql->fetch(PDO::FETCH_ASSOC);

    if (!$aut) {
        die("<script>alert('Esta pessoa NÃO está autorizada a retirar esta chave!'); window.location='dashboard.php';</script>");
    }

    $cpf_real = $aut['cpf'];

} else {
    // CPF MASTER PARA OUTROS
    $cpf_real = '00000000000';
}

// ATUALIZA STATUS DA CHAVE
$upd = $pdo->prepare("UPDATE chaves SET status = 'retirada' WHERE id = ?");
$upd->execute([$chave_id]);

// REGISTRA NO HISTÓRICO
$hist = $pdo->prepare("
    INSERT INTO historico_chaves
    (id_chave, entregue_por, horario_saida, pego_por, observacao)
    VALUES (?, ?, NOW(), ?, ?)
");
$hist->execute([
    $chave_id,
    $_SESSION['usuario']['nome'],
    $cpf_real,
    $observacao
]);

echo "<script>alert('Chave retirada com sucesso!'); window.location='dashboard.php';</script>";
