<?php 
class countries_default_schema {
    public $fields = array(
        'id',
        'country',
        'iso2',
        'iso3',
        'noc',
    );
    
    public $validate = array(
        'id' => array('int'=>array(),'range'=>array('min'=>-2147483648, 'max'=>2147483647),),
        'country' => array('length'=>array('max'=>255),),
        'iso2' => array('length'=>array('max'=>2),),
        'iso3' => array('length'=>array('max'=>3),),
        'noc' => array('length'=>array('max'=>3),),
    );

    public $field_type = array(
        'id' => 'INT',
        'country' => 'VARCHAR',
        'iso2' => 'CHAR',
        'iso3' => 'CHAR',
        'noc' => 'CHAR',
    );

    public $php_type = array(
        'id' => 'int',
        'country' => 'string',
        'iso2' => 'string',
        'iso3' => 'string',
        'noc' => 'string',
    );
}

