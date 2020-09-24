<?php
namespace accountica\controllers;
use Furina\mvc\controller\Response;

class Menus extends CrudeController {

    public $model = false;

    public $uses = array(

    );
    
    public function anonIndex($request, $response){
        $url = array();
        $root = $this->c->Router->root;
        $url = array(
            array(
                'title'=>'Bank',
                'menu'=>array(
                    'List'=>'index.php/accountica/Banks/List',
                    'Add'=>'index.php/accountica/Banks/Add',
                    'Transactions'=>'index.php/accountica/Banks/Transactions',
                    'Deposit'=>'index.php/accountica/Banks/Deposit',
                    'Withdrawal'=>'index.php/accountica/Banks/Withdrawal',
                    'Pending Transactions'=>'index.php/accountica/Banks/PendingTransactions',
                    'Transfer'=>'index.php/accountica/Banks/Transfer',
                    'TransactionForm'=>'index.php/accountica/Banks/TransactionForm',
                    'TransactionForm2'=>'index.php/accountica/Banks/TransactionForm2'
                )
            ),
            array(
                'title'=>'Currencies',
                'menu'=>array(
                    'List'=>'index.php/accountica/Curs/List',
                    'Add'=>'index.php/accountica/Curs/Add',
                    'Add Currency'=>'index.php/accountica/Curs/AddCurrency',
                    'Currency Chart'=>'index.php/accountica/curs/CurrencyChart'
                )
            ),
            array(
                'title'=>'Customers',
                'menu'=>array(
                    'Add'=>'index.php/accountica/Customers/Add',
                    'List'=>'index.php/accountica/Customers/List',
                    'Add Payment'=>'index.php/accountica/customers/addpayment',
                    'Panding Transaction'=>'index.php/accountica/customers/PendingTransactions',
                    'Assign Transaction'=>'index.php/accountica/Customers/AssignResellers',
                )
            ),
            array(
                'title'=>'Vendors',
                'menu'=>array(
                    'List'=>'index.php/accountica/Vendors/List',
                    'Add'=>'index.php/accountica/Vendors/Add',
                    'Add Payment'=>'index.php/accountica/Vendors/addpayment',
                    'Assign Gateway'=>'index.php/accountica/vendors/AssignGateways'
                )
            ),
            array(
                'title'=>'Gateways',
                'menu'=>array(
                    'List'=>'index.php/accountica/Gateways/List',
                    'Add'=>'index.php/accountica/Gateways/Add',
                )
            ),
            array(
                'title'=>'Products',
                'menu'=>array(
                    'List'=>'index.php/accountica/Products/List',
                    'Add'=>'index.php/accountica/Products/Add',
                )
            ),
            array(
                'title'=>'Resellers',
                'menu'=>array(
                    'List'=>'index.php/accountica/Resellers/List',
                    'Add'=>'index.php/accountica/Resellers/Add',
                )
            ),
            array(
                'title'=>'Servers',
                'menu'=>array(
                    'List'=>'index.php/accountica/Servers/List',
                    'Add'=>'index.php/accountica/Servers/Add',
                    'Sync List'=>'index.php/accountica/Servers/SynchronizationList',
                )
            ),
            array(
                'title'=>'Backups',
                'menu'=>array(
                    'List'=>'index.php/accountica/Backups/List',
                    'Backup'=>'index.php/accountica/Backups/Backup',
                )
            ),
            array(
                'title'=>'Settings',
                'menu'=>array(
                    'List'=>'index.php/accountica/Settings/pagesetting',
                )
            ),
            array(
                'title'=>'Schedules',
                'menu'=>array(
                    'List'=>'index.php/scheduler/schedules/TaskList',
                    'Add'=>'index.php/scheduler/schedules/AddTask',
                )
            )
        );
        
        $response->set('URL',$url);
    }
}

