<?php

namespace Moxo;

class Router {
	use Singleton;

	private $module;
	private $controller;
	private $action;
	private $params;

	private $routerMap;

	protected function __construct() {
		$this->routerMap = __getMoxoRouterMap();
	}

	public function getModule(){
		return $this->module;
	}

	public function getController() {
		return $this->controller;
	}

	public function getAction() {
		return $this->action;
	}

	public function getParams() {
		return $this->getParams;
	}

	protected function setRouter($module, $controller, $action, $params) {
		$this->module     = $module;
		$this->controller = $controller;
		$this->params     = $params;
		$this->action     = $action;

	}

	public function getRouterMap(){
		return $this->routerMap;
	}

	public function dispatch() {

		if ( ! isset($this->routerMap[$this->module][$this->controller]) )
			throw new \Exception("Nenhuma rota definida para este controller");

		$controllerClass = $this->routerMap[$this->module][$this->controller];
		$controllerName  = explode('\\',$controllerClass);
		$controllerName  = $controllerName[count($controllerName) - 1];
		$modelClass      = "\\Models\\{$controllerName}";
		$action          = $this->action;

		$obj = new $controllerClass();

		if( method_exists($obj, $action) ){
			$obj->pseudoConstruct(); //core method
			$obj->init(); //método sobrecarregado pelo user
			$obj->setParams($this->params);

			if ( file_exists(MODELS_DIR . "/{$controllerName}.php") ) {
				$id = null;
				if( $obj->getParam('id') )
					$id = $obj->getParam('id');

				$model = new $modelClass($id);
				$obj->setModel($model);
			}

			$obj->$action();
			$obj->_view($this->module, $controllerName, $action);
			$obj->shutdown();
		} else {
			die("Método não existe");
		}


	}

	public static function startApp() {
		$url        = (isset($_GET['url'])) ? $_GET['url'] : 'index/index_action';
		$pos = strripos($url, '/');
		if ( $pos === (strlen($url) - 1) ) {
			$url = substr_replace($url, '', $pos);
		}
		$explode    = explode('/', $url);

		/*
			Se a url for module/controller/action ou explode[0]
			for um módulo (ou seja, está definido no mapa de rotas)
			caso contrário o módulo default será exibido na url
		*/
		$module = $explode[0];
		if ( array_key_exists($module, __getMoxoRouterMap()) ) {
			$controller = (!isset($explode[1]) || $explode[1] == null) ? 'index' : $explode[1];
			$indAction  = 2;
			unset($explode[0]);
			unset($explode[1]);
		} else {
			$module     = DEFAULT_MODULE;
			$controller = $explode[0];
			$indAction  = 1;
			unset($explode[0]);
		}

		$action  = (!isset($explode[$indAction]) || $explode[$indAction] == null
					|| $explode[$indAction] == 'index' ) ? 'index_action' : $explode[$indAction];

		unset($explode[$indAction]);

		if (end($explode) == null)
			array_pop($explode);

		$i = 0;

		$ind 		= [];
		$values 	= [];
		if ( ! empty($explode) ) {
			foreach ( $explode as $val ) {
				if ($i % 2 == 0) {
					$ind[] = $val;
				} else {
					$values[] = $val;
				}

				$i++;
			}
		} else {
			$ind 	= [];
			$values = [];
		}

		if ( count($ind) == count($values) && ! empty($ind) && ! empty($values) )
			$params = array_combine($ind, $values);
		else
			$params = [];

		$router = Router::getInstance();
		$router->setRouter($module, $controller, $action, $params);
		try {
			$router->dispatch();
		} catch (\Exception $e){
			echo $e->getMessage();
		}


	}
}
