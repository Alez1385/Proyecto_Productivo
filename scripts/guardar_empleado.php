<?php

include ("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $nom_empleado = $_POST['nom_empleado'];
    $apell_empleado = $_POST['apell_empleado'];
    $doc_empleado = $_POST['doc_empleado'];
    $tipo_doc_emp = $_POST['tipo_doc_emp'];
    $email_emp = $_POST['email_emp'];
    $tel_empleado = $_POST['tel_empleado'];
    $fecha_nac_emp = $_POST['fecha_nac_emp'];
    $tipo_empleado = $_POST['tipo_empleado'];
    $gender = $_POST['gender'];
    $direc_empleado = $_POST['direc_empleado'];

    if (isset($_FILES['foto_emp']) && $_FILES['foto_emp']['error'] == UPLOAD_ERR_OK) {
        $foto_emp = $_FILES['foto_emp']['name'];
        $foto_target_file = "../uploads/" . basename($foto_emp);
        move_uploaded_file($_FILES["foto_emp"]["tmp_name"], $foto_target_file);
    } else {
        $foto_emp = NULL;
    }

    
    $sql = "INSERT INTO empleado (nom_empleado, apell_empleado, doc_empleado, tipo_doc_emp, email_emp, tel_empleado, fecha_nac_emp, tipo_empleado, foto_emp, direc_empleado, id_supervisor)
            VALUES ('$nom_empleado', '$apell_empleado', '$doc_empleado', '$tipo_doc_emp', '$email_emp', '$tel_empleado', '$fecha_nac_emp', '$tipo_empleado', '$foto_emp', '$direc_empleado', NULL)";

    if ($conn->query($sql) === TRUE) {
        echo "Nuevo registro creado exitosamente";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}


$conn->close();
?>