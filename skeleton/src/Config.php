<?php
# Llenar con sus datos
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_HOST', 'localhost');
define('DB_NAME', 'siadi');



# Codigo de errores
define('COD_OK', '200');
define('COD_ERROR_GENERICO', '500');
define('COD_USER_FAKE', '401');
define('COD_ERROR_PASS', '402'); // Password incorrecta
define('COD_ERROR_AUT', '403');
define('COD_ERROR_AKM', '405');
define('COD_ERROR_UNE', '406'); //Usuario no encontrado
define('COD_ERROR_ED', '407'); //Existe dispositivo
define('COD_ERROR_NED', '408'); // No existe dispositivo
define('COD_ERROR_PE', '409'); // Password vacía
define('COD_ERROR_MD', '410'); // Alcanzó el máximo de dispositivos
define('COD_ERROR_NUC', '411');  // UC ingresó vacío
define('COD_ERROR_UE', '412'); // Usuario que intenta registrar ya existe
define('COD_ERROR_UI', '413'); // Usuario suspendido
define('COD_ERROR_SE', '414'); // Sesión expirada
define('COD_ERROR_ANA', '415'); // Acceso no autorizado
# Variables globales
define('HASHTAB', 'softllama_');
define('CANT_INTENTOS_LOGIN', 3);
