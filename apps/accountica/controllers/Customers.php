<?php

namespace accountica\controllers;

use Furina\mvc\controller\Response;

class Customers extends CrudeController {

    public $model = 'Customer';
    public $uses = array(
    );

    public function anonList($request, $response) {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {    //ajax check
            $model = $this->m->{$this->model};
            $customer = new \accountica\models\Customer();
            $model->bindModel(
                    array(
                        'belongsTo' => array(
                            'accountica\models\Account' => array(
                                'localKey' => 'id',
                                'foreignKey' => 'user_id'
                            )
                        )
            ));

            $url = $this->c->Router->root . 'index.php/accountica/customers/list';
            $response->set('grid_url', $url);
            $this->c->Pagination->page_url = $url;

            $pagedata = $this->c->Pagination->paginate($model->query()->where('Customer.usertype = %d', array($model->usertype)));
            if (isset($pagedata['rows']) && !empty($pagedata['rows'])) {
                foreach ($pagedata['rows'] as $key => $values) {
                    $pagedata['data'][$key] = array_merge($values['Customer'], $values['Account']);
                    $pagedata['data'][$key]['no'] = ($key + 1);
                    $pagedata['data'][$key]['transaction'] = '<a href="' . $this->c->Router->root . 'index.php/accountica/customers/transactions/' . $values['Customer']['id'] . '">Transaction</a>';
                    $pagedata['data'][$key]['edit'] = '<a href="' . $this->c->Router->root . 'index.php/accountica/customers/edit/' . $values['Customer']['id'] . '?type=' . $model->usertype . '">edit</a>';
                    $pagedata['data'][$key]['delete'] = '<a href="' . $this->c->Router->root . 'index.php/accountica/customers/delete/' . $values['Customer']['id'] . '">delete</a>';
                }
            }
            echo json_encode($pagedata);
            die;
        }
    }

    public function anonAdd($request, $response) {
        $model = $this->m->{$this->model};
        $ledger_model = new \accountica\models\Ledger();
        $account = new \accountica\models\Account();
        $cur = new \accountica\models\Cur();
        $ptype = new \accountica\models\RemotePType();
        $response->set('currs', $this->m->Cur->getCurrencyList('Select Currency'));
        $response->set('country', $this->m->Countries->getCountryList('Select Country'));
        //$response->set('banks', $this->m->Bank->getBankAndAccountList('Select Bank', 'Account.user_id = 0'));

        // save data
        if ($request->isPost()) {
            try {
                $request->data['Customer']['usertype'] = $model->usertype;
                $request->data['Account']['last_balance'] = $request->data['Account']['opening_balance'];
                $model->saveCustomer($request->data, array('validate_with' => false));
                $customer_id = $model->lastInsertId();
                $account_id = $account->getCustomerAccountId($customer_id);
                $ledger = array(
                    'Ledger' => array(
                        'account_id' => $account_id,
                        'amount' => $request->data['Account']['opening_balance'],
                        'deposit' => $request->data['Account']['opening_balance'],
                        'cur_id' => $cur->getBaseCurrency(),
                        'deposit_cur_id' => $request->data['Account']['cur_id'],
                        'tr_data' => $request->data['Account']['opening_date'],
                        'description' => 'Opening Balance',
                        'type' => \accountica\models\PType::Opening,
                        'created_by' => $this->c->Auth->user('id')
                    )
                );
                $ledger_model->insertToLedger($ledger);

                $url = $this->c->Router->root . 'index.php/accountica/customers/list';
                redirect($url);
            } catch (\Furina\mvc\model\exceptions\InvalidException $e) {
                $response->set('error', $e->getErrors());
            }
        }
    }

