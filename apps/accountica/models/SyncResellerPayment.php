<?php
namespace accountica\models;
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of sync_reseller_payment
 *
 * @author nur
 */
class SyncResellerPayment extends \Furina\mvc\model\Model
{
    var $table = 'sync_reseller_payments';
    //var $name  = 'ResellerPayment';

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

    function integer($data){
        return true;
    }
    
    function getSyncResellerPayment($sync_data){        
        $query_data = $this->query()->where(sprintf('server_id = %d AND resellerlevel = %d AND id_reseller = %d',$sync_data['SyncReseller']['server_id'],$sync_data['SyncReseller']['level'],$sync_data['SyncReseller']['old_id']))->all();
        return $query_data;
    }

}
?>
