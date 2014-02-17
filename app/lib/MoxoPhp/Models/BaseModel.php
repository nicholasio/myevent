<?php
namespace Moxo\Models;
use Moxo;

class BaseModel {
    protected static $tableName;
    protected $id;

    protected $required = [];
    private   $__isRequired;
    private $errorMessages;

    public static function getTableName() {
        /*
            Late static binding to get the static tableName on the child class
        */
        return  static::$tableName;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    public function setRequired($bool){
        $this->__isRequired = $bool;
    }

    public function isRequired() {
        return $this->__isRequired;
    }

    public final function getBd() {
        $bd = Moxo\Banco::getInstance();
        $bd->_tabela = self::getTableName();

        return $bd;
    }


    /*
        Retorna todas as propriedades da classe filha em formato de array associativo
    */
    public final function getAllPropsArray() {
        $allProps = $this->getAllProps();
        $props = [];

        foreach($allProps as $prop) {
            $props[$prop->getName()] = $prop;
        }

        return $props;
    }

    /*
        Retorna todas os objetos de propriedade da classe filha
    */
    public final function getAllProps() {
        $reflection     = new \ReflectionClass($this);
        /*
            Pegando somente propriedades públicas
        */
        $props          = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);

        return $props;
    }

    /*
        Retorna todas as propriedades (exceto o id) definidas na classe filha (Um modelo)
        [ 'propriedade1' => valor1 ... 'propriedadeN' => valorN]
    */
    public final function getPropetiesToSave() {

        $props = $this->getAllProps();

        $arrProps       = [];

        foreach($props as $prop) {

            if( $this->isRequired() && in_array($prop->getName(), $this->required) &&
                ( is_null($this->{$prop->getName()}) || !isset($this->{$prop->getName()}))
            )
            {
                $this->addErrorMessage( ucfirst($prop->getName()) . ' é obrigatório(a)!');
            } else if(  isset($this->{$prop->getName()}) && ! is_null($this->{$prop->getName()}) )
                $arrProps[$prop->getName()] = $this->{$prop->getName()};
        }

        return $arrProps;
    }

    /*
        Retorna todos os nomes das propriedades da classe filha
    */
    public final function getKeyProperties() {
        $allProps = $this->getAllPropsArray();
        $allKeys = array_keys($allProps);

        return $allKeys;
    }

    /**
     * Adiciona uma mensagem de erro
     * @param String $message
     * @return none
     */
    public function addErrorMessage( $message ) {
        $this->errorMessages[] = $message;
    }

    /**
     * Retorna as mensagens de erro
     * @return String
     */
    public function getErrorMessages() {
        if ( !is_array($this->errorMessages) && empty($this->errorMessages) )
            return '';
        return implode($this->errorMessages, '<br />');
    }
}
