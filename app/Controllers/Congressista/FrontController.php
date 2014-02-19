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
        Eventos.deadline_inicial as deadline_inicial, Usuarios.id as idUsuarios
        FROM Submissoes, Eventos, Usuarios WHERE
            Submissoes.idEventos = Eventos.id AND Submissoes.author_id = Usuarios.id AND Submissoes.author_id = '{$userId}'";
        $events = $bd->query($sql);

        return $events;
    }

    public function getAllSubmissions(){
        

        $bd     =  \Moxo\Banco::getInstance();
        $sql    = "
        SELECT Submissoes.id as id, Eventos.nome as nomeEvento, Submissoes.created as createdSubmission, 
        Eventos.id as idEvento, Submissoes.titulo_trabalho as tituloTrabalho, Submissoes.status as status,
        Eventos.deadline_inicial as deadline_inicial, Usuarios.nomeCompleto as nomeCompleto, Usuarios.email as emailUsuario
        FROM Submissoes, Eventos, Usuarios WHERE
            Submissoes.idEventos = Eventos.id AND Submissoes.author_id = Usuarios.id";
        $events = $bd->query($sql);

        return $events;
    }

    public function getAllSubmissionsLog(){
        

        $bd     =  \Moxo\Banco::getInstance();
        $sql    = "
        SELECT Submissoes_Log.id as id, Eventos.nome as nomeEvento, 
        Eventos.id as idEvento, Submissoes_Log.titulo_trabalho as tituloTrabalho, Submissoes_Log.status as status,
        Eventos.deadline_inicial as deadline_inicial, Usuarios.nomeCompleto as nomeCompleto, Usuarios.email as emailUsuario,
        Submissoes_Log.date as dateModificacao, Submissoes_Log.operation as logOperation,
        Submissoes_Log.arquivo_inicial as  arquivo_inicial, Submissoes_Log.arquivo_final
        FROM Submissoes_Log, Eventos, Usuarios WHERE
            Submissoes_Log.idEventos = Eventos.id AND Submissoes_Log.author_id = Usuarios.id ORDER BY Submissoes_Log.date DESC";
        $events = $bd->query($sql);

        return $events;
    }
}

