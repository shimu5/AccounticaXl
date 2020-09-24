<?php
namespace accountica\models;
class Ledger extends \Furina\mvc\model\Model
{
    var $validate = array(
        'amount' => array('required', 'decimal_4', 'positive'),
        'date' => array('required', 'date', 'afterAccount','afterBankOpening'),
        'product_rate' => array('positive'),             
    );

//    function stripCommas($value,$field){
//            $value = str_replace(',','',$value);
//            return $value;
//    }

    function afterAccount($value) {        
        if(isset($this->account)) {
            $v = strtotime($value);
            $a = strtotime($this->account['Account']['opening_date']);
            if( $v >= $a ) return true;
            return 'Cannot perform transaction before account\'s opening date';
        }
        return true;
    }


    function afterBankOpening(){
       //$bank_model = App::loadModel('Bank', Furina::Dir(Furina::Models));
       $this->loadModel('Bank');
       if(!empty($this->data['Ledger']['dst_bank_id'])):
         $dst_bank = $this->Bank->find('id = '.$this->data['Ledger']['dst_bank_id'].' AND `opening_date` <= \' '.$this->data['Ledger']['date'].'\'');
         $dst_bank_info = $this->Bank->find('id = '.$this->data['Ledger']['dst_bank_id']);
         if(empty($dst_bank)){
            return sprintf('Transaction cannot be done before %s',$dst_bank_info['Bank']['opening_date']);
         }
       endif;
       if(!empty($this->data['Ledger']['src_bank_id'])):
         $dst_bank = $this->Bank->find('id = '.$this->data['Ledger']['src_bank_id'].' AND `opening_date` <= \' '.$this->data['Ledger']['date'].'\'');
         $dst_bank_info = $this->Bank->find('id = '.$this->data['Ledger']['src_bank_id']);
         
         if(empty($dst_bank)){
            return sprintf('Transaction cannot be done before %s',$dst_bank_info['Bank']['opening_date']);
         }
       endif;
       return true;
    }

   function positive($value) {
       if( isset($this->account)) {
           if($value>=0.0)
                return true;
           return 'Amount must be positive';
       }
       return true;
   }

   function status($account_id = null,$last_day = null)
   {
         if ($last_day === null)
            $last_day = date('Y-m-d');
         $ledger_info = $this->find('account_id='.$account_id." AND date <='".$last_day."'",null,null,'date,id desc');
         if (empty($ledger_info))
         {
             $account_info = $this->Account->read($account_id);
             if(!empty($account_info))
                 $ledger_info['Ledger']['balance_after'] = $account_info['opening_balance'];
             else
                 $ledger_info['Ledger']['balance_after'] = 0.0;
         }
         return $ledger_info;
    }
    
    function commit($data,$account=null, $remove_res = 0) {
        if( $data === null ) return false;
        $this->table = 'pending_ledgers';

        if(!empty($account)){
            $this->account = $account;
        }

        if(!empty($data['Ledger']['date'])){
            
            if($data['Ledger']['date'] > DEFAULT_DATE){
               $this->validationErrors = array('Ledger' => array('future_trans_date' => 'ddd')) ;
               return false;
            }
            if($this->afterAccount($data['Ledger']['date']) !== true){

               $this->validationErrors = array('Ledger' => array('before_trans_date' => 'ddd')) ;
               return false;
            }else{
                unset($this->account);
            }
            
        }

        $this->__runQuery('SET AUTOCOMMIT=0');
        $this->__runQuery('START TRANSACTION');
            
        $this->delete($data['Ledger']['id']);    //delete it after committed successfully
        
        $this->table = 'ledgers';
        $return_val = false;
        switch($data['Ledger']['type']) {
            case PType::Transfer :
            case PType::Withdrawal :
            case PType::Deposit :
                $return_val = $this->commitBankLedger($data);
                break;
            case PType::Payment :
                $return_val = $this->commitPayment($data,$remove_res);
                break;
            case PType::Bill :
                $return_val = $this->commitBill($data,$remove_res);
                break;
            case PType::AgentTransfer :
                $return_val = $this->commitAgentTransfer($data);
                break;
            case PType::AgentPayment :
                $return_val = $this->commitAgentPayment($data);
                break;
            default:
                $return_val = $this->commitInsert($data,$remove_res);
                break;
        }
        
        if($return_val){
            $this->__runQuery('COMMIT');
            return $return_val;
        }
        
        return false;
    }

