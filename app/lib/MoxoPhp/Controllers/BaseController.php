<?php
namespace Moxo\Controllers;

abstract class BaseController{
    protected $model;
    protected $viewName;
    protected $viewModule;
    protected $preventDefault = false;
    /**
     * Referência para a view
     */
    protected $__view;
    protected $params   = array();

    /**
     * Propriedades da view
     */
    public    $view;

    /**
     * Uso interno, emula um construtor
     * @return none
     */
    public final function pseudoConstruct() {
        $this->view =  new \stdClass();
    }

    /**
     * Retorna um Helper
     * @return Helper
     */
    public function getHelper( $name ) {
        switch ($name) {
            case 'flashMessages':
                return \Moxo\Helpers\FlashMessages::getInstance();
                break;
            default:
                # code...
                break;
        }

    }

    /**
     * Evita que view seja carregada automaticamente
     * @return none
     */
    public final function preventDefault() {
        $this->preventDefault = true;
    }

    public final function doNotPreventDefault() {
        $this->preventDefault = false;
    }

    public final function isPreventDefault() {
        return $this->preventDefault;
    }
    /**
     * Seta o nome da view
     * @param String $viewName
     * @return none
     */
    public final function setViewName($viewName) {
        $this->viewName = $viewName;
    }

    /**
     * Rertorna $viewName (padrão) caso $this->viewName não esteja definido
     * @param String $viewName
     * @return String
     */
    public final function getViewName($viewName) {
        if ( ! isset($this->viewName) )
            return $viewName;
        return $this->viewName;
    }

    public final function getViewModule($module) {
        if ( $module == DEFAULT_MODULE )
            $middlePath = '';
        else
            $middlePath = $module;

        if( isset($this->viewModule) && !is_null($this->viewModule) )
            $middlePath = $this->viewModule;

        return $middlePath;
    }
    public final function setViewModule($module) {
        $this->viewModule = $module;
    }

    public final function setModel($model) {
        $this->model = $model;
    }

    public final function getModel() {
        $model = $this->model;
        if (is_null($model) ) {
            $controllerClass =  get_called_class();
            $controllerName  = explode('\\',$controllerClass);
            $controllerName  = $controllerName[count($controllerName) - 1];
            $modelClass      = "\\Models\\{$controllerName}";
            try{
                $model = new $modelClass();
            } catch(\Exception $e) {}
        }

        return $model;
    }

    public final function setView($view) {
        $this->__view = $view;
    }

    public final function getView() {
        return $this->__view;
    }

    /**
     * Seta um parâmetro
     * @param Array $params
     */
    public final function setParams($params) {
        $this->params = $params;
    }
    /**
     * Retorna um parâmetro caso este exista
     * @param String $name
     * @return String
     */

    public final function getParam($name = null) {
        if ( ! is_null($name) ) {
            if (isset($this->params[$name]))
                return $this->params[$name];
            else
                return null;
            }
        else
            return $this->_params;
    }

    /**
     * Verifica se é uma Requisição POST
     * @return bool
     */
    public final function isPostRequest(){
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] )
            return true;

        return false;
    }

    /**
     * Verifica se é uma Requisição GET
     * @return bool
     */
    public final function isGetRequest(){
        if ( 'GET' == $_SERVER['REQUEST_METHOD'] )
            return true;

        return false;
    }

    /**
     * Retorna o módulo corrent
     * @return String - nome do modulo
     */
    public final function getCurrentModule() {
        $router = \Moxo\Router::getInstance();
        return $router->getModule();
    }

    /**
     * Define ações para serem executadas
     * automaticamente no carregamento do controller
     */
    public function init() {}

    /**
     * Define ações para serem executadas após a action
     */
    public function shutdown() {}
}
