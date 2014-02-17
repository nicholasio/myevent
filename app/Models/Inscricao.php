<?php
namespace Models;

use Moxo\Models\Model as BaseModel;

class Inscricao extends BaseModel {

    protected static $tableName = "Inscricoes";
    protected $required         = ['idSubEventos', 'idUsuarios'];

    public $idUsuarios;
    public $idSubEventos;


    public function __construct($id = null) {
        parent::__construct($id);
    }

    public static function getNumInscritos($id) {
        $bd     =  \Moxo\Banco::getInstance();
        return $bd->query("SELECT count(*) as
                          total FROM Inscricoes WHERE idSubEventos =
                          {$id}")[0]->total;

    }
}

