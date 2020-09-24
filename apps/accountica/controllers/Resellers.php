<?php

namespace accountica\controllers;

use Furina\mvc\controller\Response;

class Resellers extends CrudeController {

    public $model = 'Reseller';
    public $uses = array(
    );

    public function anonList($request, $response) {
        list($level) = $request->params(NULL);
        $machine = new \accountica\models\Machine();
        $ipNameList = $machine->getIpNameList();
        $response->set('ipNameList', $ipNameList);
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {    //ajax check
            $model = $this->m->{$this->model};
            $rows = $model->query();
            $url = $this->c->Router->root . 'index.php/accountica/resellers/list/' . $level;
            $response->set('grid_url', $url);
            $this->c->Pagination->page_url = $url;
            $pagedata = $this->c->Pagination->paginate($model->query()->where("client_type=%d AND level=%d", array(-1, $level)));
            if (isset($pagedata['rows']) && !empty($pagedata['rows'])) {
                foreach ($pagedata['rows'] as $key => $values) {
                    $values['Reseller']['server_id'] = $ipNameList[$values['Reseller']['server_id']];
                    $pagedata['data'][$key] = $values['Reseller'];
                    $pagedata['data'][$key]['no'] = ($key + 1);
                    $pagedata['data'][$key]['edit'] = '<a href="' . $this->c->Router->root . 'index.php/accountica/resellers/edit/' . $values['Reseller']['id'] . '">edit</a>';
                    $pagedata['data'][$key]['paymenthistory'] = '<a href="' . $this->c->Router->root . 'index.php/accountica/resellers/PaymentHistory/' . $values['Reseller']['id'] . '">Payment History</a>';
                }
            }
            echo json_encode($pagedata);
            die;
        }
    }

    public function anonAdd($request, $response) {

    }

    public function anonPaymentHistory($request, $response) {
        list($id, $level) = $request->params(NULL, NULL);
        $response->set('id', $id);
        $response->set('level', $level);
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {    //ajax check
            $reseller_payment = new \accountica\models\ResellerPayment();
            if (!empty($id) && !empty($level)) {
                $rows = $reseller_payment->query()->where("id_reseller=%d AND resellerlevel=%d", array($id, $level));
            } else {
                $rows = $reseller_payment->query();
            }
            $url = $this->c->Router->root . 'index.php/accountica/resellers/PaymentHistory/' . $level;
            $response->set('grid_url', $url);
            $this->c->Pagination->page_url = $url;
            $pagedata = $this->c->Pagination->paginate($rows);
            if (isset($pagedata['rows']) && !empty($pagedata['rows'])) {
                foreach ($pagedata['rows'] as $key => $values) {
                    $pagedata['data'][$key] = $values['ResellerPayment'];
                    $pagedata['data'][$key]['no'] = ($key + 1);
                }
            }
            echo json_encode($pagedata);
            die;
        }
    }

    public function anonEdit($request, $response) {
        list($id) = $request->params(null);
        $model = $this->m->{$this->model};
        if (!empty($id) && ($row = $model->read($id)) !== null) {
            if ($request->isPost()) {
                try {

                    $request->data[$this->model][$model->primaryKey] = $id;
                    //pr($request->data); die('edit');
                    $model->save($request->data, array('validate_with' => FALSE));
                    $url = $this->c->Router->root . 'index.php/accountica/Resellers/list/1';
                    redirect($url);
                } catch (\Furina\mvc\model\exceptions\InvalidException $e) {
                    $response->set('error', $e->getErrors());
                }
            } else {
                $machine = new \accountica\models\Machine();
                $countries = new \accountica\models\Countries();
                $response->set('reseller_type', \accountica\models\ResellersType::getAllReseller());
                $response->set('getIpNameList', $machine->getIpNameList());
                $response->set('getCountryList', $countries->getCountryList($intro));
                $response->set('data', $row);
            }
        } else {
            $url = $this->c->Router->root . 'index.php/accountica/resellers/list';
            redirect($url);
        }
        $response->render('template', 'accountica/templates/Resellers/anonAdd.php');
    }

    public function anonDelete($request, $response) {
        
    }

