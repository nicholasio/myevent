<?php
session_start();
ini_set( 'default_charset', 'utf-8');
//Verdadeiro se estiver sendo chamado via linha de comando
define('CLI', PHP_SAPI === 'cli');

/******************************************
	Constantes PHP absolutas para Diretórios
********************************************/

define('ROOT_DIR', 				str_replace('public_html','', __DIR__) );
define('APP_DIR', 				ROOT_DIR 		. 'app');
define('CONTROLLERS_DIR', 		APP_DIR 		. '/Controllers');
define('LIB_DIR', 				APP_DIR 		. '/lib');
define('MODELS_DIR', 			APP_DIR 		. '/Models');
define('VIEWS_DIR', 			APP_DIR 		. '/Views');
define('CACHE_DIR',				APP_DIR			. '/cache');
define('RESOURCES_DIR', 		ROOT_DIR 		. 'public_html/resources');
define('UPLOADS_DIR', 			ROOT_DIR  		. 'public_html/uploads/');
define('STYLES_DIR', 			RESOURCES_DIR 	. '/css');
define('SCRIPTS_DIR', 			RESOURCES_DIR 	. '/js');
define('IMG_DIR', 				RESOURCES_DIR 	. '/img');

define('MOXO_DIR', 				LIB_DIR 		. '/MoxoPhp');
define('MOXO_CONTROLLERS_DIR' , MOXO_DIR 		. '/Controllers');
define('MOXO_MODELS_DIR', 		MOXO_DIR 		. '/Models');
define('MOXO_VIEWS_DIR',		MOXO_DIR 		. '/Views');
define('MOXO_HELPERS_DIR',		MOXO_DIR 		. '/Helpers');
define('MOXO_VENDORS_DIR',      MOXO_DIR 		. '/Vendors');



/******************************************
	Constantes PHP absolutas para URL
********************************************/
define('ROOT_URL', 				'http://' 		. $_SERVER[ 'HTTP_HOST' ]);
define('RESOURCES_URL', 		ROOT_URL 		. '/resources');
define('STYLES_URL', 			RESOURCES_URL 	. '/css');
define('SCRIPTS_URL', 			RESOURCES_URL 	. '/js');
define('IMG_URL',	 			RESOURCES_URL 	. '/img');

/******************************************
	Constantes diversas
********************************************/

define('DEFAULT_MODULE', 	'default');

/******************************************
	incluindo arquivos de configuração e funções úteis
********************************************/
include_once(APP_DIR 	. '/config/db.php');
include_once(APP_DIR	. '/config/router.php');
/******************************************
	Carregando Twig
********************************************/

include_once(MOXO_DIR 	. '/Twig_Config.php');
//include_once(MOXO_VENDORS_DIR . '/mPDF/mpdf.php' );

//Se estive sendo chamado via linha de comando, não carregue o phpids
/*if( ! CLI ) {
	set_include_path(
	   get_include_path()
	   . PATH_SEPARATOR
	   . '/var/www/phpids/lib'
	  );

	  require_once 'IDS/Init.php';

	  $request = array(
	      'REQUEST' => $_REQUEST,
	      'GET' => $_GET,
	      'POST' => $_POST,
	      'COOKIE' => $_COOKIE
	  );

	  $init = IDS_Init::init('/var/www/phpids/lib/IDS/Config/Config.ini.php');
	  $ids = new IDS_Monitor($request, $init);
	  $result = $ids->run();

	  if (!$result->isEmpty()) {

	   echo $result;

	   die("Você um dia será pego");
	  }
}*/

function appAutoload($PathToclassName) {

	$path = str_replace('\\', '/', $PathToclassName);
	if (strpos($path,"Moxo") !== false && strpos($path,"Moxo") == 0)
		$path = str_replace('Moxo', MOXO_DIR, $path);
	else
		$path = APP_DIR . '/'. $path;
	$path .= '.php';

	if( file_exists($path) )
		require_once($path);
	else
		throw new Exception("Classe não existe", 1);


}

spl_autoload_register('appAutoload');

include_once(APP_DIR 	. '/config/bootstrap.php');

\Moxo\Router::startApp();
