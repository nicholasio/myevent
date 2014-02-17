<?php
namespace Controllers\Admin;

use \Controllers\AppController  as AppController;
class SubEvento extends AppController {
    use AdminController;

    public function __construct() {
        parent::__construct();
        $this->_data         = \Models\SubEvento::fetchAll();
    }
    public function init(){
        parent::init();
        $this->view->eventos = \Models\Evento::fetchAll();
    }
    /*public function index_action() {
        AdminController::index_action();
        $this->view->eventos = $this->_data;
    }*/
    public function novo() {
        $this->view->idEventos = $this->getParam('idevento');
        $this->view->id        = $this->getRequesterId('id');
        parent::novo();
    }
    public function del() {
        parent::del();
        $this->go('admin','evento');
    }

    public function save() {
        $result = parent::save();
        if( $result > 0 ){
            $this->go('admin','subevento','edit', ['id' => $result]);
        }
    }

}

