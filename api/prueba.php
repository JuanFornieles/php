<?php
$resultado_final = "";
$doctype_obligatorio = "<!DOCTYPE XLX PUBLIC -- https://xlx.vercel.app -->";
$codigo_usuario = isset($_POST['codigo']) ? $_POST['codigo'] : $doctype_obligatorio . "\n\n<titulo>Mi Web XLX</titulo>\n<texto>Escribe aquí en español</texto>";

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

    // 3️⃣ Procesar etiquetas normales
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
                } else { $html_body .= "<button onclick=\"$content\">$content</button>\n"; }
                break;
            case "enlace_externo": $html_body .= "<a href='".trim($content)."' target='_blank'>$content</a>\n"; break;
            case "alerta": $html_body .= "<script>alert('$content');</script>\n"; break;
            case "muestra_estado": $html_body .= "<h2>Estado actual = funciona</h2>\n"; break;
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
    <title>XLX Engine | Editor</title>
    <style>
        body { margin:0; height:100%; display:flex; flex-direction:column; background:#1e1e1e; font-family:sans-serif; }
        header { background:#333; color:white; padding:10px 20px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #444; }
        .editor-wrapper { position: relative; width: 50%; height: 100%; background: #1e1e1e; }
        #highlighting, #editing {
            margin: 0; padding: 20px; border: 0; width: 100%; height: 100%;
            font-family: 'Consolas', monospace; font-size: 16px; line-height: 1.5;
            position: absolute; top: 0; left: 0; white-space: pre-wrap; word-wrap: break-word; box-sizing: border-box;
        }
        #editing { background: transparent; color: transparent; caret-color: white; resize: none; outline: none; z-index: 1; }
        #highlighting { color: #d4d4d4; z-index: 0; overflow-y: auto; }
        
        .hl-doctype { color: #888; font-style: italic; }
        .hl-tag { color: #4CAF50; font-weight: bold; }
        .hl-script { color: #FFEB3B; }
        .hl-css { color: #2196F3; }
        .hl-error { background: rgba(244, 67, 54, 0.4); border-bottom: 2px solid #f44336; }
        .hl-content { color: #ffffff; }

        button { background:#4CAF50; color:white; border:none; padding:8px 20px; cursor:pointer; border-radius:4px; font-weight:bold; }
        .main { display:flex; flex:1; overflow: hidden; }
        iframe { width:100%; height:100%; border:none; background:white; }
    </style>
</head>
<body>
    <header>
        <span><b>XLX</b> | Editor en la Nube</span>
        <button type="submit" form="editorForm">🚀 COMPILAR XLX</button>
    </header>

    <div class="main">
        <form id="editorForm" method="post" style="display:contents;">
            <div class="editor-wrapper">
                <pre id="highlighting" aria-hidden="true"><code id="highlighting-content"></code></pre>
                <textarea name="codigo" id="editing" spellcheck="false" oninput="update(this.value); sync_scroll(this);" onscroll="sync_scroll(this);"><?php echo htmlspecialchars($codigo_usuario); ?></textarea>
            </div>
        </form>
        <div style="width:50%; background:white;"><iframe srcdoc="<?php echo htmlspecialchars($resultado_final); ?>"></iframe></div>
    </div>

    <script>
        function update(text) {
            let res = document.querySelector("#highlighting-content");
            let doctype = "<!DOCTYPE XLX PUBLIC -- https://xlx.vercel.app -->";
            let lines = text.split('\n');
            let finalContent = "";

            lines.forEach((line, index) => {
                let currentLine = line.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
                
                // Regla 1: Validar DOCTYPE en la línea 0
                if(index === 0) {
                    if(line.trim() === doctype.trim()) {
                        finalContent += `<span class="hl-doctype">${currentLine}</span>\n`;
                    } else {
                        finalContent += `<span class="hl-error">${currentLine}</span>\n`;
                    }
                    return;
                }

                // Resaltado de bloques
                if(currentLine.includes("&lt;js_abrir&gt;") || currentLine.includes("&lt;js_cerrar&gt;")) {
                    currentLine = `<span class="hl-tag">${currentLine}</span>`;
                } else if(currentLine.includes("&lt;estilo_abrir&gt;") || currentLine.includes("&lt;estilo_cerrar&gt;")) {
                    currentLine = `<span class="hl-tag">${currentLine}</span>`;
                } else {
                    // Etiquetas normales
                    currentLine = currentLine.replace(/(&lt;\w+&gt;)(.*?)(&lt;\w+&gt;)/g, '<span class="hl-tag">$1</span><span class="hl-content">$2</span><span class="hl-tag">$3</span>');
                }
                
                finalContent += currentLine + "\n";
            });

            res.innerHTML = finalContent;
        }

        function sync_scroll(el) {
            let h = document.querySelector("#highlighting");
            h.scrollTop = el.scrollTop; h.scrollLeft = el.scrollLeft;
        }
        update(document.querySelector("#editing").value);
    </script>
</body>
</html>
