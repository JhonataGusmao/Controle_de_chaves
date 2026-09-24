<?php
session_start();
define('DEBUG', true);
require 'includes/conexao.php';
if (DEBUG) { ini_set('display_errors',1); error_reporting(E_ALL); }

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $usuario_input = trim($_POST['usuario'] ?? '');
    $senha_input   = trim($_POST['senha'] ?? '');

    if ($usuario_input === '' || $senha_input === '') {
        $erro = 'Preencha usuário e senha.';
    } else {

        // busca usuario por login
        $sql = $pdo->prepare("SELECT * FROM usuarios WHERE login = :u OR usuario = :u LIMIT 1");
        $sql->execute([':u' => $usuario_input]);
        $user = $sql->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $erro = 'Usuário não encontrado.';
        } 
        else if ($senha_input === $user['senha']) {

            $_SESSION['usuario'] = [
                'id'    => $user['id'],
                'nome'  => $user['nome'],
                'nivel' => $user['nivel'],
                'login' => $user['login']
            ];

            // ------------------------------------
            // REDIRECIONAMENTO POR NÍVEL
            // ------------------------------------
            if ($user['nivel'] === 'admin') {
                header("Location: admin/dashboard.php");
                exit;
            } 
            elseif ($user['nivel'] === 'sargento') {
                header("Location: sargento/dashboard.php");
                exit;
            }
            else { // usuário
                header("Location: usuario/dashboard.php");
                exit;
            }

        } else {
            $erro = 'Senha incorreta.';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login - Controle de Chaves</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<h1>Controle de Chaves</h1>

<div class="bg"><img src="img/chavefab.png" class="key"></div>

<div class="wrapper">
    <div class="login-box">
        <h2>Login</h2>

        <?php if (!empty($erro)): ?>
            <p class="erro"><?= htmlspecialchars($erro) ?></p>
        <?php endif; ?>

        <form method="post" action="">
            <input type="text" name="usuario" placeholder="Usuário (login)" required
                value="<?= isset($usuario_input) ? htmlspecialchars($usuario_input) : '' ?>">

            <input type="password" name="senha" placeholder="Senha" required>

            <button type="submit">Entrar</button>
        </form>
    </div>
</div>

</body>
</html>
