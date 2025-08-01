<?php
header('Content-Type: application/json');
require '../conexion/conexion.php'; // Asegúrate que esta ruta sea correcta

// 1. Verificar que el ID exista
if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'No se proporcionó un ID']);
    exit;
}

// 2. Limpiar y validar el ID
$id = intval($_GET['id']);
if ($id <= 0) {
    echo json_encode(['error' => 'ID no válido']);
    exit;
}

// 3. Usar una sentencia preparada para seguridad y eficiencia
// Esta es la forma correcta y segura de hacer consultas.
$query = "SELECT * FROM oficios2 WHERE id = ? LIMIT 1";

// Preparar la consulta
$stmt = mysqli_prepare($conexion, $query);

if ($stmt) {
    // Vincular el parámetro (el ID)
    mysqli_stmt_bind_param($stmt, "i", $id); // "i" significa que el parámetro es un entero

    // Ejecutar la consulta
    mysqli_stmt_execute($stmt);

    // Obtener el resultado
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $oficio = mysqli_fetch_assoc($result);
        // Enviar la respuesta JSON al frontend
        echo json_encode($oficio);
    } else {
        // No se encontró el oficio con ese ID
        echo json_encode(null);
    }

    // Cerrar la sentencia
    mysqli_stmt_close($stmt);
} else {
    // Error en la preparación de la consulta
    echo json_encode(['error' => 'Error al preparar la consulta a la base de datos.']);
}

// Cerrar la conexión
mysqli_close($conexion);
?>