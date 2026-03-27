<?php
$nombre = $_GET['nombre'] ?? 'Desconocido';
echo "<h1>Hola, $nombre, bienvenido a PHP en Vercel</h1>";
echo "<p>La hora actual es: " . date('H:i:s') . "</p>";
