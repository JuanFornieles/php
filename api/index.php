<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Documentación Oficial XLX</title>
    <style>
        body { margin: 0; background: #1e1e1e; color: #ddd; font-family: sans-serif; line-height: 1.6; }
        .container { max-width: 900px; margin: 40px auto; padding: 0 20px; }
        h1 { color: #4CAF50; border-bottom: 1px solid #333; padding-bottom: 10px; }
        .tag-card { background: #252526; padding: 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #4CAF50; }
        code { background: #333; color: #4ec9b0; padding: 2px 6px; border-radius: 4px; font-family: monospace; font-size: 1.1em; }
        .ejemplo { background: #111; color: #abb2bf; padding: 15px; border-radius: 5px; margin-top: 10px; font-family: monospace; display: block; white-space: pre; }
    </style>
</head>
<body>

<header style="background: #111; color: white; padding: 10px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #4CAF50;">
    <div style="display: flex; align-items: center; gap: 15px;">
        <img src="/imagenes/logos/logo_1.png" alt="XLX" style="height: 40px; border-radius: 4px;">
        <span style="font-size: 22px; font-weight: bold; letter-spacing: 1px;">XLX</span>
    </div>
    <nav style="display: flex; align-items: center; gap: 25px;">
        <a href="/docus-xlx.php?rndval=<?php echo $rnd; ?>" style="color: #fff; text-decoration: none; font-size: 14px;">Documentación XLX</a>
        <?php $rnd = rand(1000, 9999); ?>
        <a href="/que-es.php?rndval=<?php echo $rnd; ?>" style="color: #bbb; text-decoration: none; font-size: 14px;">¿Qué es XLX?</a>
        <a href="/diccionario.php?rndval=<?php echo $rnd; ?>" style="color: #bbb; text-decoration: none; font-size: 14px;">¿Qué es XLX?</a>
        <a href="/prueba.php?rndval=<?php echo $rnd; ?>" style="background: #4CAF50; color: white; text-decoration: none; padding: 8px 16px; border-radius: 5px; font-weight: bold; font-size: 14px;">EDITOR XLX</a>
    </nav>
</header>

<div class="container">
    <h1>Guía de Etiquetas XLX</h1>
    <p>Aprende a programar tu web de forma sencilla y en español usando estas etiquetas:</p>

    <div class="tag-card">
        <strong>Etiqueta:</strong> <code>&lt;titulo&gt;...&lt;titulo&gt;</code><br>
        <span>Define el título de la pestaña y crea un encabezado principal en la página.</span>
        <div class="ejemplo">&lt;titulo&gt;Bienvenidos a mi web&lt;titulo&gt;</div>
    </div>

    <div class="tag-card">
        <strong>Etiqueta:</strong> <code>&lt;alerta&gt;...&lt;alerta&gt;</code><br>
        <span>Muestra una ventana emergente de aviso al cargar la página.</span>
        <div class="ejemplo">&lt;alerta&gt;¡Hola Mundo!&lt;alerta&gt;</div>
    </div>

    <div class="tag-card">
        <strong>Etiqueta:</strong> <code>&lt;imagen&gt;...&lt;imagen&gt;</code><br>
        <span>Inserta una imagen pasando el enlace directo.</span>
        <div class="ejemplo">&lt;imagen&gt;https://mi-web.com;</div>
    </div>

    <div class="tag-card">
        <strong>Etiqueta:</strong> <code>&lt;estilo_abrir&gt;...&lt;estilo_cerrar&gt;</code><br>
        <span>Sección para cambiar el diseño. Usa <code>fondo=color</code> o <code>letra=tipo</code>.</span>
        <div class="ejemplo">
&lt;estilo_abrir&gt;
fondo = black
letra = Arial
&lt;estilo_cerrar&gt;</div>
    </div>

</div>

</body>
</html>