    function commitInsert($data,$account=null,$remove_res = 0) {
        // prepare additional data
        
        $data['Ledger']['balance_after'] = 0;
        if( !isset($data['Ledger']['bank_balance_after'])) {
            $data['Ledger']['bank_balance_after'] = 0;
            //die('1');
        }
        
        
        if( $this->validates($data)) {
            unset($data['Ledger']['id']);
            
            if( parent::insert($data, false) ) {
                //MOST IMPORTANT
                $id = $this->lastInsertId();
                $cond_prev = sprintf("(`ledgers`.`date` < '%s' OR (`ledgers`.`date`='%s' AND `ledgers`.`id`<%d) ) ", $data['Ledger']['date'], $data['Ledger']['date'], $id);
                $cond_after= sprintf("(`ledgers`.`date` > '%s' OR (`ledgers`.`date`='%s' AND `ledgers`.`id`>%d) ) ", $data['Ledger']['date'], $data['Ledger']['date'], $id);
                if( !empty($data['Ledger']['account_id'])) {
                    $cond_acc  = sprintf('`ledgers`.`account_id`=%d AND ', $data['Ledger']['account_id']);

                //'`date` > '."'{$data['Ledger']['date']}'".' AND `id` > '.$id.' AND account_id='.$account['Account']['id'];
                // SET SELF BALANCE AFTER
                    $query = 'UPDATE ledgers SET balance_after = '.$data['Ledger']['amount'].
                        '+(IFNULL((SELECT psssst.balance_after from '.
                            '(SELECT pastl.balance_after FROM ledgers as pastl WHERE '.str_replace('`ledgers`', '`pastl`', $cond_acc.$cond_prev).' ORDER BY `pastl`.`date` DESC, `pastl`.`id` DESC LIMIT 1) '.
                          'as psssst ), 0)) '.
                        ' WHERE id = '.$id;
                    
                    if($this->__runQuery($query) == null){
                        $this->__runQuery('ROLLBACK');
                        return false;
                    }

                    // UPDATE ALL LATER TRANSACTIONS BALANCE AFTER
                    $query = 'UPDATE ledgers SET balance_after = balance_after + '.$data['Ledger']['amount'].' WHERE '.$cond_acc.$cond_after;
                    if($this->__runQuery($query) == null){
                        $this->__runQuery('ROLLBACK');
                        return false;
                    }

                    // UPD  ATE CUSTOMER ACCOUNT BALANCE
                    $query = 'UPDATE accounts SET last_update = NOW(), last_balance = last_balance + '.$data['Ledger']['amount'].' WHERE id='.$data['Ledger']['account_id'];
                    if($this->__runQuery($query) == null){
                        $this->__runQuery('ROLLBACK');
                        return false;
                    }
                }

                if( $data['Ledger']['dst_bank_id'] != 0) {
                    $cond_bank = sprintf("`dst_bank_id`=%d AND ", $data['Ledger']['dst_bank_id']);

                    if( $data['Ledger']['type'] != PType::Opening) {
                        $query = 'UPDATE ledgers SET bank_balance_after = '.$data['Ledger']['deposit'].
                            '+(SELECT psssst.bank_balance_after from '.
                                '(SELECT pastl.bank_balance_after FROM ledgers as pastl WHERE '.str_replace('`ledgers`', '`pastl`', $cond_bank.$cond_prev).' ORDER BY `pastl`.`date` DESC, pastl.id DESC LIMIT 1) '.
                              'as psssst ) '.
                            ' WHERE id = '.$id;
                        $this->__runQuery($query);

                        $query = 'UPDATE ledgers SET bank_balance_after = bank_balance_after + '.$data['Ledger']['deposit'].' WHERE '.$cond_bank.$cond_after;
                        $this->__runQuery($query);
                    }

                    $query = 'UPDATE banks SET balance = balance + '.$data['Ledger']['deposit'].' WHERE id='.$data['Ledger']['dst_bank_id'];
                    $this->__runQuery($query);
                }

                if ($remove_res == 1) {
                    $query = 'UPDATE ledgers SET keep=0 WHERE id='.$id;
                    $this->__runQuery($query);
                }
//                $this->__runQuery('COMMIT');
                return true;
            }else{
//                $this->__runQuery('COMMIT');//required to end the start commit, if not entered in previous section
            }
        }
        return false;
    }
    
