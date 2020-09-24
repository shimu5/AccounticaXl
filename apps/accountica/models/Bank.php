<?php
/**
 * Created by JetBrains PhpStorm.
 * User: zubair
 * Date: 4/18/14
 * Time: 12:43 AM
 * To change this template use File | Settings | File Templates.
 */
namespace accountica\models;

class Bank extends \Furina\mvc\model\Model {
    var $validate_insert = array(
//        'acc_name' => array('_required_'),
//        'acc_no' => array('_required_'),
        //'email' => array('_required_', 'email'),
//        'password' => array('_required_', /*'hash'*/),
        //'retype_password' => array('hash'),
        //'_post_' => array('password_match'),

    );
    
    function saveBankAccount($data, $options){
        if(isset($data['Account'])){
            $account = new \accountica\models\Account();
            
            $account->save($data, array('validate_with'=>false));
            $account_last_id = (isset($data['Account']['id']) && $data['Account']['id'] > 0)? $data['Account']['id']: $account->lastInsertId();
            $data['Bank']['account_id'] = $account_last_id;
            parent::save($data, $options);

//            $bank_id = $this->lastInsertId();
//                $ledger = array(
//                    'Ledger'=>array(
//                        'dst_bank_id'=>14,
//                        'anount'=>$request->data['Account']['last_balance'],
//                        'deposit'=>$request->data['Account']['last_balance'],
//                        'cur_id'=>$cur->getBaseCurrency(),
//                        'deposit_cur_id'=>$request->data['Account']['cur_id'],
//                        'tr_data'=>$request->data['Account']['opening_date'],
//                        'description'=>'Opening Balance',
//                        'type'=>  \accountica\models\PType::Opening,
//                        'created_by'=>  $this->c->Auth->user('id')
//                    )
//                );
//
//                $ledger_model->insertToLedger($ledger);
//                echo $ledger_model->lastInsertId();die;
        }
    }
    
    public function getBankNameList($intro = false){
        return ($intro) ? array('' => $intro) + $this->query()->orderby('bank_name ASC')->map('id', 'bank_name') : $this->query()->orderby('bank_name ASC')->map('id', 'bank_name');
    }
    
    public function getBankAccountList($intro = false){
        return ($intro) ? array('' => $intro) + $this->query()->orderby('acc_name ASC')->map('id', 'acc_name') : $this->query()->orderby('acc_name ASC')->map('id', 'acc_name');
    }
    
    public function getBankAndAccountList($intro = false, $conds = ''){
        $Account = new Account();
        $this->bindModel(
            array(
                'hasOne' => array(
                    'accountica\models\Account' => array(
                        'localKey' => 'account_id',
                        'foreignKey' => 'id'
                              )
                     )
            ) 
        );
        $c = $this->query();
        if($conds != '') $c = $c->where($conds);
        $c = $c->map('account_id', 'acc_name');
        return ($intro) ? array('' => $intro) + $c : $c;
    }
    public function getBankAccountId($bank_id = 0){
        if($bank_id){
            $bank =$this->read($bank_id);
            return $bank['Bank']['account_id'];
        }
        return 0;
    }
}
