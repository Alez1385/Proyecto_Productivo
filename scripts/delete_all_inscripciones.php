<?php
session_start();
require_once 'conexion.php';

// Solo permitir si el usuario es admin
if (!isset($_SESSION['id_usuario']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo 'Acceso denegado. Solo administradores.';
    exit;
}

try {
    $conn->begin_transaction();
    // Limpiar historial de inscripciones primero (por FK)
    $conn->query('DELETE FROM historial_inscripciones');
    // Limpiar asistencia relacionada a inscripciones
    $conn->query('DELETE FROM asistencia');
    // Limpiar pagos relacionados a inscripciones
    $conn->query('DELETE FROM pagos');
    // Limpiar inscripciones y preinscripciones
    $conn->query('DELETE FROM inscripciones');
    $conn->query('DELETE FROM preinscripciones');
    $conn->commit();

    // Eliminar archivos de comprobantes de pago en uploads/comprobantes/
    $comprobantes_dir = __DIR__ . '/../uploads/comprobantes/';
    if (is_dir($comprobantes_dir)) {
        $files = scandir($comprobantes_dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;
            $file_path = $comprobantes_dir . $file;
            if (is_file($file_path)) {
                unlink($file_path);
            }
        }
    }

    echo 'Todas las inscripciones, preinscripciones, historial, asistencias, pagos y archivos de comprobantes han sido eliminados correctamente.';
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo 'Error al eliminar: ' . $e->getMessage();
} 