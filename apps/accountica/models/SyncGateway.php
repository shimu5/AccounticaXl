<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace accountica\models;
/**
 * Description of sync_gateway
 *
 * @author nur
 */
class SyncGatewayStatus {
    public static $SyncGatewayStatus = array(
        0=> 'Shnchronized',
        1=> 'Accepted'
    );      
}

class SyncGateway extends \Furina\mvc\model\Model {

    //var $table = 'gateways';
    //var $name  = 'Gateway';
    var $primaryKey = 'id_route';
    var $savedCount;
    var $updatedCount;
    var $errorCount;
    
    function init(){
        //parent::init();
        $this->savedCount = 0;
        $this->updatedCount = 0;
        $this->errorCount = 0;
    }

    function save($data, $doValidation = true){
        if($this->insert($data, array('validate_with'=>$doValidation)) == true){
            $this->savedCount++;
            return true;
        }

        $this->errorCount++;
        return false;
        
    }
    
    function updateToSyncGateway($data, $options){
        parent::update($data,$options);
    }

    function integer($data){
        return true;
    }

}
?>
