<?php

namespace accountica\controllers;

use Furina\mvc\controller\Response;

class Banks extends CrudeController {

    public $model = 'Bank';
    public $uses = array(
    );

    public function anonList($request, $response) {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {    //ajax check
            $model = $this->m->{$this->model};
            $bank = new \accountica\models\Bank();
            $model->bindModel(array(
                'hasOne' => array(
                    'accountica\models\Account' => array(
                        'localKey' => 'account_id',
                        'foreignKey' => 'id'
                    )
                )
                    )
            );

            $url = $this->c->Router->root . 'index.php/accountica/banks/list';
            $response->set('grid_url', $url);
            $this->c->Pagination->page_url = $url;

            $pagedata = $this->c->Pagination->paginate($model->query());
            if (isset($pagedata['rows']) && !empty($pagedata['rows'])) {
                foreach ($pagedata['rows'] as $key => $values) {
                    $pagedata['data'][$key] = array_merge($values['Bank'], $values['Account']);
                    $pagedata['data'][$key]['no'] = ($key + 1);
                    $pagedata['data'][$key]['edit'] = '<a href="' . $this->c->Router->root . 'index.php/accountica/banks/edit/' . $values['Bank']['id'] . '">edit</a>';
                    $pagedata['data'][$key]['delete'] = '<a href="' . $this->c->Router->root . 'index.php/accountica/banks/delete/' . $values['Bank']['id'] . '">delete</a>';
                }
            }
            echo json_encode($pagedata);
            die;
        }
    }

    public function anonPagingDemo($request, $response) {
        $model = $this->m->{$this->model};
        $bank = new \accountica\models\Bank();
        $model->bindModel(array(
            'hasOne' => array(
                'accountica\models\Account' => array(
                    'localKey' => 'account_id',
                    'foreignKey' => 'id'
                )
            )
                )
        );


        $this->c->Pagination->limit = 1;
        $this->c->Pagination->page_url = $this->c->Router->root . 'index.php/accountica/banks/pagingdemo';
        $rows = $this->c->Pagination->paginate($model->query());

        $response->set('paginate', $rows);
        $response->set('pages', $this->c->Pagination->renderPages($rows));
    }

