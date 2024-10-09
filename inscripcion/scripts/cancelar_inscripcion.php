<?php
session_start();
require_once '../../scripts/conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit;
}

if (!isset($_POST['id_inscripcion'])) {
    echo json_encode(['success' => false, 'message' => 'ID de inscripción no proporcionado']);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$id_inscripcion = $_POST['id_inscripcion'];
//consulta para obtener el id_estudiante segun el id_usuario
$stmt = $conn->prepare("SELECT id_estudiante FROM estudiante WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$id_estudiante = $row['id_estudiante'];

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar que la inscripción pertenece al usuario
    $stmt = $pdo->prepare("SELECT * FROM inscripciones WHERE id_inscripcion = ? AND id_estudiante = ?");
    $stmt->execute([$id_inscripcion, $id_estudiante]);
    $inscripcion = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$inscripcion) {
        echo json_encode(['success' => false, 'message' => 'Inscripción no encontrada o no pertenece al usuario']);
        exit;
    }

    // Actualizar el estado de la inscripción a 'cancelado'
    $stmt = $pdo->prepare("DELETE FROM inscripciones WHERE id_inscripcion = ?");
    $stmt->execute([$id_inscripcion]);

    echo json_encode(['success' => true, 'message' => 'Inscripción cancelada exitosamente']);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al cancelar la inscripción: ' . $e->getMessage()]);
}