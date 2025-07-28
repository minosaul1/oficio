<?php
header('Content-Type: application/json');

// Conexión a la base de datos
require_once '../conexion/conexion.php';

// Validar que se recibió el ID
if (!isset($_POST['id']) || empty($_POST['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'ID no proporcionado'
    ]);
    exit;
}

$id = $_POST['id'];

// Obtener y limpiar todos los campos del formulario
$oficio_no = $_POST['oficio_no'] ?? '';
$nombre_remitente = $_POST['nombre_remitente'] ?? '';
$cargo_remitente = $_POST['cargo_remitente'] ?? '';
$area_remitente = $_POST['area_remitente'] ?? '';
$oficio_ref = $_POST['oficio_ref'] ?? '';
$cdi = $_POST['cdi'] ?? '';
$fecha_oficio = $_POST['fecha_oficio'] ?? '';
$fecha_recepcion = $_POST['fecha_recepcion'] ?? '';
$paciente_nombre = $_POST['paciente_nombre'] ?? '';

// Preparar consulta SQL para actualizar el oficio
$sql = "UPDATE oficios SET 
    oficio_no = ?, 
    nombre_remitente = ?, 
    cargo_remitente = ?, 
    area_remitente = ?, 
    oficio_ref = ?, 
    cdi = ?, 
    fecha_oficio = ?, 
    fecha_recepcion = ?, 
    paciente_nombre = ?
    WHERE id = ?";

$stmt = $conexion->prepare($sql);

// Verificar si la preparación fue exitosa
if (!$stmt) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al preparar la consulta: ' . $conexion->error
    ]);
    exit;
}

// Enlazar parámetros y ejecutar
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
    echo json_encode([
        'success' => true,
        'message' => 'Oficio actualizado correctamente'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error al actualizar el oficio: ' . $stmt->error
    ]);
}

// Cerrar la conexión
$stmt->close();
$conexion->close();
?>
