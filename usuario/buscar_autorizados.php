<?php
require '../includes/conexao.php';


if (!isset($_GET['id'])) {
    echo json_encode([]);
    exit;
}

$id_chave = $_GET['id'];

// BUSCA NOMES DOS AUTORIZADOS NA TABELA CORRETA
$sql = $pdo->prepare("
    SELECT nome 
    FROM autorizados_chave 
    WHERE id_chave = ?
");
$sql->execute([$id_chave]);

$lista = $sql->fetchAll(PDO::FETCH_COLUMN);

echo json_encode($lista);
?>
