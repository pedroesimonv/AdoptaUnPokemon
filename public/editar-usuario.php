<?php
require_once '../config/admin_auth.php';
require_once '../config/Database.php';

$database = new Database();
$db = $database->getConnection();
$mensaje = "";

// 1. Obtener los datos del usuario a editar
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Si se envía el formulario de actualización
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nombre = $_POST['nombre'];
        $email = $_POST['email'];
        $rol = $_POST['rol'];
        $equipo_id = !empty($_POST['equipo_id']) ? $_POST['equipo_id'] : null;

        $update = "UPDATE usuarios SET nombre = :nom, email = :ema, rol = :rol, equipo_id = :eq WHERE id = :id";
        $stmtUpd = $db->prepare($update);
        $stmtUpd->execute([':nom' => $nombre, ':ema' => $email, ':rol' => $rol, ':eq' => $equipo_id, ':id' => $id]);
        $mensaje = "✅ Perfil actualizado correctamente.";
    }

    // Consultar datos actuales del usuario
    $stmt = $db->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_OBJ);

    // Consultar equipos disponibles para el desplegable
    $equipos = $db->query("SELECT id, nombre_equipo, especialidad FROM equipos")->fetchAll(PDO::FETCH_OBJ);
} else {
    header("Location: registro-usuario.php");
    exit();
}

include 'includes/header.php';
?>

<div class="max-w-2xl mx-auto">
    <div class="bg-slate-900 p-10 rounded-[2.5rem] border border-slate-800 shadow-2xl relative overflow-hidden">
        
        <div class="absolute top-0 right-0 p-8">
            <span class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-700 select-none">Ficha de Personal</span>
        </div>

        <header class="mb-10">
            <h2 class="text-3xl font-black text-white italic uppercase tracking-tighter">Editar Rescatista</h2>
            <p class="text-slate-500 text-sm">Modificando el perfil de: <span class="text-yellow-500"><?php echo $user->nombre; ?></span></p>
        </header>

        <?php if($mensaje): ?>
            <div class="p-4 mb-8 bg-emerald-500/10 border border-emerald-500/50 text-emerald-400 rounded-2xl text-center font-bold animate-pulse">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase mb-2 ml-1">Nombre</label>
                    <input type="text" name="nombre" value="<?php echo $user->nombre; ?>" required 
                           class="w-full p-4 rounded-2xl bg-slate-800 border border-slate-700 text-white outline-none focus:border-yellow-500">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase mb-2 ml-1">Email</label>
                    <input type="email" name="email" value="<?php echo $user->email; ?>" required 
                           class="w-full p-4 rounded-2xl bg-slate-800 border border-slate-700 text-white outline-none focus:border-yellow-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase mb-2 ml-1">Rango del Sistema</label>
                    <select name="rol" class="w-full p-4 rounded-2xl bg-slate-800 border border-slate-700 text-white outline-none focus:border-yellow-500">
                        <option value="rescatista" <?php echo $user->rol == 'rescatista' ? 'selected' : ''; ?>>Rescatista</option>
                        <option value="admin" <?php echo $user->rol == 'admin' ? 'selected' : ''; ?>>Administrador</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase mb-2 ml-1">Asignar a Unidad</label>
                    <select name="equipo_id" class="w-full p-4 rounded-2xl bg-slate-800 border border-slate-700 text-white outline-none focus:border-yellow-500">
                        <option value="">Sin Unidad Asignada</option>
                        <?php foreach($equipos as $e): ?>
                            <option value="<?php echo $e->id; ?>" <?php echo $user->equipo_id == $e->id ? 'selected' : ''; ?>>
                                <?php echo $e->nombre_equipo; ?> (<?php echo $e->especialidad; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="pt-6 flex flex-col md:flex-row gap-4">
                <button type="submit" class="flex-1 bg-yellow-500 hover:bg-yellow-400 text-slate-950 font-black py-4 rounded-2xl transition-all shadow-lg shadow-yellow-500/20 uppercase tracking-widest text-sm">
                    Guardar Cambios
                </button>
                
                <a href="acciones-usuario.php?action=delete&id=<?php echo $user->id; ?>" 
                   onclick="return confirm('¿ESTÁS SEGURO? Esta acción es irreversible y Benito será expulsado del sistema.')"
                   class="px-8 py-4 bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white border border-red-500/50 rounded-2xl transition-all text-xs font-black uppercase tracking-widest flex items-center justify-center">
                    Eliminar
                </a>
            </div>
        </form>

        <div class="mt-8 text-center">
            <a href="registro-usuario.php" class="text-slate-600 hover:text-slate-400 text-[10px] font-black uppercase tracking-widest">← Volver al Listado</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>