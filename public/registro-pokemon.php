<?php
// ==========================================
// 1. LÓGICA DE SERVIDOR (PHP)
// ==========================================
require_once '../config/auth.php'; // 🛡️ EL PORTERO VA PRIMERO
require_once '../config/Database.php';

$mensaje = "";

// Solo se ejecuta si el usuario pulsa el botón de enviar (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $database = new Database();
    $db = $database->getConnection();

    // Preparamos la consulta SQL
    // 💡 TIP: Aquí podrías añadir un campo 'imagen_url' para guardar la foto de la API y no pedirla cada vez.
    $query = "INSERT INTO pokemon_adopcion (nombre_propio, especie_api_id, estado, descripcion, fecha_rescate) 
              VALUES (:nombre, :especie, :estado, :desc, :fecha)";
    
    $stmt = $db->prepare($query);

    // Vinculamos los datos del formulario con la consulta (Seguridad ante Inyecciones SQL)
    $stmt->bindParam(":nombre", $_POST['nombre']);
    $stmt->bindParam(":especie", $_POST['especie'], PDO::PARAM_INT);
    $stmt->bindParam(":estado", $_POST['estado']);
    $stmt->bindParam(":desc", $_POST['descripcion']);
    $stmt->bindParam(":fecha", $_POST['fecha']);

    if ($stmt->execute()) {
        $mensaje = "✅ Pokémon registrado con éxito.";
    } else {
        $mensaje = "❌ Error al registrar.";
    }
}
include 'includes/header.php'; // HEADER
?>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10"></div>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Nuevo Rescate - Registro</title>
</head>
<body class="bg-slate-950 text-slate-200 min-h-screen flex items-center justify-center p-6">

    <div class="w-full max-w-lg bg-slate-900 p-10 rounded-3xl shadow-2xl border border-slate-800">
        
        <header class="text-center mb-10">
            <h2 class="text-3xl font-black text-yellow-400 uppercase tracking-tighter italic">Nuevo Rescate</h2>
            <p class="text-slate-500 text-sm mt-1">Completa la ficha de ingreso al refugio</p>
        </header>
        
        <?php if($mensaje): ?>
            <div class="p-4 mb-6 bg-green-500/10 border border-green-500/50 text-green-400 rounded-xl text-center font-medium animate-bounce"> 
                <?php echo $mensaje; ?> 
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            
            <div class="group">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2 ml-1 group-focus-within:text-yellow-400 transition-colors">Apodo del Pokémon</label>
                <input type="text" name="nombre" required 
                       class="w-full p-3.5 rounded-xl bg-slate-800 border border-slate-700 focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 outline-none transition-all text-white"
                       placeholder="Ej: Sparky">
                </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2 ml-1">ID PokeAPI</label>
                    <input type="number" name="especie" id="input_id_api" required 
                           class="w-full p-3.5 rounded-xl bg-slate-800 border border-slate-700 focus:border-yellow-500 outline-none transition-all text-white"
                           placeholder="Ej: 25">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2 ml-1 italic text-slate-600">Especie Detectada</label>
                    <input type="text" id="nombre_especie_api" readonly 
                           class="w-full p-3.5 rounded-xl bg-slate-900 border border-slate-800 text-yellow-500 font-bold italic outline-none cursor-default"
                           placeholder="---">
                </div>
                </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2 ml-1">Fecha de Rescate</label>
                    <input type="date" name="fecha" required 
                           class="w-full p-3.5 rounded-xl bg-slate-800 border border-slate-700 focus:border-yellow-500 outline-none transition-all text-white">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2 ml-1">Estado inicial</label>
                    <select name="estado" class="w-full p-3.5 rounded-xl bg-slate-800 border border-slate-700 focus:border-yellow-500 outline-none cursor-pointer text-white">
                        <option value="disponible">Disponible</option>
                        <option value="herido">Herido / Enfermo</option>
                        <option value="en_rescate">En tránsito</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2 ml-1">Historia del encuentro</label>
                <textarea name="descripcion" rows="3" 
                          class="w-full p-3.5 rounded-xl bg-slate-800 border border-slate-700 focus:border-yellow-500 outline-none transition-all resize-none text-white"
                          placeholder="Cuéntanos cómo fue rescatado..."></textarea>
            </div>

            <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-400 text-slate-950 font-black py-4 rounded-2xl transition-all shadow-lg shadow-yellow-500/20 uppercase tracking-widest text-sm">
                Finalizar Registro
            </button>
        </form>
        
        <div class="mt-8 text-center">
            <a href="listado-pokemon.php" class="text-slate-500 hover:text-yellow-400 text-xs font-bold uppercase tracking-widest transition-colors">
                ← Ver todos los rescatados
            </a>
        </div>
    </div>

    <script>
        // Escuchamos cuando el usuario deja de escribir en el campo ID (evento 'blur')
        document.getElementById('input_id_api').addEventListener('blur', function() {
            const id = this.value;
            const displayNombre = document.getElementById('nombre_especie_api');
            
            if (id > 0) {
                displayNombre.value = "Buscando...";
                
                // Llamamos a la API directamente desde el navegador (más rápido)
                fetch(`https://pokeapi.co/api/v2/pokemon/${id}`)
                    .then(response => {
                        if (!response.ok) throw new Error('No encontrado');
                        return response.json();
                    })
                    .then(data => {
                        // 💡 TIP: Aquí podrías obtener también data.types[0].type.name para saber el tipo
                        displayNombre.value = data.name.toUpperCase();
                    })
                    .catch(error => {
                        displayNombre.value = "❌ ID Inválido";
                    });
            }
        });
    </script>


<?php 
include 'includes/footer.php'; // FOOTER
?>

</body>
</html>