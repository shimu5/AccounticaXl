<?php 
class auth_sessions_default_schema {
    public $fields = array(
        'id',
        'session_id',
        'login_time',
        'last_impression',
        'ip',
        'user_id',
        'model',
    );
    
    public $validate = array(
        'id' => array('int'=>array(),'range'=>array('min'=>-2147483648, 'max'=>2147483647),),
        'session_id' => array('length'=>array('max'=>100),),
        'login_time' => array('datetime'=>array(),),
        'last_impression' => array('datetime'=>array(),),
        'ip' => array('length'=>array('max'=>20),),
        'user_id' => array('int'=>array(),'range'=>array('min'=>-2147483648, 'max'=>2147483647),),
        'model' => array('length'=>array('max'=>32),),
    );

    public $field_type = array(
        'id' => 'INT',
        'session_id' => 'VARCHAR',
        'login_time' => 'DATETIME',
        'last_impression' => 'DATETIME',
        'ip' => 'VARCHAR',
        'user_id' => 'INT',
        'model' => 'VARCHAR',
    );

    public $php_type = array(
        'id' => 'int',
        'session_id' => 'string',
        'login_time' => 'string',
        'last_impression' => 'string',
        'ip' => 'string',
        'user_id' => 'int',
        'model' => 'string',
    );
}

