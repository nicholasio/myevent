<?php
namespace Controllers;

class Usuario extends AppController{

    public function __construct() {
        parent::__construct();
        $this->_data = \Models\Usuario::fetchAll();
    }

    /**
     * Pre-populate model and returns model and id
     * @return Array
     */
    private function preSave() {
        $id   = $this->getRequesterId();

        $user = $this->getModel();
        $user->setId($id);
        $user->populate();

        $this->populateModelFromPost($user);



        if( ! filter_var($user->email, FILTER_VALIDATE_EMAIL ) ) {
            $this->flashMessages->add('e', 'Email inválido');
            return false;
        }

        //Se estamos cadastrando um usuário
        if ( is_null($id) ) {
            $user->created = date("Y-m-d H:i:s");
            $senha         = $this->getPost('senha');
            $confirm_senha = $this->getPost('confirm_senha');
            if ( $confirm_senha != $senha)  {
                $this->flashMessages->add('e','Senhas não conferem');
                return false;
            }

            $user->setSenha($senha);

            //Validando email
            $confirm_email = $this->getPost('confirm_email');
            if( $user->email != $confirm_email ) {
                $this->flashMessages->add('e', 'Email de confirmação não confere');
                return false;
            }

        }

        //Se não tem tipo predefinido até este momento é um participante
        if ( is_null($user->tipo) || empty($user->tipo) || $user->tipo == 'PA' ) {
            if ( ! validarCPF($user->cpf)  ) {
                $this->flashMessages->add('e', 'CPF inválido');
                return false;
            }
            if ( is_null($user->celular) || empty($user->celular) ){
                $this->flashMessages->add('e', 'Celular é obrigatório');
                return false;
            }
        }

        foreach($this->_data as $d){
            if( ($user->getId() == NULL || $user->getId() != $d->id)  &&
                 $user->email == $d->email) {
                $this->flashMessages->add('e', 'Este e-mail já está cadastrado');
                return false;
            }
            if( ($user->getId() == NULL || $user->getId() != $d->id)  &&
                ! empty($user->cpf) && $user->cpf == $d->cpf) {
                $this->flashMessages->add('e', 'Este CPF já está cadastrado');
                return false;
            }
        }

        return ['id' => $id, 'model' => $user];
    }

    private function posSave($params, $onSucess, $onError){
        $id     = $params['id'];
        $user   = $params['model'];
        $result = $user->save();

        $_id = is_null($id) ? $result : $id;
        if ( $result !== false ) {
            $this->preventDefault();
            $onSucess($_id, $user);
        } else {
            $onError($_id, $user);
        }

    }

    public function newParticipant() {
        //Pre-populate
        $result = $this->preSave();
        $flashMessages = $this->flashMessages;
        if( $result !== false ) {
            $result['model']->status    = 'AP'; //Aguardando Pagamento
            $result['model']->tipo      = 'PA'; //É um participante
            if ( ! empty($result['model']->submissao) ) {
                switch( $result['model']->submissao ) {
                    case sha1('S'):
                        $result['model']->submissao = 'S';
                        break;
                    case sha1('N'):
                        $result['model']->submissao = 'N';
                        break;
                    default:
                        $flashMessages->add('e', 'Tentativa de invasão detectada (Submissão)');
                        return false;
                }
            }
            
            $this->posSave($result,
                function($id) use ($flashMessages) {
                    $flashMessages->add('s', 'Conta Criada com sucesso, faça o login');
                    $this->go(DEFAULT_MODULE,'auth','login');
                }, function($id, $user) use ($flashMessages) {
                    $flashMessages->add('e', $user->getErrorMessages());
                });
        } else {
            return false;
        }
    }

    public function dieIfPermissionDenied(){
        $authUser = $this->authUser->getUserData();
        if( $authUser->tipo == 'PA' && $this->getRequesterId() != $authUser->_id )
            $this->go($this->getCurrentModule(),'index');
    }

