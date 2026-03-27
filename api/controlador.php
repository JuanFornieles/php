<?php
// CONFIGURACIÓN BACKEND (OCULTA)
$projectId = "proyectolegalescatrp";
$jsessionid = $_GET['compruebaUsuario'] ?? null;

// Si no hay jsessionid, cortamos el acceso por seguridad
if (!$jsessionid) {
    die("Acceso denegado: Sesión no encontrada.");
}

// 1. Buscamos al agente en Firestore usando el jsessionid
$url = "https://firestore.googleapis.com/v1/projects/$projectId/databases/(default)/documents:runQuery";
$query = [
    'structuredQuery' => [
        'from' => [['collectionId' => 'usuarios']],
        'where' => [
            'fieldFilter' => [
                'field' => ['fieldPath' => 'jsessionid'],
                'op' => 'EQUAL',
                'value' => ['stringValue' => $jsessionid]
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

// 2. Extraemos los datos del Agente
$agente = [
    'nombre' => "Agente Desconocido",
    'foto'   => "https://cdn-icons-png.flaticon.com/512/1077/1077114.png"
];

if (!empty($response) && isset($response[0]['document'])) {
    $fields = $response[0]['document']['fields'];
    $agente['nombre'] = $fields['nombreAgente']['stringValue'] ?? $agente['nombre'];
    $agente['foto']   = $fields['imagenPerfil']['stringValue'] ?? $agente['foto'];
} else {
    die("Error: Agente no localizado en el sistema.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Control C.N.I</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: #0f172a; color: #f8fafc; display: flex; height: 100vh; }

        /* SIDEBAR */
        .sidebar {
            width: 280px;
            background: #1e293b;
            border-right: 1px solid #334155;
            display: flex;
            flex-direction: column;
            padding: 20px;
        }

        .sidebar-header {
            text-align: center;
            padding-bottom: 30px;
            border-bottom: 1px solid #334155;
        }

        .sidebar-header h2 { font-size: 1.2rem; color: #38bdf8; letter-spacing: 2px; }

        .sidebar-menu { margin-top: 30px; flex-grow: 1; }
        .menu-item {
            padding: 12px 15px;
            border-radius: 8px;
            color: #94a3b8;
            text-decoration: none;
            display: block;
            margin-bottom: 10px;
            transition: 0.3s;
        }
        .menu-item:hover { background: #334155; color: white; }

        /* CONTENIDO PRINCIPAL */
        .content { flex-grow: 1; padding: 40px; display: flex; flex-direction: column; align-items: center; }

        /* INFO DEL AGENTE */
        .agent-profile {
            background: #1e293b;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            text-align: center;
            width: 100%;
            max-width: 450px;
            border-top: 4px solid #38bdf8;
        }

        .agent-img {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #38bdf8;
            margin-bottom: 20px;
        }

        .agent-name {
            font-size: 1.8rem;
            font-weight: bold;
            color: #f8fafc;
            margin-bottom: 10px;
        }

        .badge {
            display: inline-block;
            background: rgba(56, 189, 248, 0.2);
            color: #38bdf8;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header">
            <h2>TERMINAL C.N.I</h2>
        </div>
        <nav class="sidebar-menu">
            <a href="#" class="menu-item">Dashboard</a>
            <a href="#" class="menu-item">Base de Datos</a>
            <a href="#" class="menu-item">Operaciones</a>
            <a href="#" class="menu-item">Ajustes</a>
        </nav>
        <div style="font-size: 0.8rem; color: #475569;">Estado: Encriptado</div>
    </div>

    <div class="content">
        <h1 style="margin-bottom: 40px; font-weight: 300;">Bienvenido panel C.N.I</h1>

        <div class="agent-profile">
            <img src="<?php echo htmlspecialchars($agente['foto']); ?>" class="agent-img" alt="Foto Agente">
            <div class="agent-name"><?php echo htmlspecialchars($agente['nombre']); ?></div>
            <div class="badge">Agente Activo</div>
        </div>
    </div>

</body>
</html>
