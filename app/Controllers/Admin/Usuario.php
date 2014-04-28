<?php
namespace Controllers\Admin;

use Controllers as Base;

class Usuario extends Base\Usuario {
    use AdminController;

    public function __construct() {
        parent::__construct();
    }

    public function index_action() {
        AdminController::index_action();
        $this->view->users = $this->_data;
    }

    /**
     * Deleta um usuário, não é possível remover o usuário logado
     * @return none
     */
    public function del() {
        $this->preventDefault();
        $id = $this->getParam('id');
        $authUser = $this->authUser->getUserData();

        $flashMessages = $this->getHelper('flashMessages');

        if ( is_null($id) ) {
            $flashMessages->add('e', 'Id não especificado');
        } else {
            if ($authUser->_id == $id) {
                $flashMessages->add('e', 'Você não pode remover você mesmo');
            } else {
                $user   = new \Models\Usuario($id);
                if ( $user->delete() ) {
                    $flashMessages->add('s', 'Usuário removido com sucesso');
                } else {
                    $flashMessages->add('e', 'Erro ao remover usuário');
                }
            }
        }

        $this->go('admin','user');
    }

    public function confirmpg() {
        $this->getModel()->setRequired(false);
        $this->getModel()->status = 'PG';
        $this->getModel()->save();
        $this->flashMessages->add('s', 'Pagamento de: '. $this->getModel()->nomeCompleto . ', email: ' . $this->getModel()->email . ' confirmado com sucesso');
        $this->go('admin', 'user');
    }
    
    public function passwordreset() {
        $this->getModel()->setRequired(false);
        $this->getModel()->setSenha($this->getModel()->cpf);
        $this->getModel()->save();
        $this->flashMessages->add('s', 'Senha de ' . $this->getModel()->nomeCompleto . ' alterada para ' . $this->getModel()->cpf);
        $this->go('admin', 'user');
    }

    public function usersreport() {

    }


}
