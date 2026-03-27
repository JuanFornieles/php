<?php
$archivo_xlx = "index.xlx";
$archivo_html = "index.html";
$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Guardar lo que el usuario escribió en el área de texto
    file_put_contents($archivo_xlx, $_POST['codigo']);

    // 2. Ejecutar tu script de Python
    // Usamos escapeshellcmd por seguridad
    $comando = escapeshellcmd("python compila.py " . $archivo_xlx);
    shell_exec($comando);
    
    $mensaje = "¡Proyecto XLX Compilado! ✅";
}

// Leer el contenido actual para que no se borre al recargar
$contenido_actual = file_exists($archivo_xlx) ? file_get_contents($archivo_xlx) : "";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>XLX Studio - Editor</title>
    <style>
        body { font-family: sans-serif; display: flex; flex-direction: column; height: 100vh; margin: 0; }
        .editor-container { display: flex; flex: 1; }
        textarea { width: 50%; height: 100%; padding: 20px; font-family: monospace; font-size: 16px; border: none; background: #282c34; color: #abb2bf; }
        iframe { width: 50%; height: 100%; border-left: 2px solid #ccc; background: white; }
        .toolbar { background: #333; color: white; padding: 10px; display: flex; justify-content: space-between; }
        button { cursor: pointer; background: #4CAF50; border: none; color: white; padding: 10px 20px; border-radius: 5px; }
    </style>
</head>
<body>

    <div class="toolbar">
        <span><b>XLX Language</b> v1.0</span>
        <span><?php echo $mensaje; ?></span>
        <form method="post" style="margin:0;">
            <!-- El textarea está oculto aquí para enviarse con el botón, 
                 usaremos JS para sincronizarlo si quieres hacerlo pro, 
                 pero por ahora el botón guarda lo que hay en el form -->
    </div>

    <div class="editor-container">
        <form id="xlxForm" method="post" style="width: 100%; display: flex;">
            <textarea name="codigo" placeholder="Escribe tu código XLX aquí..."><?php echo $contenido_actual; ?></textarea>
            <iframe src="index.html?t=<?php echo time(); ?>"></iframe>
        </form>
    </div>

    <div style="padding: 10px; background: #eee;">
        <button type="submit" form="xlxForm">🚀 COMPILAR Y ACTUALIZAR</button>
    </div>

</body>
</html>
