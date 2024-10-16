<?php
require_once('../scripts/conexion.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    
    $sql = "SELECT COUNT(*) as count FROM usuario WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    echo ($row['count'] > 0) ? 'taken' : 'available';
    
    $stmt->close();
}
$conn->close();
?>