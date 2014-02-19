<?php
namespace Models;

use Moxo\Models\Model as BaseModel;

class Submissao extends BaseModel {

    protected static $tableName = "Submissoes";
    protected $required         = ['titulo_trabalho','author_id', 'idEventos'];

    public $author_id;
    public $idEventos;
    public $arquivo_inicial;
    public $arquivo_final;
    public $titulo_trabalho;
    public $status; 
    public $comentarios;
    public $created;


    public function __construct($id = null) {
        parent::__construct($id);
    }

    public function save() {

        if ( is_null($this->id) ) 
            $this->created = date("Y-m-d H:i:s");

        if ( ! empty( $_FILES['file_arquivo_inicial'] ) ) {
            $upload = \Moxo\Helpers\UploadHelper::factory('uploads/artigos');
            $upload->file($_FILES['file_arquivo_inicial']);

            //Sobrescrevendo arquivo anterior (Precisa ajeitar)
            /*if ( ! empty($this->arquivo_inicial) ) {
                unlink( UPLOADS_DIR . 'artigos/' . $this->arquivo_inicial );
                $this->arquivo_inicial = '';
            }*/

            /*$validation = new validation;

            $upload->callbacks($validation, array('check_name_length'));*/

            $results = $upload->upload();
            



            $this->arquivo_inicial = $results['filename'];


        }
        $submission_id = parent::save();    
        if ( $submission_id ) {
            $bd     =  \Moxo\Banco::getInstance();
            $sql = "DELETE FROM Submissoes_autores WHERE Submissoes_id = {$submission_id}";
            $bd->exec($sql);
            if( is_array($_POST['autores']) && ! empty($_POST['autores']) ) {


                foreach( $_POST['autores'] as $autor_id ) {
                    $sql = "INSERT INTO Submissoes_autores (Submissoes_id, Usuarios_id) values ('{$submission_id}', '{$autor_id}')";
                    $bd->exec($sql);
                }
            }
        }
        

        return $submission_id;



    }

}