    public function anonResellersUpdate($request, $response) {
        if (!empty($request->post) && $request->post['reseller_id'] != '') {
            $base_cur = $this->m->Cur->getBaseCurrency();
            $request->data['Reseller']['id'] = $request->post['reseller_id'];
            $request->data['Reseller']['customer_id'] = $request->post['customer_id'];
            $request->data['Reseller']['rate'] = ($request->post['rate'] == '') ? 0 : $request->post['rate'];
            $model = $this->m->{$this->model};
            $model->save($request->data, array('validate_with' => FALSE));
            $deposit_cur_id = $this->m->Account->getCustomerCurrency($request->post['customer_id']);
            $acc_id = $this->m->Account->getCustomerAccountId($request->post['customer_id']);
            
            $update_query = 'UPDATE pending_ledgers SET account_id = '.$acc_id.', cur_id = \''.$base_cur.'\',deposit_cur_id = \''.$deposit_cur_id.'\', rate ='.$request->data["Reseller"]["rate"].' WHERE reseller_id ='.$request->post['reseller_id'].';';
            
            $this->m->PendingLedger->execute($update_query);
            echo 'Successfully updated.';
        } else {
            echo 'Please insert correct data!!!';
        }
        die();
    }

    public function anonSyncResellers($request, $response) {
        $sync_resellers_model = $this->m->SyncReseller;
        $status = $request->data['Reseller']['status'];
        $conditions = '1=1';
        if ($status != '') {
            $conditions .=' AND status = ' . $status;
        }
        $rows = $sync_resellers_model->query()->where($conditions)->all();
        $response->set('rows', $rows);

        $machine = new \accountica\models\Machine();
        $response->set('ipNameList', $machine->getIpNameList());

        $ajax_url = $this->c->Router->root . 'index.php/accountica/Resellers/AjaxSyncResellers';
        $response->set('url', $ajax_url);

        $response->set('syncResellerStatus', \accountica\models\SyncResellerStatus::$SyncResellerStatus);
    }

    public function anonAjaxSyncResellers($request, $response) {
        
        $base_currency = $this->m->Cur->getBaseCurrency();
        
        $ptype = new \accountica\models\RemotePType();
        
        $resel_id = '';
        foreach ($request->post['resellers_id'] as $res_id) {
            $resel_id .=',' . $res_id;
        }
        $resel_id = substr($resel_id, 1);
        $sync_resellers_model = $this->m->SyncReseller;
        $sync_resellers_rows = $sync_resellers_model->query()->where('id IN (%s)', array($resel_id))->all();
        $i = 0;
        foreach ($sync_resellers_rows as $sync_resl_rows) {
            $request->data[$i]['Reseller'] = $sync_resl_rows['SyncReseller'];
            unset($request->data[$i]['Reseller']['id']);
            $i++;
        }
        $model = $this->m->{$this->model};
        foreach ($request->data as $r_data) {
            $model->save($r_data, array('validate_with' => FALSE));
        }
        /* Update sync_resellers status */
        foreach ($sync_resellers_rows as $sync_r_data) {
            //unset($sync_r_data['SyncReseller']['status']);
            $sync_r_data['SyncReseller']['status'] = 1;
            $sync_resellers_model->updateToSyncReseller($sync_r_data, array('validate_with' => FALSE));

            $SyncResellerPayment = new \accountica\models\SyncResellerPayment();
            $ResellerPayment = new \accountica\models\ResellerPayment();

            $sync_data = $SyncResellerPayment->getSyncResellerPayment($sync_r_data);
            if (!empty($sync_data)) {
                foreach ($sync_data as $key => $values) {
                    $inserte_data['ResellerPayment'] = $values['SyncResellerPayment'];
                    unset($inserte_data['ResellerPayment']['id']);
                    $last_inserted_id = $ResellerPayment->insertToResellerPayment($inserte_data);
                    
                    // Reseller  payment added to the Panding ledger start                    
                    $res_array['PendingLedger'] = array(
                      'reseller_id'=>$inserte_data['ResellerPayment']['id_reseller'],
                      'amount'=>$inserte_data['ResellerPayment']['amount'],
                      'cur_id'=>$base_currency,// this id will be the base id;
                      'deposit'=>$inserte_data['ResellerPayment']['amount'],
                      'tr_date'=>$inserte_data['ResellerPayment']['tr_date'],
                      'description'=>$inserte_data['ResellerPayment']['description'],
                      'type'=>  \accountica\models\PType::Payment,
                      'is_posted'=>0,
                      'res_payment_id'=>$last_inserted_id,
                      'sync_flag'=>1,
                      'keep'=>0
                    );
                    
                    $this->m->PendingLedger->insertToLedger($res_array);
                    
                    // Reseller  payment added to the Panding ledger ends
                }
            }

//            die;
//            $sync_resellers_model->save(array('SyncReseller'=>array('id'=>$sync_r_data['SyncReseller']['id'],'status'=>1)),array('validate_with'=>FALSE));        
        }
        echo 'Successfully Accepted.';
        die;
    }

}

