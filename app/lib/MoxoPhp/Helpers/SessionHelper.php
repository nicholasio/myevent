<?php
namespace Moxo\Helpers;

class SessionHelper{

	use \Moxo\Singleton;

	public function createSession( $name, $value ){
		$_SESSION[$name] = $value;
		return $this;
	}
	public function createOnSession( $topName, $name , $value) {
		$_SESSION[$topName][$name] = $value;
	}

	public function selectSession( $name ){
		if( $this->checkSession($name) )
			return $_SESSION[$name];
		else
			return false;
	}

	public function deleteSession( $name ){
		unset( $_SESSION[$name] );
		return $this;
	}

	public function checkSession ( $name ){
		return isset( $_SESSION[$name] );
	}

}
?>
