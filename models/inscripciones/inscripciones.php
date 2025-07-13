        <?php
session_start();
        require_once "../../scripts/conexion.php";
        require_once "../../scripts/config.php";

// Al inicio del archivo, después de session_start():
ini_set('display_errors', 1); // Mostrar errores en pantalla para depuración
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ob_start();

// --- BLOQUE AJAX: Cambiar estado de inscripción ---
if (
    $_SERVER["REQUEST_METHOD"] == "POST" &&
    isset($_POST['id_inscripcion']) &&
    isset($_POST['nuevo_estado'])
) {
    $id_inscripcion = intval($_POST['id_inscripcion']);
    $nuevo_estado = $_POST['nuevo_estado'];
    $motivo = isset($_POST['motivo']) ? trim($_POST['motivo']) : '';
    // Obtener datos de la inscripción y estudiante
    $sql = "SELECT i.*, e.id_usuario, u.mail, u.id_tipo_usuario, u.username, c.nombre_curso FROM inscripciones i INNER JOIN estudiante e ON i.id_estudiante = e.id_estudiante LEFT JOIN usuario u ON e.id_usuario = u.id_usuario INNER JOIN cursos c ON i.id_curso = c.id_curso WHERE i.id_inscripcion = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $sql_error = $conn->error;
        error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $sql);
        safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $sql]);
        exit;
    }
    $stmt->bind_param("i", $id_inscripcion);
    $stmt->execute();
    $result = $stmt->get_result();
    $insc = $result->fetch_assoc();
    if (!$insc) {
        safe_json_response(['success'=>false,'message'=>'No se encontró la inscripción.']);
    }
    $success = false;
    $conn->begin_transaction();
    try {
        // Registrar historial de cambio de estado
        $estado_anterior = $insc['estado'];
        $id_usuario_cambio = isset($_SESSION['id_usuario']) && is_numeric($_SESSION['id_usuario']) ? intval($_SESSION['id_usuario']) : 0;
        
        // Usar consulta directa para evitar problemas con prepare
        $sql_historial = "INSERT INTO historial_inscripciones (id_inscripcion, estado_anterior, estado_nuevo, fecha_cambio, id_usuario_cambio) VALUES ($id_inscripcion, '$estado_anterior', '$nuevo_estado', NOW(), $id_usuario_cambio)";
        $result_hist = $conn->query($sql_historial);
        if (!$result_hist) {
            $sql_error = $conn->error;
            error_log('SQL error (historial_inscripciones): ' . $sql_error . ' | SQL: ' . $sql_historial);
            safe_json_response(['success'=>false,'message'=>'Error SQL (historial_inscripciones): ' . $sql_error . ' | SQL: ' . $sql_historial]);
            die($sql_error . ' | SQL: ' . $sql_historial);
        }
        // Actualizar estado
        $sql_upd = "UPDATE inscripciones SET estado = ?, fecha_actualizacion = NOW() WHERE id_inscripcion = ?";
        $stmt_upd = $conn->prepare($sql_upd);
        if (!$stmt_upd) {
            $sql_error = $conn->error;
            error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $sql_upd);
            safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $sql_upd]);
            exit;
        }
        $stmt_upd->bind_param("si", $nuevo_estado, $id_inscripcion);
        $success = $stmt_upd->execute();
        // Si es rechazo/cancelación y hay motivo, enviar mensaje y notificación
        if (($nuevo_estado == 'rechazada' || $nuevo_estado == 'cancelada') && $motivo && $insc['id_usuario']) {
            $asunto = $nuevo_estado == 'rechazada' ? 'Tu inscripción ha sido rechazada' : 'Tu inscripción ha sido cancelada';
            $contenido = "Motivo: " . $motivo;
            $id_usuario = $_SESSION['id_usuario'] ?? null;
            $tipo_remitente = $_SESSION['id_tipo_usuario'] ?? 1;
            // Insertar mensaje
            $stmt_msg = $conn->prepare("INSERT INTO mensajes (id_remitente, tipo_remitente, tipo_destinatario, id_destinatario, asunto, contenido, fecha_envio) VALUES (?, ?, 'individual', ?, ?, ?, NOW())");
            if (!$stmt_msg) {
                $sql_error = $conn->error;
                error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $sql_msg);
                safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $sql_msg]);
                exit;
            }
            $stmt_msg->bind_param("iiiss", $id_usuario, $tipo_remitente, $insc['id_usuario'], $asunto, $contenido);
            $stmt_msg->execute();
        }
        // Si el usuario es estudiante, verificar si debe pasar a user
        $notificacion_pendiente = false;
        if ($insc['id_usuario'] && $insc['id_tipo_usuario'] == 3 && ($nuevo_estado == 'rechazada' || $nuevo_estado == 'cancelada')) {
            // Verificar si tiene otras inscripciones aprobadas
            $sql_otros = "SELECT COUNT(*) as total FROM inscripciones WHERE id_estudiante = ? AND estado = 'aprobada' AND id_inscripcion != ?";
            $stmt_otros = $conn->prepare($sql_otros);
            if (!$stmt_otros) {
                $sql_error = $conn->error;
                error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $sql_otros);
                safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $sql_otros]);
                exit;
            }
            $stmt_otros->bind_param("ii", $insc['id_estudiante'], $id_inscripcion);
            $stmt_otros->execute();
            $res_otros = $stmt_otros->get_result();
            $total_aprobadas = 0;
            if ($res_otros && $row_otros = $res_otros->fetch_assoc()) {
                $total_aprobadas = $row_otros['total'];
            }
            if ($total_aprobadas == 0) {
                // Cambiar tipo de usuario a 'user' (4)
                $sql_user = "UPDATE usuario SET id_tipo_usuario = 4 WHERE id_usuario = ?";
                $stmt_user = $conn->prepare($sql_user);
                if (!$stmt_user) {
                    $sql_error = $conn->error;
                    error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $sql_user);
                    safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $sql_user]);
                    exit;
                }
                $stmt_user->bind_param("i", $insc['id_usuario']);
                $stmt_user->execute();
                // Eliminar todos los módulos de estudiante
                $conn->query("DELETE FROM asig_modulo WHERE id_tipo_usuario = {$insc['id_estudiante']}");
                $notificacion_pendiente = true;
            }
        }
        // Insertar notificación si el usuario es o pasó a ser user
        if ($insc['id_usuario']) {
            $sql_tipo = "SELECT id_tipo_usuario FROM usuario WHERE id_usuario = ?";
            $stmt_tipo = $conn->prepare($sql_tipo);
            if (!$stmt_tipo) {
                $sql_error = $conn->error;
                error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $sql_tipo);
                safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $sql_tipo]);
                exit;
            }
            $stmt_tipo->bind_param("i", $insc['id_usuario']);
            $stmt_tipo->execute();
            $res_tipo = $stmt_tipo->get_result();
            if ($res_tipo && $row_tipo = $res_tipo->fetch_assoc()) {
                if ($row_tipo['id_tipo_usuario'] == 4 && $motivo) {
                    $asunto = $nuevo_estado == 'rechazada' ? 'Tu inscripción ha sido rechazada' : 'Tu inscripción ha sido cancelada';
                    $stmt_notif = $conn->prepare("INSERT INTO notificaciones_user (id_usuario, titulo, mensaje, fecha, leido) VALUES (?, ?, ?, NOW(), 0)");
                    if (!$stmt_notif) {
                        $sql_error = $conn->error;
                        error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $sql_notif);
                        safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $sql_notif]);
                        exit;
                    }
                    $stmt_notif->bind_param("iss", $insc['id_usuario'], $asunto, $motivo);
                    $stmt_notif->execute();
                }
            }
        }
        // Limpiar ciclo: eliminar todas las inscripciones y preinscripciones previas para ese usuario/email y curso
        if ($insc['id_usuario']) {
            $sql_del_all_insc = "DELETE FROM inscripciones WHERE id_estudiante IN (SELECT id_estudiante FROM estudiante WHERE id_usuario = ?) AND id_curso = ? AND id_inscripcion != ?";
            $stmt_del_all_insc = $conn->prepare($sql_del_all_insc);
            if (!$stmt_del_all_insc) {
                $sql_error = $conn->error;
                error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $sql_del_all_insc);
                safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $sql_del_all_insc]);
                exit;
            }
            $stmt_del_all_insc->bind_param("iii", $insc['id_usuario'], $insc['id_curso'], $id_inscripcion);
            $stmt_del_all_insc->execute();
            $sql_del_all_pre = "DELETE FROM preinscripciones WHERE (id_usuario = ? OR email = ?) AND id_curso = ?";
            $stmt_del_all_pre = $conn->prepare($sql_del_all_pre);
            if (!$stmt_del_all_pre) {
                $sql_error = $conn->error;
                error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $sql_del_all_pre);
                safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $sql_del_all_pre]);
                exit;
            }
            $stmt_del_all_pre->bind_param("isi", $insc['id_usuario'], $insc['mail'], $insc['id_curso']);
            $stmt_del_all_pre->execute();
        } else {
            $sql_est = "SELECT id_estudiante FROM estudiante WHERE id_usuario IS NULL AND id_estudiante IN (SELECT id_estudiante FROM inscripciones WHERE id_curso = ? AND estado IN ('rechazada', 'cancelada'))";
            $stmt_est = $conn->prepare($sql_est);
            if (!$stmt_est) {
                $sql_error = $conn->error;
                error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $sql_est);
                safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $sql_est]);
                exit;
            }
            $stmt_est->bind_param("i", $insc['id_curso']);
            $stmt_est->execute();
            $res_est = $stmt_est->get_result();
            while ($row_est = $res_est->fetch_assoc()) {
                $sql_del_all_insc = "DELETE FROM inscripciones WHERE id_estudiante = ? AND id_curso = ? AND estado IN ('rechazada', 'cancelada')";
                $stmt_del_all_insc = $conn->prepare($sql_del_all_insc);
                if (!$stmt_del_all_insc) {
                    $sql_error = $conn->error;
                    error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $sql_del_all_insc);
                    safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $sql_del_all_insc]);
                    exit;
                }
                $stmt_del_all_insc->bind_param("ii", $row_est['id_estudiante'], $insc['id_curso']);
                $stmt_del_all_insc->execute();
            }
        }
        $conn->commit();
        safe_json_response(['success'=>true,'message'=>'Estado actualizado correctamente.']);
    } catch (\Throwable $e) {
        $conn->rollback();
        $errorMsg = $e->getMessage();
        if (isset($conn) && $conn->error) {
            $errorMsg .= ' | MySQL: ' . $conn->error;
        }
        $errorMsg .= ' | SESSION id_usuario=' . var_export($_SESSION['id_usuario'] ?? null, true);
        error_log('Error inscripciones.php: ' . $errorMsg);
        // Forzar salida para depuración
        echo 'DEBUG ERROR: ' . $errorMsg . "\n";
        safe_json_response(['success'=>false,'message'=>'Error al actualizar el estado: ' . $errorMsg]);
    }
}
// --- FIN BLOQUE AJAX ---

