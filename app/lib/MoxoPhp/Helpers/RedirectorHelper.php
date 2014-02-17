<?php
namespace Moxo\Helpers;

use Moxo;

 class RedirectorHelper{

	protected $parameters = array();

	protected function go ( $data ){
		header("Location: " . ROOT_URL . '/' . $data);
		//Atention
		exit();
	}

	public function goToController( $controller ){
		$this->go( $this->getModule($this->getCurrentModule())  .  $controller . '/index/' . $this->getUrlParameters());
	}

	public function goToAction( $action ){
		$this->go( $this->getModule($this->getCurrentModule())  . $this->getCurrentController() . "/". $action ."/" . $this->getUrlParameters() );
	}

	public function goToControllerAction( $controller, $action){
		$this->go( $this->getModule($this->getCurrentModule()) . $controller . "/". $action ."/" . $this->getUrlParameters() );
	}

	public function goToModuleControllerAction( $module, $controller, $action ) {
		$this->go( $this->getModule($module)  . $controller . "/". $action ."/" . $this->getUrlParameters() );
	}

	public function goToModuleController( $module, $controller ) {
		$this->go( $this->getModule($module)  . $controller . "/");
	}

	public function goToIndex(){
		$this->go('index');
	}

	public function goToUrl( $url ){
		header("Location : " . $url);
	}

	public function setUrlParameter( $name, $value ){
		$this->parameters[$name] = $value;
		return $this;
	}

	public function getUrlParameters(){
		$params = "";
		foreach ($this->parameters as $name => $value ){
			$params .= $name ."/".$value.'/';
		}
		return $params;
	}

	public function getCurrentController(){
		return \Moxo\Router::getInstance()->getController();
	}

	public function getCurrentAction(){
		return \Moxo\Router::getInstance()->getAction();
	}

	public function getModule( $module ) {
		if ( $module == DEFAULT_MODULE )
			return '';
		else 
			return $module . '/';
	}
	public function getCurrentModule() {
		return \Moxo\Router::getInstance()->getModule();

	}
}
