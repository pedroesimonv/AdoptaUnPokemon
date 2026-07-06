<?php
require_once '../config/auth.php';
require_once '../config/Database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pokemon_id'])) {
    $database = new Database();
    $db = $database->getConnection();

    $pokemon_id = $_POST['pokemon_id'];
    $nueva_nota = $_POST['nota_salud'];
    $nuevo_estado = $_POST['nuevo_estado'];

    //Verificamos que el rescatista tenga permiso (que el pokemon sea de su equipo)
    $stmtCheck = $db->prepare("SELECT equipo_id, descripcion FROM pokemon_adopcion WHERE id = ?");
    $stmtCheck->execute([$pokemon_id]);
    $poke = $stmtCheck->fetch(PDO::FETCH_OBJ);

    // Obtenemos el equipo del usuario logueado
    $stmtU = $db->prepare("SELECT equipo_id FROM usuarios WHERE id = ?");
    $stmtU->execute([$_SESSION['usuario_id']]);
    $user = $stmtU->fetch(PDO::FETCH_OBJ);

    if ($poke && $poke->equipo_id == $user->equipo_id) {
        //Actualizamos el estado y añadimos la nota a la bitácora (descripción)
        $fecha = date('d/m/Y');
        $descripcion_actualizada = $poke->descripcion . "\n\n[Bitácora $fecha]: " . $nueva_nota;

        $query = "UPDATE pokemon_adopcion 
                  SET estado = :est, descripcion = :desc 
                  WHERE id = :id";
        
        $stmt = $db->prepare($query);
        $stmt->execute([
            ':est'  => $nuevo_estado,
            ':desc' => $descripcion_actualizada,
            ':id'   => $pokemon_id
        ]);

        header("Location: mi-panel.php?notificado=1");
    } else {
        header("Location: mi-panel.php?error=permiso");
    }
    exit();
}