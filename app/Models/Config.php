<?php
namespace Models;

use Moxo\Models\Model as BaseModel;

class Config extends BaseModel {

	use \Moxo\Singleton;

    protected static $tableName = "Configs";
    protected $required         = ['meta_key'];

    public $meta_key;
    public $meta_value;

    public function __construct($id = null) {
        parent::__construct($id);
    }

}

