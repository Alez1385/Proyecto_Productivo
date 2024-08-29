<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include "../../../scripts/conexion.php";

    $count_success = 0;
    $id_tipo_usuario = $_POST['userType']; // ID del tipo de usuario

    // Inicializar variables de error
    $error_messages = [];

    // Obtener todos los usuarios del tipo de usuario seleccionado
    $sql_get_users = "SELECT id_tipo_usuario FROM usuario WHERE id_tipo_usuario = ?";
    if ($stmt_get_users = $conn->prepare($sql_get_users)) {
        $stmt_get_users->bind_param("i", $id_tipo_usuario);
        $stmt_get_users->execute();
        $result_users = $stmt_get_users->get_result();

        // Crear un array con los IDs de usuario
        $user_ids = [];
        while ($row = $result_users->fetch_assoc()) {
            $user_ids[] = $row['id_tipo_usuario'];
        }
        $stmt_get_users->close();
    } else {
        $error_messages[] = "Error al preparar la consulta para obtener usuarios.";
    }

    if (!empty($user_ids)) {
        // Iterar a través de los módulos seleccionados
        foreach ($_POST['id_modulo'] as $key => $value) {
            $id_modulo = $_POST['id_modulo'][$key];

            foreach ($user_ids as $id_tipo_usuario) {
                // Verificar si la asignación ya existe
                $sql_check = "SELECT COUNT(*) FROM asig_modulo WHERE id_modulo = ? AND id_tipo_usuario = ?";
                if ($stmt_check = $conn->prepare($sql_check)) {
                    $stmt_check->bind_param("ii", $id_modulo, $id_tipo_usuario);
                    $stmt_check->execute();
                    $stmt_check->bind_result($count);
                    $stmt_check->fetch();
                    $stmt_check->close();

                    if ($count == 0) {
                        // Consulta de inserción
                        $sql_insert = "INSERT INTO asig_modulo (id_modulo, id_tipo_usuario) VALUES (?, ?)";
                        if ($stmt_insert = $conn->prepare($sql_insert)) {
                            $stmt_insert->bind_param("ii", $id_modulo, $id_tipo_usuario);
                            
                            // Ejecutar la consulta y verificar si se realizó correctamente
                            if ($stmt_insert->execute()) {
                                $count_success++;
                            } else {
                                $error_messages[] = "Error al insertar la asignación del módulo ID $id_modulo para el usuario ID $id_tipo_usuario.";
                            }
                            $stmt_insert->close();
                        } else {
                            $error_messages[] = "Error al preparar la consulta de inserción para el módulo ID $id_modulo.";
                        }
                    }
                } else {
                    $error_messages[] = "Error al preparar la consulta de verificación para el módulo ID $id_modulo.";
                }
            }
        }
    } else {
        $error_messages[] = "No se encontraron usuarios para el tipo de usuario seleccionado.";
    }

    // Cerrar la conexión
    $conn->close();

    // Mostrar alertas y redirigir
    if (empty($error_messages)) {
        echo "<script>
                alert('Se han asignado $count_success módulos correctamente.');
                window.location.href = '../datos_personales/asignacion.php';
              </script>";
    } else {
        $error_message_string = implode("\n", $error_messages);
        echo "<script>
                alert('Se encontraron los siguientes errores:\n$error_message_string');
                window.location.href = '../datos_personales/asignacion.php';
              </script>";
    }
}
?>
