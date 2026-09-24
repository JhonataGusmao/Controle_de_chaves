<?php

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['nivel'] !== 'usuario') {
    
    header("Location: ../logout.php");
    
    exit;
}
