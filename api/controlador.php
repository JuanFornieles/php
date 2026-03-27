<?php
// Recogemos los datos que nos envía el index.php a través de la URL
$nombreAgente = $_GET['nombre'] ?? 'Agente Desconocido';
$imagenPerfil = $_GET['foto'] ?? 'https://via.placeholder.com/150';
$jsessionid   = $_GET['compruebaUsuario'] ?? 'Sin Sesión';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel C.N.I</title>
    <style>
        body { margin: 0; font-family: sans-serif; display: flex; height: 100vh; background: #0b0e14; color: white; }
        
        /* Sidebar lateral */
        .sidebar { width: 250px; background: #12161f; border-right: 1px solid #1f2633; padding: 20px; }
        .sidebar h2 { color: #e94560; font-size: 1.2rem; text-align: center; }
        
        /* Contenido */
        .main { flex-grow: 1; padding: 50px; text-align: center; }
        .perfil-container { background: #16213e; padding: 30px; border-radius: 10px; display: inline-block; }
        .foto-agente { width: 150px; height: 150px; border-radius: 50%; border: 3px solid #e94560; object-fit: cover; }
        h1 { margin-bottom: 30px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>SISTEMA C.N.I</h2>
        <hr style="border: 0; border-top: 1px solid #1f2633; margin: 20px 0;">
        </div>

    <div class="main">
        <h1>Bienvenido panel C.N.I</h1>

        <div class="perfil-container">
            <img src="<?php echo htmlspecialchars($imagenPerfil); ?>" class="foto-agente">
            <h2><?php echo htmlspecialchars($nombreAgente); ?></h2>
            <p style="color: #4e5e7a; font-size: 0.8rem;">ID: <?php echo htmlspecialchars($jsessionid); ?></p>
        </div>
    </div>

</body>
</html>
