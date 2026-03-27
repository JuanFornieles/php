import re
import sys
import os

# 1. Configuración de archivos
# Si PHP envía un argumento, lo usamos; si no, buscamos index.xlx en la misma carpeta
if len(sys.argv) > 1:
    archivo_entrada = sys.argv[1]
else:
    archivo_entrada = os.path.join(os.path.dirname(__file__), "index.xlx")

archivo_salida = "index.html"

# 2. Leer el código XLX
try:
    with open(archivo_entrada, "r", encoding="utf-8") as f:
        code = f.read()
except FileNotFoundError:
    print(f"Error: No se encontró {archivo_entrada}")
    sys.exit(1)

# ===========================
# 3. Procesar sección ESTILO
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
# 4. Procesar sección JS Global
# ===========================
js = ""
js_pattern = r"<js_abrir>(.*?)<js_cerrar>"
js_blocks = re.findall(js_pattern, code, re.DOTALL)
for bloque in js_blocks:
    js += bloque + "\n"

# ===========================
# 5. Procesar etiquetas XLX
# ===========================
pattern = r"<(\w+)>(.*?)<\1>"
matches = re.findall(pattern, code, re.DOTALL)

html = "<!DOCTYPE html>\n<html lang='es'>\n<head>\n<meta charset='UTF-8'>\n"

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
            html += f'<button onclick="window.open(\'{content.strip()}\', \'_blank\')">Ir a enlace</button>\n'
        else:
            html += f'<button onclick="{content.strip()}">Ejecutar</button>\n'
    elif tag == "enlace_externo":
        html += f'<a href="{content.strip()}" target="_blank">{content}</a>\n'
    elif tag == "alerta":
        html += f'<script>alert("{content.strip()}");</script>\n'
    elif tag == "muestra_estado":
        html += f"<h2>Estado actual = funciona. Guía en: <u>https://guia-xlx.vercel.app</u></h2>\n"
    elif tag == "division":
        html += "<br><br>\n"
    elif tag == "separa_linea":
        html += "&nbsp;\n"

html += "</body>\n</html>"

# 6. Guardar el resultado para que el PHP lo muestre en el iframe
try:
    with open(archivo_salida, "w", encoding="utf-8") as f:
        f.write(html)
    print("Compilación XLX exitosa") 
except Exception as e:
    print(f"Error al escribir HTML: {e}")

# webbrowser.open("index.html") <-- COMENTADO PARA USO WEB
