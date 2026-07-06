<?php
require_once '../config/auth.php'; 
require_once '../config/Database.php';
require_once '../config/PokeApi.php';

$database = new Database();
$db = $database->getConnection();

//OBTENER DATOS DEL USUARIO
$stmtU = $db->prepare("SELECT equipo_id FROM usuarios WHERE id = ?");
$stmtU->execute([$_SESSION['usuario_id']]);
$userDB = $stmtU->fetch(PDO::FETCH_OBJ);
$mi_equipo_id = $userDB->equipo_id ?? null;

//CAPTURAR FILTROS
$filtro_estado = $_GET['estado'] ?? '';
$filtro_unidad = $_GET['unidad'] ?? '';

//CONSTRUCCIÓN DINÁMICA DE LA CONSULTA SQL
$sql = "SELECT * FROM pokemon_adopcion WHERE 1=1";
$params = [];

if ($filtro_estado) {
    $sql .= " AND estado = :estado";
    $params[':estado'] = $filtro_estado;
}

if ($filtro_unidad === 'mia' && $mi_equipo_id) {
    $sql .= " AND equipo_id = :mi_eq";
    $params[':mi_eq'] = $mi_equipo_id;
}

$sql .= " ORDER BY fecha_rescate DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$pokemons = $stmt->fetchAll(PDO::FETCH_OBJ);

//DATOS PARA SELECTORES
$queryEquipos = "SELECT id, nombre_equipo FROM equipos";
$todosLosEquipos = $db->query($queryEquipos)->fetchAll(PDO::FETCH_OBJ);

$coloresTipos = [
    'fire' => 'bg-orange-600 border-orange-400', 'water' => 'bg-blue-600 border-blue-400',
    'grass' => 'bg-emerald-600 border-emerald-400', 'electric' => 'bg-yellow-500 border-yellow-300 text-slate-900',
    'bug' => 'bg-lime-600 border-lime-400', 'normal' => 'bg-slate-500 border-slate-400',
    'poison' => 'bg-purple-600 border-purple-400', 'ground' => 'bg-amber-700 border-amber-500',
    'fairy' => 'bg-pink-400 border-pink-300', 'fighting' => 'bg-red-700 border-red-500',
    'psychic' => 'bg-fuchsia-500 border-fuchsia-300', 'rock' => 'bg-stone-600 border-stone-400',
    'ghost' => 'bg-indigo-800 border-indigo-600', 'ice' => 'bg-cyan-300 border-cyan-100 text-slate-900',
    'dragon' => 'bg-violet-700 border-violet-500',
];

include 'includes/header.php';
?>

<!-- BARRA DE FILTROS -->
<div class="mb-10 bg-slate-900 p-6 rounded-[2rem] border border-slate-800 shadow-xl">
    <form method="GET" class="flex flex-wrap items-center gap-6">
        <div class="flex items-center gap-3">
            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Estado:</label>
            <select name="estado" onchange="this.form.submit()" class="bg-slate-800 border border-slate-700 text-white text-xs p-3 rounded-xl outline-none focus:border-yellow-500 transition-all">
                <option value="">Todos los estados</option>
                <option value="disponible" <?php echo $filtro_estado == 'disponible' ? 'selected' : ''; ?>>Disponibles</option>
                <option value="herido" <?php echo $filtro_estado == 'herido' ? 'selected' : ''; ?>>🚑 Heridos (Enfermería)</option>
                <option value="en_rescate" <?php echo $filtro_estado == 'en_rescate' ? 'selected' : ''; ?>>En Misión</option>
                <option value="adoptado" <?php echo $filtro_estado == 'adoptado' ? 'selected' : ''; ?>>Adoptados</option>
            </select>
        </div>

        <?php if ($mi_equipo_id): ?>
        <div class="flex items-center gap-3">
            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Unidad:</label>
            <select name="unidad" onchange="this.form.submit()" class="bg-slate-800 border border-slate-700 text-white text-xs p-3 rounded-xl outline-none focus:border-yellow-500 transition-all">
                <option value="">Todas las unidades</option>
                <option value="mia" <?php echo $filtro_unidad == 'mia' ? 'selected' : ''; ?>>Mi Unidad Asignada</option>
            </select>
        </div>
        <?php endif; ?>

        <a href="listado-pokemon.php" class="text-[10px] font-black text-slate-600 hover:text-white uppercase tracking-widest transition-colors ml-auto">Limpiar Filtros</a>
    </form>
</div>

