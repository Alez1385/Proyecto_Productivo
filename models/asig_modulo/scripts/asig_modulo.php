<?php

error_reporting(E_ALL);
ini_set('display_errors', 0);

// Captura la salida
ob_start();

header('Content-Type: application/json');
$output = ob_get_clean();

// Si hay salida, envíala como parte de la respuesta JSON
if (!empty($output)) {
    echo json_encode(['message' => 'Error de PHP: ' . $output, 'success' => false]);
    exit;
}

try {
    require_once "../../../scripts/conexion.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $userType = $_POST["userType"] ?? null;
        $action = $_POST["action"] ?? null;
        $message = "";
        $success = false;

        if (!$userType || !$action) {
            throw new Exception('Faltan parámetros requeridos');
        }

        if ($action == "assign") {
            if (isset($_POST["id_modulo"])) {
                foreach ($_POST["id_modulo"] as $id_modulo => $value) {
                    $checkSql = "SELECT * FROM asig_modulo WHERE id_modulo = ? AND id_tipo_usuario = ?";
                    $checkStmt = $conn->prepare($checkSql);
                    $checkStmt->bind_param("ii", $id_modulo, $userType);
                    $checkStmt->execute();
                    $checkResult = $checkStmt->get_result();

                    if ($checkResult->num_rows == 0) {
                        $sql = "INSERT INTO asig_modulo (id_modulo, id_tipo_usuario) VALUES (?, ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ii", $id_modulo, $userType);
                        $stmt->execute();
                    }
                }
                $message = "Módulos asignados correctamente.";
                $success = true;
            } else {
                $message = "No se seleccionaron módulos para asignar.";
            }
        } elseif ($action == "remove") {
            if (isset($_POST["id_modulo"])) {
                foreach ($_POST["id_modulo"] as $id_modulo => $value) {
                    $sql = "DELETE FROM asig_modulo WHERE id_modulo = ? AND id_tipo_usuario = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ii", $id_modulo, $userType);
                    $stmt->execute();
                }
                $message = "Módulos removidos correctamente.";
                $success = true;
            } else {
                $message = "No se seleccionaron módulos para remover.";
            }
        } else {
            throw new Exception('Acción no válida');
        }

        echo json_encode(['message' => $message, 'success' => $success]);
    } else {
        throw new Exception('Método no permitido');
    }
} catch (Exception $e) {
    echo json_encode(['message' => 'Error: ' . $e->getMessage(), 'success' => false]);
}
