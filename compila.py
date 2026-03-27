import re
import sys
import os
import webbrowser

# Archivo por defecto
if len(sys.argv) > 1:
    archivo = sys.argv[1]
else:
    archivo = os.path.join(os.path.dirname(__file__), "index.xlx")

# Leer archivo
try:
    with open(archivo, "r", encoding="utf-8") as f:
        code = f.read()
except FileNotFoundError:
    print(f"Archivo no encontrado: {archivo}")
    sys.exit(1)

# ===========================
# 1️⃣ Procesar sección estilo
# ===========================
css = ""
estilo_pattern = r"<estilo_abrir>(.*?)<estilo_cerrar>"
estilos = re.findall(estilo_pattern, code, re.DOTALL)

for bloque in estilos:
    for linea in bloque.splitlines():
        linea = linea.strip()
        if "=" in linea:
            prop, valor = linea.split("=")
            prop = prop.strip()
            valor = valor.strip()
            if prop == "fondo":
                css += f"body {{ background-color: {valor}; }}\n"
            elif prop == "letra":
                css += f"body {{ font-family: {valor}; }}\n"

# ===========================
# 2️⃣ Procesar sección JS global
# ===========================
js = ""
js_pattern = r"<js_abrir>(.*?)<js_cerrar>"
js_blocks = re.findall(js_pattern, code, re.DOTALL)
for bloque in js_blocks:
    js += bloque + "\n"

# ===========================
# 3️⃣ Procesar etiquetas normales
# ===========================
pattern = r"<(\w+)>(.*?)<\1>"
matches = re.findall(pattern, code, re.DOTALL)

html = "<!DOCTYPE html>\n<html>\n<head>\n"

if css:
    html += f"<style>\n{css}</style>\n"
if js:
    html += f"<script>\n{js}</script>\n"

html += "</head>\n<body>\n"

for tag, content in matches:
    if tag in ["estilo_abrir", "estilo_cerrar", "js_abrir", "js_cerrar"]:
        continue  
    elif tag == "titulo":
        html += f"<title>{content}</title>\n<h1>{content}</h1>\n"
    elif tag == "texto":
        html += f"<p>{content}</p>\n"
    elif tag == "subrayado":
        html += f"<u>{content}</u>\n"
    elif tag == "grande":
        html += f"<h1>{content}</h1>\n"
    elif tag == "texto_2":
        html += f"<h2>{content}</h2>\n"
    elif tag == "texto_3":
        html += f"<h3>{content}</h3>\n"
    elif tag == "texto_inaudible":
        html += f"<h4>{content}</h4>\n"
    elif tag == "imagen":
        html += f'<img src="{content.strip()}" style="max-width:100%;"/>\n'
    elif tag == "boton":
        if content.startswith("http"):
            html += f'<button onclick="window.open(\'{content.strip()}\', \'_blank\')">Abrir enlace</button>\n'
        else:
            html += f'<button onclick="{content.strip()}">Acción</button>\n'
    elif tag == "enlace_externo":
        html += f'<a href="{content.strip()}" target="_blank">{content}</a>\n'
    elif tag == "alerta":
        html += f'<script>alert("{content.strip()}");</script>\n'
    elif tag == "muestra_estado":
        html += f"<h2>Estado actual = funciona</h2>\n"
    elif tag == "division":
        html += "<br><br>\n"
    elif tag == "separa_linea":
        html += "&nbsp;\n"

html += "</body>\n</html>"

with open("index.html", "w", encoding="utf-8") as f:
    f.write(html)

print("Compilación Exitosa")
# webbrowser.open("index.html")  <-- ESTA LÍNEA SE QUEDA COMENTADA PARA QUE NO FALLE EN PHP
