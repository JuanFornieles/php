<?php
// Configuración oculta en el backend
$projectId = "proyectolegalescatrp";
$error = "";
$jsessionid_valido = null;
$mostrar_boton = false;

// 1. LÓGICA DE LOGIN (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['usuario'])) {
    $user_input = $_POST['usuario'];
    $pass_input = $_POST['password'];

    // Consultamos el documento del usuario directamente en Firestore vía REST
    $url = "https://firestore.googleapis.com/v1/projects/$projectId/databases/(default)/documents/usuarios/" . urlencode($user_input);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $data = json_decode($response, true);
    curl_close($ch);

    if (isset($data['fields'])) {
        $db_pass = $data['fields']['contrasena']['stringValue'] ?? '';
        $db_jsession = $data['fields']['jsessionid']['stringValue'] ?? '';

        if ($db_pass === $pass_input) {
            // Si es correcto, recargamos con el parámetro en la URL como pediste
            header("Location: index.php?compruebaUsuario=" . urlencode($db_jsession));
            exit();
        } else {
            $error = "identificacion incorrecta";
        }
    } else {
        $error = "identificacion incorrecta";
    }
}

// 2. LÓGICA DE VERIFICACIÓN (URL ?compruebaUsuario=...)
if (isset($_GET['compruebaUsuario'])) {
    $jsessionid_url = $_GET['compruebaUsuario'];

    /* Para buscar por jsessionid, lo más eficiente es una consulta filtrada.
       Aquí asumo que el jsessionid es único por usuario.
    */
    $queryUrl = "https://firestore.googleapis.com/v1/projects/$projectId/databases/(default)/documents:runQuery";
    
    $queryData = [
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
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($queryData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $resQuery = curl_exec($ch);
    $results = json_decode($resQuery, true);
    curl_close($ch);

    if (!empty($results) && isset($results[0]['document'])) {
        $fields = $results[0]['document']['fields'];
        
        // Revisar el array de rolesUsuario buscando "cnp"
        if (isset($fields['rolesUsuario']['arrayValue']['values'])) {
            $roles = $fields['rolesUsuario']['arrayValue']['values'];
            foreach ($roles as $rol) {
                if ($rol['stringValue'] === "cnp") {
                    $mostrar_boton = true;
                    break;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acceso Privado</title>
    <style>
        body { font-family: sans-serif; padding: 50px; text-align: center; }
        .error { color: red; font-weight: bold; }
        .btn-control { padding: 15px 25px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 20px; }
    </style>
</head>
<body>

    <?php if (!$mostrar_boton): ?>
        <h2>Identificación de Usuario</h2>
        <form method="POST" action="index.php">
            <input type="text" name="usuario" placeholder="Usuario" required><br><br>
            <input type="password" name="password" placeholder="Contraseña" required><br><br>
            <button type="submit">Entrar</button>
        </form>
        <?php if ($error) echo "<p class='error'>$error</p>"; ?>
    <?php else: ?>
        <h2>Usuario Verificado</h2>
        <p>Sesión activa: <strong><?php echo htmlspecialchars($_GET['compruebaUsuario']); ?></strong></p>
        
        <a href="/controlador.php" class="btn-control">Acceder a Controlador</a>
    <?php endif; ?>

</body>
</html>
