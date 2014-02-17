<?php
namespace Moxo\Views;

use Moxo\Models as BaseModel;

class View extends BaseView {

    /**
     * Guarda todas as informações
     */
    private $data;

    /**
     * Caminho completo para a view
     */
    private $___viewTpl; // 3 '_'

    /**
     * Referência para o controller chamador
     **/
    private $___controller;

    /**
     * Instância uma view, os atributos $model e $data serao adicionados
     * via reflexão a instância
     * @param type $viewTpl
     * @param type $model
     * @param type $data
     * @return type
     */
    public function __construct( $viewTpl, BaseModel\Model $model = null, $data = null, $controller = null) {
        $this->___viewTpl    = $viewTpl;
        $this->___controller = null;

        $data                = (array) $data;
        $arrModel            = is_null($model) ? [] : (array) $model;
        $arrData             = is_null($data) ? $arrModel : array_merge($arrModel, $data);

        $this->data          = (object) $arrData;
    }

    /**
     * Seta o controller chamador
     * @param Controller $controller
     * @return none
     */
    public final function setController($controller) {
        $this->___controller;
    }

    /**
     * Retorna o controller chamador
     * @return Controller | null
     */
    public final function getController() {
        return $this->___controller;
    }

    /**
     * Retorna o valor do parâmetro desejado se ele existir
     * @param String $name
     * @return Mixed
     */
    public function __get( $name ) {
        if ( ! empty($this->data) && isset($this->data[$name]) )
            return $this->data[$name];
        return null;
    }

    /**
     * Exibe o template
     */
    public final function show(){
        $user = \Moxo\Helpers\SessionHelper::getInstance()->selectSession('user');
        global $___MoxoTwig___;

        echo $___MoxoTwig___->render($this->___viewTpl, [
            'this' => $this->data,
            'user' => $user
        ]);
    }
}
