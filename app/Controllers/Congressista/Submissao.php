<?php
namespace Controllers\Congressista;

use \Controllers\AppController  as AppController;

class Submissao extends AppController {
    use FrontController;

    private $arrEvents;
    private $users;

    public function __construct() {
        parent::__construct();
    }

    public function init() {
        $this->arrEvents = $this->getEventsWithSubmissionsOpen();
        $this->users     = \Models\Usuario::fetchAll();
        parent::init();

    }

    public function novo() {
        $this->view->eventos = $this->arrEvents;
        $this->view->users   = $this->users;
        parent::novo();   
    }

    public function save() {

        $result = parent::save();

        if($result !== false){
            $this->go($this->getCurrentModule(),'submissao');
        }
    }

    public function del() {
        $id = $this->getRequesterId();
        
        $model = $this->getModel();

        if ( ! empty($model->arquivo_inicial) ) {
            unlink( UPLOADS_DIR . '/artigos/' .  $model->arquivo_inicial);
        }

        if ( ! empty($model->arquivo_final) ) {
            unlink( UPLOADS_DIR . '/artigos/' . $model->arquivo_final);
        }
        $bd     =  \Moxo\Banco::getInstance();
        $bd->exec("DELETE FROM Submissoes_autores WHERE Submissoes_id = '{$id}' ");

        parent::del();
        $this->go($this->getCurrentModule(), 'submissao' );
    }
    public function index_action() {
        $this->setViewName('list');
        $this->view->submissions = $this->getAllSubmissionsFromCurrentUser();
    }
}
