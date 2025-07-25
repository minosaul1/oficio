<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *"); // Opcional, depende de tu frontend

$host = 'localhost';
$db = 'oficio';
$user = 'root';
$pass = ''; // Coloca tu contraseña si tienes

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT id, oficio_no, nombre_remitente, cargo_remitente, cdi, fecha_oficio, fecha_registro, paciente_nombre FROM oficios ORDER BY fecha_registro DESC");

    $oficios = []; // inicializa el arreglo

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $oficios[] = [
            'id' => $row['id'],
            'oficio_no' => $row['oficio_no'],
            'nombre_remitente' => $row['nombre_remitente'],
            'cargo_remitente' => $row['cargo_remitente'],
            'cdi' => $row['cdi'],
            'fecha_oficio' => $row['fecha_oficio'],
            'fecha_registro' => $row['fecha_registro'],
            'paciente_nombre' => $row['paciente_nombre']
        ];
    }

    echo json_encode($oficios);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
}