    public function anonEdit($request, $response) {
        $model = $this->m->{$this->model};
        list($id) = $request->params(null);
        $usertype = $request->get('type');
        $this->check_user_type($usertype);

        $row = $model->read($id);
        if (!empty($id) && ($row) !== null) {
            $account = $this->m->Account->query()->where('user_id = %d', array($id))->one();
            if ($request->isPost()) {
                try {
                    $request->data['Customer']['usertype'] = $model->usertype;
                    $request->data[$this->model][$model->primaryKey] = $id;
                    $request->data['Account']['user_id'] = $id;
                    $request->data['Account']['id'] = $account['Account']['id'];
                    $model->saveCustomer($request->data, array('validate_with' => false));
                    $url = $this->c->Router->root . 'index.php/accountica/customers/list';
                    redirect($url);
                } catch (\Furina\mvc\model\exceptions\InvalidException $e) {
                    $response->set('error', $e->getErrors());
                }
            } else {
                $response->set('currs', $this->m->Cur->getCurrencyList('Select Currency'));
                $response->set('country', $this->m->Countries->getCountryList('Select Country'));
                //$response->set('banks', $this->m->Bank->getBankAndAccountList('Select Bank', 'Account.user_id IN(0, ' . $id . ')'));
                $row['Account'] = $account['Account'];
                $response->set('data', $row);
            }
        } else {
            //TO DO :: Set flash "item Not Found"
            $url = $this->c->Router->root . 'index.php/accountica/customers/list';
            redirect($url);
        }

        $response->render('template', 'accountica/templates/Customers/anonAdd.php');
    }

    public function anonTransactions($request, $response) {
        $ledger_model = $this->m->Ledger;
        list($customer_id) = $request->params(null);
        $banks = $this->m->Bank->getBankAccountList();
        $servers = $this->m->Machine->getListByID();
        $admins = $this->m->Admin->admins_list();
        $response->set('banks', $banks);
        $response->set('servers', $servers);
        $response->set('admins', $admins);

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {    //ajax check
            $model = $this->m->{$this->model};

            $PaymentType = new \accountica\models\RemotePType();

//            $ledger_model->bindModel(
//                    array(
//                        'belongsTo' => array(
//                            'accountica\models\Account' => array(
//                                'conditions' => "Ledger.account_id = Account.id AND Account.user_id = $customer_id",
//                                'jointype'=>'JOIN'
//                            ),
//                            'accountica\models\Reseller' => array(
//                                'fields' => array('login', 'server_id', 'table_type', 'level'),
//                                'localKey' => 'reseller_id',
//                                'foreignKey' => 'id'
//                            ), 'accountica\models\Bank' => array(
//                                'localKey' => 'dst_bank_id',
//                                'foreignKey' => 'id'
//                            )
//                        )
//                    )
//            );
            $query = "SELECT Ledger.*,Bank.bank_name, Bank.acc_name, Account.cur_id,Reseller.server_id,Reseller.login FROM ledgers AS Ledger
JOIN accounts AS Account ON (Ledger.account_id = Account.id AND Account.user_id = $customer_id)
JOIN banks AS Bank ON (Bank.account_id = Account.id)
LEFT JOIN resellers AS Reseller ON (Ledger.reseller_id = Reseller.id)
JOIN users ON Account.user_id = users.id  AND users.usertype = $model->usertype";
            $ledgers = $ledger_model->execute_query($query);
            //pr($ledgers);die;

            $url = $this->c->Router->root . 'index.php/accountica/customers/transactions/' . $customer_id;
            $response->set('grid_url', $url);
            $this->c->Pagination->page_url = $url;
//            $ledgers = $ledger_model->query();
//            $pagedata = $this->c->Pagination->paginate($ledgers);
//            if (isset($pagedata['rows']) && !empty($pagedata['rows'])) {
//                foreach ($pagedata['rows'] as $key => $values) {
//                    $values['Ledger']['bank_name'] = $banks[$values['Ledger']['dst_bank_id']];
//                    $values['Ledger']['type'] = \accountica\models\PType::toString($values['Ledger']['type']);
//                    $values['Ledger']['server_id'] = $servers[$values['Reseller']['server_id']];
//                    $values['Ledger']['cur_id'] = $values['Account']['cur_id'];
//                    $values['Ledger']['created_by'] = $admins[$values['Ledger']['created_by']];
//                    $values['Ledger']['login'] = $values['Reseller']['login'];
//                    $pagedata['data'][$key] = $values['Ledger'];
//                    $pagedata['data'][$key]['no'] = ($key + 1);
//                }
//            }

            if (isset($ledgers) && !empty($ledgers)) {
                foreach ($ledgers as $key => $values) {
                    $values['Ledger']['bank_name'] = $values['Ledger']['bank_name'] . ':' . $values['Ledger']['acc_name'];
                    $values['Ledger']['type'] = \accountica\models\PType::toString($values['Ledger']['type']);
                    $values['Ledger']['server_id'] = $servers[$values['Ledger']['server_id']];
                    $values['Ledger']['created_by'] = $admins[$values['Ledger']['created_by']];
                    $pagedata['data'][$key] = $values['Ledger'];
                    $pagedata['data'][$key]['no'] = ($key + 1);
                }
            }
            echo json_encode($pagedata);
            die;
        }

        /* Graph start */
        $start_date = date('Y-m-d 00:00:00');
        $ending_date = date('Y-m-d 23:59:59', strtotime('-5 days'));

        $graph_data = $ledger_model->query()->select(array("sum(IF(amount>=0,amount,0))" => 'credit',
                    "sum(IF(amount<0,amount,0))" => "debit", "DATE(tr_date)" => 'tr_date'))->where("tr_date <= '%s' and tr_date >= '%s' AND account_id = %d", array($start_date, $ending_date, $customer_id))->groupby("DATE(tr_date)")->all();

        $datas[] = array('Date', 'Credit', 'Debit');
        foreach ($graph_data as $gr_data) {
            $datas[] = array($gr_data['Ledger']['tr_date'], doubleval($gr_data['Ledger']['credit']), doubleval(abs($gr_data['Ledger']['debit'])));
        }

        $response->set('data', json_encode($datas));
        /* Graph end */
    }

