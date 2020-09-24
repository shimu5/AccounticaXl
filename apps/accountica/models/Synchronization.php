<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace accountica\models;
class SyncStatus{
    static $NoError          = 0;
    static $UserPwdInvalid   = 1;
    static $DatabaseNotFound = 2;
    static $TablesNotFound   = 3;
    static $ServerNoResponse = 4;
    static $OnQueue = 11;
    static $Syncing = 12;
    static $Done = 13;


    function getStatus($status_no){
        $msg = '';
        switch($status_no){
            case 0:
                $msg = 'No Error';
                break;
            case 1:
                $msg = 'User/Password Invalid';
                break;
            case 2:
                $msg = 'Database Not Found';
                break;
            case 3:
                $msg = 'Tables Not Found';
                break;
            case 4:
                $msg = 'Server didn\'t respond';
                break;
            case 11:
                $msg = 'Queue';
                break;
            case 12:
                $msg = 'Syncing';
                break;
            case 13:
                $msg = 'Done';
                break;
            default:
                $msg = 'neverReachHereUnlessSomethingVeryWrong';
                break;
        }
        return $msg;
    }
}
class Synchronization extends \Furina\mvc\model\Model {
    public $table = 'synchronizations';
    
}
?>
