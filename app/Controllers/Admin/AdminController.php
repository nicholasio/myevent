<?php
namespace Controllers\Admin;


trait AdminController {
    /**
     * Checa permissão
     */
    public function checkPermissions() {
        if (  $this->authUser->getUserData()->tipo == 'PA' ) //Participante
            $this->go('congressista', 'index');
        else if (  $this->authUser->getUserData()->tipo != 'AD' ) //Admin
            $this->go(DEFAULT_MODULE, 'auth', 'login');
    }

    public function index_action() {
        $this->setViewName('list');
    }

}