    function commitPayment($data,$remove_res = 0) {
        $data['Ledger']['type'] = PType::Payment;
        if( $this->commitInsert($data, $remove_res) ) {
            //$query = 'UPDATE banks SET balance = balance + '.$data['Ledger']['deposit'].' WHERE id='.$data['Ledger']['dst_bank_id'];
            //$this->__runQuery($query);
            return true;
        }
        return false;
    }

    function commitBill($data,$remove_res) {
        $data['Ledger']['type'] = PType::Bill;
        if( $this->commitInsert($data,$remove_res) ) {
//            $query = 'UPDATE banks SET balance = balance - '.$data['Ledger']['deposit'].' WHERE id='.$data['Ledger']['dst_bank_id'];
//            $this->__runQuery($query);
            return true;
        }
        return false;
    }

    function commitAgentTransfer($data) {
        $data['Ledger']['type'] = PType::AgentTransfer;
        if( $this->commitInsert($data) ) {
            return true;
        }
        return false;
    }
    function commitAgentPayment($data) {
        $data['Ledger']['type'] = PType::AgentPayment;
        if( $this->commitInsert($data) ) {
            return true;
        }
        return false;
    }

    function __prepareAdditionalData($data,$account){
        $data['Ledger']['balance_after'] = 0;
        $data['Ledger']['rate'] = ($data['Ledger']['amount'] != 0.0) ? $data['Ledger']['deposit'] / $data['Ledger']['amount'] : 0;
        $data['Ledger']['account_id'] = $account['Account']['id'];
        if(empty($data['Ledger']['cur_id']))
         $data['Ledger']['cur_id'] = $account['Account']['cur_id'];

        if( empty($data['Ledger']['reseller_id']))
            $data['Ledger']['reseller_id'] = 0;

        $this->account = $account;

        

        return $data;
    }

    function __prepareAmountData($data){
       $this->loadModel('RemotePType');
//       if( $data['Ledger']['type'] == PType::CreditAllow || $data['Ledger']['type'] == PType::Invoice || $data['Ledger']['type'] == PType::Withdrawal) {
//          $data['Ledger']['amount'] = '-'.$data['Ledger']['amount'];
//          $data['Ledger']['deposit'] = '-'.$data['Ledger']['deposit'];
//       }
//       else if( $data['Ledger']['type'] == PType::Transfer || $data['Ledger']['type'] == PType::Bill) {
//          $data['Ledger']['deposit'] = '-'.$data['Ledger']['deposit'];
//       }
//       else if( $data['Ledger']['type'] == PType::TransferRev ) {
//          $data['Ledger']['amount'] = '-'.$data['Ledger']['amount'];
//       }

       switch($data['Ledger']['type']){
           case PType::CreditAllow:
           case PType::Invoice:
           case PType::Withdrawal:
               $data['Ledger']['amount'] = '-'.$data['Ledger']['amount'];
               $data['Ledger']['deposit'] = '-'.$data['Ledger']['deposit'];
               break;
           case PType::Transfer:
           case PType::Bill:
           case PType::AgentPayment:
               $data['Ledger']['deposit'] = '-'.$data['Ledger']['deposit'];
               break;
           case PType::TransferRev:
               $data['Ledger']['amount'] = '-'.$data['Ledger']['amount'];
               break;
           case PType::AgentTransfer:
               $data['Ledger']['amount'] = '-'.$data['Ledger']['amount'];
               break;
           case PType::Commission:
               $data['Ledger']['amount'] = '-'.$data['Ledger']['amount'];
               break;

           
       }
       

       return $data;
    }

    function __prepareUpdateInfo($data){
       $data['Ledger']['update_info'] = '\''.$this->components['Auth']->user('name').'('.DEFAULT_DATE.')'.'\'';
       //echo '<pre>';print_r($data);

       return $data;
    }

