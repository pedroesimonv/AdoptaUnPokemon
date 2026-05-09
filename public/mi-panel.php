<?php
require_once '../config/auth.php';
require_once '../config/Database.php';
require_once '../config/PokeApi.php';

$database = new Database();
$db = $database->getConnection();

// 1. Obtenemos el equipo de Benito
$stmtU = $db->prepare("SELECT equipo_id FROM usuarios WHERE id = ?");
$stmtU->execute([$_SESSION['usuario_id']]);
$userData = $stmtU->fetch(PDO::FETCH_OBJ);
$equipo_id = $userData->equipo_id;

$equipo = null;
$compañeros = [];
$mis_pokemons = [];

if ($equipo_id) {
    // 2. Info del equipo
    $stmtE = $db->prepare("SELECT * FROM equipos WHERE id = ?");
    $stmtE->execute([$equipo_id]);
    $equipo = $stmtE->fetch(PDO::FETCH_OBJ);

    // 3. Compañeros
    $stmtC = $db->prepare("SELECT nombre, rol FROM usuarios WHERE equipo_id = ? AND id != ?");
    $stmtC->execute([$equipo_id, $_SESSION['usuario_id']]);
    $compañeros = $stmtC->fetchAll(PDO::FETCH_OBJ);

    // 4. Pokémon asignados (Aquí está la clave)
    $stmtP = $db->prepare("SELECT * FROM pokemon_adopcion WHERE equipo_id = ?");
    $stmtP->execute([$equipo_id]);
    $mis_pokemons = $stmtP->fetchAll(PDO::FETCH_OBJ);
}

include 'includes/header.php';
?>

<div class="max-w-6xl mx-auto">
    <?php if (!$equipo_id): ?>
        <div class="bg-slate-900 p-12 rounded-[3rem] border border-slate-800 text-center">
            <span class="text-6xl">🔍</span>
            <h2 class="text-3xl font-black text-white uppercase italic mt-6">Unidad no asignada</h2>
            <p class="text-slate-500 mt-4">Contacta con el administrador para recibir órdenes.</p>
        </div>
    <?php else: ?>
        
        <div class="bg-slate-900 p-10 rounded-[3rem] border border-slate-800 mb-10 flex flex-col md:flex-row justify-between items-center gap-6">
            <div>
                <span class="px-3 py-1 bg-yellow-500/10 text-yellow-500 text-[10px] font-black uppercase tracking-widest rounded-lg border border-yellow-500/20">Unidad Activa</span>
                <h2 class="text-5xl font-black text-white italic uppercase tracking-tighter mt-2"><?php echo $equipo->nombre_equipo; ?></h2>
                <p class="text-slate-400 font-bold uppercase text-xs mt-1 tracking-widest">📍 Sector: <?php echo $equipo->zona_asignada; ?></p>
            </div>
            <div class="text-center md:text-right">
                <p class="text-slate-500 text-[10px] font-black uppercase mb-1">Tu Rango</p>
                <span class="text-2xl font-black text-blue-400 italic uppercase"><?php echo $_SESSION['usuario_rol']; ?></span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <div class="space-y-6">
                <h3 class="text-xl font-black text-white uppercase italic tracking-tight flex items-center gap-2">👥 Compañeros</h3>
                <div class="bg-slate-900/50 border border-slate-800 rounded-[2rem] p-6 space-y-4">
                    <?php if (empty($compañeros)): ?>
                        <p class="text-slate-600 text-sm italic">Único miembro activo.</p>
                    <?php else: foreach($compañeros as $c): ?>
                        <div class="flex items-center justify-between p-4 bg-slate-800/30 rounded-2xl border border-slate-700/50">
                            <span class="font-bold text-slate-200"><?php echo $c->nombre; ?></span>
                            <span class="text-[9px] font-black uppercase text-slate-500"><?php echo $c->rol; ?></span>
                        </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-6">
                <h3 class="text-xl font-black text-white uppercase italic tracking-tight flex items-center gap-2">⚽ Objetivos de la Unidad</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php foreach($mis_pokemons as $mp): 
                        $pData = PokeApi::getPokemonData($mp->especie_api_id);
                    ?>
                        <div class="bg-slate-900 border border-slate-800 p-6 rounded-3xl hover:border-slate-600 transition-all flex flex-col">
                            <div class="flex items-center gap-6 mb-4">
                                <img src="<?php echo $pData['imagen']; ?>" class="w-20 h-20 object-contain drop-shadow-xl">
                                <div>
                                    <h4 class="text-xl font-black text-white uppercase italic leading-none"><?php echo $mp->nombre_propio; ?></h4>
                                    <span class="text-[9px] font-black text-slate-500 uppercase"><?php echo $pData['nombre']; ?></span>
                                    <div class="mt-3">
                                        <span class="px-2 py-1 text-[8px] font-black rounded border <?php echo $mp->estado == 'herido' ? 'border-red-500 text-red-500' : 'border-blue-500 text-blue-500'; ?>">
                                            <?php echo strtoupper($mp->estado); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-slate-800 mt-auto">
                                <?php if($mp->estado == 'herido'): ?>
                                    <form action="actualizar-salud.php" method="POST" class="space-y-3">
                                        <input type="hidden" name="pokemon_id" value="<?php echo $mp->id; ?>">
                                        <input type="hidden" name="nuevo_estado" value="disponible">
                                        <textarea name="nota_salud" placeholder="Parte médico..." required class="w-full p-3 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white outline-none focus:border-emerald-500 resize-none"></textarea>
                                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white text-[10px] font-black uppercase py-2.5 rounded-xl transition-all">Dar Alta Médica ✅</button>
                                    </form>
                                <?php else: ?>
                                    <p class="text-[10px] text-slate-600 italic text-center py-2">Estado óptimo para servicio.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php if(empty($mis_pokemons)): ?>
                        <div class="col-span-full p-10 border border-dashed border-slate-800 rounded-3xl text-center">
                            <p class="text-slate-600 italic">No hay Pokémon asignados.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>