<?php
namespace Moxo\Helpers;


class authHelper {
	use \Moxo\Singleton;

	protected $sessionHelper, $redirectorHelper, $tableName, $userColumn,
	$passColumn, $user, $pass, $model;

	protected function __construct() {
		$this->sessionHelper    = SessionHelper::getInstance();
		$this->redirectorHelper = new RedirectorHelper();

		return $this;
	}

	/**
	 * Define a instância de acordo com os parâmetros
	 * @param String $tableName
	 * @param String $userColumn
	 * @param String $passColumn
	 * @param String $lastLoginColumn
	 * @param String $model
	 * @return AuthHelper
	 */
	public function factory($tableName, $userColumn, $passColumn,
						  $lastLoginColumn, $model) {

		$instance = self::getInstance();

		$instance->setTableName($tableName);
		$instance->setUserColumn($userColumn);
		$instance->setPassColumn($passColumn);
		$instance->setLastLoginColumn($lastLoginColumn);
		$instance->setModel($model);

		return $instance;
	}

	public function isLogged() {
		$userData = $this->getUserData();

		if ( $userData )
			return true;

		return false;
	}

	/**
	 * Realiza login
	 * @return none
	 */
	public function login() {
		$db          = \Moxo\Banco::getInstance();
		$db->_tabela = $this->getTableName();

		$where       = $this->getUserColumn() . ' = "' . $this->getUser() . '"';
		$where      .= ' AND '  . $this->getPassColumn() . ' = "' . sha1($this->getPass()) . '"';
		$result      = $db->read(["{$this->tableName}.id as id"], $where);

		if( empty($result) )
			return null;

		$this->getModel()->setId($result[0]->id);
		$this->getModel()->populate();

		// Criando/atualizando session
		$this->update($this->getModel());

		//Atualizando informãção de último login
		$this->getModel()->{$this->getLastLoginColumn()} = date('Y-m-d H:i:s');
		$this->getModel()->save();

		return true;

	}

	public function update( $model ) {
		/**
		 *Convetendo para stdClass para evitar problemas com $_SESSION e
		 *__PHP__INCOMPLETE_CLASS (serialize e unserialize)
		 */
		$id    = $model->getId();

		$model = (object) ((array) $model);

		$model->_id = $id;

		$this->sessionHelper->createSession('user', $model);

	}

	public function logout(){
		$this->sessionHelper->deleteSession('user');

		if ( ! $this->getUserData() )
			return true;

		return false;
	}

	public function checkLogin( $action ){

	}

	public function checkPermission($permission){

	}

	/**
	 * Retorna o model específico representando o usuário logado
	 * @return Model
	 */
	public function getUserData(){
		return SessionHelper::getInstance()->selectSession('user');
	}

	public function setModel($model){
		$this->model = $model;
	}

	public function getModel() {
		return $this->model;
	}

	public function getTableName() {
		return $this->tableName;
	}

	public function getLastLoginColumn(){
		return $this->lastLoginColumn;
	}

	public function setLastLoginColumn($lastLoginColumn){
		$this->lastLoginColumn = $lastLoginColumn;

		return $this;
	}

	public function setTableName($tableName) {
		$this->tableName = $tableName;

		return $this;
	}

	public function getUserColumn() {
		return $this->userColumn;
	}

	public function setUserColumn($userColumn) {
		$this->userColumn = $userColumn;

		return $this;
	}

	public function getPassColumn() {
		return $this->passColumn;
	}

	public function setPassColumn($passColumn) {
		$this->passColumn = $passColumn;

		return $this;
	}

	public function getUser() {
		return $this->user;
	}

	public function setUser($user) {
		$this->user = $user;

		return $this;
	}

	public function getPass() {
		return $this->pass;
	}

	public function setPass( $pass ) {
		$this->pass = $pass;

		return $this;
	}

	public function getLoginController() {
		return $this->loginController;
	}

	public function setLoginControllerAction( $loginController, $action ) {
		$this->loginController = $loginController;
		$this->loginAction = $action;

		return $this;
	}

	public function getLoginAction() {
		return $this->loginAction;
	}

	public function getLogoutController() {
		return $this->logoutController;
	}

	public function setLogoutControllerAction($logoutController, $action ) {
		$this->logoutController = $logoutController;
		$this->logoutAction 	= $action;

		return $this;
	}

	public function getLogoutAction() {
		return $this->logoutAction();
	}

}
