<?php
/*********
 *  This Model is used to show only Pending Ledgers List
 *  save,insert,update has been rewrite in the Ledger Model
 *  save is used for only update rejected ledgers
 */
namespace accountica\models;

class Status {
    
    static $status = array(
        0 =>'Pending',
        1 =>'Accept',
        2 =>'Reject'
    );
    
    static function getStatus($type){
        return Status::$status($type);
    }
}

class PendingLedger extends \Furina\mvc\model\Model{
    
    var $table = 'pending_ledgers';
    
    function changestatus($status, $id){
        
        $data = $this->read($id);
        if(!empty($data)){
            $data['PendingLedger']['is_posted'] = $status;            
            PendingLedger::update($data,'', 'id = ' . $id);
            return $data;
        }else{
            return;
        }   
    }
    
    function insertToLedger($data,$options=false){
        parent::insert($data,$options);
    }
}
?>