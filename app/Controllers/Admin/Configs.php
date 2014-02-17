<?php
namespace Controllers\Admin;

use \Controllers\AppController  as AppController;
class Configs extends AppController {
    use AdminController;

    public function __construct() {
        parent::__construct();        
    }
    
    public function index_action() { }

    public function save() {
        $this->preventDefault();

        if ( $this->isPostRequest() ) {

            $data = $this->getPost('meta_data');

            if ( ! empty($data) ) {
                foreach( $data as $meta_key => $meta_value){
                    update_meta( $meta_key, $meta_value );
                }    
            }
            
            
            if ( ! empty($_FILES['logo_evento']['name']) ) {

                $upload = \Moxo\Helpers\UploadHelper::factory('uploads');
                $upload->file($_FILES['logo_evento']);


                $results = $upload->upload();

                $logoFile = get_meta('logo_evento');

                //Sobrescrevendo logomarca anterior
                if ( ! empty($logoFile) ) {
                    unlink( UPLOADS_DIR . $logoFile );
                }

                update_meta( 'logo_evento' , $results['filename'] );
            }
            
        
            $this->flashMessages->add('s', 'Configurações Salvas com sucesso');
            $this->go( $this->getCurrentModule(), 'configs');  
        }   
    }

}

