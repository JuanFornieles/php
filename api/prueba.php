<?php
$resultado_final = "";
$codigo_usuario = isset($_POST['codigo']) ? $_POST['codigo'] : "<titulo>Mi Web XLX</titulo>\n<estilo_abrir>\nfondo = #121212\nletra = Arial\n<estilo_cerrar>\n<texto>Escribe aquí en español</texto>\n<js_abrir>\nalert('¡Motor XLX Listo!');\n<js_cerrar>";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = $_POST['codigo'];

    // 1️⃣ Procesar sección estilo (CSS AZUL)
    $css = "";
    if (preg_match_all('/<estilo_abrir>(.*?)<estilo_cerrar>/s', $code, $estilos)) {
        foreach ($estilos[1] as $bloque) {
            foreach (explode("\n", $bloque) as $linea) {
                if (strpos($linea, '=') !== false) {
                    list($prop, $valor) = explode('=', $linea);
                    $prop = trim($prop); $valor = trim($valor);
                    if ($prop == "fondo") $css .= "body { background-color: $valor; }\n";
                    elseif ($prop == "letra") $css .= "body { font-family: $valor; }\n";
                }
            }
        }
    }

    // 2️⃣ Procesar sección JS global (JS AMARILLO)
    $js = "";
    if (preg_match_all('/<js_abrir>(.*?)<js_cerrar>/s', $code, $js_blocks)) {
        foreach ($js_blocks[1] as $bloque) { $js .= $bloque . "\n"; }
    }

    // 3️⃣ Procesar etiquetas normales (ETIQUETAS VERDES / CONTENIDO BLANCO)
    preg_match_all('/<(\w+)>(.*?)<\1>/s', $code, $matches, PREG_SET_ORDER);
    
    $html_body = "";
    foreach ($matches as $m) {
        $tag = $m[1]; $content = $m[2];
        if (in_array($tag, ["estilo_abrir", "estilo_cerrar", "js_abrir", "js_cerrar"])) continue;
        
        switch($tag) {
            case "titulo": $html_body .= "<title>$content</title><h1>$content</h1>\n"; break;
            case "texto": $html_body .= "<p>$content</p>\n"; break;
            case "subrayado": $html_body .= "<u>$content</u>\n"; break;
            case "grande": $html_body .= "<h1>$content</h1>\n"; break;
            case "texto_2": $html_body .= "<h2>$content</h2>\n"; break;
            case "texto_3": $html_body .= "<h3>$content</h3>\n"; break;
            case "texto_inaudible": $html_body .= "<h4>$content</h4>\n"; break;
            case "imagen": $html_body .= "<img src='".trim($content)."' style='max-width:100%;'>\n"; break;
            case "boton":
                if (strpos(trim($content), 'http') === 0) {
                    $html_body .= "<button onclick=\"window.open('".trim($content)."', '_blank')\">$content</button>\n";
                } else {
                    $html_body .= "<button onclick=\"$content\">$content</button>\n";
                }
                break;
            case "enlace_externo": $html_body .= "<a href='".trim($content)."' target='_blank'>$content</a>\n"; break;
            case "alerta": $html_body .= "<script>alert('$content');</script>\n"; break;
            case "muestra_estado": $html_body .= "<h2>Estado actual = funciona si no sabes entra en <u>https://guia-xlx.vercel.app</u></h2>\n"; break;
            case "division": $html_body .= "<br><br>\n"; break;
            case "separa_linea": $html_body .= "&nbsp;\n"; break;
        }
    }

    $resultado_final = "<!DOCTYPE html><html><head><meta charset='UTF-8'>";
    if ($css) $resultado_final .= "<style>$css</style>";
    if ($js) $resultado_final .= "<script>$js</script>";
    $resultado_final .= "</head><body>$html_body</body></html>";
}
?>

