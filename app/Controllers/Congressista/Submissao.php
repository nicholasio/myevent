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
            $this->go($this->getCurrentModule(),'submissao', 'edit', ['id' => $result ]);
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

    public function edit() {
        $id     = $this->getRequesterId();
        $bd     =  \Moxo\Banco::getInstance();

        $eventsModel = new \Models\Evento();
        $this->view->eventos = $eventsModel->findAll(['submissoes' => '1']);
        $this->view->evento_atual =  $bd->query("SELECT * FROM Eventos, Submissoes WHERE 
                                                    Eventos.id = Submissoes.idEventos AND Submissoes.id = {$id}
                                                ")[0];

        $this->view->users   = $this->users;
        $autores                = $bd->query("SELECT Usuarios_id FROM Submissoes_autores 
                                          WHERE Submissoes_id = {$id}
                                        ");
        $arrAutores = [];

        foreach($autores as $autor) {
            $arrAutores[] = $autor->Usuarios_id;
        }

        $this->view->autores = $arrAutores; 
        parent::edit();

    }


    public function index_action() {
        $this->setViewName('list');
        $this->view->submissions = $this->getAllSubmissionsFromCurrentUser();
    }
}
