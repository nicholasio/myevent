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
            if ( $subEventId == -1 ) { //Minicurso lotado
                //@TODO Verificar real quantidade de vagas restantes
                $this->flashMessages->add('e', 'Evento escolhido está Lotado');
                return false;
            }
        }

        return true;
    }

    public function saveInscricao() {

        foreach( $this->idEvents as $id ) {
            $inscricao  = new \Models\Inscricao();
            $subEventId = $this->getPost('event-'.$id);
            $inscricao->idSubEventos = trim($subEventId);
            $inscricao->idUsuarios   = trim($this->getPost('userId'));
            $inscricao->save();
        }

        return true;

    }

    public function viewevents() {
        $this->loadData();
    }

    public function viewinscritos() {
        $this->dieIfPermissionDenied();

        $id = $this->getParam('idsubevento');
        $this->view->idsubevento = $id;

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
