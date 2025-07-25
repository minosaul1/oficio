<?php
header('Content-Type: application/json');
require '../conexion/conexion.php'; // Ajusta ruta

if (!isset($_GET['id'])) {
  echo json_encode(null);
  exit;
}

$id = intval($_GET['id']);

$query = "SELECT * FROM oficios WHERE id = $id LIMIT 1";
$result = mysqli_query($conexion, $query);

if ($result && mysqli_num_rows($result) > 0) {
  $oficio = mysqli_fetch_assoc($result);
  echo json_encode($oficio);
} else {
  echo json_encode(null);
}
?>
