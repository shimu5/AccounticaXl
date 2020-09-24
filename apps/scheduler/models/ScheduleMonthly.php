<?php
namespace scheduler\models;
class ScheduleMonthly extends \Furina\mvc\model\Model {
	public $table = 'bp_schedule_monthly';

        
    var $validate_insert = array(           
        'schedule_id' => array('_required_'),
        'recurs_in' => array('_required_'),
        'month_days' => array('_required_'),
        '_post_'=>array('check_value'),
    );

    var $validate_update = array(
        'schedule_id' => array('_required_'),
        'recurs_in' => array('_required_'),
        'month_days' => array('_required_'),
        '_post_'=>array('check_value'),
    ); 
    
    
    function check_value(&$data, &$options){
        return true;
    }

}

