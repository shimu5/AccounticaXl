<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of sync_gw_client
 *
 * @author sakira
 */
class SyncClient extends AppModel {
    //var $table = 'clients';
    var $table = 'resellers';
    var $name  = 'Client';
    var $primaryKey = 'id';
    
    

    var $savedCount;
    var $updatedCount;
    var $errorCount;

    function init(){
        parent::init();
        $this->savedCount = 0;
        $this->updatedCount = 0;
        $this->errorCount = 0;
    }

    function save($data, $doValidation = true){
        if($this->insert($data, $doValidation) == true){
            $this->savedCount++;
            return true;
        }

        $this->errorCount++;
        return false;

    }

  function integer($data){
       return true;
    }
   
}
?>
