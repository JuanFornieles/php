<?php
// Configuración de tu proyecto
$projectId = "proyectolegalescatrp";
$jsessionid_url = $_GET['compruebaUsuario'] ?? ($_GET['session'] ?? null);

$agente_nombre = "Desconocido";
$agente_foto = ""; // URL de la imagen
$acceso_denegado = true;

if ($jsessionid_url) {
    // 1. Buscamos el documento del agente que tenga este jsessionid
    $queryUrl = "https://firestore.googleapis.com/v1/projects/$projectId/databases/(default)/documents:runQuery";
    
    $queryBody = [
        'structuredQuery' => [
            'from' => [['collectionId' => 'usuarios']],
            'where' => [
                'fieldFilter' => [
                    'field' => ['fieldPath' => 'jsessionid'],
                    'op' => 'EQUAL',
                    'value' => ['stringValue' => $jsessionid_url]
                ]
            ]
        ]
    ];

    $ch = curl_init($queryUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($queryBody));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $res = curl_exec($ch);
    $results = json_decode($res, true);
    curl_close($ch);

    // 2. Extraemos los datos del Agente
    if (!empty($results) && isset($results[0]['document'])) {
        $fields = $results[0]['document']['fields'];
        
        $agente_nombre = $fields['nombreAgente']['stringValue'] ?? 'Agente Sin Nombre';
        $agente_foto = $fields['imagenPerfil']['stringValue'] ?? 'https://via.placeholder.com/150';
        $acceso_denegado = false;
    }
}

if ($acceso_denegado) {
    die("Acceso no autorizado. Por favor, inicie sesión.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel C.N.I - Sistema de Control</title>
    <style>
        body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; display: flex; height: 100vh; background-color: #f4f7f6; }
        
        /* Sidebar */
        .sidebar { width: 250px; background-color: #1a1a2e; color: white; display: flex; flex-direction: column; transition: all 0.3s; }
        .sidebar-header { padding: 20px; text-align: center; background: #16213e; border-bottom: 1px solid #0f3460; }
        .sidebar-menu { flex-grow: 1; padding: 20px 0; }
        
        /* Contenido Principal */
        .main-content { flex-grow: 1; overflow-y: auto; padding: 30px; }
        
        /* Perfil del Agente */
        .profile-card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); text-align: center; max-width: 400px; margin: 0 auto; }
        .profile-img { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid #1a1a2e; margin-bottom: 15px; }
        .welcome-text { color: #1a1a2e; margin-bottom: 5px; font-size: 1.5rem; }
        .agent-name { color: #e94560; font-weight: bold; font-size: 1.2rem; }
        
        h1 { color: #1a1a2e; border-left: 5px solid #e94560; padding-left: 15px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header">
            <h3>SISTEMA C.N.I</h3>
        </div>
        <div class="sidebar-menu">
            </div>
    </div>

    <div class="main-content">
        <h1>Bienvenido panel C.N.I</h1>

        <div class="profile-card">
            <img src="<?php echo htmlspecialchars($agente_foto); ?>" alt="Perfil Agente" class="profile-img">
            <div class="welcome-text">Agente Autorizado</div>
            <div class="agent-name"><?php echo htmlspecialchars($agente_nombre); ?></div>
            <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">
            <p style="font-size: 0.9rem; color: #666;">ID de Sesión: <?php echo htmlspecialchars($jsessionid_url); ?></p>
        </div>
    </div>

</body>
</html>
