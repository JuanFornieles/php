<?php
$resultado_final = "";
$codigo_usuario = isset($_POST['codigo']) ? $_POST['codigo'] : "<titulo>Mi Web XLX</titulo>\n<texto>Escribe aquí en español</texto>";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = $_POST['codigo'];

    // 1️⃣ Procesar sección estilo (Tu lógica exacta de fondo y letra)
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

    // 2️⃣ Procesar sección JS global
    $js = "";
    if (preg_match_all('/<js_abrir>(.*?)<js_cerrar>/s', $code, $js_blocks)) {
        foreach ($js_blocks[1] as $bloque) { $js .= $bloque . "\n"; }
    }

    // 3️⃣ Procesar etiquetas normales (Tu sintaxis <tag>...</tag>)
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
<html style="margin:0; height:100%; overflow:hidden;">
<body style="margin:0; height:100%; display:flex; flex-direction:column; background:#1e1e1e; font-family:sans-serif;">
    <div style="background:#333; color:white; padding:10px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #444;">
        <span><b>XLX</b>Motor para XLX (php)</span>
        <button type="submit" form="editorForm" style="background:#4CAF50; color:white; border:none; padding:8px 20px; cursor:pointer; border-radius:4px; font-weight:bold;">🚀 COMPILAR</button>
    </div>
    <form id="editorForm" method="post" style="display:flex; flex:1; margin:0;">
        <textarea name="codigo" style="width:50%; background:#252526; color:#d4d4d4; padding:20px; border:none; font-family:'Consolas', monospace; font-size:16px; outline:none; resize:none;"><?php echo htmlspecialchars($codigo_usuario); ?></textarea>
        <div style="width:50%; background:white;">
            <iframe srcdoc="<?php echo htmlspecialchars($resultado_final); ?>" style="width:100%; height:100%; border:none;"></iframe>
        </div>
    </form>
</body>
</html>
