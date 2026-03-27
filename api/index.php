<?php
declare(strict_types=1);

// 1. CONFIGURACIÓN DE CABECERAS (CORS & JSON)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// 2. MIDDLEWARE DE RENDIMIENTO
$start = microtime(true);

// 3. EL "CONTENEDOR" DE RUTAS (ROUTER MINI-FRAMEWORK)
$router = [
    'GET' => [],
    'POST' => []
];

// Función para registrar rutas
$route = function(string $method, string $path, callable $handler) use (&$router) {
    $router[$method][$path] = $handler;
};

// 4. DEFINICIÓN DE SERVICIOS (Lógica Compleja)

// Servicio: Consultar múltiples APIs en paralelo (High Performance)
$fetchParallel = function(array $urls) {
    $mh = curl_multi_init();
    $requests = [];
    foreach ($urls as $i => $url) {
        $requests[$i] = curl_init($url);
        curl_setopt($requests[$i], CURLOPT_RETURNTRANSFER, true);
        curl_setopt($requests[$i], CURLOPT_TIMEOUT, 2);
        curl_multi_add_handle($mh, $requests[$i]);
    }
    
    $active = null;
    do { $mrc = curl_multi_exec($mh, $active); } while ($mrc == CURLM_CALL_MULTI_PERFORM);
    while ($active && $mrc == CURLM_OK) {
        if (curl_multi_select($mh) != -1) {
            do { $mrc = curl_multi_exec($mh, $active); } while ($mrc == CURLM_CALL_MULTI_PERFORM);
        }
    }

    $results = [];
    foreach ($requests as $i => $ch) {
        $results[] = json_decode(curl_multi_getcontent($ch), true);
        curl_multi_remove_handle($mh, $ch);
    }
    curl_multi_close($mh);
    return $results;
};

// 5. REGISTRO DE RUTAS DINÁMICAS
$route('GET', '/status', function() {
    return ['status' => 'online', 'php_version' => PHP_VERSION, 'memory_usage' => memory_get_usage()];
});

$route('GET', '/data-mashup', function() use ($fetchParallel) {
    // Simulamos traer datos de dos sitios distintos al mismo tiempo
    $urls = [
        'https://pokeapi.co',
        'https://rickandmortyapi.com'
    ];
    $data = $fetchParallel($urls);
    return [
        'pokemon' => $data[0]['name'] ?? 'unknown',
        'character' => $data[1]['name'] ?? 'unknown'
    ];
});

// 6. EJECUCIÓN DEL ROUTER (DISPATCHER)
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/api', '', $path);

try {
    if (isset($router[$method][$path])) {
        $response = $router[$method][$path]();
    } else {
        http_response_code(404);
        $response = ['error' => 'Route not found', 'path' => $path];
    }
} catch (\Throwable $e) {
    http_response_code(500);
    $response = ['error' => $e->getMessage()];
}


$response['meta'] = [
    'execution_time_ms' => round((microtime(true) - $start) * 1000, 2),
    'timestamp' => time()
];

echo json_encode($response, JSON_PRETTY_PRINT);
