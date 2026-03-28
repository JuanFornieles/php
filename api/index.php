<?php
// Función para generar las letras aleatorias en mayúsculas
function generarLetras($longitud = 20) {
    return substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $longitud);
}

// Variables para los enlaces
$rnd = rand(100000000, 999999999);
$letras = generarLetras(10);
$query = "?rndval=$rnd&ALEATORIO=$letras";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Documentación XLX</title>
    <style>
        /* Estilo Blanco Minimalista */
        body { margin: 0; background: #ffffff; color: #1d1d1f; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        
        header { 
            background: rgba(255, 255, 255, 0.8); 
            backdrop-filter: blur(10px); 
            padding: 12px 40px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            border-bottom: 1px solid #e5e5e5; 
            position: sticky; 
            top: 0;
            z-index: 100;
        }

        .logo-box { display: flex; align-items: center; gap: 12px; text-decoration: none; color: #000; }
        .logo-img { height: 35px; border-radius: 6px; }
        .logo-txt { font-size: 20px; font-weight: 700; letter-spacing: -0.5px; }

        nav { display: flex; align-items: center; gap: 30px; }
        nav a { text-decoration: none; color: #515154; font-size: 14px; font-weight: 500; transition: 0.2s; }
        nav a:hover { color: #000; }

        .btn-editor { 
            background: #000; 
            color: #fff !important; 
            padding: 8px 18px; 
            border-radius: 20px; 
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .btn-editor:hover { transform: translateY(-1px); box-shadow: 0 6px 15px rgba(0,0,0,0.15); }

        .content { max-width: 800px; margin: 60px auto; padding: 0 20px; }
        h1 { font-size: 48px; font-weight: 800; letter-spacing: -1px; margin-bottom: 10px; }
        .tag-info { border-bottom: 1px solid #f0f0f0; padding: 25px 0; }
        code { background: #f5f5f7; color: #d70015; padding: 4px 8px; border-radius: 6px; font-family: "SF Mono", monospace; font-size: 0.9em; }
        .ejemplo-box { background: #f9f9fb; border: 1px solid #eee; padding: 15px; border-radius: 10px; margin-top: 10px; color: #444; font-family: monospace; }
    </style>
</head>
<body>

<header>
    <a href="/" class="logo-box">
        <img src="/imagenes/logos/logo_1.png" class="logo-img" alt="Logo">
    </a>

    <nav>
        <a href="/docus-xlx.xlx<?php echo $query; ?>">Documentación</a>
        <a href="/que-es.xlx<?php echo $query; ?>">¿Qué es?</a>
        <a href="/prueba.xlx<?php echo $query; ?>" class="btn-editor">EDITOR XLX</a>
    </nav>
</header>

<div class="content">
    <h1>Guía XLX</h1>
    <p style="font-size: 20px; color: #86868b;">Simplicidad absoluta en cada etiqueta.</p>

    <div class="tag-info">
        <p><strong>Uso de Texto:</strong></p>
        <code>&lt;texto&gt;Tu mensaje aquí&lt;texto&gt;</code>
        <div class="ejemplo-box">&lt;texto&gt;Hola, esto es XLX&lt;texto&gt;</div>
    </div>

    <div class="tag-info">
        <p><strong>Alertas del Sistema:</strong></p>
        <code>&lt;alerta&gt;Contenido&lt;alerta&gt;</code>
        <div class="ejemplo-box">&lt;alerta&gt;Bienvenido al editor&lt;alerta&gt;</div>
    </div>
</div>

</body>
</html>
