<?php

namespace accountica\models;

class Account extends \Furina\mvc\model\Model {

    var $validate = array('name' => array("required" => "Enter account name."), 'opening_balance' => array('required', 'decimal_4'));

    function findList($condition, $fields, $groupby=null, $orderby=null, $usertype = null) {
        $admin = $this->components['Auth']->user('id');
        $permitted_users = '';
        if ($admin != 1 && $usertype != UserType::$agent) {
            $users = App::loadModel('Admin', Furina::Dir(Furina::Models));
            $users = $users->find(sprintf('id=%d', $admin), array('customers', 'vendors'));

            if (!empty($users['Admin']['customers']))
                $permitted_users = $users['Admin']['customers'];
            if (!empty($users['Admin']['vendors'])) {
                if (!empty($permitted_users))
                    $permitted_users .= ',';
                $permitted_users .= $users['Admin']['vendors'];
            }

            if (!empty($permitted_users)) {
                if ($condition != '')
                    $condition .= ' AND';
                $condition .= ' `User`.`id` IN (' . $permitted_users . ')';
            }
        }

        return parent::findList($condition, $fields, $groupby, $orderby);
    }

    function get_type($type = 'Cash') {
        if ($type == 'Cash')
            $output = 0;
        elseif ($type == 'Bank')
            $output = 1;
        else
            $output = 'No Type';
        return $output;
    }

    function checkDate($transaction_date, $id) {
        $result = $this->find('`id` =' . $id . ' AND `opening_date` <= \'' . $transaction_date . '\'');
        if (empty($result))
            return false;
        else
            return true;
    }

    function olderDate($transaction_date, $ago) {
        //echo $old30_from_today = date('Y-m-d',strtotime('-1 months'));
        //echo $transaction_date - $old30_from_today;
        $days = floor((time() - strtotime($transaction_date)) / 86400);
        if ($days > $ago)
            return false;
        return true;
    }

    function get_account_info($id=null) {
        if (!empty($id)) {
            $this->id = $id;
            return $this->read($id);
        }
    }

    function get_account_opening_balance($user_id=null) {

        if (!empty($user_id)) {

            $balance = $this->find('`user_id` = ' . $user_id);

            return $balance;
        }
    }

    function account_balance_update($account_id, $balance, $update) {
        $this->id = $account_id;
        $data['Account']['last_balance'] = $balance;
        $data['Account']['last_update'] = $update;
        $this->save($data);
    }

    function get_account_balance($id=null, $user_id = null) {
        if ($id) {
            $balance = $this->find('`Account`.`id` = ' . $id);
            return $balance['Account']['last_balance'];
        } else if ($user_id) {
            $balance = $this->find('`Account`.`user_id` = ' . $user_id);
            return $balance['Account']['last_balance'];
        }
        return 0.00;
    }

    function get_user_account($userid) {
        $this->bindModel(array('hasOne' => array('Cur' => array('localKey' => 'cur_id', 'foreignKey' => 'id'))));
        $account = $this->find('`Account`.`user_id` = ' . $userid);
        //Log::printSqls();
        return $account;
    }

    function get_my_account() {
        return $this->get_user_account(0);
    }

    function insert_old($data, $doValidation=true) {
        if (parent::validates($data)) {
            $data['Account']['last_balance'] = -(double) ($data['Account']['last_balance']);
            parent::insert($data, false);
            // create ledger
            $this->loadModel('Ledger');
            if ($this->Ledger->saveAccount(
                            array('Ledger' => array(
                                    'account_id' => $this->lastInsertId(),
                                    'date' => $data['Account']['last_update'],
//                    'time'=>date('H:i:s'),  // required only in mimtel_9004
                                    'cur_id' => $data['Account']['cur_id'],
                                    'amount' => $data['Account']['last_balance'])
                    )) === false) {
                //Log::printSqls();
                die('STILL CRITICAL ERROR AT LEDGER');
            }


            return true;
        }
        return false;
    }

    function insertToLedger($data, $options) {
//        $data['Account']['last_balance'] = -(double)($data['Account']['last_balance']);
//        parent::insert($data, array('validate_with'=>false));
//        $Ledger = new Ledger(); 
//        $L = array('Ledger'=>array(
//                    'account_id'=>$this->lastInsertId(),
//                    'date'=>$data['Account']['last_update'],
//                    'cur_id'=>$data['Account']['cur_id'],
//                    'amount'=> $data['Account']['last_balance']
//                    )
//                );
//        if( $Ledger->saveAccount($L) === false ) {
//            //Log::printSqls();
//            die('STILL CRITICAL ERROR AT LEDGER');
//        }
        return true;
    }

    function getCustomerCurrency($customer_id) {
        $currency_name = $this->query()->select(array('cur_id'))->where("user_id = %d", array($customer_id))->one();
        return $currency_name['Account']['cur_id'];
    }

    public function getCustomerAccountId($user_id = 0) {
        if ($user_id) {
            $account = $this->query()->where('user_id = %d', array($user_id))->one();
            return $account['Account']['id'];
        }
        return 0;
    }

}

?>