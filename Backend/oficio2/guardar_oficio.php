
<?php
header('Content-Type: application/json');

// Configura conexión a tu base de datos
$host = "localhost";
$user = "root";
$pass = "";
$db = "oficio";

$conexion = new mysqli($host, $user, $pass, $db);
if ($conexion->connect_errno) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión: ' . $conexion->connect_error]);
    exit;
}

// Recibe datos POST
$nombre_remitente   = $_POST['nombre_remitente']   ?? '';
$cargo_remitente    = $_POST['cargo_remitente']    ?? '';
$area_remitente     = $_POST['area_remitente']     ?? '';
$oficio_ref         = $_POST['oficio_ref']         ?? '';
$cdi                = $_POST['cdi']                ?? '';
$fecha_oficio       = $_POST['fecha_oficio']       ?? '';
$fecha_recepcion    = $_POST['fecha_recepcion']    ?? '';
$paciente_nombre    = $_POST['paciente_nombre']    ?? '';

// Validar campo obligatorio
if (!$nombre_remitente || !$paciente_nombre) {
    echo json_encode(['success' => false, 'error' => 'Campos obligatorios faltantes']);
    exit;
}

// Generar oficio_no automático
$result = $conexion->query("SELECT oficio_no FROM oficios2 ORDER BY id DESC LIMIT 1");
$ultimo = $result->fetch_assoc()['oficio_no'] ?? null;

if ($ultimo) {
    preg_match('/(\d+)$/', $ultimo, $matches);
    $numero = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
} else {
    $numero = 1;
}

$oficio_no = "CRM-" . str_pad($numero, 4, "0", STR_PAD_LEFT);

// Preparar consulta
$stmt = $conexion->prepare("INSERT INTO oficios2 (
    oficio_no, nombre_remitente, cargo_remitente, area_remitente, 
    oficio_ref, cdi, fecha_oficio, fecha_recepcion, paciente_nombre
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Error en prepare: ' . $conexion->error]);
    exit;
}

$stmt->bind_param(
    "sssssssss",
    $oficio_no,
    $nombre_remitente,
    $cargo_remitente,
    $area_remitente,
    $oficio_ref,
    $cdi,
    $fecha_oficio,
    $fecha_recepcion,
    $paciente_nombre
);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'oficio_no' => $oficio_no]);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al insertar: ' . $stmt->error]);
}

$stmt->close();
$conexion->close();
