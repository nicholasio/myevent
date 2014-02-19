<?php
namespace Models;

use Moxo\Models\Model as BaseModel;

class Evento extends BaseModel {

    protected static $tableName = "Eventos";
    protected $required         = ['nome'];

    public $nome;
    public $descricao;
    public $qtdInscricoes;
    public $status;
    public $submissoes;
    public $deadline_inicial;
    public $deadline_final;

    public function __construct($id = null) {
        parent::__construct($id);
    }

    public function save() {

        if ( $this->deadline_inicial )
            $this->deadline_inicial = date_format( date_create_from_format('d/m/Y', $this->deadline_inicial), 'Y-m-d');
        if ( $this->deadline_final )
            $this->deadline_final = date_format( date_create_from_format('d/m/Y', $this->deadline_final), 'Y-m-d');

        return parent::save();
    }

}
