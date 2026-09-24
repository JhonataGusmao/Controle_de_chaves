<?php
session_start();
require '../includes/conexao.php';

require '../valida_secao.php';


$id = $_GET['id'];

$sql = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
$sql->execute([':id' => $id]);

header("Location: listar_usuarios.php?msg=deleted");
exit;
