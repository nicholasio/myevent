<?php
namespace Controllers\Congressista;



trait FrontController  {

    /**
     * Checa permissão
     */
    public function checkPermissions() {
        return true;
        /*if (  $this->authUser->getUserData()->tipo == 'AD' ) //Admin
            $this->go('admin', 'index');
        else if (  $this->authUser->getUserData()->tipo != 'PA' ) //PA
            $this->go(DEFAULT_MODULE, 'auth', 'login');*/
    }

    public function isInscrito() {
        $inscricoesPermitidas = $this->getInscricoesPermitidas();
        $inscricoesRealizadas = $this->getInscrioesRealizadas();

        if( $inscricoesRealizadas !== false ) {
            if ( count($inscricoesRealizadas) <= count($inscricoesPermitidas) )
                return true;
        }
        return false;
    }

    public function registrationAvaliable() {

        $inscricoesPermitidas = $this->getInscricoesPermitidas();
        $inscricoesRealizadas = $this->getInscrioesRealizadas();

        if( $inscricoesRealizadas !== false ) {
            if ( count($inscricoesRealizadas) < count($inscricoesPermitidas) )
                return true;
            if ( count($inscricoesPermitidas) == count($inscricoesRealizadas) )
                return false;
        }

        return true;
    }

    public function getInscricoesPermitidas() {
        $eventos   = new \Models\Evento();
        $inscricoesPermitidas = $eventos->findAll(['status' => 'AT' ]);

        return $inscricoesPermitidas;
    }

    public function getInscrioesRealizadas() {
        $inscricao = new \Models\Inscricao();
        $userId = $this->authUser->getUserData()->_id;
        $inscricoesRealizadas = $inscricao->findAll(['idUsuarios' => $userId]);

        return $inscricoesRealizadas;
    }

    public function getAllAvaliableEvents( $id ) {
        $userId = $id;

        $bd     =  \Moxo\Banco::getInstance();
        $sql    = "
        SELECT *,Eventos.id as idEventos ,Eventos.nome as nomeEvento, SubEventos.nome as SubEventoNome
        FROM Eventos,SubEventos WHERE Eventos.id = SubEventos.idEventos
        AND Eventos.status = 'AT' AND Eventos.id NOT IN
            (
                SELECT Eventos.id FROM Inscricoes, Eventos, SubEventos WHERE
                idUsuarios = {$userId} AND Inscricoes.idSubEventos = SubEventos.id
                AND SubEventos.idEventos = Eventos.id
            ) ";
        $events = $bd->query($sql);

        return $events;
    }

    public function getAllEvents() {
        $bd     =  \Moxo\Banco::getInstance();
        $sql    = "
        SELECT *,Eventos.id as idEventos ,Eventos.nome as nomeEvento, SubEventos.nome as SubEventoNome
        FROM Eventos,SubEventos WHERE Eventos.id = SubEventos.idEventos
        AND Eventos.status = 'AT'";
        $events = $bd->query($sql);

        return $events;
    }

    public function getEventsWithSubmissionsOpen() {
        $userId = $this->authUser->getUserData()->_id;

        $bd     =  \Moxo\Banco::getInstance();
        $sql    = "
        SELECT *
        FROM Eventos WHERE 
        Eventos.status = 'AT' AND Eventos.submissoes = '1' 
        AND NOW() <= Eventos.deadline_inicial AND Eventos.id";
        $events = $bd->query($sql);

        return $events;
    }

    public function getAllSubmissionsFromCurrentUser(){
        $userId = $this->authUser->getUserData()->_id;

        $bd     =  \Moxo\Banco::getInstance();
        $sql    = "
        SELECT Submissoes.id as id, Eventos.nome as nomeEvento, Submissoes.created as createdSubmission, 
        Eventos.id as idEvento, Submissoes.titulo_trabalho as tituloTrabalho, Submissoes.status as status,
        Eventos.deadline_inicial as deadline_inicial
        FROM Submissoes, Eventos WHERE
            Submissoes.idEventos = Eventos.id AND Submissoes.author_id = '{$userId}'";
        $events = $bd->query($sql);

        return $events;
    }
}