    public function anonAdd($request, $response) {
        $model = $this->m->{$this->model};
        $cur = new \accountica\models\Cur();
        $ptype = new \accountica\models\RemotePType();
        if ($request->isPost()) {
            try {
                $request->data['Account']['last_balance'] = $request->data['Account']['opening_balance'];
                $model->saveBankAccount($request->data);
                $bank_id = $model->lastInsertId();
                $ledger = array(
                    'Ledger' => array(
                        'dst_bank_id' => $bank_id,
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
                $ledger_model = new \accountica\models\Ledger();
                $ledger_model->insertToLedger($ledger);
                $url = $this->c->Router->root . 'index.php/accountica/banks/list';
                redirect($url);
            } catch (\Furina\mvc\model\exceptions\InvalidException $e) {
                $response->set('error', $e->getErrors());
            }
        } else {
            $response->set('currencies', $cur->getCurrencyList());
        }
    }

    public function anonEdit($request, $response) {
        $account = new \accountica\models\Account();

        list($id) = $request->params(null);
        $model = $this->m->{$this->model};
        if (!empty($id) && ($row = $model->read($id)) !== null) {
            if ($request->isPost()) {
                try {
                    $request->data[$this->model][$model->primaryKey] = $id;
                    $request->data['Account']['id'] = $row['Bank']['account_id'];
                    $model->saveBankAccount($request->data);
                    $url = $this->c->Router->root . 'index.php/accountica/banks/list';
                    redirect($url);
                } catch (\Furina\mvc\model\exceptions\InvalidException $e) {
                    $response->set('error', $e->getErrors());
                }
            } else {
                $currency = new \accountica\models\Cur();
                $response->set('currencies', $currency->getCurrencyList());
                $account_info = $account->read($row['Bank']['account_id']);
                $row['Account'] = $account_info['Account'];
                $response->set('data', $row);
            }
        } else {
            $url = $this->c->Router->root . 'index.php/accountica/banks/list';
            redirect($url);
        }
        $response->render('template', 'accountica/templates/Banks/anonAdd.php');
    }

    public function anonDelete($request, $response) {
        $account = new \accountica\models\Account();
        list($id) = $request->params(null);
        $model = $this->m->{$this->model};
        $account_id = $model->query()->select(array('account_id', 'id'))->where('id=%d', array($id))->one();

        if (!empty($id) && ($data = $model->read($id)) !== null) {
            $model->delete($id);
            $account->delete($account_id['Bank']['account_id']);
        } else {
            //TODO :: setflash - item not found
        }
        $url = $this->c->Router->root . 'index.php/accountica/banks/list';
        redirect($url);
    }

    public function anonTransactions($request, $response) {
        $model = $this->m->{$this->model};
        list($bank_id) = $request->params(null);
        $admin = new \accountica\models\Admin();
        $ledger = new \accountica\models\Ledger();
        $account = new \accountica\models\Account();
        $RemotePType = new \accountica\models\RemotePType();
        $admins = $admin->admins_list();
        $response->set('admins', $admins);
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {    //ajax check
            $model->bindModel(array(
                'belongsTo' => array(
                    'accountica\models\Account' => array(
                        'localKey' => 'account_id',
                        'foreignKey' => 'id',
                    ),
                    '\accountica\models\Ledger' => array(
                        'className' => '\accountica\models\Ledger',
                        'localKey' => 'id',
                        'foreignKey' => 'dst_bank_id',
                    )
                )
            ));

            $url = $this->c->Router->root . 'index.php/accountica/banks/transactions';
            $response->set('grid_url', $url);
            $this->c->Pagination->page_url = $url;

            $pagedata = $this->c->Pagination->paginate($model->query()->where('Ledger.dst_bank_id=%d', array($bank_id)));
            if (isset($pagedata['rows']) && !empty($pagedata['rows'])) {
                foreach ($pagedata['rows'] as $key => $values) {
                    $values['Ledger']['acc_name'] = $values['Bank']['acc_name'];
                    $values['Ledger']['type'] = \accountica\models\PType::toString($values['Ledger']['type']);
                    $values['Ledger']['cur_id'] = $values['Account']['cur_id'];
                    $values['Ledger']['created_by'] = $admins[$values['Ledger']['created_by']];
                    $pagedata['data'][$key] = $values['Ledger'];
                    $pagedata['data'][$key]['no'] = ($key + 1);
                    $pagedata['data'][$key]['edit'] = '<a href="' . $this->c->Router->root . 'index.php/accountica/banks/edit/' . $values['Bank']['id'] . '">edit</a>';
                    $pagedata['data'][$key]['delete'] = '<a href="' . $this->c->Router->root . 'index.php/accountica/banks/delete/' . $values['Bank']['id'] . '">delete</a>';
                }
            }
            echo json_encode($pagedata);
            die;
        }


        if (!empty($rows)) {
            /* Graph start */
            $start_date = date('Y-m-d 00:00:00');
            $ending_date = date('Y-m-d 23:59:59', strtotime('-5 days'));

            $graph_data = $ledger->query()->select(array("sum(IF(amount>=0,amount,0))" => 'credit',
                        "sum(IF(amount<0,amount,0))" => "debit", "DATE(tr_date)" => 'tr_date'))->where("tr_date <= '%s' and tr_date >= '%s' AND Ledger.dst_bank_id=%d", array($start_date, $ending_date, $bank_id))->groupby("DATE(tr_date)")->all();

            $datas[] = array('Date', 'Credit', 'Debit');
            foreach ($graph_data as $gr_data) {
                $datas[] = array($gr_data['Ledger']['tr_date'], doubleval($gr_data['Ledger']['credit']), doubleval(abs($gr_data['Ledger']['debit'])));
            }

            $response->set('data', json_encode($datas));
        }
        /* Graph end */
    }

    public function anonDeposit($request, $response) {
        $model = $this->m->{$this->model};
        $response->set('bankNameList', $model->getBankNameList(false));
        $response->set('bank_account', $model->getBankAccountList(false));
        $response->set('base_cur', $this->m->Cur->getBaseCurrency(false));
        $pending_ledger = new \accountica\models\PendingLedger();
        $remoteType = new \accountica\models\RemotePType(); // For loading PType Class in RemotePType

        $response->set('category', \accountica\models\PType::$commitCatNames);
        //Request dta

        if (!empty($request->data)) {

            //for getting Cur id from Acount table
            $model->bindModel(array(
                'belongsTo' => array(
                    'accountica\models\Account' => array(
                        'conditions' => 'account_id = Account.id',
                        'fields' => array('cur_id')
                    )
                )
            ));

            $cur = $model->query()->select('cur_id')->where('Bank.id = ' . $request->data['PendingLedger']['dst_bank_id'])->one();
            $request->data['PendingLedger']['keep'] = 1;
            $request->data['PendingLedger']['is_posted'] = 0;
            $request->data['PendingLedger']['type'] = \accountica\models\PType::Deposit;
            $request->data['PendingLedger']['deposit_cur_id'] = $cur['Bank']['cur_id']; //bank acount cur id
            $request->data['PendingLedger']['created_by'] = $this->Auth->user('id');
            $request->data['PendingLedger']['tr_date'] = empty($request->data['PendingLedger']['tr_date']) ? date('Y-m-d H:i:s') : $request->data['PendingLedger']['tr_date'];
            $request->data['PendingLedger']['cur_id'] = $this->m->Cur->getBaseCurrency(false);

            try {
                $pending_ledger->insertToLedger($request->data, array('validate_with' => false));
                $url = $this->c->Router->root . 'index.php/accountica/banks/PendingTransactions';
                redirect($url);
            } catch (\Furina\mvc\model\exceptions\InvalidException $e) {
                $response->set('error', $e->getErrors());
                pr($e->getErrors());
            }
        }
    }

    public function anonPendingTransactions($request, $response) {
        $pending_ledger = new \accountica\models\PendingLedger();
        $RemotePType = new \accountica\models\RemotePType(); // for loading PType

        $bank = new \accountica\models\Bank();
        $response->set('bank', $bank->query()->select(array('id', 'bank_name'))->map('id', 'bank_name'));
        $admin = new \accountica\models\Admin();
        $response->set('admins', $admin->admins_list());
        $response->set('rows', $pending_ledger->query()->where('is_posted = 0')->all());
        $response->set('url', $this->c->Router->root . 'index.php/accountica/banks/PendingTransactionsLedger');
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

    public function anonWithdrawal($request, $response) {
        $model = $this->m->{$this->model};
        $response->set('bankNameList', $model->getBankNameList());
        $response->set('bank_account', $model->getBankAccountList());
        $response->set('base_cur', $this->m->Cur->getBaseCurrency());
        $pending_ledger = new \accountica\models\PendingLedger();
        $remoteType = new \accountica\models\RemotePType(); // For loading PType Class in RemotePType

        $response->set('category', \accountica\models\PType::$commitCatNames);
        //Request dta

        if (!empty($request->data)) {
            //for getting Cur id from Acount table
            $model->bindModel(array(
                'belongsTo' => array(
                    'accountica\models\Account' => array(
                        'conditions' => 'account_id = Account.id',
                        'fields' => array('cur_id')
                    )
                )
            ));
            $cur = $model->query()->select('cur_id')->where('Bank.id = ' . $request->data['PendingLedger']['dst_bank_id'])->one();
            $request->data['PendingLedger']['keep'] = 1;
            $request->data['PendingLedger']['is_posted'] = 0;
            $request->data['PendingLedger']['type'] = \accountica\models\PType::Withdrawal;
            $request->data['PendingLedger']['deposit_cur_id'] = $cur['Bank']['cur_id']; //bank acount cur id
            $request->data['PendingLedger']['created_by'] = $this->Auth->user('id');
            $request->data['PendingLedger']['tr_date'] = empty($request->data['PendingLedger']['tr_date']) ? date('Y-m-d H:i:s') : $request->data['PendingLedger']['tr_date'];
            $request->data['PendingLedger']['cur_id'] = $this->m->Cur->getBaseCurrency();

            try {
                $pending_ledger->insertToLedger($request->data, array('validate_with' => false));
                $url = $this->c->Router->root . 'index.php/accountica/banks/PendingTransactions';
                redirect($url);
            } catch (\Furina\mvc\model\exceptions\InvalidException $e) {
                $response->set('error', $e->getErrors());
                pr($e->getErrors());
            }
        }
    }

    public function anonTransfer($request, $response) {
        $model = $this->m->{$this->model};
        $response->set('bankNameList', $model->getBankNameList());
        $response->set('bank_account', $model->getBankAccountList());
        $response->set('base_cur', $this->m->Cur->getBaseCurrency());
        $pending_ledger = new \accountica\models\PendingLedger();
        $remoteType = new \accountica\models\RemotePType(); // For loading PType Class in RemotePType

        $response->set('category', \accountica\models\PType::$commitCatNames);
        //Request dta

        if (!empty($request->data)) {

            //for getting Cur id from Acount table
            $model->bindModel(array(
                'belongsTo' => array(
                    'accountica\models\Account' => array(
                        'conditions' => 'account_id = Account.id',
                        'fields' => array('cur_id')
                    )
                )
            ));

            $cur = $model->query()->select('cur_id')->where('Bank.id = ' . $request->data['PendingLedger']['dst_bank_id'])->one();
            $request->data['PendingLedger']['keep'] = 1;
            $request->data['PendingLedger']['is_posted'] = 0;
            $request->data['PendingLedger']['type'] = \accountica\models\PType::Transfer;
            $request->data['PendingLedger']['deposit_cur_id'] = $cur['Bank']['cur_id']; //bank acount cur id
            $request->data['PendingLedger']['created_by'] = $this->Auth->user('id');
            $request->data['PendingLedger']['tr_date'] = empty($request->data['PendingLedger']['tr_date']) ? date('Y-m-d H:i:s') : $request->data['PendingLedger']['tr_date'];
            $request->data['PendingLedger']['cur_id'] = $this->m->Cur->getBaseCurrency();

            try {
                $pending_ledger->insertToLedger($request->data, array('validate_with' => false));
            } catch (\Furina\mvc\model\exceptions\InvalidException $e) {
                $response->set('error', $e->getErrors());
                pr($e->getErrors());
            }
        }
    }

    public function anonBalanceSummary($request, $response) {

    }

    public function anonTransactionForm($request, $response) {
        $model = $this->m->{$this->model};
        $response->set('bankNameList', $model->getBankNameList());
        $response->set('bank_account', $model->getBankAccountList());
    }

    public function anonTransactionForm2($request, $response) {
        $model = $this->m->{$this->model};
        $response->set('bankNameList', $model->getBankNameList());
        $response->set('bank_account', $model->getBankAccountList());
    }

    public function anonGetExchangeRate($request,$response) {
        $bank_id = $request->post['bank'];
        $model = $this->m->{$this->model};
        $cur_model = $this->m->Cur;
        $rate_model = $this->m->Rate;
        $base_cur = $cur_model->getBaseCurrency();
        $model->bindModel(array(
                'belongsTo' => array(
                    'accountica\models\Account' => array(
                        'conditions' => 'account_id = Account.id',
                        'fields' => array('cur_id')
                    )
                )
            ));
        $bank = $model->read($bank_id);
        $bank_cur = $bank['Account']['cur_id'];
        $exchange_data = $rate_model->query()->where('base_cur_id = "%s" AND cur_id = "%s" AND end_date = "0000-00-00"',array($base_cur,$bank_cur,))->one();
        if(!empty($exchange_data))
            $exchange_rate = $exchange_data['Rate']['rate'];
        else
            $exchange_rate = 1;
        echo $exchange_rate; die();
    }
}

