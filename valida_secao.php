<?php

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['nivel'] !== 'admin') {
    
    header("Location: ../logout.php");
    
    exit;
}
