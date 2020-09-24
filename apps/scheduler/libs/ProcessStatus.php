<?php
namespace scheduler;
class ProcessStatus {
    static $Force_Stop_Request = 6;
    static $Force_Stop = 5;
    static $Error = 4;
    static $DONE = 3;
    static $Processing = 2;
    static $On_Queue = 1;
    static $Waiting = 0;

    public static $messages = array(
        'Waiting',
        'On Queue',
        'Processing',
        'Done',
        'Error',
        'Force Stopped',
        'Force Stop Requested',
    );
    
    public static function getStatusString($status) {
        if (isset(ProcessStatus::$messages[$status]))
            return ProcessStatus::$messages[$status];
        return 'Unknown';
    }

    /**
     * return array of all status messages
     * @param $empty string
     */
    public static function getAllStatus($empty=false) {
        if ($empty!==false) return array('' => $empty) + ProcessStatus::$messages;
        return ProcessStatus::$messages;
    }
}
