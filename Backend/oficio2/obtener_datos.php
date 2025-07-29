<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *"); // Opcional

$host = 'localhost';
$db = 'oficio';
$user = 'root';
$pass = ''; // Cambia si tienes contraseña

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 👇 CAMBIADO a la tabla correcta
    $stmt = $pdo->query("SELECT id, oficio_no, nombre_remitente, cargo_remitente, cdi, fecha_oficio, fecha_creacion AS fecha_registro, paciente_nombre FROM oficios2 ORDER BY fecha_creacion DESC");

    $oficios = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $oficios[] = [
            'id' => $row['id'],
            'oficio_no' => $row['oficio_no'],
            'nombre_remitente' => $row['nombre_remitente'],
            'cargo_remitente' => $row['cargo_remitente'],
            'cdi' => $row['cdi'],
            'fecha_oficio' => $row['fecha_oficio'],
            'fecha_registro' => $row['fecha_registro'], // timestamp de creación
            'paciente_nombre' => $row['paciente_nombre']
        ];
    }

    echo json_encode($oficios);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
}
