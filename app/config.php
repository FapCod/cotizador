<?php

session_start();
// PARA SABER SI ESTAMOS EN PRODUCCION LOCAL O EN PRODUCCION REMOTA
define('IS_LOCAL', in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']));
define('URL', (IS_LOCAL ? 'http://localhost:8848/cotizador/' : 'http://www.example.com/'));
//RUTAS PARA CARPETAS
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', getcwd() . DS);
define('APP', ROOT . 'app' . DS);
define('ASSETS', ROOT . 'assets' . DS);
define('TEMPLATES', ROOT . 'templates' . DS);
define('INCLUDES', TEMPLATES . 'includes' . DS);
define('MODULES', TEMPLATES . 'modules' . DS);
define('VIEWS', TEMPLATES . 'views' . DS);
define('UPLOADS',  'assets/uploads/');
//RUTAS PARA ARCHIVOS QUE SE INCLUYAN EN EL HTML CSS JS Y IMAGENES
define('CSS', URL . 'assets/css/');
define('JS', URL . 'assets/js/');
define('IMG', URL . 'assets/img/');
define('FONTS', URL . 'assets/fonts/');
define('FAVICON', URL . 'assets/img/favicon.ico');
define('LOGO', URL . 'assets/img/logo.png');

//PERSONALIZACION
define('APP_NAME', 'CotizadorApp');
define('DESCRIPTION', 'CotizadorApp');
define('TAXES_RATE', 16);
define('SHIPPING', 99.50);
// autoload composer
require_once ROOT.'vendor/autoload.php';

//CARGAR LAS FUNCIONES
require_once APP . 'functions.php';
