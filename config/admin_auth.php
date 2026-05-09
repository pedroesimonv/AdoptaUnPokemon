<?php
require_once 'auth.php'; // Primero verificamos que esté logueado

if ($_SESSION['usuario_rol'] !== 'admin') {
    // Si no es admin, lo mandamos al listado normal con un aviso
    header("Location: listado-pokemon.php?error=no_autorizado");
    exit();
}