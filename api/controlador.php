<?php
// Configuración de acceso
$projectId = "proyectolegalescatrp";

// Capturamos el jsessionid que viene de la URL del index
$jsessionid_url = $_GET['compruebaUsuario'] ?? null;

if (!$jsessionid_url) {
    die("Error: Acceso denegado. No se encontró identificador de sesión.");
}

// Consultamos Firestore para obtener los datos del Agente asociado a ese jsessionid
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

// Extraer datos si el documento existe
$agente_nombre = "Agente Desconocido";
$agente_foto = "https://cdn-icons-png.flaticon.com/512/1077/1077114.png"; // Imagen por defecto

if (!empty($results) && isset($results[0]['document'])) {
    $fields = $results[0]['document']['fields'];
    $agente_nombre = $fields['nombreAgente']['stringValue'] ?? $agente_nombre;
    $agente_foto = $fields['imagenPerfil']['stringValue'] ?? $agente_foto;
} else {
    die("Error: Sesión no válida en la base de datos.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>C.N.I. - Terminal de Control</title>
    <style>
        * { box-sizing: border-box; }
        body { 
            margin: 0; 
            font-family: 'Courier New', Courier, monospace; 
            background-color: #0b0e14; 
            color: #d1d1d1; 
            display: flex; 
            height: 100vh; 
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background-color: #12161f;
            border-right: 1px solid #1f2633;
            display: flex;
            flex-direction: column;
            padding: 20px;
        }
        .sidebar h2 {
            font-size: 1.2rem;
            color: #00ff41; /* Verde Matrix/Terminal */
            text-align: center;
            border-bottom: 1px solid #1f2633;
            padding-bottom: 20px;
        }
        .sidebar-nav {
            margin-top: 30px;
            flex-grow: 1;
        }
        .nav-item {
            padding: 12px;
            color: #888;
            text-decoration: none;
            display: block;
            border-bottom: 1px solid #1a1f29;
        }
        .nav-item:hover {
            color: #fff;
            background: #1a1f29;
        }

        /* Área de Contenido */
        .main-panel {
            flex-grow: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .header-title {
            width: 100%;
            border-bottom: 2px solid #e94560;
            margin-bottom: 40px;
        }

        /* Tarjeta de Agente */
        .agent-card {
            background: #16213e;
            width: 450px;
            padding: 30px;
            border-radius: 5px;
            border: 1px solid #0f3460;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            text-align: center;
        }

        .agent-photo {
            width: 150px;
            height: 150px;
            border-radius: 5px;
            object-fit: cover;
            border: 2px solid #e94560;
            margin-bottom: 20px;
            filter: grayscale(40%);
        }

        .agent-info h3 {
            margin: 10px 0;
            font-size: 1.6rem;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .badge {
            background: #e94560;
            color: white;
            padding: 5px 15px;
            font-size: 0.8rem;
            border-radius: 3px;
            display: inline-block;
            margin-bottom: 10px;
        }

        .session-id {
            font-size: 0.7rem;
            color: #4e5e7a;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>SISTEMA C.N.I.</h2>
        <div class="sidebar-nav">
            <a href="#" class="nav-item">INICIO</a>
            <a href="#" class="nav-item">ARCHIVOS CLASIFICADOS</a>
            <a href="#" class="nav-item">BASE DE DATOS</a>
            <a href="#" class="nav-item">COMUNICACIONES</a>
        </div>
        <div style="font-size: 0.7rem; color: #333;">v.4.0.26-STABLE</div>
    </div>

    <div class="main-panel">
        <div class="header-title">
            <h1 style="margin: 0 0 10px 0;">Bienvenido panel C.N.I</h1>
        </div>

        <div class="agent-card">
            <img src="<?php echo htmlspecialchars($agente_foto); ?>" alt="Foto del Agente" class="agent-photo">
            <div class="agent-info">
                <span class="badge">AGENTE AUTORIZADO</span>
                <h3><?php echo htmlspecialchars($agente_nombre); ?></h3>
                <p style="color: #00ff41;">Estado: EN LÍNEA</p>
            </div>
            
            <div class="session-id">
                TOKEN_SESIÓN: <?php echo htmlspecialchars($jsessionid_url); ?>
            </div>
        </div>
    </div>

</body>
</html>
