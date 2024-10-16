<?php
require_once('../scripts/conexion.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo 'invalid';
        exit;
    }
    
    $sql = "SELECT COUNT(*) as count FROM usuario WHERE mail = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    echo ($row['count'] > 0) ? 'taken' : 'available';
    
    $stmt->close();
}
$conn->close();
?>