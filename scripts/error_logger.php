<?php

// Cargar la configuración de logs
$log_config = require 'log_config.php';

/**
 * Registra un mensaje de error en el archivo de log
 *
 * @param string $message El mensaje a registrar
 * @param string $level El nivel de error (INFO, WARNING, ERROR, CRITICAL)
 * @return void
 */
function logError($message, $level = 'ERROR') {
    global $log_config;

    // Verificar si el nivel de error es válido
    if (!in_array($level, $log_config['log_levels'])) {
        error_log("Nivel de error inválido: $level. Se usará ERROR por defecto.");
        $level = 'ERROR';
    }

    $log_file = $log_config['log_file'];
    $max_size = $log_config['max_size'];

    // Verificar si el archivo de log existe y es escribible
    if (!file_exists($log_file) && !is_writable(dirname($log_file))) {
        error_log("No se puede crear el archivo de log en: $log_file");
        return;
    }

    if (file_exists($log_file) && !is_writable($log_file)) {
        error_log("El archivo de log no es escribible: $log_file");
        return;
    }

    // Rotación de logs si el archivo excede el tamaño máximo
    if (file_exists($log_file) && filesize($log_file) > $max_size) {
        $old_file = dirname($log_file) . '/error_' . date('Y-m-d_H-i-s') . '.log';
        if (!rename($log_file, $old_file)) {
            error_log("No se pudo rotar el archivo de log: $log_file");
            return;
        }
    }

    // Formatear el mensaje de log
    $log_message = sprintf(
        "[%s] [%s] %s%s",
        date('Y-m-d H:i:s'),
        $level,
        $message,
        PHP_EOL
    );

    // Escribir en el archivo de log
    if (error_log($log_message, 3, $log_file) === false) {
        error_log("No se pudo escribir en el archivo de log: $log_file");
    }
}

/**
 * Registra un mensaje de depuración
 *
 * @param mixed $data Los datos a registrar
 * @param string $label Una etiqueta opcional para identificar los datos
 * @return void
 */
function logDebug($data, $label = '') {
    if ($label) {
        $message = $label . ': ' . print_r($data, true);
    } else {
        $message = print_r($data, true);
    }
    logError($message, 'DEBUG');
}

/**
 * Registra una excepción
 *
 * @param Exception $exception La excepción a registrar
 * @return void
 */
function logException(Exception $exception) {
    $message = sprintf(
        "Exception: %s\nFile: %s\nLine: %d\nTrace:\n%s",
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine(),
        $exception->getTraceAsString()
    );
    logError($message, 'CRITICAL');
}

// Manejo de errores fatales
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        logError(sprintf(
            "Fatal Error: %s in file %s on line %d",
            $error['message'],
            $error['file'],
            $error['line']
        ), 'CRITICAL');
    }
});