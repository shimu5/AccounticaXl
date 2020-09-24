<?php

namespace accountica\controllers;

use Furina\mvc\controller\Response;

class Vendors extends CrudeController {

    public $model = 'Vendor';
    public $uses = array(
    );

    public function anonList($request, $response) {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {    //ajax check
            $model = $this->m->{$this->model};
            $customer = new \accountica\models\Vendor();
            $model->bindModel(
                    array(
                        'belongsTo' => array(
                            'accountica\models\Account' => array(
                                'localKey' => 'id',
                                'foreignKey' => 'user_id'
                            )
                        )
            ));

            $url = $this->c->Router->root . 'index.php/accountica/vendors/list';
            $response->set('grid_url', $url);
            $this->c->Pagination->page_url = $url;

            $pagedata = $this->c->Pagination->paginate($model->query()->where('Vendor.usertype = %d', array($model->usertype)));
            if (isset($pagedata['rows']) && !empty($pagedata['rows'])) {
                foreach ($pagedata['rows'] as $key => $values) {
                    $pagedata['data'][$key] = array_merge($values['Vendor'], $values['Account']);
                    $pagedata['data'][$key]['no'] = ($key + 1);
                    $pagedata['data'][$key]['transaction'] = '<a href="' . $this->c->Router->root . 'index.php/accountica/vendors/transactions/' . $values['Vendor']['id'] . '">Transaction</a>';
                    $pagedata['data'][$key]['edit'] = '<a href="' . $this->c->Router->root . 'index.php/accountica/vendors/edit/' . $values['Vendor']['id'] . '?type=' . $model->usertype . '">edit</a>';
                    $pagedata['data'][$key]['delete'] = '<a href="' . $this->c->Router->root . 'index.php/accountica/vendors/delete/' . $values['Vendor']['id'] . '">delete</a>';
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
                $request->data['Vendor']['usertype'] = $model->usertype;
                $request->data['Account']['last_balance'] = $request->data['Account']['opening_balance'];
                $model->saveVendor($request->data, array('validate_with' => false));
                $vendor_id = $model->lastInsertId();
                $account_id = $account->getCustomerAccountId($vendor_id);
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

                
                $url = $this->c->Router->root . 'index.php/accountica/vendors/list';
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
                    $request->data['Vendor']['usertype'] = $model->usertype;
                    $request->data[$this->model][$model->primaryKey] = $id;
                    $request->data['Account']['user_id'] = $id;
                    $request->data['Account']['id'] = $account['Account']['id'];
                    $model->saveVendor($request->data, array('validate_with' => false));
                    $url = $this->c->Router->root . 'index.php/accountica/vendors/list';
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
            $url = $this->c->Router->root . 'index.php/accountica/vendors/list';
            redirect($url);
        }

        $response->render('template', 'accountica/templates/Vendors/anonAdd.php');
    }

    public function anonTransactions($request, $response) {
        $ledger_model = $this->m->Ledger;
        list($vendor_id) = $request->params(0);
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
//                                'conditions' => "Ledger.account_id = Account.id AND Account.user_id = $vendor_id",
//                                'jointype'=>'JOIN'
//                            ),
//                            'accountica\models\Reseller' => array(
//                                'fields' => array('login', 'server_id', 'table_type', 'level'),
//                                'localKey' => 'reseller_id',
//                                'foreignKey' => 'id'
//                            ), 'accountica\models\Bank' => array(
//                                'localKey' => 'account_id',
//                                'foreignKey' => 'account_id'
//                            )
//                        )
//                    )
//            );


            $GatewayModel = new \accountica\models\Gateway(); // for loading PType
            $gateway_list = $GatewayModel->getGatewayList();

            $is_vendor = ($vendor_id) ? "AND Account.user_id = $vendor_id" : "";

//            $query = "SELECT Ledger.*, Account.cur_id,Reseller.server_id,Reseller.login FROM ledgers AS Ledger
//JOIN accounts AS Account ON (Ledger.account_id = Account.id AND Account.user_id = $vendor_id)
//LEFT JOIN resellers AS Reseller ON (Ledger.reseller_id = Reseller.id)
//JOIN users ON Account.user_id = users.id  AND users.usertype = $model->usertype";

            $query = "SELECT Ledger.*,Bank.bank_name, Bank.acc_name,Account.cur_id,Gateways.server_id,Gateways.ip_number FROM ledgers AS Ledger
JOIN accounts AS Account ON (Ledger.account_id = Account.id $is_vendor)
JOIN banks AS Bank ON (Bank.account_id = Account.id)
LEFT JOIN gateways AS Gateways ON (Ledger.reseller_id = Gateways.id)
JOIN users ON Account.user_id = users.id  AND users.usertype = $model->usertype";

            $ledgers = $ledger_model->execute_query($query);

            $url = $this->c->Router->root . 'index.php/accountica/customers/transactions/' . $vendor_id;
            $response->set('grid_url', $url);
//            $this->c->Pagination->page_url = $url;
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
                    //$values['Ledger']['server_id'] = $servers[$values['Ledger']['server_id']];
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

    public function anonPendingTransactions($request, $response) {
        $model = $this->m->{$this->model};
        $pending_ledger = new \accountica\models\PendingLedger();
        $RemotePType = new \accountica\models\RemotePType(); // for loading PType
        $GatewayModel = new \accountica\models\Gateway(); // for loading PType
        $response->set('gateway_list', $GatewayModel->getGatewayList());

        $bank = new \accountica\models\Bank();
        $response->set('bank', $bank->query()->select(array('id', 'bank_name'))->map('id', 'bank_name'));
        $admin = new \accountica\models\Admin();
        $response->set('admins', $admin->admins_list());
        //$response->set('rows', $pending_ledger->query()->where('is_posted = 0')->all());

        list($vendor_id) = $request->params(0);

        $is_vendor = ($vendor_id) ? "AND Account.user_id = $vendor_id" : "";
        $query = "SELECT Ledger.*,Bank.bank_name, Bank.acc_name, Account.cur_id,Gateways.server_id,Gateways.ip_number FROM pending_ledgers AS Ledger
JOIN accounts AS Account ON (Ledger.account_id = Account.id $is_vendor)
JOIN banks AS Bank ON (Bank.account_id = Account.id)
LEFT JOIN gateways AS Gateways ON (Ledger.reseller_id = Gateways.id)
JOIN users ON Account.user_id = users.id  AND users.usertype = $model->usertype where Ledger.is_posted = 0";
        $pledgers = $pending_ledger->execute_query($query);
        $response->set('rows', $pledgers);
        $response->set('url', $this->c->Router->root . 'index.php/accountica/vendors/PendingTransactionsLedger');
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

    public function anonDelete($request, $response) {
        $model = $this->m->{$this->model};
        list($id) = $request->params(null);

        if (!empty($id) && ($data = $model->read($id)) !== null) {
            $this->check_user_type($data[$this->model]['usertype']);
            try {
                $model->delete($id);
                $url = $this->c->Router->root . 'index.php/accountica/vendors/list';
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
            $url = $this->c->Router->root . 'index.php/accountica/vendors/list';
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

    public function anonAssignGateways($request, $response) {
        //pr($request->data);
        $server_id = $request->data['Vendor']['server_id'];
        $vendor_id = $request->data['Vendor']['vendor_id'];

        $gateway_model = $this->m->Gateway;
        $conditions = '1=1';
        if (!empty($server_id)) {
            $conditions .= ' AND server_id = ' . $server_id;
        }
        if (!empty($customer_id)) {
            $conditions .= ' AND vendor_id = ' . $vendor_id;
        }
        $rows = $gateway_model->query()->where($conditions)->all();
//        //pr($rows);
        $response->set('rows', $rows);

        $vendor_model = $this->m->Vendor;
        $response->set('getVendorNameList', $vendor_model->getVendorNameList());
        $machine_model = $this->m->Machine;
        $response->set('getServerNameList', $machine_model->getServerNameList());
//        $response->set('reseller_type',\accountica\models\ResellersType::getAllReseller());

        $ajax_url = $this->c->Router->root . 'index.php/accountica/Gateways/GetwaysUpdate';
        $response->set('url', $ajax_url);
    }

    public function anonAddPayment($request, $response) {
        $model = $this->m->{$this->model};
        $Bank_model = $this->m->Bank;
        $response->set('bankNameList', $Bank_model->getBankNameList());
        $response->set('bank_account', $Bank_model->getBankAccountList());

        $remPtype_model = $this->m->RemotePType;
        $response->set('vendorList', $model->getAccountVendorNameList());
        $ajax_url_res = $this->c->Router->root . 'index.php/accountica/Vendors/Gateways';
        $response->set('url', $ajax_url_res);
        $ajax_url_rate = $this->c->Router->root . 'index.php/accountica/Vendors/GatewayRate';
        $response->set('url_rate', $ajax_url_rate);
        $cur_model = $this->m->Cur;
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

    public function anonGateways($request, $response) {
        $gateways = $this->m->{$this->model}->getGatewaysInfo($request->post['vendor_id']);
        echo json_encode($gateways);
        die();
    }

    public function anonGatewayRate($request, $response) {
        echo $rate = $this->m->Gateway->getRate($request->post['gateway_id']);
        die();
    }

}

