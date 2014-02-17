<?php

function __getMoxoRouterMap(){

    /*
        Para adicionar rotas modifique o Array
    */

    $__MoxoRouterMap =  [
                DEFAULT_MODULE     => [
                    'index' => '\Controllers\Index',
                    'auth'  => '\Controllers\Auth',
                    'user'  => '\Controllers\Usuario'
                ],
                'congressista' => [
                    'index'     => '\Controllers\Congressista\Index',
                    'inscricao' => '\Controllers\Congressista\Inscricao',
                    'submissao' => '\Controllers\Congressista\Submissao'
                ],
                'admin' => [
                    'index'     => '\Controllers\Admin\Index',
                    'user'      => '\Controllers\Admin\Usuario',
                    'evento'    => '\Controllers\Admin\Evento',
                    'subevento' => '\Controllers\Admin\SubEvento',
                    'configs'   => '\Controllers\Admin\Configs',
                    'reports'   => '\Controllers\Admin\Reports'
                ]
    ];

    return $__MoxoRouterMap;
}
