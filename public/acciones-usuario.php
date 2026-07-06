<?php
require_once '../config/admin_auth.php'; //Solo el Admin hace esto
require_once '../config/Database.php';

if (isset($_GET['action']) && isset($_GET['id'])) {
    $database = new Database();
    $db = $database->getConnection();
    $id = $_GET['id'];

    // 1. Alternar Rango (Admin <-> Rescatista)
    if ($_GET['action'] === 'toggle_role') {
        $query = "SELECT rol FROM usuarios WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_OBJ);

        if ($user) {
            $nuevoRol = ($user->rol === 'admin') ? 'rescatista' : 'admin';
            $db->prepare("UPDATE usuarios SET rol = ? WHERE id = ?")->execute([$nuevoRol, $id]);
        }
    }

    // 2. Eliminar Usuario
    if ($_GET['action'] === 'delete') {
        if ($id != $_SESSION['usuario_id']) {
            $db->prepare("DELETE FROM usuarios WHERE id = ?")->execute([$id]);
        }
    }

    // 3. NUEVO: Resetear Contraseña a "Temporal123!"
    if ($_GET['action'] === 'reset_pass') {
        $passTemporal = password_hash("Temporal123!", PASSWORD_BCRYPT);
        $query = "UPDATE usuarios SET password = ? WHERE id = ?";
        $stmt = $db->prepare($query);
        
        if ($stmt->execute([$passTemporal, $id])) {
            header("Location: registro-usuario.php?reset_success=1");
            exit();
        }
    }
    
    header("Location: registro-usuario.php");
    exit();
}