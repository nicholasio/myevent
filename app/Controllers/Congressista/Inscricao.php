<?php
namespace Controllers\Congressista;

use \Controllers\AppController  as AppController;
use \Models\Inscricao as mInscricao;

class Inscricao extends AppController{

    use FrontController;

    private $arrEvents;
    private $idEvents;

    public function index_action() {
        if (  /* $this->isInscrito() */
            ! $this->registrationAvaliable() ||
            $this->authUser->getUserData()->status == 'AP' ) {
            $this->go('congressista', 'index');
        }


        $this->loadData();

        if ( ! $this->isPostRequest() ) return;

        $this->dieIfPermissionDenied();

        if ($this->validadeData() ) {
            $this->preventDefault();
            if ( $this->saveInscricao() ){
                $this->flashMessages->add('s', 'Inscrição realizada com sucesso!');
                $this->go($this->getCurrentModule(), 'index');
            }
        }
    }

    public function dieIfPermissionDenied(){
        $authUser = $this->authUser->getUserData();
        if( $authUser->tipo == 'PA' && $this->getRequester('userId') != $authUser->_id )
            $this->go($this->getCurrentModule(),'index');
    }

    public function loadData() {
        $id = $this->getParam('userId');
        if ( ! is_null($id) )
            $events = $this->getAllAvaliableEvents( $id );
        else
            $events = $this->getAllEvents();

        $this->arrEvents = [];
        $this->idEvents  = [];

        foreach($events as $event){
            $nInscritos     = mInscricao::getNumInscritos($event->id);
            $event->nVagas -= $nInscritos;
            $this->arrEvents[ $event->nomeEvento ][] = $event;


            if( ! in_array($event->idEventos, $this->idEvents) )
                $this->idEvents[] = $event->idEventos;

        }
        $this->view->events = $this->arrEvents;
        $this->view->userId = $this->getParam('userId');
    }

    public function validadeData() {

        foreach( $this->idEvents as $id ) {
            $subEventId = $this->getPost('event-'.$id); //Pega um id de subevento
            if ( $subEventId == -1 || ! $this->haveSeats($subEventId) ) { 
                $this->flashMessages->add('e', 'Atividade escolhida está lotada');
                return false;
            }
        }

        return true;
    }

    /*
        
    */
    public function saveInscricao() {

        foreach( $this->idEvents as $id ) { 
            $subEventId = $this->getPost('event-'.$id);
            $userId     = $this->getPost('userId');

            $this->register( $userId, $subEventId );
        }

        return true;

    }

    /*
        Função que retorna true se um subevento tiver vaga
    */
    public function haveSeats( $subEventId ) {
        $subEvent   = new \Models\SubEvento( $subEventId );
        $nInscritos = mInscricao::getNumInscritos( $subEventId );

        if ( $subEvent->nVagas - $nInscritos > 0 ) {
            return true;
        } else {
            return false;
        }

    }
    /* 
        Registra um único usuário em um subevento
    */
    public function register( $userId, $subEventId ) {  
        if ( ! $this->haveSeats( $subEventId ) ) {
            return false;
        }

        $inscricao  = new \Models\Inscricao();
        $inscricao->idSubEventos = trim($subEventId);
        $inscricao->idUsuarios   = trim($userId);

        return $inscricao->save();
    }

    /*
       Action para registrar um único usuário em um subevento
    */
    public function registeruser() {
        $subEventId = $this->getParam('subEventId');
        $userId = $this->getPost('userId');

        if ( $this->register($userId, $subEventId) ) {
            $this->flashMessages->add('s', 'Inscrição realizada com sucesso');
        } else {
            $this->flashMessages->add('e', 'Atividade escolhida está lotada!');
        }

        $this->go('congressista','inscricao', 'viewinscritos',  ['idsubevento' => $subEventId]);

    }
    public function viewevents() {
        $this->loadData();
    }

    public function viewinscritos() {
        $this->dieIfPermissionDenied();

        $id = $this->getParam('idsubevento');
        $this->view->idsubevento = $id;

        $this->view->allUsers = \Models\Usuario::fetchAll(null, 'nomeCompleto ASC');

        $bd = \Moxo\Banco::getInstance();
        $inscritos = $bd->query("SELECT *, SubEventos.nome as SubEventoNome, Eventos.nome as EventoNome,
            Usuarios.id as idUsuarios, Inscricoes.id as idInscricoes
            FROM Usuarios, Inscricoes, SubEventos, Eventos WHERE Inscricoes.idSubEventos = SubEventos.id AND
            SubEventos.idEventos = Eventos.id AND Inscricoes.idUsuarios = Usuarios.id AND SubEventos.id = {$id}
            ");
        $this->view->inscritos = $inscritos;
    }

    public function viewuserevents() {
        $this->dieIfPermissionDenied();
        $id = $this->getParam('idusuario');
        $this->view->idUsuario = $id;

        $bd = \Moxo\Banco::getInstance();
        $events = $bd->query("SELECT *, SubEventos.nome as SubEventoNome, Eventos.nome as EventoNome,
            Usuarios.id as idUsuarios, Inscricoes.id as idInscricoes
            FROM Usuarios, Inscricoes, SubEventos, Eventos WHERE Inscricoes.idSubEventos = SubEventos.id AND
            SubEventos.idEventos = Eventos.id AND Inscricoes.idUsuarios = Usuarios.id AND Usuarios.id = {$id}
            ");
        $this->view->events = $events;

    }
    public function del() {
        $this->dieIfPermissionDenied();
        parent::del();

        if( $this->getParam('idsubevento') )
            $this->go('congressista','inscricao', 'viewinscritos',  ['idsubevento' => $this->getParam('idsubevento')]);
        else if (  $this->getParam('idusuario') )
            $this->go('congressista','inscricao', 'viewuserevents',  ['idusuario' => $this->getParam('idusuario')]);


    }
}
