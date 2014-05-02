<?php
namespace Controllers\Admin;

use \Controllers\AppController  as AppController;

class Index extends AppController {
    use AdminController;

    public function index_action() {
        $userModel = new \Models\Usuario();
        $this->view->participants = $userModel
                                    ->findAll(['tipo' => 'PA'],
                                        'created DESC',
                                    ['0','4']);
        $bd = \Moxo\Banco::getInstance();
        $estatisticas = [];
        /*$estatisticas['Minicursos'] = $bd->query('
            SELECT count(*) as total FROM Eventos, SubEventos WHERE
            Eventos.nome = "Minicursos" AND Eventos.id = SubEventos.idEventos
            ')[0]->total;
        $estatisticas['Palestras'] = $bd->query('
            SELECT count(*) as total FROM Eventos, SubEventos WHERE
            Eventos.nome = "Palestra" AND Eventos.id = SubEventos.idEventos
            ')[0]->total; */
        $estatisticas['Inscritos'] = $bd->query('
            SELECT count(*) as total FROM Usuarios WHERE tipo = "PA"
            ')[0]->total;
        $estatisticas['Inscrições confirmadas'] = $bd->query('
            SELECT count(*) as total FROM Usuarios WHERE tipo = "PA" AND status = "PG"
            ')[0]->total;
        $estatisticas['Inscrições não confirmadas'] = $bd->query('
            SELECT count(*) as total FROM Usuarios WHERE tipo = "PA" AND status = "AP"
            ')[0]->total;
        $estatisticas['Credenciados'] = $bd->query('
            SELECT count(*) as total FROM Usuarios WHERE tipo = "PA" AND presence = 1
            ')[0]->total;
        $this->view->estatisticas = $estatisticas;

    }

}