    function update($data,$account){
        $this->table = 'pending_ledgers';

        // prepare additional data
        $data = $this->__prepareAdditionalData($data,$account);
        $data = $this->__prepareUpdateInfo($data);
        
        if( $this->validates($data)) {
            $data = $this->__prepareAmountData($data);
            //$data['Ledger']['update_info'] = '\''.$this->components['Auth']->user('name').'('.DEFAULT_DATE.')'.'\'';
            //echo '<pre>';print_r($data);
            if( parent::update($data, false) ) {


                // update history edited by admins 
                $query = 'SELECT `update_info` FROM `pending_ledgers` WHERE `id` = '.$data['Ledger']['id'];
                $old_update_info = $this->query($query);
                $new_update_info = $this->components['Auth']->user('name').'('.date('Y-m-d h:i').')';
                
                if(!empty($old_update_info[0]['Ledger']['update_info'])){
                     $new_update_info = $old_update_info[0]['Ledger']['update_info'].','.$new_update_info;
                }
                $query = 'UPDATE pending_ledgers SET `update_info` = '
                           .'\''.$new_update_info.'\''
                           .' WHERE `id` = '.$data['Ledger']['id'];

                $this->__runQuery($query);
                // end
                return true;
            }
        }
        return false;
    }



    

    function insert($data, $account,$doValidation = true) {
        $this->table = 'pending_ledgers';
        
        // prepare additional data
        $data = $this->__prepareAdditionalData($data,$account);
        
        if($doValidation === true){
            if( $this->validates($data) === false ) 
                return false;
        }
        
        $data = $this->__prepareAmountData($data);

        if( parent::insert($data, false) ) {
               //Log::printSQLs();
                return true;
        }
        
        return false;
    }

    function savePayment($data, $account, $myaccount) {
        $data['Ledger']['type'] = PType::Payment;
        
        if(!empty($this->id)){
           if( $this->update($data, $account) ) {
            return true;
         }
        }
        else{
         if( $this->insert($data, $account) ) {
            return true;
         }
        }
        return false;
    }

    function saveBill($data, $account, $myaccount) {
        $data['Ledger']['type'] = PType::Bill;
        if(!empty($this->id)){
         if( $this->update($data, $account) ) {
            return true;
         }
        }
        else{
         if( $this->insert($data, $account) ) {
            return true;
         }
        }
        return false;
    }

    function saveAgentTransfer($data, $account, $myaccount) {
        $data['Ledger']['type'] = PType::AgentTransfer;
        if(!empty($this->id)){
         if( $this->update($data, $account) ) {
            return true;
         }
        }
        else{
         if( $this->insert($data, $account) ) {
            return true;
         }
        }
        return false;
    }

    function saveAgentPayment($data, $account, $myaccount) {
        $data['Ledger']['type'] = PType::AgentPayment;
        if(!empty($this->id)){
         if( $this->update($data, $account) ) {
            return true;
         }
        }
        else{
         if( $this->insert($data, $account) ) {
            return true;
         }
        }
        return false;
    }

    function saveAccount($data) {
        $data['Ledger']['balance_after'] = $data['Ledger']['amount'];
        $data['Ledger']['rate'] = 0;
        $data['Ledger']['reseller_id'] = 0;
        $data['Ledger']['deposit'] = $data['Ledger']['amount'];

        return parent::insert($data, false);
    }

