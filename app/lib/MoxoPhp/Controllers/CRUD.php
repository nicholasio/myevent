<?php
namespace Moxo\Controllers;

abstract class CRUD extends BaseController {

    /**
     * Popula as informações da view com as informacoes enviadas via $_POST
     * @return none
     */
    public function populateViewFromPost() {
        $data = $_POST;

        foreach($data as $key => $value) {
            if ( ! empty($value) )
                $this->view->{$key} = $value;
        }
    }

    /**
     * Popula o model definido no controller com as informacoes enviadas via $_POST
     * @param Model $model
     * @return none
     */
    public function populateModelFromPost($model) {
        $data = $_POST;
        unset($data['id']);

        foreach($data as $key => $value) {
            if ( ! is_null($value) )
                $model->{$key} = $value;
        }

    }

    /**
     * Retorna o ID requisitado
     * @return int
     */
    protected function getRequesterId() {
        $id  = $this->getPost('id');

        if ( is_null($id) )
            $id  =  $this->getParam('id');

        return $id;
    }

    protected function getRequester( $name ) {
        $param = $this->getPost( $name );

        if( is_null( $param ) ) 
            $param = $this->getParam( $param );

        return $param;
    }

    /**
     * Remove um registro
     * @return bool
     */
    public function del($sucessMessage = 'Registro removido com sucesso',
                        $errorMessage = 'Erro ao remover registro: ') {
        $this->preventDefault();
        $id = $this->getParam('id');

        $flashMessages = $this->getHelper('flashMessages');

        if ( is_null($id) ) {
            $flashMessages->add('e', 'Id não especificado');
        } else {
            $model   = $this->getModel();
            $model->setId($id);
            $model->populate();

            if ( $model->delete() ) {
                $flashMessages->add('s', $sucessMessage);
                return true;
            } else {
                $flashMessages->add('e', $errorMessage . $model->getErrorMessages());
                return false;
            }
        }
    }

    /**
     * Carrega form para edição de um usuário
     * @return none
     */
    public function edit() {
        if ( $this->isPostRequest() ) {
            if ( ! $this->save() ) {
                $this->populateViewFromPost();
            }
        } else {
            $this->view->id = $this->getParam('id');
        }

    }

    /**
     * Carrega formulário de cadastro
     * @return none
     */
    public function novo($viewName = 'edit') {
        $this->setViewName($viewName);

        if ( $this->isPostRequest() ) {
            if ( ! $this->save() ) {
                $this->populateViewFromPost();
            }
        }
    }

    /**
     * Salva as informacoes enviadas via $_POST no banco de dados
     * @param String $sucessMessage
     * @param String $errorMessage
     * @return int
     */
    public function save($sucessMessage = 'Informações salvas com sucesso',
                         $errorMessage  = null) {
        if ( $this->isPostRequest() ) {

            $id = $this->getRequesterId();

            $model = $this->getModel();
            $model->setId($id);
            $model->populate();

            $this->populateModelFromPost($model);

            $result = $model->save();

            if ( $result !== false ) {
                $this->preventDefault();
                $_id = is_null($id) ? $result : $id;
                $this->flashMessages->add('s', $sucessMessage);

                return $_id;
            } else{
                $errMsg = $model->getErrorMessages();

                if( !is_null($errorMessage) )
                    $errMsg = $errorMessage;

                $this->flashMessages->add('e', $errMsg);

                return -1;
            }

        }
    }
}
