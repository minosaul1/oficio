<?php
header('Content-Type: application/json');
require '../conexion/conexion.php'; // Asegúrate que esta ruta sea correcta

if (!isset($_GET['id'])) {
  echo json_encode(null);
  exit;
}

$id = intval($_GET['id']);

// Cambiado para usar la tabla "oficios2"
$query = "SELECT * FROM oficios2 WHERE id = $id LIMIT 1";
$result = mysqli_query($conexion, $query);

if ($result && mysqli_num_rows($result) > 0) {
  $oficio = mysqli_fetch_assoc($result);
  echo json_encode($oficio);
} else {
  echo json_encode(null);
}
?>
