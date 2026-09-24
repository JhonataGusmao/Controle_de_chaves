<?php
session_start();
require '../includes/conexao.php';

require '../valida_secao.php';



// Verifica se id foi enviado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: chaves.php?msg=missing_id");
    exit;
}

$id = (int) $_GET['id'];

try {
    // Inicia transaction
    $pdo->beginTransaction();

    // 1) Apaga movimentos relacionados (se existirem)
    $delMov = $pdo->prepare("DELETE FROM movimentos WHERE id_chave = ?");
    $delMov->execute([$id]);

    // 2) Apaga autorizados relacionados (tabela pode chamar 'autorizados_chave' ou similar)
    // Ajuste o nome da tabela se for diferente
    $delAut = $pdo->prepare("DELETE FROM autorizados_chave WHERE id_chave = ?");
    $delAut->execute([$id]);

    // 3) Apaga a própria chave
    $delChave = $pdo->prepare("DELETE FROM chaves WHERE id = ?");
    $delChave->execute([$id]);

    $pdo->commit();

    header("Location: chaves.php?msg=deleted");
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    // Log do erro (em ambiente dev é útil mostrar, em prod não)
    error_log("Erro ao excluir chave: " . $e->getMessage());
    header("Location: chaves.php?msg=error");
    exit;
}
