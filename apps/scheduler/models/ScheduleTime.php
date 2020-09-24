<?php
namespace scheduler\models;
class ScheduleTime extends \Furina\mvc\model\Model {
	public $table = 'bp_schedule_times';

        
    var $validate_insert = array(           
        'schedule_id' => array('_required_'),
        'time' => array('_required_')
    );

    var $validate_update = array(
        'schedule_id' => array('_required_'),
        'time' => array('_required_')
    ); 
      
    function check_value(&$data, &$options){
        return true;
    }

}

