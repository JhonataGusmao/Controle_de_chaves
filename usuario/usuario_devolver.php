```php
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
 
    $sql = $pdo->prepare(" 
        SELECT * FROM autorizados_chave  
        WHERE id_chave = ? AND LOWER(nome) = LOWER(?) 
    "); 
    $sql->execute([$chave_id, $nome_autorizado]); 
    $aut = $sql->fetch(PDO::FETCH_ASSOC); 
 
    if (!$aut) { 
        die("<script>alert('Esta pessoa NÃO está autorizada a devolver esta chave!'); window.location='dashboard.php';</script>"); 
    } 
 
    $cpf_real = $aut['cpf']; 
 
} else { 
    // CPF MASTER PARA OUTROS 
    $cpf_real = '00000000000'; 
} 
 
// ATUALIZA STATUS 
$upd = $pdo->prepare("UPDATE chaves SET status = 'disponivel' WHERE id = ?"); 
$upd->execute([$chave_id]); 
 
// ATUALIZA HISTÓRICO 
$hist = $pdo->prepare(" 
    UPDATE historico_chaves 
    SET devolvido_por = ?, 
        horario_devolucao = NOW(), 
        observacao_devolucao = ? 
    WHERE id = ( 
        SELECT id FROM historico_chaves 
        WHERE id_chave = ? AND devolvido_por IS NULL 
        ORDER BY id DESC LIMIT 1 
    ) 
"); 
 
$hist->execute([ 
    $cpf_real, 
    $observacao, 
    $chave_id 
]); 
 
echo "<script>alert('Chave devolvida com sucesso!'); window.location='dashboard.php';</script>"; 
