<?php
// CONFIGURACIÓN DIRECTA (BACKEND)
$projectId = "proyectolegalescatrp";
$jsessionid_url = $_GET['compruebaUsuario'] ?? null;

if (!$jsessionid_url) {
    die("ERROR: Acceso denegado. Sin token de sesión.");
}

// BUSCAR DATOS EN FIREBASE FIRESTORE
$url = "https://firestore.googleapis.com/v1/projects/$projectId/databases/(default)/documents:runQuery";
$query = [
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

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($query));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = json_decode(curl_exec($ch), true);
curl_close($ch);

// EXTRAER CAMPOS
$nombreAgente = "Desconocido";
$imagenPerfil = "https://via.placeholder.com/150";

if (!empty($response) && isset($response[0]['document'])) {
    $fields = $response[0]['document']['fields'];
    $nombreAgente = $fields['nombreAgente']['stringValue'] ?? 'Agente Sin Nombre';
    $imagenPerfil = $fields['imagenPerfil']['stringValue'] ?? $imagenPerfil;
} else {
    die("ERROR: Sesión no válida en Firebase.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel C.N.I</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Courier New', monospace; background: #0a0a0a; color: #00ff41; display: flex; height: 100vh; }

        /* SIDEBAR */
        .sidebar {
            width: 260px;
            background: #111;
            border-right: 2px solid #00ff41;
            display: flex;
            flex-direction: column;
            padding: 20px;
        }
        .sidebar-header {
            padding-bottom: 20px;
            border-bottom: 1px solid #00ff41;
            text-align: center;
        }

        /* CONTENIDO */
        .main-content {
            flex-grow: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .agent-info {
            background: #1a1a1a;
            padding: 30px;
            border: 1px solid #00ff41;
            text-align: center;
            width: 400px;
            box-shadow: 0 0 15px rgba(0, 255, 65, 0.2);
        }

        .profile-img {
            width: 150px;
            height: 150px;
            border: 2px solid #00ff41;
            object-fit: cover;
            margin-bottom: 15px;
        }

        .name-tag { font-size: 1.5rem; font-weight: bold; margin-bottom: 10px; }
        .session-tag { font-size: 0.7rem; color: #666; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header">
            <h3>SISTEMA C.N.I</h3>
        </div>
        <div style="flex-grow: 1; margin-top: 20px;">
            </div>
        <div style="font-size: 0.8rem;">ESTADO: CONECTADO</div>
    </div>

    <div class="main-content">
        <h1 style="margin-bottom: 40px;">Bienvenido panel C.N.I</h1>

        <div class="agent-info">
            <img src="<?php echo htmlspecialchars($imagenPerfil); ?>" class="profile-img">
            <div class="name-tag"><?php echo htmlspecialchars($nombreAgente); ?></div>
            <div class="session-tag">ID_SESIÓN: <?php echo htmlspecialchars($jsessionid_url); ?></div>
        </div>
    </div>

</body>
</html>
