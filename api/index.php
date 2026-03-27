<?php
/**
 * PHP God Mode en Vercel - Single File App
 */

// --- 1. SETTINGS & MIDDLEWARE ---
header("Content-Type: text/html; charset=UTF-8");
$start = microtime(true);

// --- 2. EL "MOTOR" (Funciones complejas integradas) ---

// Generador de un patrón visual matemático (SVG)
function generatePattern($seed) {
    $svg = '<svg width="200" height="200" xmlns="http://www.w3.org">';
    for ($i = 0; $i < 50; $i++) {
        $x = hash('crc32', $seed . $i) % 200;
        $y = hash('crc32', $i . $seed) % 200;
        $color = substr(md5($seed . $i), 0, 6);
        $svg .= "<circle cx='$x' cy='$y' r='5' fill='#$color' opacity='0.6' />";
    }
    return $svg . '</svg>';
}

// Cálculo de números primos (Stress Test para la CPU de Vercel)
function getPrimes($limit) {
    $primes = [];
    for ($i = 2; $i < $limit; $i++) {
        $count = 0;
        for ($j = 1; $j <= $i; $j++) {
            if ($i % $j == 0) $count++;
        }
        if ($count == 2) $primes[] = $i;
    }
    return $primes;
}

// --- 3. LÓGICA DE CONTROL ---
$action = $_GET['do'] ?? 'info';
$value  = $_GET['val'] ?? 'Vercel';

echo "<html><head><style>body{font-family:sans-serif;background:#f0f2f5;padding:40px;} .card{background:#fff;padding:20px;border-radius:10px;box-shadow:0 4px-6px rgba(0,0,0,0.1);max-width:600px;margin:auto;} code{background:#eee;padding:2px 5px;}</style></head><body>";
echo "<div class='card'>";

switch ($action) {
    case 'stress':
        $limit = (int)$value;
        $p = getPrimes($limit > 5000 ? 5000 : $limit);
        echo "<h2>Stress Test: Números Primos</h2>";
        echo "Calculados los primos hasta $limit. Encontrados: " . count($p);
        break;

    case 'art':
        echo "<h2>Arte Matemático Generativo</h2>";
        echo generatePattern($value);
        echo "<p>Semilla: <code>$value</code></p>";
        break;

    default:
        echo "<h2>Consola Super-PHP en Vercel</h2>";
        echo "<p>Este archivo es un sistema autónomo. Prueba estas rutas:</p>";
        echo "<ul>
                <li><a href='?do=stress&val=3000'>Stress Test (CPU)</a></li>
                <li><a href='?do=art&val=".uniqid()."'>Generar Arte (SVG)</a></li>
                <li><a href='?do=art&val=VercelRocks'>Arte con Semilla Fija</a></li>
              </ul>";
        break;
}

// --- 4. FOOTER (Métricas en tiempo real) ---
$time = round((microtime(true) - $start) * 1000, 2);
$mem = round(memory_get_usage() / 1024 / 1024, 2);
echo "<hr><small>Ejecutado en <b>{$time}ms</b> | Memoria: <b>{$mem}MB</b> | PHP: <b>".PHP_VERSION."</b></small>";
echo "</div></body></html>";
