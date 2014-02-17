<?php
namespace Controllers\Congressista;

use \Controllers\AppController  as AppController;

class Index extends AppController{

    use FrontController;

    public function index_action() {
        $this->view->isInscrito            = $this->isInscrito();
        $this->view->registrationAvaliable = $this->registrationAvaliable();

        //Atualizando session (em caso de administrador confirmar pagamento eqt usuário logado)
        $authUser = $this->authUser;
        $user     = new \Models\Usuario( $authUser->getUserData()->_id );
        $authUser->update($user);

        if( $this->isInscrito() ) {
            $bd  = \Moxo\Banco::getInstance();
            $sql = "SELECT *, Eventos.nome AS nomeEvento, SubEventos.nome AS SubEventoNome
                    FROM Inscricoes, SubEventos, Eventos, Usuarios
                    WHERE Inscricoes.idSubEventos = SubEventos.id
                    AND SubEventos.idEventos = Eventos.id
                    AND Inscricoes.idUsuarios = Usuarios.id AND Usuarios.id = {$user->getId()}";
            $result = $bd->query($sql);
            $this->view->inscricoes = $result;

            $this->view->inscricoesDisponiveis = $this->getAllAvaliableEvents( $user->getId() );
        }
    }

}