    public function anonDelete($request, $response) {
        $model = $this->m->{$this->model};
        list($id) = $request->params(null);

        if (!empty($id) && ($data = $model->read($id)) !== null) {

            $this->check_user_type($data[$this->model]['usertype']);
            try {
                $model->delete($id);
                $url = $this->c->Router->root . 'index.php/accountica/customers/list';
                redirect($url);
            } catch (\Furina\mvc\model\exceptions\InvalidException $e) {
                $response->set('error', $e->getErrors());
            }
        } else {
            
        }
    }

    function check_user_type($usertype = 0) {
        $model = $this->m->{$this->model};
        if ($usertype != $model->usertype) {
            $url = $this->c->Router->root . 'index.php/accountica/customers/list';
            redirect($url);
            die();
        }
    }

    public function anonGridTest($request, $response) {

    }

    public function anonGridData($request, $response) {
        $ledger_model = $this->m->Ledger;

        $pageSize = (isset($request->post['pageSize'])) ? $request->post['pageSize'] : 10;
        $pageNum = (isset($request->post['pageNum'])) ? $request->post['pageNum'] : 10;
        ;
        $totalPages = Intval($totalRows / $pageSize);

        $ledger_data = $ledger_model->query()->limit($pageSize)->all();
        $totalRows = count($ledger_data);
        $data = array('totalRows' => $totalRows, 'pageSize' => $pageSize, 'pageNum' => $pageNum, 'totalPages' => $totalPages);
        foreach ($ledger_data as $key => $ledger) {
            $data['data'][$key]['id'] = ($key + 1);
            $data['data'][$key]['reseller_id'] = $ledger['Ledger']['reseller_id'];
            $data['data'][$key]['amount'] = $ledger['Ledger']['amount'];
        }
        echo json_encode($data);
        die;
    }

    public function anonAssignResellers($request, $response) {
        /**
         * TO DO: Params e customer id dile customer list disabled ho be and 
         * with selected customer 
         */
        $server_id = $request->data['Customer']['server_id'];
        $level = $request->data['Customer']['level'];
        $customer_id = $request->data['Customer']['customer_id'];

        $reseller_model = $this->m->Reseller;

        $conditions = '1=1';
        if (!empty($server_id)) {
            $conditions .= ' AND server_id = ' . $server_id;
        }
        if (!empty($level)) {
            $conditions .= ' AND level = ' . $level;
        }
        if (!empty($customer_id)) {
            $conditions .= ' AND customer_id = ' . $customer_id;
        }

        $rows = $reseller_model->query()->where($conditions)->all();

//        pr($rows);die;
        $response->set('rows', $rows);

        $model = $this->m->{$this->model};
        $response->set('getCustomerNameList', $model->getCustomerNameList());
        $machine_model = $this->m->Machine;
        $response->set('getServerNameList', $machine_model->getServerNameList());
        $response->set('reseller_type', \accountica\models\ResellersType::getAllReseller());

        $ajax_url = $this->c->Router->root . 'index.php/accountica/resellers/ResellersUpdate';
        $response->set('url', $ajax_url);

        $response->set('data', $rows);
    }