    function commitBankLedger($data) {
        $this->table = 'ledgers';
        $type = $data['Ledger']['type'];

        if( $this->commitInsert($data) ) {
            if($type==PType::Transfer) {
                
                $t = $data['Ledger']['src_bank_id'];
                $data['Ledger']['src_bank_id'] = $data['Ledger']['dst_bank_id'];;
                $data['Ledger']['dst_bank_id'] = $t;

                $t = $data['Ledger']['deposit_cur_id'];
                $data['Ledger']['deposit_cur_id'] = $data['Ledger']['cur_id'];
                $data['Ledger']['cur_id'] = $t;

                $t = $data['Ledger']['deposit'];
                $data['Ledger']['deposit'] = $data['Ledger']['amount'];
                $data['Ledger']['amount'] = $t;

                //$account['Cur']['id'] = $data['Ledger']['cur_id'];//source bank accounts cur_id;
                //$data['Ledger']['type'] = PType::TransferRev;
                $this->id = null;
                $data['Ledger']['type'] = PType::TransferRev;
                if( $this->commitBankLedger($data) === false ) {
                    die('CRITICAL ERROR WITH REVERSE TRANSFER');
                }
                //$query = 'UPDATE banks SET balance = balance + '.$data['Ledger']['deposit'].' WHERE id='.$data['Ledger']['dst_bank_id'];
                //$this->__runQuery($query);
            }
            return true;
        }
        return false;
    }

    
    function saveBankLedger($data, $account, $type) {
        $data['Ledger']['type'] = $type;

        if($type==PType::Opening) {
            $data['Ledger']['bank_balance_after'] = $data['Ledger']['deposit'];
            if( $this->commitBankLedger($data) )
                return true;
            return false;
        }

        if($type==PType::Transfer)
            //$account['Cur']['id'] = $data['Ledger']['cur_id'];
            $account['Cur']['id'] = $data['Ledger']['other_cur_id'];

        if(!empty($this->id)){
           if( $this->update($data, $account) ) {
               unset($this->account);
               $id = $this->lastInsertId();
               $data = $this->read($id);
               return true;
            }
        }else{
           if( $this->insert($data, $account) ) {
               unset($this->account);
               $id = $this->lastInsertId();
               $data = $this->read($id);
               return true;
           }
        }
        return false;
    }


    function save($data, $account,$doValidation= true) {
        
        if(!empty($this->id)){
         if( $this->update($data, $account) ) {
            return true;
         }
        }
        else{
         if( $this->insert($data, $account,$doValidation) ) {
            return true;
         }
        }
        return false;
    }



    function ledgerDelete($id){
        $ledger = $this->read($id);
        pdebug($ledger);



        if(!empty($ledger)){
            $type = $ledger['Ledger']['type'];
            $amount = 0.00;
            $amount = $ledger['Ledger']['amount'];
            $date = $ledger['Ledger']['date'];
            $account_id = $ledger['Ledger']['account_id'];
            switch($type){
                case PType::Opening:
                    break;
                case PType::Invoice:
                case PType::Credit:
                case PType::CreditAllow:
                case PType::CreditReturn:
                    $amount = $amount*(-1) ;
                    break;
                    
                case PType::Payment:
                    break;
                case PType::Bill:
                    break;
               case PType::Withdrawal:
                    break;
                case PType::Deposit:
                    break;
                case PType::Transfer:
                    break;
            }


            // update balance_after
            $update_balance_after_query = ' UPDATE `ledgers` set `balance_after` = `balance_after` +  '.$amount.' WHERE `account_id` = '.$account_id.' AND `id` > '.$id.' AND `date` > '.$date;
            $this->__runQuery($update_balance_after_query);
            // update user account
            $update_user_account = ' UPDATE `accounts` set `last_balance` = `last_balance` + '.$amount.' WHERE id ='.$account_id;
            $this->__runQuery($update_user_account);

            // delete the ledger
            return parent::delete('`id` = '.$id);



        }
    }

    function __save($data,$validation = false) {
        return parent::update($data, false);
    }




