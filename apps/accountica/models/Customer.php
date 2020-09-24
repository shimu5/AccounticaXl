<?php

/**
 * Created by JetBrains PhpStorm.
 * User: zubair
 * Date: 4/18/14
 * Time: 12:43 AM
 * To change this template use File | Settings | File Templates.
 */

namespace accountica\models;

class CustomerStatus {
    const ACTIVE = 1;
    const DELETED = 0;

    public static $messages = array(
        CustomerStatus::ACTIVE => 'Active',
        CustomerStatus::DELETED => 'Deleted'
    );

    public static function getStatusString($status) {
        if (isset(CustomerStatus::$messages[$status]))
            return CustomerStatus::$messages[$status];
        return 'Unknown';
    }

    /**
     * return array of all status messages
     * @param $empty string
     */
    public static function getAllStatus($empty=false) {
        if ($empty !== false)
            return array('' => $empty) + CustomerStatus::$messages;
        return CustomerStatus::$messages;
    }        
}

class Customer extends \Furina\mvc\model\Model {

    public $table = 'users';
    public $usertype = 1;
    public $validate_cust = array(
        'name' => array('_required_'),
        'user_name' => array('_required_'),
        'password' => array('_required_'),
        'email' => array('_required_', 'email'),
    );
    
    function saveCustomer($data, $options){
        if(isset($data['Account'])){
            parent::save($data, $options);
            $user_id = (isset($data['Account']['user_id']) && $data['Account']['user_id'] > 0)? $data['Account']['user_id']: $this->lastInsertId();
            
            $account = new \accountica\models\Account();
            
            //remove previous one
            //$query = "UPDATE `accounts` SET `user_id`='0' WHERE (`user_id`='$user_id')";
            //$this->execute($query);

            $data['Account']['user_id'] = $user_id;
            $account->save($data, $options);
        }
    }
    
    public function getCustomerNameList($intro = false){
        //echo 'type:'.$this->usertype;
        return ($intro) ? array('' => $intro) + $this->query()->where('usertype=%d',array($this->usertype))->orderby('name ASC')->map('id', 'name') : $this->query()->where('usertype=%d',array($this->usertype))->orderby('name ASC')->map('id', 'name');
    }

    public function getAccountCustomerNameList($intro = false){
        $this->bindModel(
                array(
                    'belongsTo' => array(
                        'accountica\models\Account' => array(
                            'conditions' => "Customer.id = Account.user_id"
                        )
                    )
                )
        );
        
        return $ret =  ($intro) ? array('' => $intro) + $this->query()->select(array('Account.id'=>'account_id','name'))->where('usertype=%d',array($this->usertype))->orderby('name ASC')->map('account_id', 'name') : $this->query()->select(array('Account.id'=>'account_id','name'))->where('usertype=%d',array($this->usertype))->orderby('name ASC')->map('account_id', 'name');        
    }

    public function getResellersInfo($acc_id= ''){
        $reseller_model = new Reseller();
        $reseller_model->bindModel(
                array(
                    'belongsTo' => array(
                        'accountica\models\Account' => array(
                            'conditions' => "Reseller.customer_id = Account.user_id"
                        )
                    )
                )
        );       
        return $ret =  ($intro) ? array('' => $intro) + $reseller_model->query()->select(array('Reseller.id','Reseller.login','Reseller.rate'))->where('Account.id=%d',array($acc_id))->orderby('login ASC')->all() : $reseller_model->query()->select(array('Reseller.id','Reseller.login','Reseller.rate'))->where('Account.id=%d',array($acc_id))->orderby('login ASC')->all();
    }

    

}
