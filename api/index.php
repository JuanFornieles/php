<?php
session_start();

// 1. CONFIGURACIÓN: Usuarios "ocultos" (User => Password_Hash)
// Generé estos hashes con password_hash('tu_password', PASSWORD_DEFAULT)
$usuarios_registrados = [
    'admin' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password: password
    'usuario' => '$2y$10$M78.R7vJpIuR2v6vS5vB9.0L3yY/3M9q3Z2yY3Z2yY3Z2yY3Z2yY2' // password: 12345
];

// 2. LÓGICA DE LOGIN
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $user = $_POST['user'] ?? '';
    $pass = $_POST['pass'] ?? '';

    if (isset($usuarios_registrados[$user]) && password_verify($pass, $usuarios_registrados[$user])) {
        $_SESSION['autenticado'] = $user;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}

// 3. LÓGICA DE LOGOUT
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>PHP Login Vercel</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; padding-top: 50px; background: #f4f7f6; }
        .box { background: white; padding: 2rem; border-radius: 8px; shadow: 0 2px 10px rgba(0,0,0,0.1); width: 300px; }
        input { width: 100%; padding: 8px; margin: 10px 0; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #000; color: #fff; border: none; cursor: pointer; }
        .error { color: red; font-size: 0.8rem; }
    </style>
</head>
<body>
    <div class="box">
        <?php if (!isset($_SESSION['autenticado'])): ?>
            <h2>Identifícate</h2>
            <?php if ($error) echo "<p class='error'>$error</p>"; ?>
            <form method="POST">
                <input type="text" name="user" placeholder="Usuario" required>
                <input type="password" name="pass" placeholder="Contraseña" required>
                <button type="submit" name="login">Entrar</button>
            </form>
        <?php else: ?>
            <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['autenticado']); ?></h2>
            <p>Estás dentro de la zona segura en Vercel.</p>
            <a href="?logout=1">Cerrar Sesión</a>
        <?php endif; ?>
    </div>
</body>
</html>
