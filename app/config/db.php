<?php

if( CLI ) {
	define("MOXO_ENVIROMENT", 'test');
} else {
	define("MOXO_ENVIROMENT",'prod');
}


/*
	PHP Constants for Dev Enviroment
*/
define('MOXO_APP_DEV_DSN', 'mysql:host=localhost;dbname=petroweek');
define('MOXO_APP_DEV_USER', 'root');
define('MOXO_APP_DEV_PASS', '#rosana#moniky!');

/*
	PHP Constantes for Production Enviroment
*/
define('MOXO_APP_TEST_DSN', 'mysql:host=localhost;dbname=petroweek');
define('MOXO_APP_TEST_USER', 'root');
define('MOXO_APP_TEST_PASS', '#rosana#moniky!');

/*
	PHP Constantes for Production Enviroment
*/
define('MOXO_APP_PROD_DSN', 'mysql:host=localhost;dbname=petroweek');
define('MOXO_APP_PROD_USER', 'root');
define('MOXO_APP_PROD_PASS', '#rosana#moniky!');