    function ledgerUpdate($ledger_info,$data) {
        //$this->loadModel('Ledger');

        $data['Ledger']['type'] = $ledger_info['Ledger']['type'];
        $data = $this->__prepareAmountData($data);
        $data['Ledger']['account_id'] = $ledger_info['Ledger']['account_id'];
        $data['Ledger']['dst_bank_id'] = $ledger_info['Ledger']['dst_bank_id'];
        $data['Ledger']['amount_diff'] = $ledger_info['Ledger']['amount'] - $data['Ledger']['amount'];
        $data['Ledger']['deposit_diff'] = $ledger_info['Ledger']['deposit'] - $data['Ledger']['deposit'];
        $id = $ledger_info['Ledger']['id'];




        $data['Ledger']['id'] = $id;
        if (empty($data['Ledger']['reseller_id']))
            $data['Ledger']['reseller_id'] = 0;




        $this->loadModel('Setting');
        $rate_setting = $this->Setting->get('reseller.rate');
        if (($rate_setting['reseller.rate'] == 1) && !empty($data['Ledger']['prate']) && !empty($data['Ledger']['reseller_id']) ){
            $data['Ledger']['reseller_rate'] = $data['Ledger']['prate'];
        }
        else {
            if(empty($data['Ledger']['rate']))
                $data['Ledger']['rate'] = ($data['Ledger']['prate']==0.00)?$data['Ledger']['prate']:(1.00/$data['Ledger']['prate']);
        }


        $this->__save($data);
        $cond_prev = sprintf("(`ledgers`.`date` < '%s' OR (`ledgers`.`date`='%s' AND `ledgers`.`id`<%d) ) ", $data['Ledger']['date'], $data['Ledger']['date'], $id);
        $cond_after= sprintf("(`ledgers`.`date` > '%s' OR (`ledgers`.`date`='%s' AND `ledgers`.`id` > %d) ) ", $data['Ledger']['date'], $data['Ledger']['date'], $id);
        if( !empty($data['Ledger']['account_id'])) {
            $cond_acc  = sprintf('`ledgers`.`account_id`=%d AND ', $data['Ledger']['account_id']);

        //'`date` > '."'{$data['Ledger']['date']}'".' AND `id` > '.$id.' AND account_id='.$account['Account']['id'];
        // SET SELF BALANCE AFTER
            $query = 'UPDATE ledgers SET balance_after = '.$data['Ledger']['amount'].
                '+(SELECT psssst.balance_after from '.
                    '(SELECT pastl.balance_after FROM ledgers as pastl WHERE '.str_replace('`ledgers`', '`pastl`', $cond_acc.$cond_prev).' ORDER BY `pastl`.`date` DESC, `pastl`.`id` DESC LIMIT 1) '.
                  'as psssst ) '.
                ' WHERE id = '.$id;
            $this->__runQuery($query);

            // UPDATE ALL LATER TRANSACTIONS BALANCE AFTER
            $query = 'UPDATE ledgers SET balance_after = balance_after - '.$data['Ledger']['amount_diff'].' WHERE '.$cond_acc.$cond_after;
            $this->__runQuery($query);

            // UPDATE CUSTOMER ACCOUNT BALANCE
            $query = 'UPDATE accounts SET last_update = NOW(), last_balance = last_balance - '.$data['Ledger']['amount_diff'].' WHERE id='.$data['Ledger']['account_id'];
            $this->__runQuery($query);
        }
        // TODO
        // if dst_bank_id changed, then update the both bank account

        if( $data['Ledger']['dst_bank_id'] != 0) {
            $cond_bank = sprintf("`dst_bank_id`=%d AND ", $data['Ledger']['dst_bank_id']);

            if( $data['Ledger']['type'] != PType::Opening) {
                $query = 'UPDATE ledgers SET bank_balance_after = '.$data['Ledger']['deposit'].
                    '+(SELECT psssst.bank_balance_after from '.
                        '(SELECT pastl.bank_balance_after FROM ledgers as pastl WHERE '.str_replace('`ledgers`', '`pastl`', $cond_bank.$cond_prev).' ORDER BY `pastl`.`date` DESC, pastl.id DESC LIMIT 1) '.
                      'as psssst ) '.
                    ' WHERE id = '.$id;
                $this->__runQuery($query);

                $query = 'UPDATE ledgers SET bank_balance_after = bank_balance_after - '.$data['Ledger']['deposit_diff'].' WHERE '.$cond_bank.$cond_after;
                $this->__runQuery($query);
            }

            $query = 'UPDATE banks SET balance = balance - '.$data['Ledger']['deposit_diff'].' WHERE id='.$data['Ledger']['dst_bank_id'];
            $this->__runQuery($query);
        }
    }


   



//    function prepareCredits(& $ledgers) {
//        $this->loadModel('Cur');
//        $curs = $this->Cur->findList(null, array('id', 'name'));
//        foreach( $ledgers as &$ledger) {
//            $ledger['Ledger']['cur'] = $curs[$ledger['Ledger']['cur_id']];
//        }
//    }
//
//    function preparePayments(& $ledgers) {
//        $this->loadModel('Cur');
//        $curs = $this->Cur->findList( null, array('id', 'name'));
//        foreach( $ledgers as &$ledger) {
//            $ledger['Ledger']['cur'] = $curs[$ledger['Ledger']['cur_id']];
//        }
//    }
    
