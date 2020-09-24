<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class SyncClientPayment extends AppModel {
    //var $table = 'vps_payments';
    var $table = 'resellerspayments';
    var $name  = 'ClientPayment';
    var $primaryKey = 'id';



    var $savedCount;
    var $updatedCount;
    var $errorCount;

    function init() {
        parent::init();
        $this->savedCount = 0;
        $this->updatedCount = 0;
        $this->errorCount = 0;
    }

    function save($data, $doValidation = true) {
        if ($this->insert($data, $doValidation) == true) {
            $this->savedCount++;
            return true;
        }

        $this->errorCount++;
        return false;

    }

    function integer($data) {
        return true;
    }
}
?>
