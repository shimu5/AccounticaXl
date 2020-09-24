<?php
namespace scheduler\models;
class Job extends \Furina\mvc\model\Model {
    public $table = 'bp_job';
        
    var $validate_insert = array(           
        'job_name' => array('_required_'),
        'job_type' => array('_required_'),
        'schedule_id' => array('_required_')
    );

    var $validate_update = array(
        'job_name' => array('_required_'),
        'job_type' => array('_required_'),
        'schedule_id' => array('_required_')
    ); 

}

