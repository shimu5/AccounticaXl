<?php
/*********
 *  This Model is used to show only Pending Ledgers List
 *  save,insert,update has been rewrite in the Ledger Model
 *  save is used for only update rejected ledgers
 */
namespace accountica\models;
class PendingLedger extends \Furina\mvc\model\Model{
    var $name = 'Ledger';
    var $table = 'pending_ledgers';

    function save($data){
        //die('Invalid Use. Check for manual.');
        return parent::save($data);
    }
    function insert(){
        die('Invalid Use. Check for manual.');
    }
    
    /*
    function __insertToPendingLedger($data,$user_id){


        $this->loadModel('CustomerResRel');
        $this->loadModel('Bank');
        $this->loadModel('Setting');
        $this->loadModel('Reseller');
        $condition = ' `Reseller`.`ip` =  '.$data['ResellerPayment']['ip'].' AND '.
                     ' `Reseller`.`port` = '.$data['ResellerPayment']['port'].' AND '.
                     ' `Reseller`.`level` = '.$data['ResellerPayment']['resellerlevel'].' AND '.
                     ' `Reseller`.`old_id` = '.$data['ResellerPayment']['id_reseller'];
        $res_id = $this->Reseller->find($condition);
        if(!empty($res_id)){
            $this->CustomerResRel->bindModel(array(
                        'hasOne' => array('Account' => array(
                            'localKey' => 'customer_id',
                            'foreignKey' => 'user_id'
                        ))
                ));
            $condition = ' `CustomerResRel`.`ip` =  '.$data['ResellerPayment']['ip'].' AND '.
                         ' `CustomerResRel`.`port` = '.$data['ResellerPayment']['port'].' AND '.
                         ' `CustomerResRel`.`reseller_level` = '.$data['ResellerPayment']['resellerlevel'].' AND '.
                         ' `CustomerResRel`.`reseller_id` = '.$res_id['Reseller']['id'];
            $assignedRes = $this->CustomerResRel->find($condition);
            if(!empty($assignedRes)){

                $bank_info = $this->Bank->getUniversalBank();
                
                
                $rate = $this->Setting->rate_chart[$assignedRes['Account']['cur_id']]['Rate'][$bank_info['Bank']['cur_id']];
                $deposit = $data['ResellerPayment']['money']*$rate;
                $pending_ledger_data = array( 'Ledger' => array(
                    'reseller_id' => $assignedRes['CustomerResRel']['reseller_id'],
                    'account_id'  => $assignedRes['Account']['id'],
                    'cur_id' => $assignedRes['Account']['cur_id'],
                    'dst_bank_id' => $bank_info['Bank']['id'],
                    'deposit_cur_id' => $bank_info['Bank']['cur_id'],
                    'amount' => $data['ResellerPayment']['money'],
                    'deposit' => $deposit,
                    'rate' => $rate,
                    'date' => date('Y-m-d',strtotime($data['ResellerPayment']['data'])),
                    'type' => PType::Payment,
                    'product_id' => 0,
                    'description' => 'remote',
                    'created_by' => $user_id


                ));
                //pdebug($pending_ledger_data);
                $this->save($pending_ledger_data);
                //pdebug($this->validationErrors);
                

             }
        }
    }
     * */
     



    
}
?>