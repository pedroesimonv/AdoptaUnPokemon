<?php
session_start();
session_destroy(); // Borramos todos los datos de la sesión
header("Location: index.php"); // Volvemos al login
exit();
?>