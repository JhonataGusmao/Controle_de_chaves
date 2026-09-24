<?php
require 'includes/auth.php';
require 'includes/conexao.php';

$sql = $pdo->query("SELECT * FROM chaves");
$chaves = $sql->fetchAll();
?>
