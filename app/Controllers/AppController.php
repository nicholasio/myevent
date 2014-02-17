<?php
namespace Controllers;

use Moxo\Controllers    as BaseController;

abstract class AppController extends BaseController\Controller {
    protected $authUser;
    protected $flashMessages;

    /**
     * Armazena todos os registros do model
     * É populado na subclasse
    */

    protected $_data;

    public function __construct() {
        $this->authUser      = \Moxo\Helpers\AuthHelper::getInstance();
        $this->flashMessages = $this->getHelper('flashMessages');
    }
    /**
     * Checa autenticação e redireciona caso não esteja autenticado
     */
    public function init() {
        if ( ! $this->authUser->isLogged() )
            $this->go(DEFAULT_MODULE, 'auth');

        $this->checkPermissions();
    }

    public function checkPermissions() {}

}
