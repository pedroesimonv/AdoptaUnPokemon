<?php
require_once '../config/auth.php';
require_once '../config/Database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pokemon_id'])) {
    $database = new Database();
    $db = $database->getConnection();

    $id = $_POST['pokemon_id'];
    $equipo_id = !empty($_POST['nuevo_equipo']) ? $_POST['nuevo_equipo'] : null;

    // Actualizamos el equipo del Pokémon
    $query = "UPDATE pokemon_adopcion SET equipo_id = :eq WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':eq', $equipo_id);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        header("Location: listado-pokemon.php?actualizado=1");
    } else {
        header("Location: listado-pokemon.php?error=1");
    }
    exit();
} else {
    header("Location: listado-pokemon.php");
    exit();
}