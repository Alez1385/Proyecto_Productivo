<?php
include "conexion.php";

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

// Get the data from the request body
$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data) && isset($data['id_usuario']) && isset($data['module_order'])) {
    // Start a transaction
    $conn->begin_transaction();
    try {
        $id_usuario = filter_var($data['id_usuario'], FILTER_VALIDATE_INT);
        $module_order = json_encode($data['module_order']); // Store as JSON string

        if ($id_usuario === false) {
            throw new Exception("Invalid user ID");
        }

        // Prepare SQL to insert or update the user's module order
        $sql = "INSERT INTO user_module_order (id_usuario, module_order) 
                VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE module_order = ?";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error preparing SQL query: " . $conn->error);
        }

        $stmt->bind_param("iss", $id_usuario, $module_order, $module_order);
        
        if (!$stmt->execute()) {
            throw new Exception("Error executing SQL query: " . $stmt->error);
        }

        $stmt->close();

        // Commit the transaction
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Module order updated successfully']);
    } catch (Exception $e) {
        // Rollback the transaction in case of error
        $conn->rollback();
        error_log('Error updating module order: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error updating module order']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No data received or invalid data format']);
}

$conn->close();
