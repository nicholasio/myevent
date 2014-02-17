<?php
namespace Controllers\Admin;

use \Controllers\AppController  as AppController;

class Reports extends AppController {
    use AdminController;

    public function init() {
        $this->preventDefault();
    }

    public function users() {
        $users = \Models\Usuario::fetchAll();


        header("Content-type: text/html; charset=utf-8");
        ob_start(); // Inicia o fluxo
        ?>
        <div style="text-align: center" >
            <img src="/uploads/<?php echo get_meta('logo_evento'); ?>" />
            <h3><?php echo get_meta('nome_evento'); ?></h3>
        </div>
        <br />
        <h5 style="text-align: center">Lista de Frequência Geral</h5>

        <table border="1" cellpadding="5px" cellspacing="0" style="border-collapse: collapse;" >
            <tr style="background: #BDA580; ">
                <th width="250px">Nome: </th>
                <th width="250px">E-mail: </th>
                <th width="300px">Assinatura: </th>
            </tr>
            <?php 
            $i = 0;
            foreach( $users as $user ) : ?>
                <tr <?php if ( $i % 2 == 0) :  ?>  style="background: #CCC " <?php endif; ?>>
                    <td> <?php echo $user->nomeCompleto; ?> </td>
                    <td> <?php echo $user->email; ?></td>
                    <td></td>
                </tr>
            <?php $i++; endforeach; ?>
        </table>
         
        <?php
        $html = ob_get_contents();
        ob_end_clean(); // Finaliza o fluxo
        
    
        
        // cria um novo container PDF no formato A4 com orientação customizada
        $mpdf = new \mPDF('pt','A4',12);
         
        // muda o charset para aceitar caracteres acentuados iso 8859-1 utilizados por mim no banco de dados e na geracao do conteudo PHP com HTML
        $mpdf->allow_charset_conversion=true;
        $mpdf->charset_in='utf-8';
         
        //Algumas configurações do PDF
        $mpdf->SetDisplayMode('fullpage');
        // modo de visualização
        $mpdf->SetHeader('Lista de frequência Geral - ' . get_meta('nome_evento') );

        $mpdf->SetFooter('Relatório gerado no dia {DATE j/m/Y-H:i}|{PAGENO}/{nb}| ' . get_meta('nome_evento'));
        
       
        // incorpora o corpo ao PDF na posição 2 e deverá ser interpretado como footage. Todo footage é posicao 2 ou 0(padrão).
        $mpdf->WriteHTML($html);
         
        // define um nome para o arquivo PDF
        $arquivo = date("ymdhis").'_lista_frequencia_geral.pdf';
         
        // gera o relatório
        $mpdf->Output($arquivo,'D');
         
        exit();
         
    }

    public function subevents() {
        $id = $this->getRequesterId();

        $bd = \Moxo\Banco::getInstance();
       
        $users = $bd->query("
            SELECT Usuarios.nomeCompleto as nomeCompleto, Eventos.nome as eventoNome,
                    SubEventos.nome as subeventoNome, Usuarios.email as email
             FROM Usuarios, Inscricoes, Eventos, SubEventos WHERE
                Usuarios.id = Inscricoes.idUsuarios AND Inscricoes.idSubEventos = SubEventos.id AND
                SubEventos.idEventos = Eventos.id AND SubEventos.id = {$id}
            ");

        header("Content-type: text/html; charset=utf-8");
        ob_start(); // Inicia o fluxo
        ?>
        <div style="text-align: center" >
            <img src="/uploads/<?php echo get_meta('logo_evento'); ?>" />
            <h3><?php echo get_meta('nome_evento'); ?></h3>
        </div>
        <br />
        <h5 style="text-align: center"><?php echo $users[0]->eventoNome . ' ' . $users[0]->subeventoNome; ?></h5>
        <table border="1" cellpadding="5px" cellspacing="0" style="border-collapse: collapse;" >
            <tr style="background: #BDA580; ">
                <th width="250px">Nome: </th>
                <th width="250px">E-mail: </th>
                <th width="300px">Assinatura: </th>
            </tr>
            <?php 
            $i = 0;
            foreach( $users as $user ) : ?>
                <tr <?php if ( $i % 2 == 0) :  ?>  style="background: #CCC " <?php endif; ?>>
                    <td> <?php echo $user->nomeCompleto; ?> </td>
                    <td> <?php echo $user->email; ?></td>
                    <td></td>
                </tr>
            <?php $i++; endforeach; ?>
        </table>
         
        <?php
        $html = ob_get_contents();
        ob_end_clean(); // Finaliza o fluxo
        
    
        
        // cria um novo container PDF no formato A4 com orientação customizada
        $mpdf = new \mPDF('pt','A4',12);
         
        // muda o charset para aceitar caracteres acentuados iso 8859-1 utilizados por mim no banco de dados e na geracao do conteudo PHP com HTML
        $mpdf->allow_charset_conversion=true;
        $mpdf->charset_in='utf-8';
         
        //Algumas configurações do PDF
        $mpdf->SetDisplayMode('fullpage');
        // modo de visualização
        $mpdf->SetHeader('Lista de frequência do ' .  $users[0]->eventoNome . ' ' . $users[0]->subeventoNome );

        $mpdf->SetFooter('Relatório gerado no dia {DATE j/m/Y-H:i}|{PAGENO}/{nb}| ' . get_meta('nome_evento'));
        
       
        // incorpora o corpo ao PDF na posição 2 e deverá ser interpretado como footage. Todo footage é posicao 2 ou 0(padrão).
        $mpdf->WriteHTML($html);
         
        // define um nome para o arquivo PDF
        $arquivo = date("ymdhis").'_lista_minicurso_'. slugify($users[0]->subeventoNome) .'.pdf';
         
        // gera o relatório
        $mpdf->Output($arquivo,'D');
         
        exit();

    }


}
