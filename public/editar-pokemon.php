<?php
require_once '../config/admin_auth.php'; // 🛡️ Solo los jefes editan fichas
require_once '../config/Database.php';
require_once '../config/PokeApi.php';

$database = new Database();
$db = $database->getConnection();
$mensaje = "";

//Verificar si tenemos el ID del Pokémon
if (!isset($_GET['id'])) {
    header("Location: listado-pokemon.php");
    exit();
}

$id = $_GET['id'];

//Procesar la actualización si se envía el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre_propio'];
    $estado = $_POST['estado'];
    $descripcion = $_POST['descripcion'];
    $equipo_id = !empty($_POST['equipo_id']) ? $_POST['equipo_id'] : null;

    $queryUpd = "UPDATE pokemon_adopcion 
                 SET nombre_propio = :nom, estado = :est, descripcion = :desc, equipo_id = :eq 
                 WHERE id = :id";
    
    $stmtUpd = $db->prepare($queryUpd);
    $stmtUpd->execute([
        ':nom'  => $nombre,
        ':est'  => $estado,
        ':desc' => $descripcion,
        ':eq'   => $equipo_id,
        ':id'   => $id
    ]);
    
    $mensaje = "✅ Ficha de rescate actualizada.";
}

// 3. Obtener los datos actuales del Pokémon
$stmt = $db->prepare("SELECT * FROM pokemon_adopcion WHERE id = ?");
$stmt->execute([$id]);
$pokemon = $stmt->fetch(PDO::FETCH_OBJ);

if (!$pokemon) {
    die("Pokémon no encontrado en la base de datos.");
}

//Obtener datos de la API para la cabecera visual
$pokeData = PokeApi::getPokemonData($pokemon->especie_api_id);

//Obtener equipos para el desplegable
$equipos = $db->query("SELECT id, nombre_equipo FROM equipos")->fetchAll(PDO::FETCH_OBJ);

include 'includes/header.php';
?>

<div class="max-w-3xl mx-auto">
    <div class="bg-slate-900 rounded-[3rem] border border-slate-800 shadow-2xl overflow-hidden">
        
        <div class="bg-slate-800/50 p-10 flex items-center gap-8 border-b border-slate-800">
            <img src="<?php echo $pokeData['imagen']; ?>" class="w-32 h-32 object-contain drop-shadow-2xl">
            <div>
                <span class="text-yellow-500 font-black uppercase tracking-widest text-[10px]">Editando Rescate #<?php echo $pokemon->id; ?></span>
                <h2 class="text-4xl font-black text-white italic uppercase tracking-tighter">Ficha de <?php echo $pokemon->nombre_propio; ?></h2>
                <p class="text-slate-400 text-sm italic">Especie original: <span class="capitalize text-slate-200"><?php echo $pokeData['nombre']; ?></span></p>
            </div>
        </div>

        <form method="POST" class="p-10 space-y-8">
            
            <?php if($mensaje): ?>
                <div class="p-4 bg-emerald-500/10 border border-emerald-500/50 text-emerald-400 rounded-2xl text-center font-bold">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase mb-3 ml-1 tracking-widest">Nombre del Pokémon</label>
                    <input type="text" name="nombre_propio" value="<?php echo htmlspecialchars($pokemon->nombre_propio); ?>" required 
                           class="w-full p-4 rounded-2xl bg-slate-800 border border-slate-700 text-white outline-none focus:border-yellow-500 transition-all">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase mb-3 ml-1 tracking-widest">Estado de Salud/Legal</label>
                    <select name="estado" class="w-full p-4 rounded-2xl bg-slate-800 border border-slate-700 text-white outline-none focus:border-yellow-500 transition-all">
                        <option value="disponible" <?php echo $pokemon->estado == 'disponible' ? 'selected' : ''; ?>>Disponible para Misión / Adopción</option>
                        <option value="herido" <?php echo $pokemon->estado == 'herido' ? 'selected' : ''; ?>>En Enfermería (Herido)</option>
                        <option value="en_rescate" <?php echo $pokemon->estado == 'en_rescate' ? 'selected' : ''; ?>>En Misión Activa</option>
                        <option value="adoptado" <?php echo $pokemon->estado == 'adoptado' ? 'selected' : ''; ?>>Ya Adoptado</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase mb-3 ml-1 tracking-widest">Unidad de Rescate Asignada</label>
                <select name="equipo_id" class="w-full p-4 rounded-2xl bg-slate-800 border border-slate-700 text-white outline-none focus:border-yellow-500 transition-all">
                    <option value="">Sin Unidad (En Reserva)</option>
                    <?php foreach($equipos as $e): ?>
                        <option value="<?php echo $e->id; ?>" <?php echo $pokemon->equipo_id == $e->id ? 'selected' : ''; ?>>
                            <?php echo $e->nombre_equipo; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="text-[9px] text-slate-600 mt-2 ml-1 italic">* Solo asignar unidades si el Pokémon está en estado "Disponible".</p>
            </div>

            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase mb-3 ml-1 tracking-widest">Notas del Rescate / Historia</label>
                <textarea name="descripcion" rows="4" required 
                          class="w-full p-4 rounded-2xl bg-slate-800 border border-slate-700 text-white outline-none focus:border-yellow-500 transition-all resize-none"><?php echo htmlspecialchars($pokemon->descripcion); ?></textarea>
            </div>

            <div class="flex flex-col md:flex-row gap-4 pt-4">
                <button type="submit" class="flex-1 bg-yellow-500 hover:bg-yellow-400 text-slate-950 font-black py-4 rounded-2xl transition-all shadow-lg shadow-yellow-500/20 uppercase tracking-widest text-sm">
                    Guardar Cambios
                </button>
                <a href="listado-pokemon.php" class="flex-1 bg-slate-800 hover:bg-slate-700 text-white font-black py-4 rounded-2xl transition-all text-center uppercase tracking-widest text-sm border border-slate-700">
                    Volver al Listado
                </a>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>