<!-- REJILLA DE CARTAS -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
    <?php if (count($pokemons) > 0): ?>
        <?php foreach ($pokemons as $p): 
            $pokeData = PokeApi::getPokemonData($p->especie_api_id);
            $imgUrl = $pokeData['imagen'] ?? 'assets/img/placeholder.png';
            $nombreOficial = $pokeData['nombre'] ?? '???';
            $tipos = $pokeData['tipos'] ?? ['normal'];
            $colorBase = $coloresTipos[$tipos[0]] ?? 'bg-slate-800';

            $nombreEquipoAsignado = "Sin Unidad";
            if ($p->equipo_id) {
                foreach($todosLosEquipos as $eq) {
                    if($eq->id == $p->equipo_id) $nombreEquipoAsignado = $eq->nombre_equipo;
                }
            }
        ?>
            <!-- TARJETA POKÉMON -->
            <div class="group bg-slate-900 rounded-[2.5rem] overflow-hidden border border-slate-800 shadow-2xl hover:-translate-y-2 transition-all duration-500">
                <div class="relative h-52 flex items-center justify-center p-8 bg-slate-800/30">
                    <div class="absolute inset-0 opacity-10 <?php echo explode(' ', $colorBase)[0]; ?>"></div>
                    <img src="<?php echo $imgUrl; ?>" class="h-full object-contain z-10 drop-shadow-2xl group-hover:scale-110 transition-transform duration-500">
                    
                    <div class="absolute top-4 left-6 flex gap-1 z-20">
                        <?php foreach($tipos as $t): ?>
                            <span class="px-2 py-0.5 text-[8px] font-black uppercase rounded border <?php echo $coloresTipos[$t] ?? 'bg-slate-700'; ?>">
                                <?php echo $t; ?>
                            </span>
                        <?php endforeach; ?>
                    </div>

                    <span class="absolute top-2 right-6 text-slate-800 font-black text-5xl italic opacity-40 z-0">
                        #<?php echo str_pad($p->especie_api_id, 3, "0", STR_PAD_LEFT); ?>
                    </span>
                </div>

                <div class="p-8">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h2 class="text-3xl font-black capitalize text-white italic tracking-tighter leading-none"><?php echo htmlspecialchars($p->nombre_propio); ?></h2>
                            <span class="text-[10px] text-slate-500 font-bold uppercase tracking-widest block mt-1">(<?php echo $nombreOficial; ?>)</span>
                        </div>
                        <span class="px-2 py-1 text-[9px] font-black rounded border-2 whitespace-nowrap
                            <?php echo $p->estado == 'disponible' ? 'border-blue-500 text-blue-400' : ($p->estado == 'herido' ? 'border-red-500 text-red-400' : 'border-emerald-500 text-emerald-400'); ?>">
                            <?php echo strtoupper($p->estado); ?>
                        </span>
                    </div>

                    <p class="text-slate-400 text-sm line-clamp-2 h-10 italic mb-4">
                        "<?php echo htmlspecialchars($p->descripcion); ?>"
                    </p>

                    <div class="py-3 px-4 bg-slate-950/50 rounded-xl border border-slate-800/50 flex justify-between items-center mb-6">
                        <span class="text-[9px] font-black text-slate-600 uppercase">Unidad:</span>
                        <span class="text-[10px] font-bold <?php echo ($p->estado == 'herido') ? 'text-red-400 animate-pulse' : 'text-yellow-500'; ?> italic text-right ml-2">
                            <?php echo ($p->estado == 'herido') ? '🏥 EN ENFERMERÍA' : strtoupper($nombreEquipoAsignado); ?>
                        </span>
                    </div>

                    <?php if ($_SESSION['usuario_rol'] === 'admin'): ?>
                        <div class="space-y-3 pt-4 border-t border-slate-800">
                            <?php if ($p->estado != 'herido' && $p->estado != 'adoptado'): ?>
                                <form action="acciones-pokemon.php" method="POST">
                                    <input type="hidden" name="pokemon_id" value="<?php echo $p->id; ?>">
                                    <select name="nuevo_equipo" onchange="this.form.submit()" class="w-full text-[10px] bg-slate-800 border border-slate-700 text-slate-300 p-2.5 rounded-xl outline-none focus:border-yellow-500 transition-all">
                                        <option value="">Reasignar Unidad...</option>
                                        <?php foreach($todosLosEquipos as $te): ?>
                                            <option value="<?php echo $te->id; ?>" <?php echo ($p->equipo_id == $te->id) ? 'selected' : ''; ?>>
                                                <?php echo $te->nombre_equipo; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>
                            <?php endif; ?>

                            <div class="flex gap-2">
                                <?php if ($p->estado == 'disponible'): ?>
                                    <a href="adoptar.php?id=<?php echo $p->id; ?>" onclick="return confirm('¿Confirmar adopción?')" class="flex-[3] bg-emerald-600 hover:bg-emerald-500 text-white text-[10px] font-black uppercase py-3 rounded-xl text-center transition-all tracking-widest min-w-[100px]">Adoptar</a>
                                <?php endif; ?>
                                <a href="editar-pokemon.php?id=<?php echo $p->id; ?>" class="flex-1 p-3 bg-slate-800 hover:bg-slate-700 rounded-xl text-yellow-500 border border-slate-700 flex items-center justify-center transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-span-full text-center py-20 bg-slate-900 rounded-[3rem] border border-dashed border-slate-800">
            <p class="text-slate-600 font-bold uppercase tracking-widest italic">No se han encontrado Pokémon con estos filtros.</p>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>