// Limpiar cualquier salida previa y forzar JSON puro
function safe_json_response($data) {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// --- BLOQUE PHP: Procesar aprobación/rechazo de preinscripción rápida ---
        if (isset($_POST['aprobar_preinscripcion']) && isset($_POST['id_preinscripcion'])) {
    header('Content-Type: application/json');
            $id_preinscripcion = intval($_POST['id_preinscripcion']);
            // Obtener datos de la preinscripción
            $sql = "SELECT p.*, c.nombre_curso, u.id_usuario, u.id_tipo_usuario, u.username 
                    FROM preinscripciones p 
                    INNER JOIN cursos c ON p.id_curso = c.id_curso 
                    LEFT JOIN usuario u ON p.id_usuario = u.id_usuario
                    WHERE p.id_preinscripcion = ?";
            $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $sql_error = $conn->error;
        error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $sql);
        safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $sql]);
        exit;
    }
            $stmt->bind_param("i", $id_preinscripcion);
            $stmt->execute();
            $result = $stmt->get_result();
            $pre = $result->fetch_assoc();
            if ($pre) {
                $conn->begin_transaction();
                try {
                    // Si el usuario existe y es tipo "user", convertirlo a "estudiante"
                    if ($pre['id_usuario'] && $pre['id_tipo_usuario'] == 4) { // 4 = user
                        $update_user = "UPDATE usuario SET id_tipo_usuario = 3 WHERE id_usuario = ?";
                        $stmt_user = $conn->prepare($update_user);
                if (!$stmt_user) {
                    $sql_error = $conn->error;
                    error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $update_user);
                    safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $update_user]);
                    exit;
                }
                        $stmt_user->bind_param("i", $pre['id_usuario']);
                        $stmt_user->execute();
                // Buscar estudiante para el usuario (debe haber solo uno)
                $check_estudiante = "SELECT id_estudiante FROM estudiante WHERE id_usuario = ? ORDER BY id_estudiante ASC LIMIT 1";
                        $stmt_check = $conn->prepare($check_estudiante);
                if (!$stmt_check) {
                    $sql_error = $conn->error;
                    error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $check_estudiante);
                    safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $check_estudiante]);
                    exit;
                }
                        $stmt_check->bind_param("i", $pre['id_usuario']);
                        $stmt_check->execute();
                        $result_check = $stmt_check->get_result();
                if ($result_check->num_rows > 0) {
                    $estudiante = $result_check->fetch_assoc();
                    $id_estudiante = $estudiante['id_estudiante'];
                } else {
                            $insert_estudiante = "INSERT INTO estudiante (id_usuario, fecha_registro, estado) VALUES (?, CURDATE(), 'activo')";
                            $stmt_estudiante = $conn->prepare($insert_estudiante);
                    if (!$stmt_estudiante) {
                        $sql_error = $conn->error;
                        error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $insert_estudiante);
                        safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $insert_estudiante]);
                        exit;
                    }
                            $stmt_estudiante->bind_param("i", $pre['id_usuario']);
                            $stmt_estudiante->execute();
                            $id_estudiante = $conn->insert_id;
                }
            } else if ($pre['id_usuario']) {
                // Ya es estudiante
                $check_estudiante = "SELECT id_estudiante FROM estudiante WHERE id_usuario = ? ORDER BY id_estudiante ASC LIMIT 1";
                $stmt_check = $conn->prepare($check_estudiante);
                if (!$stmt_check) {
                    $sql_error = $conn->error;
                    error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $check_estudiante);
                    safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $check_estudiante]);
                    exit;
                }
                $stmt_check->bind_param("i", $pre['id_usuario']);
                $stmt_check->execute();
                $result_check = $stmt_check->get_result();
                if ($result_check->num_rows > 0) {
                            $estudiante = $result_check->fetch_assoc();
                            $id_estudiante = $estudiante['id_estudiante'];
                } else {
                    $insert_estudiante = "INSERT INTO estudiante (id_usuario, fecha_registro, estado) VALUES (?, CURDATE(), 'activo')";
                    $stmt_estudiante = $conn->prepare($insert_estudiante);
                    if (!$stmt_estudiante) {
                        $sql_error = $conn->error;
                        error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $insert_estudiante);
                        safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $insert_estudiante]);
                        exit;
                    }
                    $stmt_estudiante->bind_param("i", $pre['id_usuario']);
                    $stmt_estudiante->execute();
                    $id_estudiante = $conn->insert_id;
                        }
                    } else {
                        // Si no hay usuario asociado, crear uno nuevo
                        $id_usuario = null;
                        $id_estudiante = null;
                        $insert_usuario = "INSERT INTO usuario (nombre, apellido, mail, telefono, id_tipo_usuario, username, clave, fecha_registro) 
                                 VALUES (?, '', ?, ?, 3, ?, ?, NOW())";
                        $username = strtolower(str_replace(' ', '', $pre['nombre'])) . rand(100, 999);
                $password_hash = password_hash('123456', PASSWORD_DEFAULT);
                $nombre = $pre['nombre'];
                $email = $pre['email'];
                $telefono = $pre['telefono'];
                        $stmt_usuario = $conn->prepare($insert_usuario);
                if (!$stmt_usuario) {
                    $sql_error = $conn->error;
                    error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $insert_usuario);
                    safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $insert_usuario]);
                    exit;
                }
                $stmt_usuario->bind_param("sssss", $nombre, $email, $telefono, $username, $password_hash);
                        $stmt_usuario->execute();
                        $id_usuario = $conn->insert_id;
                        $insert_estudiante = "INSERT INTO estudiante (id_usuario, fecha_registro, estado) VALUES (?, CURDATE(), 'activo')";
                        $stmt_estudiante = $conn->prepare($insert_estudiante);
                if (!$stmt_estudiante) {
                    $sql_error = $conn->error;
                    error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $insert_estudiante);
                    safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $insert_estudiante]);
                    exit;
                }
                        $stmt_estudiante->bind_param("i", $id_usuario);
                        $stmt_estudiante->execute();
                        $id_estudiante = $conn->insert_id;
                    }
            // Verificar si ya existe inscripción APROBADA o PENDIENTE para este estudiante y curso
            $sql_check_insc = "SELECT id_inscripcion FROM inscripciones WHERE id_curso = ? AND id_estudiante = ? AND estado IN ('aprobada', 'pendiente')";
            $stmt_check_insc = $conn->prepare($sql_check_insc);
            if (!$stmt_check_insc) {
                $sql_error = $conn->error;
                error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $sql_check_insc);
                safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $sql_check_insc]);
                exit;
            }
            $stmt_check_insc->bind_param("ii", $pre['id_curso'], $id_estudiante);
            $stmt_check_insc->execute();
            $result_check_insc = $stmt_check_insc->get_result();
            if ($result_check_insc->num_rows > 0) {
                $conn->commit();
                echo json_encode(['success'=>false, 'message'=>'El estudiante ya está inscrito en este curso.']);
                exit;
            } else {
                // Eliminar todas las inscripciones previas para ese usuario/email y curso
                if ($pre['id_usuario']) {
                    $sql_del_all_insc = "DELETE FROM inscripciones WHERE id_estudiante IN (SELECT id_estudiante FROM estudiante WHERE id_usuario = ?) AND id_curso = ?";
                    $stmt_del_all_insc = $conn->prepare($sql_del_all_insc);
                    if (!$stmt_del_all_insc) {
                        $sql_error = $conn->error;
                        error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $sql_del_all_insc);
                        safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $sql_del_all_insc]);
                        exit;
                    }
                    $stmt_del_all_insc->bind_param("ii", $pre['id_usuario'], $pre['id_curso']);
                    $stmt_del_all_insc->execute();
                } else {
                    $sql_est = "SELECT id_estudiante FROM estudiante WHERE id_usuario IS NULL AND id_estudiante IN (SELECT id_estudiante FROM inscripciones WHERE id_curso = ?)";
                    $stmt_est = $conn->prepare($sql_est);
                    if (!$stmt_est) {
                        $sql_error = $conn->error;
                        error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $sql_est);
                        safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $sql_est]);
                        exit;
                    }
                    $stmt_est->bind_param("i", $pre['id_curso']);
                    $stmt_est->execute();
                    $res_est = $stmt_est->get_result();
                    while ($row_est = $res_est->fetch_assoc()) {
                        $sql_del_all_insc = "DELETE FROM inscripciones WHERE id_estudiante = ? AND id_curso = ?";
                        $stmt_del_all_insc = $conn->prepare($sql_del_all_insc);
                        if (!$stmt_del_all_insc) {
                            $sql_error = $conn->error;
                            error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $sql_del_all_insc);
                            safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $sql_del_all_insc]);
                            exit;
                        }
                        $stmt_del_all_insc->bind_param("ii", $row_est['id_estudiante'], $pre['id_curso']);
                        $stmt_del_all_insc->execute();
                    }
                }
                // Eliminar todas las preinscripciones previas para ese usuario/email y curso
                if ($pre['id_usuario']) {
                    $sql_del_all = "DELETE FROM preinscripciones WHERE (id_usuario = ? OR email = ?) AND id_curso = ?";
                    $stmt_del_all = $conn->prepare($sql_del_all);
                    if (!$stmt_del_all) {
                        $sql_error = $conn->error;
                        error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $sql_del_all);
                        safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $sql_del_all]);
                        exit;
                    }
                    $stmt_del_all->bind_param("isi", $pre['id_usuario'], $pre['email'], $pre['id_curso']);
                    $stmt_del_all->execute();
                } else {
                    $sql_del_all = "DELETE FROM preinscripciones WHERE email = ? AND id_curso = ?";
                    $stmt_del_all = $conn->prepare($sql_del_all);
                    if (!$stmt_del_all) {
                        $sql_error = $conn->error;
                        error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $sql_del_all);
                        safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $sql_del_all]);
                        exit;
                    }
                    $stmt_del_all->bind_param("si", $pre['email'], $pre['id_curso']);
                    $stmt_del_all->execute();
                }
                // Crear la inscripción formal como aprobada
                    $insert_inscripcion = "INSERT INTO inscripciones (id_curso, id_estudiante, fecha_inscripcion, estado) 
                                         VALUES (?, ?, CURDATE(), 'aprobada')";
                    $stmt_inscripcion = $conn->prepare($insert_inscripcion);
                if (!$stmt_inscripcion) {
                    $sql_error = $conn->error;
                    error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $insert_inscripcion);
                    safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $insert_inscripcion]);
                    exit;
                }
                    $stmt_inscripcion->bind_param("ii", $pre['id_curso'], $id_estudiante);
                    $stmt_inscripcion->execute();
                if ($stmt_inscripcion->affected_rows === 0) {
                    $conn->rollback();
                    echo json_encode(['success'=>false, 'message'=>'Error: No se pudo crear la inscripción formal. Por favor, revisa la base de datos.']);
                    exit;
                } else {
                    // Eliminar la preinscripción
                    $delete_preinscripcion = "DELETE FROM preinscripciones WHERE id_preinscripcion = ?";
                    $stmt_delete = $conn->prepare($delete_preinscripcion);
                    if (!$stmt_delete) {
                        $sql_error = $conn->error;
                        error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $delete_preinscripcion);
                        safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $delete_preinscripcion]);
                        exit;
                    }
                    $stmt_delete->bind_param("i", $id_preinscripcion);
                    $stmt_delete->execute();
                    $conn->commit();
                    echo json_encode(['success'=>true, 'message'=>'Preinscripción aprobada exitosamente. El usuario ha sido convertido a estudiante y se ha creado la inscripción formal.']);
                    exit;
                }
            }
                } catch (Exception $e) {
                    $conn->rollback();
            echo json_encode(['success'=>false, 'message'=>'Error al procesar la preinscripción: ' . $e->getMessage()]);
            exit;
                }
            } else {
        echo json_encode(['success'=>false, 'message'=>'No se encontró la preinscripción.']);
        exit;
    }
}
if (isset($_POST['rechazar_preinscripcion']) && isset($_POST['id_preinscripcion'])) {
    header('Content-Type: application/json');
    $id_preinscripcion = intval($_POST['id_preinscripcion']);
    $motivo = isset($_POST['motivo']) ? trim($_POST['motivo']) : '';
    // Obtener datos de la preinscripción para saber a quién notificar
    $sql = "SELECT p.*, u.id_usuario, u.mail, u.id_tipo_usuario FROM preinscripciones p LEFT JOIN usuario u ON p.id_usuario = u.id_usuario WHERE p.id_preinscripcion = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $sql_error = $conn->error;
        error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $sql);
        safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $sql]);
        exit;
    }
    $stmt->bind_param("i", $id_preinscripcion);
                $stmt->execute();
                $result = $stmt->get_result();
    $pre = $result->fetch_assoc();
    $success = false;
    if ($pre) {
        $sql2 = "UPDATE preinscripciones SET estado = 'rechazada' WHERE id_preinscripcion = ?";
        $stmt2 = $conn->prepare($sql2);
        if (!$stmt2) {
            $sql_error = $conn->error;
            error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $sql2);
            safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $sql2]);
            exit;
        }
        $stmt2->bind_param("i", $id_preinscripcion);
        $success = $stmt2->execute();
        // Enviar mensaje si hay motivo y usuario
        if ($success && $motivo && $pre['id_usuario']) {
            $asunto = 'Tu preinscripción ha sido rechazada';
            $contenido = "Motivo: " . $motivo;
            $id_usuario = $_SESSION['id_usuario'] ?? null;
            $tipo_remitente = $_SESSION['id_tipo_usuario'] ?? 1;
            $stmt_msg = $conn->prepare("INSERT INTO mensajes (id_remitente, tipo_remitente, tipo_destinatario, id_destinatario, asunto, contenido, fecha_envio) VALUES (?, ?, 'individual', ?, ?, ?, NOW())");
            if (!$stmt_msg) {
                $sql_error = $conn->error;
                error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $sql_msg);
                safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $sql_msg]);
                exit;
            }
            $stmt_msg->bind_param("iiiss", $id_usuario, $tipo_remitente, $pre['id_usuario'], $asunto, $contenido);
            $stmt_msg->execute();
        }
        // --- Lógica para tipo usuario si era estudiante solo de ese curso ---
        $notificacion_pendiente = false;
        if ($success && $pre['id_usuario'] && $pre['id_tipo_usuario'] == 3) { // era estudiante
            // Buscar id_estudiante
            $sql_est = "SELECT id_estudiante FROM estudiante WHERE id_usuario = ?";
            $stmt_est = $conn->prepare($sql_est);
            if (!$stmt_est) {
                $sql_error = $conn->error;
                error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $sql_est);
                safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $sql_est]);
                exit;
            }
            $stmt_est->bind_param("i", $pre['id_usuario']);
            $stmt_est->execute();
            $res_est = $stmt_est->get_result();
            if ($res_est && $row_est = $res_est->fetch_assoc()) {
                $id_estudiante = $row_est['id_estudiante'];
                // Verificar si tiene otras inscripciones aprobadas
                $sql_otros = "SELECT COUNT(*) as total FROM inscripciones WHERE id_estudiante = ? AND estado = 'aprobada'";
                $stmt_otros = $conn->prepare($sql_otros);
                if (!$stmt_otros) {
                    $sql_error = $conn->error;
                    error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $sql_otros);
                    safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $sql_otros]);
                    exit;
                }
                $stmt_otros->bind_param("i", $id_estudiante);
                $stmt_otros->execute();
                $res_otros = $stmt_otros->get_result();
                $total_aprobadas = 0;
                if ($res_otros && $row_otros = $res_otros->fetch_assoc()) {
                    $total_aprobadas = $row_otros['total'];
                }
                if ($total_aprobadas == 0) {
                    // Cambiar tipo de usuario a 'user' (4)
                    $sql_user = "UPDATE usuario SET id_tipo_usuario = 4 WHERE id_usuario = ?";
                    $stmt_user = $conn->prepare($sql_user);
                    if (!$stmt_user) {
                        $sql_error = $conn->error;
                        error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $sql_user);
                        safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $sql_user]);
                        exit;
                    }
                    $stmt_user->bind_param("i", $pre['id_usuario']);
                    $stmt_user->execute();
                    // Eliminar todos los módulos de estudiante
                    $conn->query("DELETE FROM asig_modulo WHERE id_tipo_usuario = $id_estudiante");
                    $notificacion_pendiente = true;
                }
                // Eliminar inscripción rechazada/cancelada si existe
                $sql_del = "DELETE FROM inscripciones WHERE id_estudiante = ? AND id_curso = ? AND (estado = 'rechazada' OR estado = 'cancelada')";
                $stmt_del = $conn->prepare($sql_del);
                if (!$stmt_del) {
                    $sql_error = $conn->error;
                    error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $sql_del);
                    safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $sql_del]);
                    exit;
                }
                $stmt_del->bind_param("ii", $id_estudiante, $pre['id_curso']);
                $stmt_del->execute();
            }
        }
        // Eliminar la preinscripción rechazada para permitir nueva preinscripción
        $sql_del_pre = "DELETE FROM preinscripciones WHERE id_preinscripcion = ?";
        $stmt_del_pre = $conn->prepare($sql_del_pre);
        if (!$stmt_del_pre) {
            $sql_error = $conn->error;
            error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $sql_del_pre);
            safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $sql_del_pre]);
            exit;
        }
        $stmt_del_pre->bind_param("i", $id_preinscripcion);
        $stmt_del_pre->execute();
        // Eliminar TODAS las preinscripciones anteriores para ese usuario/email y curso
        if ($pre['id_usuario']) {
            $sql_del_all = "DELETE FROM preinscripciones WHERE (id_usuario = ? OR email = ?) AND id_curso = ?";
            $stmt_del_all = $conn->prepare($sql_del_all);
            if (!$stmt_del_all) {
                $sql_error = $conn->error;
                error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $sql_del_all);
                safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $sql_del_all]);
                exit;
            }
            $stmt_del_all->bind_param("isi", $pre['id_usuario'], $pre['email'], $pre['id_curso']);
            $stmt_del_all->execute();
        } else {
            $sql_del_all = "DELETE FROM preinscripciones WHERE email = ? AND id_curso = ?";
            $stmt_del_all = $conn->prepare($sql_del_all);
            if (!$stmt_del_all) {
                $sql_error = $conn->error;
                error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $sql_del_all);
                safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $sql_del_all]);
                exit;
            }
            $stmt_del_all->bind_param("si", $pre['email'], $pre['id_curso']);
            $stmt_del_all->execute();
        }
        // Eliminar TODAS las inscripciones para ese usuario/email y curso
        if ($pre['id_usuario']) {
            $sql_del_all_insc = "DELETE FROM inscripciones WHERE id_estudiante IN (SELECT id_estudiante FROM estudiante WHERE id_usuario = ?) AND id_curso = ?";
            $stmt_del_all_insc = $conn->prepare($sql_del_all_insc);
            if (!$stmt_del_all_insc) {
                $sql_error = $conn->error;
                error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $sql_del_all_insc);
                safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $sql_del_all_insc]);
                exit;
            }
            $stmt_del_all_insc->bind_param("ii", $pre['id_usuario'], $pre['id_curso']);
            $stmt_del_all_insc->execute();
        } else {
            // Si no hay usuario, buscar por email en estudiante y eliminar inscripciones
            $sql_est = "SELECT id_estudiante FROM estudiante WHERE id_usuario IS NULL AND id_estudiante IN (SELECT id_estudiante FROM inscripciones WHERE id_curso = ?)";
            $stmt_est = $conn->prepare($sql_est);
            if (!$stmt_est) {
                $sql_error = $conn->error;
                error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $sql_est);
                safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $sql_est]);
                exit;
            }
            $stmt_est->bind_param("i", $pre['id_curso']);
            $stmt_est->execute();
            $res_est = $stmt_est->get_result();
            while ($row_est = $res_est->fetch_assoc()) {
                $sql_del_all_insc = "DELETE FROM inscripciones WHERE id_estudiante = ? AND id_curso = ? AND estado IN ('rechazada', 'cancelada')";
                $stmt_del_all_insc = $conn->prepare($sql_del_all_insc);
                if (!$stmt_del_all_insc) {
                    $sql_error = $conn->error;
                    error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $sql_del_all_insc);
                    safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $sql_del_all_insc]);
                    exit;
                }
                $stmt_del_all_insc->bind_param("ii", $row_est['id_estudiante'], $pre['id_curso']);
                $stmt_del_all_insc->execute();
            }
        }
        // Insertar notificación si el usuario es o pasó a ser user
        if ($pre['id_usuario']) {
            $sql_tipo = "SELECT id_tipo_usuario FROM usuario WHERE id_usuario = ?";
            $stmt_tipo = $conn->prepare($sql_tipo);
            if (!$stmt_tipo) {
                $sql_error = $conn->error;
                error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $sql_tipo);
                safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $sql_tipo]);
                exit;
            }
            $stmt_tipo->bind_param("i", $pre['id_usuario']);
            $stmt_tipo->execute();
            $res_tipo = $stmt_tipo->get_result();
            if ($res_tipo && $row_tipo = $res_tipo->fetch_assoc()) {
                if ($row_tipo['id_tipo_usuario'] == 4) {
                    $stmt_notif = $conn->prepare("INSERT INTO notificaciones_user (id_usuario, titulo, mensaje, fecha, leido) VALUES (?, ?, ?, NOW(), 0)");
                    if (!$stmt_notif) {
                        $sql_error = $conn->error;
                        error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $sql_notif);
                        safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $sql_notif]);
                        exit;
                    }
                    $stmt_notif->bind_param("iss", $pre['id_usuario'], $asunto, $motivo);
                    $stmt_notif->execute();
                }
            }
        }
        // Eliminar todas las inscripciones previas para ese usuario/email y curso
        if ($pre['id_usuario']) {
            $sql_del_all_insc = "DELETE FROM inscripciones WHERE id_estudiante IN (SELECT id_estudiante FROM estudiante WHERE id_usuario = ?) AND id_curso = ?";
            $stmt_del_all_insc = $conn->prepare($sql_del_all_insc);
            if (!$stmt_del_all_insc) {
                $sql_error = $conn->error;
                error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $sql_del_all_insc);
                safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $sql_del_all_insc]);
                exit;
            }
            $stmt_del_all_insc->bind_param("ii", $pre['id_usuario'], $pre['id_curso']);
            $stmt_del_all_insc->execute();
        } else {
            $sql_est = "SELECT id_estudiante FROM estudiante WHERE id_usuario IS NULL AND id_estudiante IN (SELECT id_estudiante FROM inscripciones WHERE id_curso = ?)";
            $stmt_est = $conn->prepare($sql_est);
            if (!$stmt_est) {
                $sql_error = $conn->error;
                error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $sql_est);
                safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $sql_est]);
                exit;
            }
            $stmt_est->bind_param("i", $pre['id_curso']);
            $stmt_est->execute();
            $res_est = $stmt_est->get_result();
            while ($row_est = $res_est->fetch_assoc()) {
                $sql_del_all_insc = "DELETE FROM inscripciones WHERE id_estudiante = ? AND id_curso = ? AND estado IN ('rechazada', 'cancelada')";
                $stmt_del_all_insc = $conn->prepare($sql_del_all_insc);
                if (!$stmt_del_all_insc) {
                    $sql_error = $conn->error;
                    error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $sql_del_all_insc);
                    safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $sql_del_all_insc]);
                    exit;
                }
                $stmt_del_all_insc->bind_param("ii", $row_est['id_estudiante'], $pre['id_curso']);
                $stmt_del_all_insc->execute();
            }
        }
        // Eliminar todas las preinscripciones previas para ese usuario/email y curso
        if ($pre['id_usuario']) {
            $sql_del_all = "DELETE FROM preinscripciones WHERE (id_usuario = ? OR email = ?) AND id_curso = ?";
            $stmt_del_all = $conn->prepare($sql_del_all);
            if (!$stmt_del_all) {
                $sql_error = $conn->error;
                error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $sql_del_all);
                safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $sql_del_all]);
                exit;
            }
            $stmt_del_all->bind_param("isi", $pre['id_usuario'], $pre['email'], $pre['id_curso']);
            $stmt_del_all->execute();
        } else {
            $sql_del_all = "DELETE FROM preinscripciones WHERE email = ? AND id_curso = ?";
            $stmt_del_all = $conn->prepare($sql_del_all);
            if (!$stmt_del_all) {
                $sql_error = $conn->error;
                error_log('SQL prepare error: ' . $sql_error . ' | SQL: ' . $sql_del_all);
                safe_json_response(['success'=>false,'message'=>'Error SQL prepare: ' . $sql_error . ' | SQL: ' . $sql_del_all]);
                exit;
            }
            $stmt_del_all->bind_param("si", $pre['email'], $pre['id_curso']);
            $stmt_del_all->execute();
        }
        // --- Fin lógica tipo usuario ---
    }
    if ($success) {
        echo json_encode(['success'=>true, 'message'=>'Preinscripción rechazada correctamente.']);
    } else {
        echo json_encode(['success'=>false, 'message'=>'Error al rechazar la preinscripción.']);
    }
    exit;
}
// --- FIN BLOQUE PHP ---
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Inscripciones</title>
    <link rel="stylesheet" href="css/ins.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
