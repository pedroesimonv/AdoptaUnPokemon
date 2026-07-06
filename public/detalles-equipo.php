<?php
require_once '../config/auth.php';
require_once '../config/Database.php';
require_once '../config/PokeApi.php';

$database = new Database();
$db = $database->getConnection();

if (isset($_GET['id'])) {
    $equipo_id = $_GET['id'];

    //Obtener datos del equipo
    $stmt = $db->prepare("SELECT * FROM equipos WHERE id = ?");
    $stmt->execute([$equipo_id]);
    $equipo = $stmt->fetch(PDO::FETCH_OBJ);

    //Obtener miembros del equipo (Usuarios)
    $stmtM = $db->prepare("SELECT nombre, rol FROM usuarios WHERE equipo_id = ?");
    $stmtM->execute([$equipo_id]);
    $miembros = $stmtM->fetchAll(PDO::FETCH_OBJ);

    //Obtener Pokémon asignados a esta unidad
    $stmtP = $db->prepare("SELECT * FROM pokemon_adopcion WHERE equipo_id = ?");
    $stmtP->execute([$equipo_id]);
    $pokemons_asignados = $stmtP->fetchAll(PDO::FETCH_OBJ);
} else {
    header("Location: gestion-equipos.php");
    exit();
}

include 'includes/header.php';
?>

<div class="max-w-6xl mx-auto">
    <div class="bg-slate-900 p-10 rounded-[3rem] border border-slate-800 mb-10 relative overflow-hidden">
        <div class="absolute top-0 right-0 p-10 opacity-10 text-9xl">
            <?php echo ($equipo->especialidad == 'Incendios') ? '🔥' : (($equipo->especialidad == 'Rescate Acuático') ? '🌊' : '🏔️'); ?>
        </div>
        
        <span class="text-yellow-500 font-black uppercase tracking-[0.3em] text-[10px]">Unidad Operativa Especializada</span>
        <h2 class="text-5xl font-black text-white italic uppercase tracking-tighter mt-2"><?php echo $equipo->nombre_equipo; ?></h2>
        <p class="text-slate-400 mt-4 max-w-2xl">Especialistas en <b><?php echo $equipo->especialidad; ?></b>. Operando actualmente en la zona: <b><?php echo $equipo->zona_asignada; ?></b>.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <div class="bg-slate-900/50 p-8 rounded-[2.5rem] border border-slate-800">
            <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                <span class="text-blue-400">👤</span> Personal de la Unidad
            </h3>
            <ul class="space-y-4">
                <?php foreach($miembros as $m): ?>
                    <li class="flex items-center justify-between p-4 bg-slate-800/40 rounded-2xl border border-slate-700/50">
                        <span class="font-bold text-slate-200"><?php echo $m->nombre; ?></span>
                        <span class="text-[9px] font-black uppercase px-2 py-1 bg-slate-700 rounded text-slate-400"><?php echo $m->rol; ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="lg:col-span-2">
            <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                <span class="text-yellow-500">⚽</span> Fuerza Pokémon Desplegada
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach($pokemons_asignados as $pa): 
                    $pData = PokeApi::getPokemonData($pa->especie_api_id);
                ?>
                    <div class="flex items-center gap-4 p-4 bg-slate-900 border border-slate-800 rounded-2xl">
                        <img src="<?php echo $pData['imagen']; ?>" class="w-16 h-16 object-contain" alt="">
                        <div>
                            <h4 class="font-black text-white uppercase italic text-sm"><?php echo $pa->nombre_propio; ?></h4>
                            <span class="text-[10px] text-slate-500 uppercase font-bold">Estado: <?php echo $pa->estado; ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if(empty($pokemons_asignados)) echo "<p class='text-slate-600 italic'>No hay Pokémon asignados a esta unidad todavía.</p>"; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>