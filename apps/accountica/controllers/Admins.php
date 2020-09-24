<?php
namespace accountica\controllers;
use Furina\mvc\controller\Response;

class Admins extends CrudeController {

    public $model = 'Admin';

    public $uses = array(

    );

    public function anonList($request, $response) {
        parent::_list($request, $response);
    }
    
    public function anonAdd($request, $response) {                 
        parent::_add($request, $response);
        if($request->isPost()){
            $url = $this->c->Router->root . 'index.php/accountica/admins/list';
            redirect($url);
        }
    }
 
    public function anonEdit($request, $response) {
        parent::_edit($request, $response);
        $response->render('template', 'accountica/templates/Admins/anonAdd.php');  
        if($request->isPost()){
            $url = $this->c->Router->root . 'index.php/accountica/admins/list';
            redirect($url);
        }
    }

    public function anonDelete($request, $response){
        parent::_delete($request, $response);       
        $url = $this->c->Router->root . 'index.php/accountica/admins/list';
        redirect($url);
    }   
    
    public function anonLogin($request, $response){
        $this->c->Auth->loginAction($request, $response);
    }
    
    public function anonLogout($request, $response){
        $this->c->Auth->logoutAction($request, $response);
    }
    
    public function anonGraph($request, $response){
         
        $datas[] = array('Year', 'Sales', 'Expenses');
        $datas[] = array('2001',1,2);
        $datas[] = array('2002',10,20);
        $datas[] = array('2003',20,30);
        
        $response->set('data',  json_encode($datas));
    }
}