    //for save into Ledger from Pending Ledger table
    
    function insertToLedger($data){
        $RemotePType = new RemotePType();
        //pr($data);
        die;
        parent::insert($data,array('validate_with'=>false));

        switch($data['Ledger']['type']){
           case PType::Opening:
//               $last_balance = 'last_balance + '.$data['Ledger']['amount'];
//               $account_balance_update = "UPDATE accounts SET last_update = '".date('Y-m-d')."', last_balance=$last_balance WHERE id=".$data['Ledger']['account_id'];
//               $this->execute($account_balance_update);
               break;
           case PType::CreditAllow:
               $last_balance = 'last_balance - '.$data['Ledger']['amount'];
               $account_balance_update = "UPDATE accounts SET last_update = '".date('Y-m-d')."', last_balance=$last_balance WHERE id=".$data['Ledger']['account_id'];
               $this->execute($account_balance_update);
               break;
           case PType::CreditReturn:
               $last_balance = 'last_balance + '.$data['Ledger']['amount'];
               $account_balance_update = "UPDATE accounts SET last_update = '".date('Y-m-d')."', last_balance=$last_balance WHERE id=".$data['Ledger']['account_id'];
               $this->execute($account_balance_update);
               break;
           case PType::Payment:
               $last_balance = 'last_balance + '.$data['Ledger']['amount'];
               $account_balance_update = "UPDATE accounts SET last_update = '".date('Y-m-d')."', last_balance=$last_balance WHERE id=".$data['Ledger']['account_id'];
               $this->execute($account_balance_update);
               break;
           case PType::Invoice:
               $last_balance = 'last_balance - '.$data['Ledger']['amount'];
               $account_balance_update = "UPDATE accounts SET last_update = '".date('Y-m-d')."', last_balance=$last_balance WHERE id=".$data['Ledger']['account_id'];
               $this->execute($account_balance_update);
               break;
           case PType::Bill:
               $last_balance = 'last_balance + '.$data['Ledger']['amount'];
               $account_balance_update = "UPDATE accounts SET last_update = '".date('Y-m-d')."', last_balance=$last_balance WHERE id=".$data['Ledger']['account_id'];
               $this->execute($account_balance_update);
               break;
           case PType::Deposit:
               $bank = new Bank();
               $account_id = $bank->getBankAccountId($data['Ledger']['dst_bank_id']);
               $last_balance = 'last_balance + '.$data['Ledger']['amount'];
               $account_balance_update = "UPDATE accounts SET last_update = '".date('Y-m-d')."', last_balance=$last_balance WHERE id=".$account_id;
               $this->execute($account_balance_update);
               break;
           case PType::Withdrawal:
               $bank = new Bank();
               $account_id = $bank->getBankAccountId($data['Ledger']['dst_bank_id']);
               $last_balance = 'last_balance - '.$data['Ledger']['amount'];
               $account_balance_update = "UPDATE accounts SET last_update = '".date('Y-m-d')."', last_balance=$last_balance WHERE id=".$account_id;
               $this->execute($account_balance_update);
               break;
           case PType::Transfer:
               $bank = new Bank();
               $account_id = $bank->getBankAccountId($data['Ledger']['src_bank_id']);
               $last_balance = 'last_balance - '.$data['Ledger']['amount'];
               $account_balance_update = "UPDATE accounts SET last_update = '".date('Y-m-d')."', last_balance=$last_balance WHERE id=".$account_id;
               $this->execute($account_balance_update);
               $account_id = $bank->getBankAccountId($data['Ledger']['dst_bank_id']);
               $last_balance = 'last_balance + '.$data['Ledger']['amount'];
               $account_balance_update = "UPDATE accounts SET last_update = '".date('Y-m-d')."', last_balance=$last_balance WHERE id=".$account_id;
               $this->execute($account_balance_update);
               break;
        }
//        echo $account_balance_update;
//        pr($data);
//        die(' here');
        
    }
}
?>
