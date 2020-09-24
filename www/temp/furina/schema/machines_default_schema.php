<?php 
class machines_default_schema {
    public $fields = array(
        'id',
        'ip',
        'ip_alias',
        'server_type',
        'port',
        'db_name',
        'host',
        'password',
        'flag',
        'type',
        'status',
        'last_admin_id',
        'sync_start_date',
        'start_status',
    );
    
    public $validate = array(
        'id' => array('int'=>array(),'range'=>array('min'=>-2147483648, 'max'=>2147483647),),
        'ip' => array('int'=>array(),'range'=>array('min'=>0, 'max'=>4294967296),),
        'ip_alias' => array('length'=>array('max'=>255),),
        'server_type' => array('int'=>array(),'range'=>array('min'=>-32768, 'max'=>32767),),
        'port' => array('int'=>array(),'range'=>array('min'=>-2147483648, 'max'=>2147483647),),
        'db_name' => array('length'=>array('max'=>255),),
        'host' => array('length'=>array('max'=>255),),
        'password' => array('length'=>array('max'=>255),),
        'flag' => array('int'=>array(),'range'=>array('min'=>-2147483648, 'max'=>2147483647),),
        'type' => array('int'=>array(),'range'=>array('min'=>-2147483648, 'max'=>2147483647),),
        'status' => array('int'=>array(),'range'=>array('min'=>-2147483648, 'max'=>2147483647),),
        'last_admin_id' => array('int'=>array(),'range'=>array('min'=>-2147483648, 'max'=>2147483647),),
        'sync_start_date' => array('datetime'=>array(),),
        'start_status' => array('int'=>array(),'range'=>array('min'=>-2147483648, 'max'=>2147483647),),
    );

    public $field_type = array(
        'id' => 'INT',
        'ip' => 'INT',
        'ip_alias' => 'VARCHAR',
        'server_type' => 'SMALLINT',
        'port' => 'INT',
        'db_name' => 'VARCHAR',
        'host' => 'VARCHAR',
        'password' => 'VARCHAR',
        'flag' => 'INT',
        'type' => 'INT',
        'status' => 'INT',
        'last_admin_id' => 'INT',
        'sync_start_date' => 'DATETIME',
        'start_status' => 'INT',
    );

    public $php_type = array(
        'id' => 'int',
        'ip' => 'int',
        'ip_alias' => 'string',
        'server_type' => 'int',
        'port' => 'int',
        'db_name' => 'string',
        'host' => 'string',
        'password' => 'string',
        'flag' => 'int',
        'type' => 'int',
        'status' => 'int',
        'last_admin_id' => 'int',
        'sync_start_date' => 'string',
        'start_status' => 'int',
    );
}

