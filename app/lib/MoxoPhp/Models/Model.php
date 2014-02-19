<?php
namespace Moxo\Models;
use Moxo;

abstract class Model extends BaseModel{

	public function __construct($id = null) {
		$this->setRequired(true);
		if ( isset($id) ) {
			$this->id = $id;
			$this->populate();
		}
	}

	/**
	 * Salva o registro
	 * @return Int | Boolean
	 */
	public function save() {
		$dados = $this->getPropetiesToSave();
		$tableName = self::getTableName();

		if ( $this->getErrorMessages() )
			return false;

		if ( isset($this->id) && $this->id !== false ){
			$this->getBd()->update($dados, "{$tableName}.id = {$this->id}");
			return $this->id;
		}
		else{
			$this->getBd()->insert($dados);
			$this->id = $this->getBd()->lastInsertId();
			return $this->id;
		}
	}

	public function delete() {
		$tableName = self::getTableName();

		if ( !isset($this->id) )
			return false;
		try{
			if ( $this->getBd()->del("{$tableName}.id = {$this->id}") ) {
			$props = $this->getAllProps();

			foreach($props as $prop) {
				$prop->setValue($this, null);
			}

			return true;
			} else
				return false;
		} catch(\Exception $e) {
			$this->addErrorMessage('Existem registros que dependem deste');
			return false;
		}

	}

	public function deleteAll() {
		$tableName = self::getTableName();

		$whereToDel = $this->getPropetiesToSave();
		$where = [];

		//Ajustar
		foreach($whereToDel as $key => $value) {
			$where[] = $key . '= "' . $value . '" ';
		}

		if ( $this->getBd()->del(implode($where,' AND ')) ) {
			return true;
		} else
			return false;

	}

	/*
		If the id and the tableName is set, then select and populate data
	*/
	public function populate() {
		$tableName = self::getTableName();

		if ( !isset($this->id) || is_null($this->id) || $this->id === false)
			return false;


		$allKeys = $this->getKeyProperties();

		$data 	= $this->getBd()->read($allKeys, "{$tableName}.id = {$this->id}");

		if( empty($data) )
			return false;

		$data 	= $data[0];

		$this->_populateModel($data);

	}
	/**
	 * Popula as propriedades do model com as informações do banco
	 * @return none
	 */
	public function _populateModel($data) {
		$props 	= $this->getAllPropsArray();

		foreach($data as $key => $value) {
			if ( $key == 'id' ){
				$this->setId($value);
			} else{
				$props[$key]->setValue($this, $value);
			}
		}
	}

	/**
	 * Procura por um registro $key = $value
	 * PRECISA TESTAR
	 * @param String $key
	 * @param String $value
	 * @return none
	 */
	public function find(Array $keys) {

		$data = $this->_find($keys);
		if($data) {
			$data = $data[0];
			$this->_populateModel($data);
			return true;
		}
		return false;


	}
	
	private function _find(Array $keys, $orderby = null, $limit = null) {
		$tableName = self::getTableName();

		$allKeys = $this->getKeyProperties();
		$allKeys[] = "{$tableName}.id as id";

		$where = [];
		foreach ($keys as $key => $value) {
			$where[] = "{$key} = \"{$value}\" ";
		}

		$data = $this->getBd()->read($allKeys, implode($where, ' AND '), $orderby, $limit);

		if ( empty($data) )
			return false;

		return $data;
	}

	/**
	 * Retorna todos os registros
	 * @param Array $keys
	 * @return Array
	 */
	public function findAll(Array $keys, $orderby = null, $limit = null) {
		return $this->_find($keys, $orderby, $limit);
	}

	public static function fetchAll() {
		$class = get_called_class();
		$all = new $class();

		$data = $all->getBd()->read('*');

		return $data;

	}
}
