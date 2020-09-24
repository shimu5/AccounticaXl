<?php 
class banks_default_schema {
    public $fields = array(
        'id',
        'account_id',
        'bank_name',
        'branch',
        'acc_no',
        'acc_name',
        'country_id',
    );
    
    public $validate = array(
        'id' => array('int'=>array(),'range'=>array('min'=>-2147483648, 'max'=>2147483647),),
        'account_id' => array('int'=>array(),'range'=>array('min'=>-2147483648, 'max'=>2147483647),),
        'bank_name' => array('length'=>array('max'=>30),),
        'branch' => array('length'=>array('max'=>30),),
        'acc_no' => array('length'=>array('max'=>255),),
        'acc_name' => array('length'=>array('max'=>255),),
        'country_id' => array('length'=>array('max'=>5),),
    );

    public $field_type = array(
        'id' => 'INT',
        'account_id' => 'INT',
        'bank_name' => 'VARCHAR',
        'branch' => 'VARCHAR',
        'acc_no' => 'VARCHAR',
        'acc_name' => 'VARCHAR',
        'country_id' => 'VARCHAR',
    );

    public $php_type = array(
        'id' => 'int',
        'account_id' => 'int',
        'bank_name' => 'string',
        'branch' => 'string',
        'acc_no' => 'string',
        'acc_name' => 'string',
        'country_id' => 'string',
    );
}

