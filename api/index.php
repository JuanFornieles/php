<?php
$archivo_xlx = "index.xlx";
$archivo_html = "index.html";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['codigo'])) {
    file_put_contents($archivo_xlx, $_POST['codigo']);
    // Ejecutamos tu script de python
    shell_exec("python compila.py index.xlx");
}

$codigo = file_exists($archivo_xlx) ? file_get_contents($archivo_xlx) : "<titulo>Web en XLX</titulo>\n<texto>Hola!</texto>";
?>
<!DOCTYPE html>
<html>
<head>
    <title>XLX EDITOR</title>
    <style>
        body { margin: 0; display: flex; flex-direction: column; height: 100vh; font-family: sans-serif; background: #222; color: white; }
        .editor-box { display: flex; flex: 1; }
        textarea { width: 50%; background: #1e1e1e; color: #4ec9b0; padding: 20px; font-family: monospace; font-size: 16px; border: none; outline: none; }
        iframe { width: 50%; border: none; background: white; }
        .nav { padding: 10px; background: #333; display: flex; justify-content: space-between; }
        button { background: #4CAF50; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 4px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="nav">
        <span><b>XLX Studio</b> - Programación en Español</span>
        <button type="submit" form="formX">🚀 COMPILAR Y ACTUALIZAR</button>
    </div>
    <div class="editor-box">
        <form id="formX" method="post" style="display:contents;">
            <textarea name="codigo"><?php echo htmlspecialchars($codigo); ?></textarea>
        </form>
        <iframe src="index.html?v=<?php echo time(); ?>"></iframe>
    </div>
</body>
</html>
