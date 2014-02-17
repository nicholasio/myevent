<?php
namespace Models;

use Moxo\Models\Model as BaseModel;

class SubEvento extends BaseModel {

    protected static $tableName = "SubEventos";
    protected $required         = ['idEventos', 'nome','nVagas'];

    public $idEventos;
    public $nome;
    public $descricao;
    public $nVagas;

    public function __construct($id = null) {
        parent::__construct($id);
    }

}