<div class="container-flex">
    <?php include "../../scripts/sidebar.php"; ?>
        <div class="main-content">
            <header class="header">
                <div class="header-left">
                    <h1>Administración de Inscripciones</h1>
                </div>
                <div class="header-right">
                    <button id="nuevaInscripcionBtn" class="btn-primary">Nueva Inscripción</button>
                </div>
            </header>

            <section class="content">
                <div id="mensajeExito" class="mensaje exito" style="display: none;">Nueva inscripción creada con éxito.</div>
            <div id="notificacion-fija" style="display:none;margin-bottom:15px;padding:12px 18px;border-radius:6px;font-size:16px;font-weight:500;"></div>

                <div id="nuevaInscripcionModal" class="modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="modal-title">Nueva Inscripción</h2>
                            <span class="close">&times;</span>
                        </div>
                        <form id="nuevaInscripcionForm" class="modal-form" method="post" enctype="multipart/form-data">
                            <select name="id_estudiante" required>
                                <option value="">Seleccione un estudiante</option>
                                <?php foreach ($estudiantes as $estudiante): ?>
                                    <option value="<?= htmlspecialchars($estudiante['id_estudiante']) ?>">
                                        <?= htmlspecialchars($estudiante['nombre'] . ' ' . $estudiante['apellido']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <select name="id_curso" required>
                                <option value="">Seleccione un curso</option>
                                <?php foreach ($cursos as $curso): ?>
                                    <option value="<?= htmlspecialchars($curso['id_curso']) ?>">
                                        <?= htmlspecialchars($curso['nombre_curso']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="file-input">
                                <input type="file" name="comprobante_pago" id="comprobante_pago" accept="image/*" required>
                                <label for="comprobante_pago">
                                    <span class="file-name" id="file-name-display">Inserte Comprobante De Pago</span>
                                    <span class="file-name-before"></span>
                                </label>
                            </div>
                            <input type="hidden" name="nueva_inscripcion" value="1">
                            <button type="submit" class="btn-primary">Crear Inscripción</button>
                        </form>
                    </div>
                </div>

                <!-- Filters -->
                <div class="filter-bar">
                    <form id="filterForm" action="" method="GET" class="filter-form">
                        <select name="estado" class="filter-select">
                            <option value="">Todos los estados</option>
                            <option value="pendiente" <?= isset($_GET['estado']) && $_GET['estado'] == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                            <option value="aprobada" <?= isset($_GET['estado']) && $_GET['estado'] == 'aprobada' ? 'selected' : '' ?>>Aprobada</option>
                            <option value="rechazada" <?= isset($_GET['estado']) && $_GET['estado'] == 'rechazada' ? 'selected' : '' ?>>Rechazada</option>
                            <option value="cancelada" <?= isset($_GET['estado']) && $_GET['estado'] == 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
                        </select>
                        <select name="curso" class="filter-select">
                            <option value="">Todos los cursos</option>
                            <?php foreach ($cursos as $curso): ?>
                                <option value="<?= htmlspecialchars($curso['id_curso']) ?>" <?= isset($_GET['curso']) && $_GET['curso'] == $curso['id_curso'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($curso['nombre_curso']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>

                <div id="detallesModal" class="modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="modal-title">Detalles de la inscripción</h2>
                            <span class="close">&times;</span>
                        </div>
                        <div class="modal-body">
                            <!-- Los detalles de la inscripción se cargarán aquí dinámicamente -->
                        </div>
                    </div>
                </div>

                <div class="inscriptions-list">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Estudiante</th>
                                <th>Username</th>
                                <th>Tipo Usuario</th>
                                <th>Curso</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="inscripcionesTableBody">
                            <!-- Las inscripciones se cargarán aquí dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </section>
            <!-- SECCIÓN DE PREINSCRIPCIONES RÁPIDAS -->
            <section class="content" style="margin-top:40px;">
                <h2 style="color:#333;margin-bottom:25px;font-size:1.8em;border-bottom:3px solid #3498db;padding-bottom:10px;">
                    <i class="fas fa-clock" style="color:#3498db;margin-right:10px;"></i>
                    Preinscripciones Rápidas Pendientes
                </h2>
                <div class="inscriptions-list">
                    <div class="preinscripciones-container">
                        <table class="preinscripciones-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Curso</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Obtener preinscripciones pendientes
                                $sql_preinscripciones = "SELECT p.*, c.nombre_curso, u.username, u.id_tipo_usuario 
                                                        FROM preinscripciones p 
                                                        INNER JOIN cursos c ON p.id_curso = c.id_curso 
                                                        LEFT JOIN usuario u ON p.id_usuario = u.id_usuario
                                                        WHERE p.estado = 'pendiente' 
                                                        ORDER BY p.fecha_preinscripcion DESC";
                                $result_preinscripciones = $conn->query($sql_preinscripciones);
                                
                                if ($result_preinscripciones && $result_preinscripciones->num_rows > 0):
                                    while ($pre = $result_preinscripciones->fetch_assoc()):
                                ?>
                                <tr class="preinscripcion-row" id="preinsc-row-<?php echo $pre['id_preinscripcion']; ?>">
                                        <td><?php echo $pre['id_preinscripcion']; ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($pre['nombre']); ?></strong>
                                            <?php if ($pre['username']): ?>
                                                <br><small style="color:#666;">Usuario: <?php echo htmlspecialchars($pre['username']); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($pre['email']); ?></td>
                                        <td><?php echo htmlspecialchars($pre['telefono']); ?></td>
                                        <td>
                                            <span class="curso-badge"><?php echo htmlspecialchars($pre['nombre_curso']); ?></span>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($pre['fecha_preinscripcion'])); ?></td>
                                        <td>
                                            <span class="status-badge pendiente">Pendiente</span>
                                        </td>
                                        <td>
                                        <div class="acciones-pre-btns">
                                            <button class="btn-aprobar btn-mini aprobar-pre-btn" data-id="<?php echo $pre['id_preinscripcion']; ?>" title="Aprobar">
                                                    <i class="fas fa-check"></i> Aprobar
                                                </button>
                                            <button class="btn-rechazar btn-mini rechazar-pre-btn" data-id="<?php echo $pre['id_preinscripcion']; ?>" title="Rechazar">
                                                <i class="fas fa-times"></i> Rechazar
                                            </button>
                                        </div>
                                        </td>
                                    </tr>
                                <?php 
                                    endwhile;
                                else:
                                ?>
                                    <tr>
                                        <td colspan="8" style="text-align:center;padding:30px;color:#666;">
                                            <i class="fas fa-inbox" style="font-size:2em;color:#ddd;margin-bottom:10px;"></i>
                                            <br>No hay preinscripciones rápidas pendientes
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script>
        // Función global para mostrar notificación flotante
        function mostrarNotificacionFlotante(mensaje, tipo) {
            var notif = document.getElementById('notificacion-flotante');
            // Asegura que mensaje sea string
            if (typeof mensaje !== 'string') {
                try { mensaje = JSON.stringify(mensaje); } catch(e) { mensaje = String(mensaje); }
            }
            notif.textContent = mensaje;
            notif.style.background = tipo === 'success' ? '#d4edda' : (tipo === 'warning' ? '#fff3cd' : '#f8d7da');
            notif.style.color = tipo === 'success' ? '#155724' : (tipo === 'warning' ? '#856404' : '#721c24');
            notif.style.display = 'block';
            setTimeout(function(){ notif.style.display = 'none'; }, 4000);
        }

        function mostrarNotificacionFija(mensaje, tipo) {
            var notif = document.getElementById('notificacion-fija');
            notif.textContent = mensaje;
            notif.style.display = 'block';
            notif.style.background = tipo === 'success' ? '#d4edda' : (tipo === 'warning' ? '#fff3cd' : '#f8d7da');
            notif.style.color = tipo === 'success' ? '#155724' : (tipo === 'warning' ? '#856404' : '#721c24');
            setTimeout(function(){ notif.style.display = 'none'; }, 3000);
        }

        // Función para cargar inscripciones usando Ajax
        function cargarInscripciones(filtros = null) {
            // Si no se pasan filtros, mostrar todos
            if (!filtros) {
                filtros = { estado: '', curso: '' };
                // También actualiza los selects de filtro visualmente
                if (document.querySelector('select[name="estado"]')) document.querySelector('select[name="estado"]').value = '';
                if (document.querySelector('select[name="curso"]')) document.querySelector('select[name="curso"]').value = '';
            }
            
            console.log('Filtros aplicados:', filtros);
            
            $.ajax({
                url: "ajax/get_inscripciones.php",
                method: "GET",
                data: filtros,
                dataType: 'json',
                success: function(inscripciones) {
                    console.log('Inscripciones recibidas:', inscripciones);
                    var tbody = $("#inscripcionesTableBody");
                    tbody.empty();
                    inscripciones.forEach(function(inscripcion) {
                        let selectHtml = `
    <form action="" method="POST" class="status-form">
        <input type="hidden" name="id_inscripcion" value="${inscripcion.id_inscripcion}">
        <select name="nuevo_estado" onchange="cambiarEstado(this)" class="status-select" data-estado-anterior="${inscripcion.estado}">
            <option value="pendiente" ${inscripcion.estado === 'pendiente' ? 'selected' : ''}>Pendiente</option>
            <option value="aprobada" ${inscripcion.estado === 'aprobada' ? 'selected' : ''}>Aprobada</option>
            <option value="rechazada" ${inscripcion.estado === 'rechazada' ? 'selected' : ''}>Rechazada</option>
            <option value="cancelada" ${inscripcion.estado === 'cancelada' ? 'selected' : ''}>Cancelada</option>
        </select>
    </form>
`;
                        var row = `
                    <tr>
                        <td>${inscripcion.id_inscripcion}</td>
                        <td>${inscripcion.nombre} ${inscripcion.apellido}</td>
                        <td>${inscripcion.username}</td>
                        <td>${inscripcion.tipo_usuario}</td>
                        <td>${inscripcion.nombre_curso}</td>
                        <td>${inscripcion.fecha_inscripcion}</td>
                        <td>
                            <span class="status-badge ${inscripcion.estado}">
                                ${inscripcion.estado.charAt(0).toUpperCase() + inscripcion.estado.slice(1)}
                            </span>
                        </td>
                        <td>
                            ${selectHtml}
                            <button class="btn-view" onclick="verDetalles(${inscripcion.id_inscripcion})">Ver Detalles</button>
                        </td>
                    </tr>
                `;
                        tbody.append(row);
                    });
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("Error cargando inscripciones:", textStatus, errorThrown);
                    console.error("Response:", jqXHR.responseText);
                }
            });
        }

        // Cargar inscripciones al cargar la página
        $(document).ready(function() {
            console.log('Cargando inscripciones...');
            cargarInscripciones();

            // Manejar cambios en los filtros
            $("#filterForm select").on("change", function() {
                var filtros = {
                    estado: $("select[name='estado']").val(),
                    curso: $("select[name='curso']").val()
                };
                cargarInscripciones(filtros);
            });
        });
        // Modal para nueva inscripción
        var modal = document.getElementById("nuevaInscripcionModal");
        var btn = document.getElementById("nuevaInscripcionBtn");
        var span = modal.querySelector(".close");

        // Abrir el modal
        btn.onclick = function() {
            modal.style.display = "block";
        }

        // Cerrar el modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // Cerrar modal si se hace clic fuera del modal
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        // Enviar nueva inscripción usando Ajax
        $("#nuevaInscripcionForm").on("submit", function(event) {
    event.preventDefault();

    // Crear un objeto FormData para manejar tanto datos como archivos
    var formData = new FormData(this);

    $.ajax({
        url: "ajax/insertar_inscripcion.php", // Ruta al archivo PHP que procesa el formulario
        method: "POST",
        data: formData, // Usar FormData para manejar archivos
        contentType: false, // Evita que jQuery establezca el tipo de contenido incorrecto
        processData: false, // Evita que jQuery procese los datos, ya que estamos usando FormData
        dataType: 'json', // Esperar respuesta en formato JSON
        success: function(response) {
            if (response.success) {
                mostrarNotificacionFlotante(response.message, 'success');
                modal.style.display = "none";
                cargarInscripciones();
                $("#nuevaInscripcionForm")[0].reset();
            } else {
                // Manejar diferentes tipos de errores
                switch (response.message) {
                    case "Datos de entrada inválidos. Por favor, verifica los campos del formulario.":
                        mostrarNotificacionFlotante("Por favor, verifica que todos los campos del formulario estén completos.", 'warning');
                        break;
                    case "El estudiante ya está inscrito en este curso.":
                        mostrarNotificacionFlotante("Ya estás inscrito en este curso.", 'warning');
                        break;
                    case "Ya existe una inscripción para este estudiante en este curso.":
                        mostrarNotificacionFlotante("Ya existe una inscripción para este curso.", 'warning');
                        break;
                    case "El curso o el estudiante especificado no existe.":
                        mostrarNotificacionFlotante("Hubo un problema con los datos del curso o del estudiante. Por favor, inténtalo de nuevo.", 'warning');
                        break;
                    default:
                        mostrarNotificacionFlotante("Ocurrió un error: " + response.message, 'error');
                }
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            // Mostrar el error exacto devuelto por el servidor o el error de la llamada AJAX
            var errorMessage = jqXHR.responseJSON ? jqXHR.responseJSON.message : "Hubo un problema en el servidor.";
            mostrarNotificacionFlotante("Error específico: " + errorMessage + " (AJAX error: " + textStatus + " - " + errorThrown + ")", 'error');
            console.error("Error AJAX:", textStatus, errorThrown, "Response:", jqXHR.responseText);
        }
    });
});


        // Función para ver detalles de una inscripción
        function verDetalles(idInscripcion) {
            $.ajax({
                url: "ajax/detalles_inscripcion.php",
                method: "GET",
                data: {
                    id_inscripcion: idInscripcion
                },
                dataType: 'json',
                success: function(data) {
                    var detallesModal = document.getElementById("detallesModal");
                    detallesModal.innerHTML = `
                        <div class="modal-content">
                            <div class="modal-header">
                                <h2 class="modal-title">Detalles de la inscripción</h2>
                                <span class="close">&times;</span>
                            </div>
                            <div class="modal-body">
                                <a href="${data.comprobante_pago}" target="_blank">
                                    <img src="${data.comprobante_pago}" alt="Comprobante de Pago" style="max-width: 100%; height: auto;">
                                </a>
                                <p><strong>ID:</strong> ${data.id_inscripcion}</p>
                                <p><strong>Estudiante:</strong> ${data.nombre} ${data.apellido}</p>
                                <p><strong>Curso:</strong> ${data.nombre_curso}</p>
                                <p><strong>Fecha de inscripción:</strong> ${data.fecha_inscripcion}</p>
                                <p><strong>Estado:</strong> ${data.estado}</p>
                                <p><strong>Historial de cambios:</strong></p>
                                <ul>
                                    ${data.historial_cambios.map(cambio => `<li>${cambio.estado_anterior} -> ${cambio.estado_nuevo} (${cambio.fecha_cambio})</li>`).join('')}
                                </ul>
                            </div>
                        </div>
                    `;
                    detallesModal.style.display = "block";

                    // Cierre del modal de detalles
                    var detallesCloseBtn = detallesModal.querySelector(".close");

                    detallesCloseBtn.onclick = function() {
                        detallesModal.style.display = "none";
                    }

                    window.onclick = function(event) {
                        if (event.target == detallesModal) {
                            detallesModal.style.display = "none";
                        }
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("Error cargando detalles:", textStatus, errorThrown);
                }
            });
        }

        document.getElementById('comprobante_pago').addEventListener('change', function() {
            var fileName = this.files[0] ? this.files[0].name : 'Inserte Comprobante De Pago';
            document.getElementById('file-name-display').textContent = fileName;
        });

        // Manejo AJAX para aprobar preinscripción rápida
        $(document).on('submit', '.aprobar-pre-form', function(e) {
            e.preventDefault();
            var form = $(this);
            var id = form.data('id');
            var row = $('#preinsc-row-' + id);
            var btn = form.find('.aprobar-pre-btn');
            if (!confirm('¿Estás seguro de que quieres aprobar esta preinscripción?')) {
                return;
            }
            btn.prop('disabled', true);
            $.ajax({
                url: '',
                method: 'POST',
                data: form.serialize() + '&aprobar_preinscripcion=1',
                dataType: 'json',
                success: function(resp) {
                    if (resp.success) {
                        mostrarNotificacionFija(resp.message, 'success');
                        row.fadeOut(400, function(){ $(this).remove(); });
                        setTimeout(function(){ cargarPreinscripcionesRapidas(); }, 500);
                        // Recargar la tabla de inscripciones también
                        var filtros = {
                            estado: $("select[name='estado']").val(),
                            curso: $("select[name='curso']").val()
                        };
                        cargarInscripciones(filtros);
                    } else {
                        mostrarNotificacionFija(resp.message, 'error');
                        btn.prop('disabled', false);
                    }
                },
                error: function(xhr) {
                    mostrarNotificacionFija('Error inesperado al aprobar la preinscripción.', 'error');
                    btn.prop('disabled', false);
                }
            });
        });

        // Manejo AJAX para rechazar preinscripción rápida
        $(document).on('click', '.rechazar-pre-btn', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var row = $('#preinsc-row-' + id);
            var btn = $(this);
            if (!confirm('¿Estás seguro de que quieres rechazar esta preinscripción?')) {
                return;
            }
            btn.prop('disabled', true);
            $.ajax({
                url: '',
                method: 'POST',
                data: { id_preinscripcion: id, rechazar_preinscripcion: 1 },
                dataType: 'json',
                success: function(resp) {
                    if (resp.success) {
                        mostrarNotificacionFija(resp.message, 'success');
                        row.fadeOut(400, function(){ $(this).remove(); });
                        setTimeout(function(){ cargarPreinscripcionesRapidas(); }, 500);
                    } else {
                        mostrarNotificacionFija(resp.message, 'error');
                        btn.prop('disabled', false);
                    }
                },
                error: function(xhr) {
                    mostrarNotificacionFija('Error inesperado al rechazar la preinscripción.', 'error');
                    btn.prop('disabled', false);
                }
            });
        });

        // Aprobar preinscripción directa
        $(document).on('click', '.aprobar-pre-btn', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            if (!confirm('¿Estás seguro de que quieres aprobar esta preinscripción?')) return;
            var row = $('#preinsc-row-' + id);
            var btn = $(this);
            btn.prop('disabled', true);
            $.ajax({
                url: '',
                method: 'POST',
                data: { id_preinscripcion: id, aprobar_preinscripcion: 1 },
                dataType: 'json',
                success: function(resp) {
                    if (resp.success) {
                        mostrarNotificacionFija(resp.message, 'success');
                        row.fadeOut(400, function(){ $(this).remove(); });
                        setTimeout(function(){ cargarPreinscripcionesRapidas(); }, 500);
                        var filtros = {
                            estado: $("select[name='estado']").val(),
                            curso: $("select[name='curso']").val()
                        };
                        cargarInscripciones(filtros);
                    } else {
                        mostrarNotificacionFija(resp.message, 'error');
                        btn.prop('disabled', false);
                    }
                },
                error: function(xhr) {
                    mostrarNotificacionFija('Error inesperado al aprobar la preinscripción.', 'error');
                    btn.prop('disabled', false);
                }
            });
        });
        // Rechazar preinscripción directa
        $(document).on('click', '.rechazar-pre-btn', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            if (!confirm('¿Estás seguro de que quieres rechazar esta preinscripción?')) return;
            var row = $('#preinsc-row-' + id);
            var btn = $(this);
            btn.prop('disabled', true);
            $.ajax({
                url: '',
                method: 'POST',
                data: { id_preinscripcion: id, rechazar_preinscripcion: 1 },
                dataType: 'json',
                success: function(resp) {
                    if (resp.success) {
                        mostrarNotificacionFija(resp.message, 'success');
                        row.fadeOut(400, function(){ $(this).remove(); });
                        setTimeout(function(){ cargarPreinscripcionesRapidas(); }, 500);
                    } else {
                        mostrarNotificacionFija(resp.message, 'error');
                        btn.prop('disabled', false);
                    }
                },
                error: function(xhr) {
                    mostrarNotificacionFija('Error inesperado al rechazar la preinscripción.', 'error');
                    btn.prop('disabled', false);
                }
            });
        });

        // Función para recargar la tabla de preinscripciones rápidas por AJAX
        function cargarPreinscripcionesRapidas() {
            $.ajax({
                url: '',
                method: 'GET',
                data: { recargar_preinscripciones: 1 },
                success: function(html) {
                    // Reemplaza solo el tbody de la tabla de preinscripciones
                    var nuevaTabla = $(html).find('.preinscripciones-table tbody').html();
                    $('.preinscripciones-table tbody').html(nuevaTabla);
                }
            });
        }
    </script>

    <style>
    /* SOLO scroll vertical y altura máxima para las tablas, sin cambiar el layout global */
    .inscriptions-list {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 4px 24px rgba(52, 152, 219, 0.07);
        overflow: hidden;
        margin-top: 20px;
        max-width: 100%;
    }
    .inscriptions-list table {
        width: 100%;
        border-collapse: collapse;
        font-size: 15px;
        background: #fff;
    }
    .inscriptions-list tbody {
        display: block;
        max-height: 340px;
        overflow-y: auto;
    }
    .inscriptions-list thead, .inscriptions-list tbody tr {
        display: table;
        width: 100%;
        table-layout: fixed;
    }
    /* Scrollbar moderna */
    .inscriptions-list tbody::-webkit-scrollbar {
        width: 8px;
        background: #eaf6fb;
        border-radius: 8px;
    }
    .inscriptions-list tbody::-webkit-scrollbar-thumb {
        background: #b3d6f5;
        border-radius: 8px;
    }
    .inscriptions-list tbody {
        scrollbar-width: thin;
        scrollbar-color: #b3d6f5 #eaf6fb;
    }
    @media (max-width: 900px) {
        .sidebar { width: 60px; min-width: 60px; }
        .main-content { margin-left: 60px; width: calc(100% - 60px); padding: 15px; }
        .inscriptions-list tbody { max-height: 220px; }
    }
    .container-flex {
        display: flex;
        min-height: 100vh;
    }
    /* El sidebar y el main-content usan los estilos originales del CSS global */
    .btn-aprobar {
        background: #2176ae;
        color: #fff;
        border: none;
        border-radius: 6px;
        padding: 8px 18px;
        font-size: 15px;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(33,118,174,0.08);
        cursor: pointer;
        outline: none;
        margin: 0 2px;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        transition: background 0.2s, box-shadow 0.2s;
    }
    .btn-aprobar:hover {
        background: #1766a0;
        box-shadow: 0 4px 16px rgba(33,118,174,0.15);
    }
    .btn-rechazar {
        background: #e74c3c;
        color: #fff;
        border: none;
        border-radius: 6px;
        padding: 8px 18px;
        font-size: 15px;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(231,76,60,0.08);
        cursor: pointer;
        outline: none;
        margin: 0 2px;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        transition: background 0.2s, box-shadow 0.2s;
    }
    .btn-rechazar:hover {
        background: #c0392b;
        box-shadow: 0 4px 16px rgba(231,76,60,0.15);
    }
    .acciones-pre-btns {
        display: flex;
        flex-direction: row;
        gap: 6px;
        align-items: center;
        justify-content: flex-start;
    }
    .btn-mini {
        padding: 4px 10px;
        font-size: 15px;
        border-radius: 4px;
        min-width: 28px;
        min-height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
    }
    .btn-mini i { font-size: 1.1em; }
    </style>

<!-- MODAL PARA MOTIVO DE RECHAZO/CANCELACIÓN -->
<div id="motivoModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title" id="motivoModalTitle">Motivo</h2>
            <span class="close" id="motivoModalClose">&times;</span>
        </div>
        <form id="motivoForm">
            <input type="hidden" id="motivo_id" name="motivo_id">
            <input type="hidden" id="motivo_tipo" name="motivo_tipo">
            <input type="hidden" id="motivo_accion" name="motivo_accion">
            <div style="margin-bottom:15px;">
                <label for="motivo_textarea"><strong>Por favor, escribe el motivo:</strong></label>
                <textarea id="motivo_textarea" name="motivo" rows="4" style="width:100%; border:2px solid #3498db; background:#eaf6fb; border-radius:7px; padding:12px; font-size:16px; color:#222; outline:none; transition:border 0.2s;" required></textarea>
            </div>
            <button type="submit" class="btn-aprobar" id="motivoEnviarBtn">Enviar</button>
            <button type="button" class="btn-rechazar" id="motivoCancelarBtn">Cancelar</button>
        </form>
    </div>
</div>

<script>
// --- MODAL DE MOTIVO ---
let motivoModal = document.getElementById('motivoModal');
let motivoForm = document.getElementById('motivoForm');
let motivoClose = document.getElementById('motivoModalClose');
let motivoCancelarBtn = document.getElementById('motivoCancelarBtn');
let motivoTextarea = document.getElementById('motivo_textarea');
let motivoEnviarBtn = document.getElementById('motivoEnviarBtn');
let motivoIdInput = document.getElementById('motivo_id');
let motivoTipoInput = document.getElementById('motivo_tipo');
let motivoAccionInput = document.getElementById('motivo_accion');
let motivoCallback = null;

function abrirMotivoModal({id, tipo, accion, callback, title}) {
    motivoIdInput.value = id;
    motivoTipoInput.value = tipo;
    motivoAccionInput.value = accion;
    motivoTextarea.value = '';
    document.getElementById('motivoModalTitle').textContent = title || 'Motivo';
    motivoModal.style.display = 'block';
    motivoCallback = callback;
    motivoTextarea.focus();
}
function cerrarMotivoModal() {
    motivoModal.style.display = 'none';
    motivoCallback = null;
}
motivoClose.onclick = cerrarMotivoModal;
motivoCancelarBtn.onclick = function(e){ e.preventDefault(); cerrarMotivoModal(); };
window.onclick = function(event) { if (event.target == motivoModal) cerrarMotivoModal(); };

motivoForm.onsubmit = function(e) {
    e.preventDefault();
    if (!motivoTextarea.value.trim()) {
        motivoTextarea.focus();
        return;
    }
    if (motivoCallback) motivoCallback(motivoTextarea.value.trim());
    cerrarMotivoModal();
};

// --- PREINSCRIPCIONES: RECHAZAR ---
$(document).off('click', '.rechazar-pre-btn').on('click', '.rechazar-pre-btn', function(e) {
    e.preventDefault();
    var id = $(this).data('id');
    abrirMotivoModal({
        id: id,
        tipo: 'preinscripcion',
        accion: 'rechazar',
        title: 'Motivo de rechazo de preinscripción',
        callback: function(motivo) {
            var row = $('#preinsc-row-' + id);
            var btn = $(this);
            $.ajax({
                url: '',
                method: 'POST',
                data: { id_preinscripcion: id, rechazar_preinscripcion: 1, motivo: motivo },
                dataType: 'json',
                success: function(resp) {
                    if (resp.success) {
                        mostrarNotificacionFija(resp.message, 'success');
                        row.fadeOut(400, function(){ $(this).remove(); });
                        setTimeout(function(){ cargarPreinscripcionesRapidas(); }, 500);
                    } else {
                        mostrarNotificacionFija(resp.message, 'error');
                    }
                },
                error: function(xhr) {
                    mostrarNotificacionFija('Error inesperado al rechazar la preinscripción.', 'error');
                }
            });
        }
    });
});

// --- INSCRIPCIONES: RECHAZAR/CANCELAR ---
function cambiarEstado(selectElement) {
    var form = $(selectElement).closest('form');
    var nuevoEstado = $(selectElement).val();
    var estadoAnterior = $(selectElement).data('estado-anterior') || selectElement.getAttribute('data-estado-anterior') || selectElement.defaultValue;
    var idInscripcion = form.find('input[name="id_inscripcion"]').val();
    var label = {
        'pendiente': 'Pendiente',
        'aprobada': 'Aprobada',
        'rechazada': 'Rechazada',
        'cancelada': 'Cancelada'
    };
    if (nuevoEstado === 'rechazada' || nuevoEstado === 'cancelada') {
        abrirMotivoModal({
            id: idInscripcion,
            tipo: 'inscripcion',
            accion: nuevoEstado,
            title: 'Motivo de ' + (nuevoEstado === 'rechazada' ? 'rechazo' : 'cancelación') + ' de inscripción',
            callback: function(motivo) {
                enviarCambioEstadoInscripcion(form, selectElement, nuevoEstado, estadoAnterior, idInscripcion, motivo);
            }
        });
    } else {
        if (!confirm('¿Estás seguro de que quieres cambiar el estado a "' + (label[nuevoEstado] || nuevoEstado) + '"?')) {
            $(selectElement).val(estadoAnterior);
            return;
        }
        enviarCambioEstadoInscripcion(form, selectElement, nuevoEstado, estadoAnterior, idInscripcion, null);
    }
}
function enviarCambioEstadoInscripcion(form, selectElement, nuevoEstado, estadoAnterior, idInscripcion, motivo) {
    var data = form.serialize();
    if (motivo) data += '&motivo=' + encodeURIComponent(motivo);
    $.ajax({
        url: "",
        method: "POST",
        data: data,
        success: function(response) {
            if (typeof response === 'string') {
                try { response = JSON.parse(response); } catch(e) { response = {}; }
            }
            let debugMsg = `Intento cambiar id_inscripcion=${idInscripcion}, nuevo_estado=${nuevoEstado}. Respuesta: ${JSON.stringify(response)}`;
            if (response.success) {
                mostrarNotificacionFija((response.message || 'Estado actualizado correctamente.') + '\n' + debugMsg, 'success');
                var filtros = {
                    estado: $("select[name='estado']").val(),
                    curso: $("select[name='curso']").val()
                };
                cargarInscripciones(filtros);
            } else {
                mostrarNotificacionFija((response.message || 'Error al actualizar el estado.') + '\n' + debugMsg, 'error');
                $(selectElement).val(estadoAnterior);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log("Respuesta cruda del backend:", jqXHR.responseText);
            let debugMsg = `Intento cambiar id_inscripcion=${idInscripcion}, nuevo_estado=${nuevoEstado}. Error AJAX: ${textStatus}`;
            mostrarNotificacionFija('Error AJAX: ' + textStatus + '\n' + debugMsg, 'error');
            $(selectElement).val(estadoAnterior);
            console.error("Error cambiando estado:", textStatus, errorThrown);
        }
    });
}
</script>

<?php if (isset($_SESSION['flash_message'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    mostrarNotificacionFlotante(<?= json_encode($_SESSION['flash_message'][0]) ?>, <?= json_encode($_SESSION['flash_message'][1]) ?>);
});
</script>
<?php unset($_SESSION['flash_message']); endif; ?>

</body>

</html>