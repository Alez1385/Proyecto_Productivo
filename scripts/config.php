<?php
// config.php

// Definir la URL base de tu proyecto
// Ajusta '/mi_proyecto/' según la estructura de tu proyecto en el servidor web
// Si tu proyecto está en la raíz del servidor, puedes usar simplemente '/'
define('BASE_URL', '/'); 

// Definir la ruta absoluta en el sistema de archivos del servidor
// dirname(__FILE__) obtiene el directorio donde se encuentra este archivo
define('BASE_PATH', dirname(__FILE__) . '/');