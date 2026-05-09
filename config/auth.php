<?php
session_start();

// Si no existe la variable de sesión 'usuario_id', es que no se ha logueado
if (!isset($_SESSION['usuario_id'])) {
    // Lo mandamos de vuelta al login
    header("Location: index.php");
    exit(); // Bloqueamos el resto del código
}
?>