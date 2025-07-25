<?php
$host = 'localhost'; // o 127.0.0.1
$usuario = 'root'; // por defecto en WAMP es 'root'
$contrasena = ''; // en WAMP normalmente está vacío
$base_de_datos = 'oficio'; // <-- cambia esto

$conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>