<!DOCTYPE html>
<html lang="es" style="margin:0; height:100%; overflow:hidden;">
<head>
    <meta charset="UTF-8">
    <title>XLX Engine - Editor Pro</title>
    <style>
        body { margin:0; height:100%; display:flex; flex-direction:column; background:#1e1e1e; font-family:sans-serif; }
        
        /* Contenedor del Editor */
        .editor-wrapper { position: relative; width: 50%; height: 100%; background: #1e1e1e; }
        #highlighting, #editing {
            margin: 0; padding: 20px; border: 0; width: 100%; height: 100%;
            font-family: 'Consolas', 'Monaco', monospace; font-size: 16px; line-height: 1.5;
            position: absolute; top: 0; left: 0; white-space: pre-wrap; word-wrap: break-word; box-sizing: border-box;
        }
        #editing {
            background: transparent; color: transparent; caret-color: white; 
            resize: none; outline: none; z-index: 1;
        }
        #highlighting { color: #d4d4d4; z-index: 0; overflow-y: auto; }

        /* Colores Solicitados */
        .hl-tag { color: #4CAF50; font-weight: bold; } /* Verde para etiquetas */
        .hl-script { color: #FFEB3B; }               /* Amarillo para JS */
        .hl-css { color: #2196F3; }                  /* Azul para Estilos */
        .hl-error { background: rgba(244, 67, 54, 0.4); border-bottom: 2px solid #f44336; } /* Rojo para errores */
        .hl-content { color: #ffffff; }              /* Blanco para lo de dentro */

        /* Interfaz */
        header { background:#333; color:white; padding:10px 20px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #444; }
        button { background:#4CAF50; color:white; border:none; padding:8px 20px; cursor:pointer; border-radius:4px; font-weight:bold; transition: 0.2s; }
        button:hover { background: #45a049; }
        .main { display:flex; flex:1; margin:0; overflow: hidden; }
        iframe { width:100%; height:100%; border:none; background:white; }
    </style>
</head>
<body>

    <header>
        <span><b>XLX</b> | Motor de Programación en Español</span>
        <button type="submit" form="editorForm">🚀 COMPILAR XLX</button>
    </header>

    <div class="main">
        <form id="editorForm" method="post" style="display:contents;">
            <div class="editor-wrapper">
                <pre id="highlighting" aria-hidden="true"><code id="highlighting-content"></code></pre>
                <textarea name="codigo" id="editing" spellcheck="false" 
                    oninput="update(this.value); sync_scroll(this);" 
                    onscroll="sync_scroll(this);"><?php echo htmlspecialchars($codigo_usuario); ?></textarea>
            </div>
        </form>
        
        <div style="width:50%; background:white;">
            <iframe srcdoc="<?php echo htmlspecialchars($resultado_final); ?>"></iframe>
        </div>
    </div>

    <script>
        function update(text) {
            let result_element = document.querySelector("#highlighting-content");
            
            // Escapar entidades HTML
            let content = text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");

            // 1. JS AMARILLO
            content = content.replace(/(&lt;js_abrir&gt;)([\s\S]*?)(&lt;js_cerrar&gt;)/g, 
                '<span class="hl-tag">$1</span><span class="hl-script">$2</span><span class="hl-tag">$3</span>');

            // 2. CSS AZUL
            content = content.replace(/(&lt;estilo_abrir&gt;)([\s\S]*?)(&lt;estilo_cerrar&gt;)/g, 
                '<span class="hl-tag">$1</span><span class="hl-css">$2</span><span class="hl-tag">$3</span>');

            // 3. ETIQUETAS VERDES Y CONTENIDO BLANCO
            // Buscamos <etiqueta>contenido<etiqueta>
            content = content.replace(/(&lt;(\w+)&gt;)([\s\S]*?)(&lt;\/\2&gt;|&lt;\2&gt;)/g, function(match, open, tag, inner, close) {
                // Si ya está coloreado como JS o CSS, no lo tocamos
                if (tag === "js_abrir" || tag === "estilo_abrir") return match;
                return '<span class="hl-tag">' + open + '</span><span class="hl-content">' + inner + '</span><span class="hl-tag">' + close + '</span>';
            });

            // 4. ERRORES ROJOS
            // Detecta etiquetas que no se cierran en la misma línea o caracteres sueltos sospechosos
            content = content.replace(/^(?!.*&gt;).*&lt;.*$/gm, '<span class="hl-error">$&</span>');

            result_element.innerHTML = content;
        }

        function sync_scroll(element) {
            let res = document.querySelector("#highlighting");
            res.scrollTop = element.scrollTop;
            res.scrollLeft = element.scrollLeft;
        }

        // Carga inicial
        update(document.querySelector("#editing").value);
    </script>
</body>
</html>