    public function edit(){
        $this->dieIfPermissionDenied();

        $this->setViewModule('admin');
        parent::edit();
    }
    /**
     * Altera a senha de um usuário
     * @return none
     */
    public function change_pass() {
        $this->preventDefault();
        if (  $this->isPostRequest() ) {
            $id = $this->getPost('id');

            if ( is_null($id) )
                return;
            $oldPass     = $this->getPost('old_pass');
            $newPass     = $this->getPost('new_pass');
            $confirmPass = $this->getPost('confirm_pass');
            $flashMessages = $this->getHelper('flashMessages');

            if ( ! is_null($oldPass) && ! is_null($newPass) && ! is_null($confirmPass) ){
                $authUser    = $this->authUser->getUserData();
                $user       = new \Models\Usuario($id);

                $oldPass     = sha1($oldPass);
                $newPass     = sha1($newPass);
                $confirmPass = sha1($confirmPass);

                if ( $user->senha == $oldPass ) {
                    if ( $newPass == $confirmPass ) {
                        $user->senha = $newPass;
                        if ( $user->save() !== false ) {
                            if ( $user->getId() == $authUser->_id ) {
                                $_SESSION['user']->senha = $newPass;
                            }

                            $this->flashMessages->add('s', 'Senha alterada com sucesso');
                            $this->go($this->getCurrentModule(), 'user', 'edit', array('id' => $user->getId()));
                            return;
                        } else {
                            $this->flashMessages->add('e', 'Problemas na sua requisição');
                        }
                    } else {
                        $this->flashMessages->add('e', 'Senhas não são iguais');
                    }
                } else{
                    $this->flashMessages->add('e', 'Senha atual incorreta');
                }
            } else {
                $this->flashMessages->add('e', 'Preencha todos os campos');
            }
            $this->go('admin','user', 'pass', array('id' => $id) );
        }


    }

    /**
     * Formulário para alteração de senha
     * @return none
     */
    public function pass() {
        $this->dieIfPermissionDenied();

        $this->setViewModule('admin');
        $this->view->id = $this->getParam('id');
    }
    /**
     * Salva informações dos usuários
     * @return none
     */
    public function save() {
        if ( !$this->isPostRequest() ) return;
        $this->dieIfPermissionDenied();

        $flashMessages = $this->flashMessages;
        $authUser      = $this->authUser;

        $result = $this->preSave();
        if ($result === false) return;

        $_user  = $result['model'];
        switch( $_user->status ) {
            case sha1('AP'):
                $_user->status = 'AP';
                break;
            case sha1('PG'):
                $_user->status = 'PG';
                break;
            default: //Não salva no banco
                $result = false;
                $flashMessages->add('e', 'Tentativa de invasão detectada (Status)');
        }

        switch( $_user->tipo ) {
            case sha1('AD'):
                $_user->tipo = 'AD';
                break;
            case sha1('PA'):
                $_user->tipo = 'PA';
                break;
            default:
                $result = false;
                $flashMessages->add('e', 'Tentativa de invasão detectada (tipo)');
        }

        switch( $_user->submissao ) {
            case sha1('S'):
                $_user->submissao = 'S';
                break;
            case sha1('N'):
                $_user->submissao = 'N';
                break;
            default:
                $result = false;
                $flashMessages->add('e', 'Tentativa de invasão detectada (Submissão)');
        }

        if( $result === false ) return;

        //Enviando Comprovante
        $this->sendreceipt( false );

        $this->posSave($result,
            function($id, $user) use ($flashMessages, $authUser) {
                $flashMessages->add('s', 'Conta Atualizada com sucesso');

                /*
                    Conta do usuário logado foi alterado,
                    é preciso atualizar session
                */
                if ( $id == $authUser->getUserData()->_id ) {
                    $authUser->update($user);
                }

                $this->go($this->getCurrentModule(),'user','edit', ['id' => $id] );
            },
            function($id, $user) use ($flashMessages) {
                $flashMessages->add('e', $user->getErrorMessages());
            }
        );

    }

    public function sendreceipt( $redirect = true) {
        $this->preventDefault();

        if ( $this->isPostRequest() ) {
            if ( ! empty( $_FILES['payment-receipt-file']['name'] ) ) {
                $upload = \Moxo\Helpers\UploadHelper::factory('uploads');
                $upload->file($_FILES['payment-receipt-file']);

                /*$validation = new validation;

                $upload->callbacks($validation, array('check_name_length'));*/

                $results = $upload->upload();

                
                $id   = $this->getRequesterId();
                $user = $this->getModel();
                $user->setId($id);

                //Sobrescrevendo comprovante de pagamento anterior
                if ( ! empty($user->payment_receipt) ) {
                    unlink( UPLOADS_DIR . $user->payment_receipt );
                    $user->payment_receipt = '';
                }

                $user->payment_receipt = $results['filename'];
                $user->setRequired(false);
                if ( $user->save() && $redirect == true ) {
                    $this->flashMessages->add('s', 'Comprovante de pagamento enviado com sucesso');
                    $this->go($this->getCurrentModule(), 'user', 'edit', ['id' => $id]);
                }

            }
        }

    }
}
