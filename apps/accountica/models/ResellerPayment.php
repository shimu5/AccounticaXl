<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace accountica\models;
/**
 * Description of reseller_payment
 *
 * @author nur
 */

class RemoteResellerPaymentType{
   const CREDIT = 1;
   const CREDIT_RETURN = 3;
   static $names = array(
        1=>'Prepaid',
        3=>'Return',
   );
   static function toString($type) {
        return RemoteResellerPaymentType::$names[$type];
   }
}

class ResellerPayment extends \Furina\mvc\model\Model {
   var $table = 'reseller_payments';
   var $assignedResList;
   var $rateList;
   var $deposit_info;
   var $temp_id;

   function prepareAdditionalInfo(){
       $Setting = new Setting();
       $Setting->init();
       $this->assignedResList = $this->__assignedResList();
       $this->rateList = $Setting::$rate_chart;
       $this->deposit_info = $Setting->getDepositCurrency();
   }

   function __assignedResList(){
       $Reseller = new Reseller();
       $Reseller->table = 'resellers';
       $Machine = new Machine();
       
       $condition = ' `id` IS NOT NULL ';
       $resellers = $Reseller->query()->where($condition)->all();
       
       $assignedRes = array();
       foreach($resellers as $reseller){
           $assignedRes[$reseller['Reseller']['server_id']]
                       [$reseller['Reseller']['level']]
                       [$reseller['Reseller']['old_id']] = array(
                           'reseller_id' => $reseller['Reseller']['id'],
                           'customer_id' => $reseller['Reseller']['customer_id'],
                           'rate'        => $reseller['Reseller']['rate']
                       );

       }

       return $assignedRes;
   }


   function __paymentType($type){

       $RemotePType = new RemotePType();
       $payment_type = null;
       switch($type){
           case RemoteResellerPaymentType::CREDIT :
                $payment_type = PType::CreditAllow;
                break;
           case RemoteResellerPaymentType::CREDIT_RETURN :
               $payment_type = PType::CreditReturn;
               break;
       }

       return $payment_type;
   }


   function insertToLedger($data){
        
       // Assigned Reseller List

       // Rate List
       $ip = $data['ResellerPayment']['ip'];
       $port = $data['ResellerPayment']['port'];
       $level = $data['ResellerPayment']['resellerlevel'];
       $old_id = $data['ResellerPayment']['id_reseller'];

       $assignedRes = !empty($this->assignedResList[$ip][$port][$level][$old_id])?$this->assignedResList[$ip][$port][$level][$old_id]:null;

       if(!empty($assignedRes)){
           //$this->loadModel('Bank');
           $Account = new Account();
           $Ledger = new Ledger();
           //$bank_info = $this->Bank->getUniversalBank();

           $account_info = $Account->query()->where('`user_id` = '.$assignedRes['customer_id'])->one();
           $account_cur_id = $account_info['Account']['cur_id'];

           //$rate = $this->Setting->rate_chart[$account_cur_id]['Rate'][$bank_info['Bank']['cur_id']];
           $rate = $this->rateList[$this->deposit_info['Cur']['id']]['Rate'][$account_cur_id];
           // changed
           $deposit = $data['ResellerPayment']['money'];
           
           $type = $this->__paymentType($data['ResellerPayment']['type']);

           //$amount = $data['ResellerPayment']['money'] * $assignedRes['rate'];
           $amount = $deposit * $assignedRes['rate'];
           $deposit_cur_id = ( ($type!= PType::CreditAllow) &&($type!= PType::CreditReturn))? $this->deposit_info['Cur']['id']:0;

           $pending_ledger_data = array( 'Ledger' => array(
                'reseller_id' => $assignedRes['reseller_id'],
                'account_id'  => $account_info['Account']['id'],
                'cur_id' => $account_info['Account']['cur_id'],
                //'dst_bank_id' => $bank_info['Bank']['id'],
                //'deposit_cur_id' => $bank_info['Bank']['cur_id'],
                'dst_bank_id' => 0,
                'deposit_cur_id' => $deposit_cur_id,
                'amount' => $amount,
                'deposit' => $deposit,
                'rate' => $rate,
                'tr_date' => date('Y-m-d',strtotime($data['ResellerPayment']['tr_date'])),
                'type' => $type,
                'product_id' => 0,
                'description' => !empty($data['ResellerPayment']['description'])?$data['ResellerPayment']['description']:'',
                'created_by' => $data['ResellerPayment']['admin_id'], // need to implement;
                'reseller_rate' => $assignedRes['rate'], // added on 23.03.10
                'sync_flag' => !empty($data['ResellerPayment']['sync_flag'])?$data['ResellerPayment']['sync_flag']:0, // // added on 24.04.10
                'res_payment_id' => !empty($data['ResellerPayment']['id'])?$data['ResellerPayment']['id']:0, // // added on 24.04.10
                'res_old_amount' => !empty($data['ResellerPayment']['money'])?$data['ResellerPayment']['money']:0.00 // added on 24.04.10

           ));
           //pdebug($pending_ledger_data);

           if($this->Ledger->save($pending_ledger_data,$account_info,false)){
               $this->temp_id = $this->Ledger->temp_id;
               return true;
           }
       }

       return false;
   
   }
   
   function insertToResellerPayment($inserte_data){
           $this->save($inserte_data,array('validate_with'=> false));// pending
           return $this->lastInsertId();
   }

   function access_temp_id(){
       return $this->temp_id;
   }

   
}
?>
