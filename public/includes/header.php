    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="assets/css/style.css">
        <title>Centro de Rescate Pokémon</title>
    </head>
    <body class="bg-slate-950 text-white min-h-screen p-6">

        <div class="max-w-6xl mx-auto">
            <header class="flex flex-col md:flex-row justify-between items-center mb-12 gap-6 border-b border-slate-800 pb-10">
                <div>
                    <h1 class="text-4xl font-black text-yellow-400 uppercase tracking-tighter italic drop-shadow-md">
                        <a href="listado-pokemon.php">🐾 Centro de Rescate</a>
                    </h1>
                    <?php if(isset($_SESSION['usuario_nombre'])): ?>
                        <p class="text-slate-500 text-sm mt-1 font-medium italic">
                            Sesión activa: <span class="text-slate-300"><?php echo $_SESSION['usuario_nombre']; ?></span>
                        </p>
                    <?php endif; ?>
                </div>

                <nav class="flex items-center gap-6">
        <a href="listado-pokemon.php" class="text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-white">Inicio</a>
        
        <?php if ($_SESSION['usuario_rol'] === 'admin'): ?>
            <div class="flex gap-4 border-l border-slate-800 pl-6">
                <a href="registro-usuario.php" class="text-[10px] font-black uppercase tracking-widest text-yellow-500 hover:text-yellow-400 transition-colors">
                    👥 Usuarios
                </a>
                <a href="gestion-equipos.php" class="text-[10px] font-black uppercase tracking-widest text-yellow-500 hover:text-yellow-400 transition-colors">
                    🛡️ Equipos
                </a>
            </div>
        <?php endif; ?>

        
<?php if ($_SESSION['usuario_rol'] === 'rescatista'): ?>
    <a href="mi-panel.php" class="text-xs font-bold uppercase tracking-widest text-blue-400 hover:text-blue-300 transition-colors">
        🛸 Mi Unidad
    </a>
<?php endif; ?>

        <a href="registro-pokemon.php" class="bg-slate-800 hover:bg-slate-700 text-white px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-widest transition-all border border-slate-700">
            + Registrar Pokémon
        </a>
        
        <a href="logout.php" class="text-[10px] font-black uppercase tracking-widest text-red-500/70 hover:text-red-400 transition-colors border-l border-slate-800 pl-6">
            Salir
        </a>
    </nav>
            </header>