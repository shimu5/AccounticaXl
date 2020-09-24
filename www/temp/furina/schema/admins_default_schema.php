<?php 
class admins_default_schema {
    public $fields = array(
        'id',
        'user_name',
        'password',
        'name',
        'phone',
        'email',
        'address',
        'country_id',
        'usertype',
        'flag',
        'status',
        'customers',
        'vendors',
    );
    
    public $validate = array(
        'id' => array('int'=>array(),'range'=>array('min'=>-2147483648, 'max'=>2147483647),),
        'user_name' => array('length'=>array('max'=>60),),
        'password' => array('length'=>array('max'=>30),),
        'name' => array('length'=>array('max'=>60),),
        'phone' => array('length'=>array('max'=>30),),
        'email' => array('length'=>array('max'=>30),),
        'address' => array(),
        'country_id' => array('length'=>array('max'=>5),),
        'usertype' => array('int'=>array(),'range'=>array('min'=>-2147483648, 'max'=>2147483647),),
        'flag' => array('int'=>array(),'range'=>array('min'=>-128, 'max'=>127),),
        'status' => array('int'=>array(),'range'=>array('min'=>-2147483648, 'max'=>2147483647),),
        'customers' => array(),
        'vendors' => array(),
    );

    public $field_type = array(
        'id' => 'INT',
        'user_name' => 'VARCHAR',
        'password' => 'VARCHAR',
        'name' => 'VARCHAR',
        'phone' => 'VARCHAR',
        'email' => 'VARCHAR',
        'address' => 'TEXT',
        'country_id' => 'VARCHAR',
        'usertype' => 'INT',
        'flag' => 'TINYINT',
        'status' => 'INT',
        'customers' => 'TEXT',
        'vendors' => 'TEXT',
    );

    public $php_type = array(
        'id' => 'int',
        'user_name' => 'string',
        'password' => 'string',
        'name' => 'string',
        'phone' => 'string',
        'email' => 'string',
        'address' => 'string',
        'country_id' => 'string',
        'usertype' => 'int',
        'flag' => 'int',
        'status' => 'int',
        'customers' => 'string',
        'vendors' => 'string',
    );
}

