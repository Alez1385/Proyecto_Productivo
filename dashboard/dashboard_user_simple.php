<body>
<?php include '../scripts/profile_notification.php'; ?>

<!-- DIAGNÓSTICO DE LA FUNCIÓN -->
<div style="background: #ffff00; color: black; padding: 15px; margin: 10px; border-radius: 8px; font-family: monospace; font-size: 12px;">
    <strong>DIAGNÓSTICO:</strong><br>
    <?php 
    if (function_exists('getProfileIncompleteInfo')) {
        $profileInfo = getProfileIncompleteInfo($user);
        echo "getProfileIncompleteInfo existe<br>";
        echo "Resultado: " . ($profileInfo['incompleto'] ? 'INCOMPLETO' : 'COMPLETO') . "<br>";
        echo "Campos faltantes: " . implode(', ', $profileInfo['campos_faltantes']) . "<br>";
        echo "Usuario: " . htmlspecialchars($user['username']) . "<br>";
        echo "Nombre: '" . htmlspecialchars($user['nombre'] ?? 'NULL') . "'<br>";
        echo "Apellido: '" . htmlspecialchars($user['apellido'] ?? 'NULL') . "'<br>";
        echo "Teléfono: '" . htmlspecialchars($user['telefono'] ?? 'NULL') . "'<br>";
        echo "Dirección: '" . htmlspecialchars($user['direccion'] ?? 'NULL') . "'<br>";
        echo "Fecha nac: '" . htmlspecialchars($user['fecha_nac'] ?? 'NULL') . "'<br>";
    } else {
        echo "ERROR: getProfileIncompleteInfo NO existe";
    }
    ?>
</div>

<!-- NOTIFICACIÓN DE PRUEBA - SIEMPRE VISIBLE -->
<div style="background: #ff0000; color: white; padding: 20px; margin: 10px; border-radius: 8px; font-weight: bold; text-align: center;">
    🔴 NOTIFICACIÓN DE PRUEBA - SI VES ESTO, EL CSS FUNCIONA
</div>
<!-- FIN DEL DASHBOARD LIMPIO PARA PRUEBA --> 