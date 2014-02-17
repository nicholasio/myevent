<?php
namespace Models;

use Moxo\Models\Model as BaseModel;

class Submissao extends BaseModel {

    protected static $tableName = "Submissoes";
    protected $required         = ['arquivo_inicial', 'author_id', 'idEventos'];

    public $author_id;
    public $idEventos;
    public $arquivo_inicial;
    public $arquivo_final;
    public $titulo_trabalho;
    public $created;


    public function __construct($id = null) {
        parent::__construct($id);
    }

    public function save() {

        if ( is_null($this->id) ) 
            $this->created = date("Y-m-d H:i:s");

        if ( ! empty( $_FILES['arquivo_inicial'] ) ) {
            $upload = \Moxo\Helpers\UploadHelper::factory('uploads/artigos');
            $upload->file($_FILES['arquivo_inicial']);

            /*$validation = new validation;

            $upload->callbacks($validation, array('check_name_length'));*/

            $results = $upload->upload();
            

            //Sobrescrevendo arquivo anterior
            if ( ! empty($this->arquivo_inicial) ) {
                unlink( UPLOADS_DIR . '/artigos/' . $this->arquivo_inicial );
                $this->arquivo_inicial = '';
            }

            $this->arquivo_inicial = $results['filename'];


        }
        
        $submission_id = parent::save();    
        $bd     =  \Moxo\Banco::getInstance();

        if( is_array($_POST['autores']) && ! empty($_POST['autores']) ) {
            foreach( $_POST['autores'] as $autor_id ) {

                $sql = "INSERT INTO Submissoes_autores (Submissoes_id, Usuarios_id) values ('{$submission_id}', '{$autor_id}')";
               /* $stmt = $bd->getCon()->prepare($sql);
                $stmt->bindParam(':submission_id', $submission_id, \PDO::PARAM_INT);
                $stmt->bindParam(':autor_', $autor_id, \PDO::PARAM_INT);

                $stmt->execute();*/
                //echo $sql;
                $bd->exec($sql);
            }
        }

        return $submission_id;



    }

}

