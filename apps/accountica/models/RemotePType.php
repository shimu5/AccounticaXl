<?php
namespace accountica\models;
class PType{
    
    // customer transactions
    const Opening = 0;
    const Payment = 1;
    const CreditAllow = 2;
    const CreditReturn = 3;
    //const PaymentMy = 11;

    static $customerTransType = array(
        PType::Payment  => 'Receive Payment',
        PType::CreditAllow  => 'Credit Allow',
        PType::CreditReturn  => 'Credit Return',
       
    );
    static $vendorTransType = array(
        PType::Bill  => 'Bill',
        PType::Invoice  => 'Invoice'
       
    );
    
    // vendor transactions
    const Bill = 4;
    const Credit = 5;
    const Invoice = 6;

    // Bank Transactions
    const Withdrawal = 7;
    const Deposit = 8;
    const Transfer = 9;
    const TransferRev = 10;


    // Agent Transactions
    const AgentTransfer = 11;
    const AgentPayment = 12;
    const Commission = 13;

    //Category
    const Cash = 1;
    
    static $names = array(
        0  => 'Open',
        1  => 'Payment',
        2  => 'Credit Allow',
        3  => 'Credit Return',
        4  => 'Bill Payment',
        5  => 'Credit Received',
        6  => 'Invoice Received',
        7  => 'Withdrawal',
        8  => 'Deposit',
        9  => 'Transfer',
        10 => 'Transfer',
        11 => 'Transfer',// agent transfer (11)
        12 => 'Payment',// agent payment (12)
        13 => 'Commission'
    );
    static function toString($type) {
        return PType::$names[$type];
    }


    static $actionNames = array(
        
        1  => 'payment_edit',
        2  => 'credit_allow_edit',
        3  => 'credit_given_edit',
        4  => 'bill_edit',
        5  => 'credit_edit',
        6  => 'invoice_edit',
        7  => 'withdraw_edit',
        8  => 'deposit_edit',
        9  => 'transfer_edit',
        11 => 'transfer_edit',
        12 => 'payment_edit',
        13 => 'commission_edit'
        
    );
    static function getAction($type){
        return PType::$actionNames[$type];
    }


    static $commitActionNames = array(

        1  => 'payment_commit',
        2  => 'credit_allow_commit',
        3  => 'credit_given_commit',
        4  => 'bill_commit',
        5  => 'credit_commit',
        6  => 'invoice_commit',
        7  => 'withdraw_commit',
        8  => 'deposit_commit',
        9  => 'transfer_commit',
        11 => 'transfer_commit',
        12 => 'payment_commit',
        13 => 'commission_commit'

    );
    static function getCommitAction($type){
        return PType::$commitActionNames[$type];
    }
    
    static $commitCatNames = array(
        PType::Cash  => 'Cash',

    );
    static function getCatName($type){
        return PType::$commitCatNames[$type];
    }



}
class RemotePType extends \Furina\mvc\model\Model {
    var $table = false;
}

?>