<?php
namespace Moxo\Views;
/**
 * Métodos utilitários diversos para Views
 * @package Moxo\Views
 */
abstract class BaseView {

    /**
     * Dado um controller e uma action gera a url correta,
     * caso as rotas não estejam definidas para o controller não retorna nada
     * @param String $controller nome da rota para o controller
     * @param String $action
     * @param Array $data
     * @return String Url formatada
     */
    public final function getUrl($module, $controller, $action = '', Array $data = null){
        $router    = \Moxo\Router::getInstance();
        $routerMap = $router->getRouterMap();

        if( ! array_key_exists($module, $routerMap) )
            return;

        if( ! array_key_exists($controller, $routerMap[$module]) )
            return;

        if( $module == DEFAULT_MODULE )
            $module = '';

        $url       = ROOT_URL . '/' . $module . '/' . $controller . '/' . $action;
        return $url;

    }

}
