<?php
// ==========================================
// 1. INICIO DE SESIÓN Y LÓGICA
// ==========================================
session_start();
require_once '../config/Database.php';

// Si ya está logueado, lo mandamos directo al listado
if (isset($_SESSION['usuario_id'])) {
    header("Location: listado-pokemon.php");
    exit();
}

$error = "";

// Verificamos si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $database = new Database();
    $db = $database->getConnection();

    $email = $_POST['email'];
    $password = $_POST['password'];

    // Buscamos al usuario por su email
    $query = "SELECT * FROM usuarios WHERE email = :email";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    // Importante: FETCH_OBJ para que funcione el $user->password
    $user = $stmt->fetch(PDO::FETCH_OBJ);

    // Verificamos si existe y si la contraseña coincide
    if ($user && password_verify($password, $user->password)) {
        $_SESSION['usuario_id'] = $user->id;
        $_SESSION['usuario_nombre'] = $user->nombre;
        $_SESSION['usuario_rol'] = $user->rol; // Guardamos el rol para el header
        
        header("Location: listado-pokemon.php");
        exit();
    } else {
        $error = "Credenciales incorrectas. Inténtalo de nuevo.";
    }
} // <--- Aquí es donde probablemente faltaba el cierre antes
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Login - Centro de Rescate</title>
</head>
<body class="bg-slate-950 text-slate-200 min-h-screen flex items-center justify-center p-6">

    <div class="w-full max-w-md bg-slate-900 p-10 rounded-[2.5rem] shadow-2xl border border-slate-800">
        
        <div class="flex justify-center mb-8">
            <div class="w-20 h-20 bg-yellow-500 rounded-3xl flex items-center justify-center shadow-lg shadow-yellow-500/20 rotate-3">
                <span class="text-4xl">🐾</span>
            </div>
        </div>

        <header class="text-center mb-10">
            <h2 class="text-3xl font-black text-white uppercase tracking-tighter italic">Acceso Rescatistas</h2>
            <p class="text-slate-500 text-sm mt-1">Identifícate para gestionar el refugio</p>
        </header>
        
        <?php if($error): ?>
            <div class="p-4 mb-6 bg-red-500/10 border border-red-500/50 text-red-400 rounded-2xl text-center text-xs font-bold uppercase tracking-widest"> 
                <?php echo $error; ?> 
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase mb-2 ml-1 tracking-widest">Correo Electrónico</label>
                <input type="email" name="email" required 
                       class="w-full p-4 rounded-2xl bg-slate-800 border border-slate-700 focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 outline-none transition-all text-white"
                       placeholder="nombre@ejemplo.com">
            </div>

            <div class="relative">
    <input type="password" id="password" name="password" required 
           class="w-full p-4 rounded-2xl bg-slate-800 border border-slate-700 text-white outline-none focus:border-yellow-500 transition-all">
    <button type="button" onclick="togglePassword()" class="absolute right-4 top-4 text-slate-500 hover:text-white">
        <span id="eye-icon">👁️</span>
    </button>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    const icon = document.getElementById('eye-icon');
    if (input.type === "password") {
        input.type = "text";
        icon.innerText = "🔒"; // O un icono de ojo cerrado
    } else {
        input.type = "password";
        icon.innerText = "👁️";
    }
}
</script>
            

            <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-400 text-slate-950 font-black py-4 rounded-2xl transition-all shadow-lg shadow-yellow-500/20 uppercase tracking-widest text-sm">
                Entrar al Panel
            </button>
        </form>
        
        <div class="mt-10 pt-6 border-t border-slate-800 text-center">
            <p class="text-slate-600 text-[10px] uppercase font-bold tracking-[0.2em]">Sistema de Adopción v1.0</p>
        </div>
    </div>

</body>
</html>