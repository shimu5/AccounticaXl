<?php

/**
 * Created by JetBrains PhpStorm.
 * User: zubair
 * Date: 4/18/14
 * Time: 12:43 AM
 * To change this template use File | Settings | File Templates.
 */

namespace accountica\models;

class VendorStatus {
    const ACTIVE = 1;
    const DELETED = 0;

    public static $messages = array(
        VendorStatus::ACTIVE => 'Active',
        VendorStatus::DELETED => 'Deleted'
    );

    public static function getStatusString($status) {
        if (isset(VendorStatus::$messages[$status]))
            return VendorStatus::$messages[$status];
        return 'Unknown';
    }

    /**
     * return array of all status messages
     * @param $empty string
     */
    public static function getAllStatus($empty=false) {
        if ($empty !== false)
            return array('' => $empty) + VendorStatus::$messages;
        return VendorStatus::$messages;
    }

}

class Vendor extends \Furina\mvc\model\Model {

    public $table = 'users';
    public $usertype = 2;
    public $validate_vend = array(
        'name' => array('_required_'),
        'user_name' => array('_required_'),
        'password' => array('_required_'),
        'email' => array('_required_', 'email'),
    );
    
    function saveVendor($data, $options){
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
    
    public function getVendorNameList($intro=false){
        //echo 'type:'.$this->usertype;
        return ($intro) ? array('' => $intro) + $this->query()->where('usertype=%d',array($this->usertype))->orderby('name ASC')->map('id', 'name') : $this->query()->where('usertype=%d',array($this->usertype))->orderby('name ASC')->map('id', 'name');
    }
    
    public function getAccountVendorNameList($intro = false){
        $this->bindModel(
                array(
                    'belongsTo' => array(
                        'accountica\models\Account' => array(
                            'conditions' => "Vendor.id = Account.user_id"
                        )
                    )
                )
        );
        
        return $ret =  ($intro) ? array('' => $intro) + $this->query()->select(array('Account.id'=>'account_id','name'))->where('usertype=%d',array($this->usertype))->orderby('name ASC')->map('account_id', 'name') : $this->query()->select(array('Account.id'=>'account_id','name'))->where('usertype=%d',array($this->usertype))->orderby('name ASC')->map('account_id', 'name');        
    }
    
    public function getGatewaysInfo($acc_id= ''){
        $gateway_model = new Gateway();
        $gateway_model->bindModel(
                array(
                    'belongsTo' => array(
                        'accountica\models\Account' => array(
                            'conditions' => "Gateway.vendor_id = Account.user_id"
                        )
                    )
                )
        );
        return $ret =  ($intro) ? array('' => $intro) + $gateway_model->query()->select(array('Gateway.id','Gateway.ip_number','Gateway.rate'))->where('Account.id=%d',array($acc_id))->orderby('ip_number ASC')->all() : $gateway_model->query()->select(array('Gateway.id','Gateway.ip_number','Gateway.rate'))->where('Account.id=%d',array($acc_id))->orderby('ip_number ASC')->all();
    }
}
