<?php
header('Content-Type: application/json');

// Conexión a la base de datos
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'oficio';

$conexion = new mysqli($host, $user, $pass, $db);
if ($conexion->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión']);
    exit;
}

// Recibir datos del POST
$id                = $_POST['id'] ?? null;
$oficio_no         = $_POST['oficio_no'] ?? '';
$nombre_remitente  = $_POST['nombre_remitente'] ?? '';
$cargo_remitente   = $_POST['cargo_remitente'] ?? '';
$area_remitente    = $_POST['area_remitente'] ?? '';
$oficio_ref        = $_POST['oficio_ref'] ?? '';
$cdi               = $_POST['cdi'] ?? '';
$fecha_oficio      = $_POST['fecha_oficio'] ?? '';
$fecha_recepcion   = $_POST['fecha_recepcion'] ?? '';
$paciente_nombre   = $_POST['paciente_nombre'] ?? '';

// Validaciones básicas
if (!$id || !$nombre_remitente || !$paciente_nombre) {
    echo json_encode(['success' => false, 'error' => 'Faltan campos obligatorios']);
    exit;
}

// Actualizar el registro en la tabla oficios2
$stmt = $conexion->prepare("UPDATE oficios2 SET 
    oficio_no = ?, 
    nombre_remitente = ?, 
    cargo_remitente = ?, 
    area_remitente = ?, 
    oficio_ref = ?, 
    cdi = ?, 
    fecha_oficio = ?, 
    fecha_recepcion = ?, 
    paciente_nombre = ?
    WHERE id = ?
");

if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Error en prepare: ' . $conexion->error]);
    exit;
}

$stmt->bind_param(
    "sssssssssi",
    $oficio_no,
    $nombre_remitente,
    $cargo_remitente,
    $area_remitente,
    $oficio_ref,
    $cdi,
    $fecha_oficio,
    $fecha_recepcion,
    $paciente_nombre,
    $id
);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al actualizar: ' . $stmt->error]);
}

$stmt->close();
$conexion->close();
