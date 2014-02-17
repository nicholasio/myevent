<?php
namespace Controllers\Admin;

use \Controllers\AppController  as AppController;
use \Models\Inscricao as mInscricao;

class Evento extends AppController {
    use AdminController;

    public function __construct() {
        parent::__construct();
        $this->_data = \Models\Evento::fetchAll();
    }

    public function index_action() {
        AdminController::index_action();
        $eventos             = $this->_data;
        $this->view->eventos = $eventos;
        $arrSubEventos       = [];

        foreach($eventos as $evento) {
            $sub       = new \Models\SubEvento();
            $subEvents = $sub->findAll(['idEventos' => $evento->id]);
            if ( $subEvents ) {
                foreach($subEvents as $subEvent ) {
                    $subEvent->nVagasRestantes = $subEvent->nVagas - mInscricao::getNumInscritos($subEvent->id);
                }
            }

            $arrSubEventos[$evento->id] = $subEvents;
        }

        $this->view->subeventos = $arrSubEventos;
    }
    public function del() {
        parent::del();
        $this->go('admin','evento');
    }

    public function save() {
        $result = parent::save();
        if($result !== false){
            $this->go('admin','evento','edit', ['id' => $result]);
        }
    }

}
