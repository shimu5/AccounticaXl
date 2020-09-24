<?php
namespace accountica\controllers;
use Furina\mvc\controller\Response;

class Servers extends CrudeController {
    
    public $model = 'Server';
    
    public function anonIndex($request, $response) {
        $mac = new \accountica\models\Machine();
        $mac->syncStart('127.0.0.1', '3306');
        $BP = new \scheduler\components\BProcessor();
        $BP->set_process_status(array(array('job_history_id'=>$request->get['job_history_id'])), \scheduler\ProcessStatus::$DONE, 'Done Successfully');
        die;
    }
    
    public function anonList($request, $response) {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {    //ajax check
            $model = $this->m->{$this->model};
            $rows = $model->query();
            $url = $this->c->Router->root . 'index.php/accountica/servers/list';
            $response->set('grid_url', $url);
            $this->c->Pagination->page_url = $url;
            $pagedata = $this->c->Pagination->paginate($model->query());
            if (isset($pagedata['rows']) && !empty($pagedata['rows'])) {
                foreach ($pagedata['rows'] as $key => $values) {
                    $pagedata['data'][$key] = $values['Server'];
                    $pagedata['data'][$key]['no'] = ($key + 1);
                    $pagedata['data'][$key]['edit'] = '<a href="' . $this->c->Router->root . 'index.php/accountica/servers/edit/' . $values['Server']['id'] . '">edit</a>';
                    $pagedata['data'][$key]['delete'] = '<a href="' . $this->c->Router->root . 'index.php/accountica/servers/delete/' . $values['Server']['id'] . '">delete</a>';
                }
            }
            echo json_encode($pagedata);
            die;
        }
    }
    
    public function anonAdd($request, $response) {
        parent::_add($request, $response);
        if($request->isPost()){
            $url = $this->c->Router->root . 'index.php/accountica/servers/list';
            redirect($url);
        }
    }
    
    public function anonEdit($request, $response){
        parent::_edit($request, $response);
        $response->render('template', 'accountica/templates/Servers/anonAdd.php');  
        if($request->isPost()){
            $url = $this->c->Router->root . 'index.php/accountica/servers/list';
            redirect($url);
        }
    }
    public function anonDelete($request, $response){
        parent::_delete($request, $response);       
        $url = $this->c->Router->root . 'index.php/accountica/servers/list';
        redirect($url);
    }
    
    public function anonSynchronizationList($request, $response){
        if(!defined('RESELLER4_ON')){  // ToDo:: Setting Config;
            define("RESELLER4_ON", true);
        }
        
        $model = $this->m->{$this->model};
        $SynModel  = new \accountica\models\Synchronization();
        $Machine = new \accountica\models\Machine();
        $Admin = new \accountica\models\Admin();
        
        
        $admin_list = $Admin->admins_list();       
        
        $Machine->getList();
        $query_data = $SynModel->query()->all();
//        $ipaddress = $this->execute_query(' SELECT INET_ATON(\''.$ip.'\' ) AS `Machine.ip`;'); // address to number 
//        $ipaddress = $this->execute_query(' SELECT INET_NTOA(\''.$ip.'\' ) AS `Machine.ip`;');// number to address 
        
        
        $response->set('rows', $query_data);
        $response->set('admin_list', $admin_list);
        
    }
    
}