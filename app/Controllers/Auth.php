<?php
namespace Controllers;

use Moxo\Controllers	as BaseController;
use Moxo\Helpers		as Helpers;


class Auth extends BaseController\Controller {

	public function index_action() {
        $auth = Helpers\AuthHelper::getInstance();
        if ( $auth->isLogged() ){
            switch($auth->getUserData()->tipo){
                case 'PA':
                    $this->go('congressista','index');
                    break;
                case 'AD':
                    $this->go('admin', 'index');
                    break;
                default:
                    $this->go(DEFAULT_MODULE, 'auth', 'login');
                    break;
            }
        } else
            $this->go(DEFAULT_MODULE, 'auth', 'login');
	}

	public function login() {
        if ( $this->isPostRequest() ) {
            $userName = $this->getPost('email');
            $password = $this->getPost('senha');

            $user = new \Models\Usuario();

            $auth = Helpers\AuthHelper::factory(
                'Usuarios', 'email', 'senha',
                'lastLogin', $user
            );

            $auth->setUser($userName);
            $auth->setPass($password);

            if ( $auth->login() ) {
                $user = $auth->getUserData();
                switch ($user->tipo) {
                    case 'AD':
                        $this->go('admin','index');
                        break;
                    case 'PA':
                        $this->go('congressista','index');
                        break;
                    default:
                        die("Possível problema de segurança, contacte o administrador");
                        break;
                }

            } else {
                $this->getHelper('flashMessages')->
                add('e', 'Login ou senhas incorretas');
            }
        }

	}

    public function logout() {
        $this->preventDefault();
        $auth = Helpers\AuthHelper::getInstance();

        if ( $auth->isLogged() ) {
            $auth->logout();
        }

        $this->go(DEFAULT_MODULE, 'auth');
    }

    public function newaccount() {
        $this->getHelper('flashMessages')->add('e', "Inscrições encerradas!");
        $this->go(DEFAULT_MODULE, 'auth');
        if ( $this->isPostRequest() ) {
            $this->preventDefault();
            $user   = new \Controllers\Admin\Usuario();
            $result = $user->newParticipant();
            if ($result === false ) {
                $this->view = (object) $_POST;
                $this->doNotPreventDefault();
            }
        }
    }

    public function passrecovery() {
       if( ! $this->isPostRequest() ) return;

       $user  = new \Models\Usuario();
       $email = $this->getPost('email');
       $cpf   = $this->getPost('cpf');

       if ( $user->find(['email' => $email, 'cpf' => $cpf]) ) {
            $novaSenha = rand(1, 9999);
            $user->setSenha($novaSenha);

            $_email = new \Moxo\Helpers\EmailHelper(
                get_meta('email_principal'), $email,
                '[' . get_meta('nome_evento') . '] Recuperar senha de acesso',
                'A sua nova senha de acesso ao ' . get_meta('nome_evento') . ' é ' . $novaSenha);

            if ( $user->save() && $_email->send() ) {
                $this->getHelper('flashMessages')->
                add('s', 'Uma nova senha será enviada para a sua conta de email (Verifique a sua caixa de spam!)');

            } else {
                $this->getHelper('flashMessages')->
                add('e', 'Problemas na sua solicitação');
            }

       } else {
            $this->getHelper('flashMessages')->
            add('e', 'Conta inexistente');
       }
    }

}
