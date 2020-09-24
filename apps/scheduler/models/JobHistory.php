<?php
namespace scheduler\models;
class JobHistory extends \Furina\mvc\model\Model {
    public $table = 'bp_job_history';        
    var $validate_insert = array();
    var $validate_update = array(); 
}

