<?php

namespace accountica\controllers;

use Furina\mvc\controller\Response;

class Gateways extends CrudeController {

    public $model = 'Gateway';
    public $uses = array(
    );

    public function anonList($request, $response) {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {    //ajax check
            $model = $this->m->{$this->model};
            $rows = $model->query();

            $url = $this->c->Router->root . 'index.php/accountica/gateways/list';
            $response->set('grid_url', $url);
            $this->c->Pagination->page_url = $url;

            $pagedata = $this->c->Pagination->paginate($model->query());
            if (isset($pagedata['rows']) && !empty($pagedata['rows'])) {
                foreach ($pagedata['rows'] as $key => $values) {
                    $pagedata['data'][$key] = $values['Gateway'];
                    $pagedata['data'][$key]['no'] = ($key + 1);
                    $pagedata['data'][$key]['edit'] = '<a href="' . $this->c->Router->root . 'index.php/accountica/gateways/edit/' . $values['Gateway']['id'] . '">edit</a>';
                }
            }
            echo json_encode($pagedata);
            die;
        }
    }

    public function anonAdd($request, $response) {
        
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
                    $url = $this->c->Router->root . 'index.php/accountica/Gateways/list';
                    redirect($url);
                } catch (\Furina\mvc\model\exceptions\InvalidException $e) {
                    $response->set('error', $e->getErrors());
                }
            } else {
                $response->set('data', $row);
            }
        } else {
            $url = $this->c->Router->root . 'index.php/accountica/Gateways/list';
            redirect($url);
        }
        $response->render('template', 'accountica/templates/Gateways/anonAdd.php');
    }

    public function anonDelete($request, $response) {
        
    }

    
    public function anonGetwaysUpdate($request, $response){
        //pr($request->post);
        if(!empty($request->post) && $request->post['gateway_id'] !=''){
            $request->data['Gateway']['id'] = $request->post['gateway_id'];
            $request->data['Gateway']['vendor_id'] = $request->post['vendor_id'];
            $request->data['Gateway']['rate'] = $request->post['rate'];
            
            $model = $this->m->{$this->model};
            $model->save($request->data,array('validate_with'=>FALSE));
            echo 'Successfully updated.';
        }else{
            echo 'Please insert correct data!!!';
        }
        die();
    }

    
    public function anonSyncGetways($request, $response){
        $sync_gateways_model = $this->m->SyncGateway;
        $status = $request->data['Gateway']['status'];
        $conditions = '1=1';
        if($status !=''){
            $conditions .=' AND status = '.$status;
        }
        $rows = $sync_gateways_model->query()->where($conditions)->all();
        //pr($rows);
        $response->set('rows',$rows);
        
        $ajax_url = $this->c->Router->root . 'index.php/accountica/Gateways/AjaxSyncGateways';
        $response->set('url',$ajax_url);
        
        $response->set('syncResellerStatus',  \accountica\models\SyncGatewayStatus::$SyncGatewayStatus);
    }
    
    public function anonAjaxSyncGateways($request, $response){
        //pr($request->post);
        $gatew_id = '';
        foreach($request->post['gateways_id'] as $gat_id){
            $gatew_id .=','.$gat_id;
        }
        $gatew_id = substr($gatew_id, 1);
        $sync_gateway_model = $this->m->SyncGateway;
        $sync_gateways_rows = $sync_gateway_model->query()->where('id IN (%s)',array($gatew_id))->all();
        //pr($sync_gateways_rows);
        $i=0;
        foreach($sync_gateways_rows as $sync_gatew_rows){            
            $request->data[$i]['Gateway'] = $sync_gatew_rows['SyncGateway'];
            unset($request->data[$i]['Gateway']['id']);
            $i++;
        }
        //pr($request->data);
        $model = $this->m->{$this->model};
        foreach($request->data as $g_data){     
            $model->save($g_data,array('validate_with'=>FALSE));     
        }
        /*Update sync_gateways status*/
        foreach($sync_gateways_rows as $sync_g_data){
            //pr($sync_r_data);
            //unset($sync_r_data['SyncReseller']['status']);
            $sync_g_data['SyncGateway']['status'] = 1;
            $sync_gateway_model->updateToSyncGateway($sync_g_data,array('validate_with'=>FALSE));
//            $sync_gateway_model->save(array('SyncGateway'=>array('id'=>$sync_g_data['SyncGateway']['id'],'status'=>1)),array('validate_with'=>FALSE));        
        }
        echo 'Successfully Accepted.';
        die;
    }
    

}

