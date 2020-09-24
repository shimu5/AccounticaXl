<?php 
class accounts_default_schema {
    public $fields = array(
        'id',
        'type',
        'cur_id',
        'opening_balance',
        'user_id',
        'opening_date',
        'last_balance',
        'last_update',
    );
    
    public $validate = array(
        'id' => array('int'=>array(),'range'=>array('min'=>-2147483648, 'max'=>2147483647),),
        'type' => array('length'=>array('max'=>10),),
        'cur_id' => array('length'=>array('max'=>5),),
        'opening_balance' => array('double'=>array("precision"=>array("M"=>20, "D"=>4)),),
        'user_id' => array('int'=>array(),'range'=>array('min'=>-2147483648, 'max'=>2147483647),),
        'opening_date' => array('date'=>array(),),
        'last_balance' => array('double'=>array("precision"=>array("M"=>20, "D"=>4)),),
        'last_update' => array('date'=>array(),),
    );

    public $field_type = array(
        'id' => 'INT',
        'type' => 'VARCHAR',
        'cur_id' => 'VARCHAR',
        'opening_balance' => 'DOUBLE',
        'user_id' => 'INT',
        'opening_date' => 'DATE',
        'last_balance' => 'DOUBLE',
        'last_update' => 'DATE',
    );

    public $php_type = array(
        'id' => 'int',
        'type' => 'string',
        'cur_id' => 'string',
        'opening_balance' => 'double',
        'user_id' => 'int',
        'opening_date' => 'string',
        'last_balance' => 'double',
        'last_update' => 'string',
    );
}

