<?php
require_once '../config/auth.php';
require_once '../config/Database.php';

// Verificamos que nos llegue un ID por la URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $database = new Database();
    $db = $database->getConnection();

    // Preparamos la actualización
    $query = "UPDATE pokemon_adopcion SET estado = 'adoptado' WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // Si todo sale bien, volvemos al listado con un mensaje de éxito
        header("Location: listado-pokemon.php?adoptado=1");
    } else {
        echo "Error al procesar la adopción.";
    }
} else {
    header("Location: listado-pokemon.php");
}