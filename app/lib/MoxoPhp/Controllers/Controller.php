<?php
namespace Moxo\Controllers;

use Moxo\Models  as BaseModel;
use Moxo\Views   as Views;
use Moxo\Helpers as Helpers;

abstract class Controller extends CRUD{


	/**
	 * Carrega a view caso ela exista
	 * @param String $controller
	 * @param String $action
	 */
	public final function _view($module, $controller, $action ) {
		if( $this->isPreventDefault() )
			return;

		$middlePath = $this->getViewModule($module);

		switch( $action ) {
			case 'index_action' :
				$name  = $this->getViewName('');
				if ( ! empty($name) )
					$name = '_' . $name;
			break;
			default:
				$name  = '_' . $this->getViewName($action);
			break;
		}

		$viewTpl = "{$middlePath}/{$controller}{$name}.html";

		$data = !empty($this->view) ? $this->view : null;
		$this->setView(new Views\View($viewTpl, $this->getModel(), $data, $this));



		$this->getView()->show();
	}

	/**
	 * Retorna um parâmetro enviado via $_POST;
	 * @param String $value
	 * @return String | null
	 */
	public final function getPost( $value ) {
		if ( ! isset($_POST[$value]) || empty($_POST[$value]) )
			return null;

		return $_POST[$value];
	}

	/**
	 * Retorna um parâmetro enviado via $_GET
	 * @param String $value
	 * @return String | null
	 */
	public final function getGet( $value ) {
		if ( ! isset($_GET[$value]) || empty($_GET[$value]) )
			return null;

		return $_GET[$value];
	}

	/**
	 * Redireciona para uma module/controller/action
	 * @param String $controller
	 * @param String $action
	 * @param Array $params
	 * @return none
	 */
	public function go($module, $controller, $action = null, Array $params = null){
		$helper = new Helpers\RedirectorHelper();

		if ( !is_null($action) && !is_null($params) && !empty($params) ) {
			foreach ($params as $key => $value){
				$helper->setUrlParameter($key, $value);
			}
		}

		if ( ! is_null($action) )
			$helper->goToModuleControllerAction($module, $controller, $action);
		else
			$helper->goToModuleController($module, $controller);
	}



}
