<?php
namespace Moxo;

class Banco {
	use Singleton;

	protected $conexao;

	public $_tabela;

	protected function __construct(){
		switch(MOXO_ENVIROMENT) {
			case 'dev':
				$dsn 	= MOXO_APP_DEV_DSN;
				$user 	= MOXO_APP_DEV_USER;
				$pass 	= MOXO_APP_DEV_PASS;
			break;
			case 'prod':
				$dsn 	= MOXO_APP_PROD_DSN;
				$user 	= MOXO_APP_PROD_USER;
				$pass 	= MOXO_APP_PROD_PASS;
			break;
			case 'test':
				$dsn 	= MOXO_APP_TEST_DSN;
				$user 	= MOXO_APP_TEST_USER;
				$pass 	= MOXO_APP_TEST_PASS;
		}
		$this->conexao = new \PDO($dsn, $user, $pass);
		$this->conexao->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);
	}

	public function getCon(){
		return $this->conexao;
	}
	
	public function exec($sql) {
		return $stm = $this->conexao->exec($sql);
	}

	public function query($sql) {
		$stm = $this->conexao->query($sql);

		return $stm->fetchAll(\PDO::FETCH_OBJ);
	}

	public function lastInsertId() {
		return $this->conexao->lastInsertId();
	}

	/*
		Lê do banco de dados
	*/
	public function read($select = null, $where = null, $orderby = null, $limit = null ) {
	 	$select 	= ($select != null && is_array($select)) ? implode($select, ',') : '*';
	 	$where 		= ($where != null) ? "WHERE {$where}" : "";
		$orderby 	= ($orderby != null) ? "ORDER BY {$orderby}" : "";
		$limitSql 	= ($limit != null && is_array($limit)) ? "limit {$limit[0]},{$limit[1]}" : "";

		$sql 		= "SELECT {$select} FROM `{$this->_tabela}` $where $orderby $limitSql";
		return $this->query($sql);
	}

	public function del( $where = null){
		$where 		= ($where != null) ? "WHERE {$where}" : "";
		$sql 		= "DELETE FROM `{$this->_tabela}` {$where}";

		return $this->conexao->exec($sql);
	}

	public function insert( Array $dados ){
		$campos 	= array_keys($dados);
		$values 	= array_values($dados);
		$values     = array_map(function($el){ return addslashes($el); }, $values);
		$sql 		= "INSERT INTO `{$this->_tabela}` (" . implode(',', $campos) . ") ";
		$sql 	   .= "VALUES (\"" . implode("\",\"",$values) . "\")";


		return $this->conexao->exec($sql);
	}

	public function update( Array $dados, $where = null ) {
		$where 		= ($where != null) ? "WHERE {$where}" : "";
		$sql 		= "UPDATE `{$this->_tabela}` SET ";

		foreach($dados as $campo => $value){
			$value = addslashes($value);
			$campos[] = $campo . " = '{$value}' " ;
		}

		$sql   	   .= implode(", ", $campos) . "{$where}";
		return $this->conexao->exec($sql);
	}
}
