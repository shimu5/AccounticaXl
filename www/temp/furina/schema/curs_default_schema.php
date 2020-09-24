<?php 
class curs_default_schema {
    public $fields = array(
        'id',
        'name',
        'sign',
        'base',
    );
    
    public $validate = array(
        'id' => array('int'=>array(),'range'=>array('min'=>-2147483648, 'max'=>2147483647),),
        'name' => array('length'=>array('max'=>30),),
        'sign' => array('length'=>array('max'=>10),),
        'base' => array('int'=>array(),'range'=>array('min'=>-128, 'max'=>127),),
    );

    public $field_type = array(
        'id' => 'INT',
        'name' => 'VARCHAR',
        'sign' => 'VARCHAR',
        'base' => 'TINYINT',
    );

    public $php_type = array(
        'id' => 'int',
        'name' => 'string',
        'sign' => 'string',
        'base' => 'int',
    );
}

