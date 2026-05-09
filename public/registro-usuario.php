<?php
require_once '../config/admin_auth.php'; // Solo entran Admins
require_once '../config/Database.php';

$database = new Database();
$db = $database->getConnection();
$mensaje = "";

// 1. Cargamos los equipos para el desplegable
$queryEquipos = "SELECT id, nombre_equipo FROM equipos";
$stmtEquipos = $db->prepare($queryEquipos);
$stmtEquipos->execute();
$equipos = $stmtEquipos->fetchAll(PDO::FETCH_OBJ);

// 2. Procesamos el formulario de registro
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $rol = $_POST['rol'];
    $equipo_id = !empty($_POST['equipo_id']) ? $_POST['equipo_id'] : null;

    $query = "INSERT INTO usuarios (nombre, email, password, rol, equipo_id) 
              VALUES (:nom, :ema, :pass, :rol, :eq)";
    $stmt = $db->prepare($query);
    
    $stmt->bindParam(':nom', $nombre);
    $stmt->bindParam(':ema', $email);
    $stmt->bindParam(':pass', $pass);
    $stmt->bindParam(':rol', $rol);
    $stmt->bindParam(':eq', $equipo_id);

    if ($stmt->execute()) {
        $mensaje = "✅ Usuario '$nombre' creado con éxito.";
    } else {
        $mensaje = "❌ Error al crear el usuario.";
    }
}

// 3. Consulta para el listado de usuarios
$queryListado = "SELECT u.*, e.nombre_equipo 
                 FROM usuarios u 
                 LEFT JOIN equipos e ON u.equipo_id = e.id 
                 ORDER BY u.rol ASC, u.nombre ASC";
$stmtListado = $db->prepare($queryListado);
$stmtListado->execute();
$usuarios = $stmtListado->fetchAll(PDO::FETCH_OBJ);

include 'includes/header.php';
?>

<div class="max-w-6xl mx-auto space-y-12">
    
    <!-- SECCIÓN: FORMULARIO DE ALTA -->
    <div class="max-w-2xl mx-auto">
        <div class="bg-slate-900 p-10 rounded-[2.5rem] border border-slate-800 shadow-2xl">
            <header class="mb-8">
                <h2 class="text-3xl font-black text-white italic uppercase tracking-tighter">Alta de Personal</h2>
                <p class="text-slate-500 text-sm">Registra nuevos administradores o rescatistas para el centro.</p>
            </header>

            <!-- Alerta de Reseteo de Password -->
            <?php if (isset($_GET['reset_success'])): ?>
                <div class="p-4 mb-6 bg-blue-500/10 border border-blue-500/50 text-blue-400 rounded-2xl text-center font-bold animate-pulse">
                    🔑 Contraseña reseteada con éxito a: <span class="text-white">Temporal123!</span>
                </div>
            <?php endif; ?>

            <!-- Alerta de Creación de Usuario -->
            <?php if($mensaje): ?>
                <div class="p-4 mb-6 bg-emerald-500/10 border border-emerald-500/50 text-emerald-400 rounded-2xl text-center font-bold">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase mb-2 ml-1 tracking-widest">Nombre Completo</label>
                        <input type="text" name="nombre" required class="w-full p-4 rounded-2xl bg-slate-800 border border-slate-700 text-white outline-none focus:border-yellow-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase mb-2 ml-1 tracking-widest">Email Oficial</label>
                        <input type="email" name="email" required class="w-full p-4 rounded-2xl bg-slate-800 border border-slate-700 text-white outline-none focus:border-yellow-500 transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase mb-2 ml-1 tracking-widest">Contraseña Temporal</label>
                        <input type="password" name="password" required class="w-full p-4 rounded-2xl bg-slate-800 border border-slate-700 text-white outline-none focus:border-yellow-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase mb-2 ml-1 tracking-widest">Rango / Rol</label>
                        <select name="rol" class="w-full p-4 rounded-2xl bg-slate-800 border border-slate-700 text-white outline-none focus:border-yellow-500 transition-all">
                            <option value="rescatista">Rescatista (Estándar)</option>
                            <option value="admin">Administrador (Total)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase mb-2 ml-1 tracking-widest">Asignar a Equipo (Opcional)</label>
                    <select name="equipo_id" class="w-full p-4 rounded-2xl bg-slate-800 border border-slate-700 text-white outline-none focus:border-yellow-500 transition-all">
                        <option value="">Sin equipo asignado</option>
                        <?php foreach($equipos as $e): ?>
                            <option value="<?php echo $e->id; ?>"><?php echo $e->nombre_equipo; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-400 text-slate-950 font-black py-4 rounded-2xl transition-all shadow-lg shadow-yellow-500/20 uppercase tracking-widest text-sm">
                    Confirmar Registro
                </button>
            </form>
        </div>
    </div>

    <!-- SECCIÓN: LISTADO DE PERSONAL -->
    <div class="bg-slate-900 rounded-[2.5rem] border border-slate-800 shadow-2xl overflow-hidden">
        <div class="p-8 border-b border-slate-800 bg-slate-800/20">
            <h3 class="text-xl font-black text-white italic uppercase tracking-tighter">Personal Registrado</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[10px] font-black text-slate-500 uppercase tracking-widest border-b border-slate-800">
                        <th class="p-6">Nombre</th>
                        <th class="p-6">Email / Contacto</th>
                        <th class="p-6">Rango</th>
                        <th class="p-6">Unidad / Equipo</th>
                        <th class="p-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    <?php foreach($usuarios as $user): ?>
                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="p-6 font-bold text-white"><?php echo $user->nombre; ?></td>
                        <td class="p-6 text-slate-400 text-sm"><?php echo $user->email; ?></td>
                        <td class="p-6">
                            <span class="px-3 py-1 text-[9px] font-black rounded-md border <?php echo $user->rol === 'admin' ? 'border-yellow-500 text-yellow-500' : 'border-blue-400 text-blue-400'; ?>">
                                <?php echo strtoupper($user->rol); ?>
                            </span>
                        </td>
                        <td class="p-6">
                            <span class="text-xs <?php echo $user->nombre_equipo ? 'text-slate-200' : 'text-slate-600 italic'; ?>">
                                <?php echo $user->nombre_equipo ?? 'Sin asignar'; ?>
                            </span>
                        </td>
                        <td class="p-6 text-center">
                            <div class="flex gap-2 justify-center">
                                <!-- Botón Editar (Lápiz) -->
                                <a href="editar-usuario.php?id=<?php echo $user->id; ?>" class="p-2 bg-slate-800 hover:bg-slate-700 rounded-lg text-yellow-500 transition-colors" title="Editar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </a>

                                <!-- Botón Reset Password (Llave) -->
                                <a href="acciones-usuario.php?action=reset_pass&id=<?php echo $user->id; ?>" 
                                   onclick="return confirm('¿Resetear contraseña de este usuario a: Temporal123! ?')"
                                   class="p-2 bg-slate-800 hover:bg-slate-700 rounded-lg text-blue-400 transition-colors" title="Resetear Password">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                                </a>

                                <!-- Botón Cambiar Rango (Flechas) -->
                                <a href="acciones-usuario.php?action=toggle_role&id=<?php echo $user->id; ?>" class="p-2 bg-slate-800 hover:bg-slate-700 rounded-lg text-emerald-400 transition-colors" title="Cambiar Rango">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>