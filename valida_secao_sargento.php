<?php

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['nivel'] !== 'sargento') {
    
    header("Location: ../logout.php");
    
    exit;
}
