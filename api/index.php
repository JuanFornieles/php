<?php
// Configuración de tu proyecto (No visible en el navegador)
$projectId = "proyectolegalescatrp";
$error = "";
$usuario_valido = false;
$jsessionid_url = $_GET['compruebaUsuario'] ?? null;
$mostrar_boton = false;

// 1. LÓGICA DE LOGIN (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['usuario'])) {
    $user_login = $_POST['usuario'];
    $pass_login = $_POST['password'];

    // Accedemos directamente al DOCUMENTO que se llama como el usuario
    $url = "https://firestore.googleapis.com/v1/projects/$projectId/databases/(default)/documents/usuarios/" . urlencode($user_login);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $data = json_decode($response, true);
    curl_close($ch);

    // Verificamos si el documento existe y la contraseña coincide
    if (isset($data['fields'])) {
        $db_pass = $data['fields']['contrasena']['stringValue'] ?? '';
        $db_jsession = $data['fields']['jsessionid']['stringValue'] ?? '';

        if ($db_pass === $pass_login) {
            // Éxito: Recargamos la página pasando el jsessionid por URL
            header("Location: index.php?compruebaUsuario=" . urlencode($db_jsession));
            exit();
        } else {
            $error = "identificacion incorrecta";
        }
    } else {
        $error = "identificacion incorrecta";
    }
}

// 2. LÓGICA DE VERIFICACIÓN POR URL (?compruebaUsuario=...)
if ($jsessionid_url) {
    // Buscamos el documento donde el campo jsessionid coincida con el de la URL
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

    // Si encontramos al usuario, verificamos sus roles
    if (!empty($results) && isset($results[0]['document'])) {
        $fields = $results[0]['document']['fields'];
        
        // Verificamos si en el array 'rolesUsuario' existe el valor 'cnp'
        if (isset($fields['rolesUsuario']['arrayValue']['values'])) {
            $roles = $fields['rolesUsuario']['arrayValue']['values'];
            foreach ($roles as $rol) {
                if (isset($rol['stringValue']) && $rol['stringValue'] === "cnp") {
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
    <title>Login Firestore</title>
    <style>
        body { font-family: Arial; text-align: center; padding-top: 50px; }
        .error { color: red; }
        .btn-success { background: green; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>

    <?php if (!$mostrar_boton): ?>
        <h2>Acceso Usuarios</h2>
        <form method="POST">
            <input type="text" name="usuario" placeholder="Usuario" required><br><br>
            <input type="password" name="password" placeholder="Contraseña" required><br><br>
            <button type="submit">Entrar</button>
        </form>
        <?php if ($error) echo "<p class='error'>$error</p>"; ?>
    <?php else: ?>
        <h2>Sesión Confirmada</h2>
        <p>ID: <?php echo htmlspecialchars($jsessionid_url); ?></p>
        
        <a href="controlador.php" class="btn-success">Ir a controlador.php</a>
    <?php endif; ?>

</body>
</html>
