<?php
namespace scheduler\models;
class Schedule extends \Furina\mvc\model\Model {
    public $table = 'bp_schedule';
        
    var $validate_insert = array(           
        '_post_'=>array('check_value'),
    );

    var $validate_update = array(
        '_post_'=>array('check_value'),
    ); 
    
    
    function check_value(&$data, &$options){

        if(!isset($data['schedule_name']) || trim($data['schedule_name'])==""){
            throw new \Exception("Job Name is empty");
        }
        
        if(!isset($data['start_date']) || trim($data['start_date'])==""){
            throw new \Exception("Start date is empty");
        }       
        
        if(!isset($data['start_time']) || trim($data['start_time'])==""){
            throw new \Exception("Start time is empty");
        }
        $data['start_time'] = trim($data['start_time']);
        $data['start_date'] = trim($data['start_date']);
        $data['schedule_name'] = trim($data['schedule_name']);

    }

}

