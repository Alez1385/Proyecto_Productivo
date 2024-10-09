<?php
session_start();
require_once '../../scripts/conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit;
}

if (!isset($_POST['id_preinscripcion'])) {
    echo json_encode(['success' => false, 'message' => 'ID de preinscripción no proporcionado']);
    exit;
}

$user_id = $_SESSION['id_usuario'];
$id_preinscripcion = $_POST['id_preinscripcion'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar que la preinscripción pertenece al usuario
    $stmt = $pdo->prepare("SELECT * FROM preinscripciones WHERE id_preinscripcion = ? AND id_usuario = ?");
    $stmt->execute([$id_preinscripcion, $user_id]);
    $preinscripcion = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$preinscripcion) {
        echo json_encode(['success' => false, 'message' => 'Preinscripción no encontrada o no pertenece al usuario']);
        exit;
    }

    // Actualizar el estado de la preinscripción a 'cancelado'
    $stmt = $pdo->prepare("DELETE FROM preinscripciones WHERE id_preinscripcion = ?");
    $stmt->execute([$id_preinscripcion]);

    echo json_encode(['success' => true, 'message' => 'Preinscripción cancelada exitosamente']);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al cancelar la preinscripción: ' . $e->getMessage()]);
}