    public function anonAddPayment($request, $response) {
        $model = $this->m->{$this->model};
        $remPtype_model = $this->m->RemotePType;
        $response->set('customerList', $model->getAccountCustomerNameList());
        $ajax_url_res = $this->c->Router->root . 'index.php/accountica/Customers/Resellers';
        $response->set('url', $ajax_url_res);
        $ajax_url_rate = $this->c->Router->root . 'index.php/accountica/Customers/ResellerRate';
        $response->set('url_rate', $ajax_url_rate);
        $cur_model = $this->m->Cur;
//        $response->set("cur_id",$cur_model->getBaseCurrency());
//        $response->set("dep_cur_id",$cur_model->getBaseCurrency());
        if ($request->isPost()) {
            try {

                $acc_info = $this->m->Account->get_account_info($request->data['PendingLedger']['account_id']);
                $request->data['PendingLedger']['deposit_cur_id'] = $acc_info['Account']['cur_id'];

                $this->m->PendingLedger->insertToLedger($request->data, array('validate_with' => false));
            } catch (\Furina\mvc\model\exceptions\InvalidException $e) {
                $response->set('error', $e->getErrors());
            }
        }
    }

    public function anonResellers($request, $response) {
        $resellers = $this->m->{$this->model}->getResellersInfo($request->post['customer_id']);
        echo json_encode($resellers);
        die();
    }

    public function anonResellerRate($request, $response) {
        echo $rate = $this->m->Reseller->getRate($request->post['reseller_id']);
        die();
    }

    public function anonPendingTransactions($request, $response) {
        $model = $this->m->{$this->model};
        $pending_ledger = new \accountica\models\PendingLedger();
        $RemotePType = new \accountica\models\RemotePType(); // for loading PType
        $ResellerModel = new \accountica\models\Reseller(); // for loading PType
        $response->set('reseller_list', $ResellerModel->getResellerList());

        $bank = new \accountica\models\Bank();
        $response->set('bank', $bank->query()->select(array('id', 'bank_name'))->map('id', 'bank_name'));
        $admin = new \accountica\models\Admin();
        $response->set('admins', $admin->admins_list());
        //$response->set('rows', $pending_ledger->query()->where('is_posted = 0')->all());

        list($customer_id) = $request->params(0);

        $is_customer = ($customer_id) ? "AND Account.user_id = $customer_id" : "";
        $query = "SELECT Ledger.*,Bank.bank_name, Bank.acc_name,Account.cur_id,Reseller.server_id,Reseller.login FROM pending_ledgers AS Ledger
JOIN accounts AS Account ON (Ledger.account_id = Account.id $is_customer)
JOIN banks AS Bank ON (Bank.account_id = Account.id)
LEFT JOIN resellers AS Reseller ON (Ledger.reseller_id = Reseller.id)
JOIN users ON Account.user_id = users.id  AND users.usertype = $model->usertype where Ledger.is_posted = 0";
        $pledgers = $pending_ledger->execute_query($query);
        pr($pledgers);die;
        $response->set('rows', $pledgers);
        $response->set('url', $this->c->Router->root . 'index.php/accountica/customers/PendingTransactionsLedger');
    }

    public function anonPendingTransactionsLedger($request, $response) {
        $pending_ledger = new \accountica\models\PendingLedger();
        $ledger = new \accountica\models\Ledger();

        $data = $pending_ledger->changestatus($request->post['postdata'], $request->post['id']);
        if ($request->post['postdata'] == 1) {
            unset($data['PendingLedger']['id']);
            unset($data['PendingLedger']['update_info']);
            $dataLadger['Ledger'] = $data['PendingLedger'];
            $ledger->insertToLedger($dataLadger);
        }
        die;
    }

}

