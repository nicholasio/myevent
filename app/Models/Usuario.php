<?php
namespace Models;

use Moxo\Models\Model as BaseModel;

class Usuario extends BaseModel {

	protected static $tableName = "Usuarios";
	protected $required         = ['nomeCompleto','email','senha'];

	public $nomeCompleto;
	public $email;
	public $cpf;
	public $celular;
	public $instituicao;
	public $senha;
	public $submissao;
	public $tipo;
	public $status;
	public $lastLogin;
	public $created;
	public $payment_receipt;

	public function __construct($id = null) {
		parent::__construct($id);
	}

	public function setSenha( $senha ) {
		if ( !empty($senha)  && ! is_null($senha) )
			$this->senha = sha1( $senha );
	}

}
