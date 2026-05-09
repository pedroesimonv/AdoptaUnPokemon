<?php
require_once '../config/admin_auth.php';
require_once '../config/Database.php';

$database = new Database();
$db = $database->getConnection();
$mensaje = "";

// 1. Procesar creación de equipo (Mantenemos tu lógica anterior)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre_equipo'];
    $especialidad = $_POST['especialidad'];
    $zona = $_POST['zona_asignada'];

    $query = "INSERT INTO equipos (nombre_equipo, especialidad, zona_asignada) 
              VALUES (:nom, :esp, :zona)";
    $stmt = $db->prepare($query);
    if ($stmt->execute([':nom' => $nombre, ':esp' => $especialidad, ':zona' => $zona])) {
        $mensaje = "🛡️ Unidad '$nombre' desplegada correctamente.";
    }
}

// 2. Obtener todos los equipos
$equipos = $db->query("SELECT * FROM equipos")->fetchAll(PDO::FETCH_OBJ);

include 'includes/header.php';
?>

<div class="max-w-6xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        
        <!-- FORMULARIO (Columna Izquierda) -->
        <div class="lg:col-span-1">
            <div class="bg-slate-900 p-8 rounded-[2rem] border border-slate-800 sticky top-6">
                <h2 class="text-2xl font-black text-white italic uppercase mb-6 tracking-tighter">Nueva Unidad</h2>
                <?php if($mensaje): ?>
                    <div class="p-3 mb-6 bg-yellow-500/10 border border-yellow-500/50 text-yellow-400 rounded-xl text-xs font-bold text-center italic">
                        <?php echo $mensaje; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-4">
                    <input type="text" name="nombre_equipo" required placeholder="Nombre del Escuadrón" class="w-full p-4 rounded-2xl bg-slate-800 border border-slate-700 text-white outline-none focus:border-yellow-500 transition-all">
                    <select name="especialidad" class="w-full p-4 rounded-2xl bg-slate-800 border border-slate-700 text-white outline-none focus:border-yellow-500">
                        <option value="Incendios">🔥 Control de Incendios</option>
                        <option value="Rescate Acuático">🌊 Rescate Acuático</option>
                        <option value="Escombros/Sismos">🏔️ Escombros / Sismos</option>
                        <option value="Médico">🏥 Unidad Médica</option>
                    </select>
                    <input type="text" name="zona_asignada" placeholder="Zona de Operación" class="w-full p-4 rounded-2xl bg-slate-800 border border-slate-700 text-white outline-none focus:border-yellow-500 transition-all">
                    <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-400 text-slate-950 font-black py-4 rounded-2xl transition-all shadow-lg shadow-yellow-500/20 uppercase tracking-widest text-xs">
                        Fundar Unidad
                    </button>
                </form>
            </div>
        </div>

        <!-- LISTADO DE EQUIPOS (Columna Derecha) -->
        <div class="lg:col-span-2 space-y-6">
            <h2 class="text-2xl font-black text-white italic uppercase tracking-tighter">Unidades en Activo</h2>
            
            <div class="space-y-4">
                <?php foreach($equipos as $e): 
                    // Consultas rápidas para los detalles
                    $miembros = $db->prepare("SELECT nombre FROM usuarios WHERE equipo_id = ?");
                    $miembros->execute([$e->id]);
                    $listaMiembros = $miembros->fetchAll(PDO::FETCH_OBJ);

                    $pokes = $db->prepare("SELECT nombre_propio FROM pokemon_adopcion WHERE equipo_id = ?");
                    $pokes->execute([$e->id]);
                    $listaPokes = $pokes->fetchAll(PDO::FETCH_OBJ);
                ?>
                    <div class="bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden transition-all hover:border-slate-700">
                        <!-- Cabecera de la Tarjeta -->
                        <div class="p-6 flex flex-wrap items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <span class="p-3 bg-slate-800 rounded-2xl text-xl">
                                    <?php 
                                        if($e->especialidad == 'Incendios') echo '🔥';
                                        elseif($e->especialidad == 'Rescate Acuático') echo '🌊';
                                        elseif($e->especialidad == 'Escombros/Sismos') echo '🏔️';
                                        else echo '🏥';
                                    ?>
                                </span>
                                <div>
                                    <h3 class="text-xl font-bold text-white"><?php echo $e->nombre_equipo; ?></h3>
                                    <p class="text-[10px] text-yellow-500 font-black uppercase tracking-widest"><?php echo $e->especialidad; ?> | 📍 <?php echo $e->zona_asignada; ?></p>
                                </div>
                            </div>
                            
                            <!-- BOTÓN DETALLES (Ahora con función JS) -->
                            <button onclick="toggleEquipo(<?php echo $e->id; ?>)" class="bg-slate-800 hover:bg-slate-700 text-slate-300 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all border border-slate-700">
                                <span id="text-<?php echo $e->id; ?>">Ver Detalles</span>
                            </button>
                        </div>

                        <!-- PANEL DESPLEGABLE (Oculto por defecto) -->
                        <div id="details-<?php echo $e->id; ?>" class="hidden bg-slate-950/50 border-t border-slate-800 p-8 animate-fade-in">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <!-- Miembros -->
                                <div>
                                    <h4 class="text-[10px] font-black text-slate-500 uppercase mb-4 tracking-widest flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span> Personal Asignado
                                    </h4>
                                    <div class="flex flex-wrap gap-2">
                                        <?php if($listaMiembros): foreach($listaMiembros as $m): ?>
                                            <span class="bg-slate-800 text-slate-300 px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-700"><?php echo $m->nombre; ?></span>
                                        <?php endforeach; else: ?>
                                            <p class="text-slate-600 text-xs italic">Sin personal asignado.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Pokémon -->
                                <div>
                                    <h4 class="text-[10px] font-black text-slate-500 uppercase mb-4 tracking-widest flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full"></span> Pokémon de la Unidad
                                    </h4>
                                    <div class="flex flex-wrap gap-2">
                                        <?php if($listaPokes): foreach($listaPokes as $p): ?>
                                            <span class="bg-slate-800 text-yellow-500/80 px-3 py-1.5 rounded-lg text-xs font-black italic border border-slate-700/50">
                                                <?php echo $p->nombre_propio; ?>
                                            </span>
                                        <?php endforeach; else: ?>
                                            <p class="text-slate-600 text-xs italic">Sin Pokémon en esta unidad.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPT PARA EL DESPLEGABLE -->
<script>
function toggleEquipo(id) {
    const panel = document.getElementById('details-' + id);
    const text = document.getElementById('text-' + id);
    
    // Cerramos todos los demás paneles para que sea un acordeón real (opcional)
    // document.querySelectorAll('[id^="details-"]').forEach(p => {
    //     if(p.id !== 'details-' + id) p.classList.add('hidden');
    // });

    if (panel.classList.contains('hidden')) {
        panel.classList.remove('hidden');
        text.innerText = 'Cerrar';
    } else {
        panel.classList.add('hidden');
        text.innerText = 'Ver Detalles';
    }
}
</script>

<?php include 'includes/footer.php'; ?>