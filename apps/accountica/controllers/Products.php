<?php
namespace accountica\controllers;
use Furina\mvc\controller\Response;

class Products extends CrudeController {

    public $model = 'Product';

    public $uses = array(

    );

    public function anonList($request, $response) {
        parent::_list($request, $response);
    }

    public function anonAdd($request, $response) {                 
        parent::_add($request, $response);
    }
 
    public function anonEdit($request, $response) {
        parent::_edit($request, $response);
        $response->render('template', 'accountica/templates/Products/anonAdd.php');  
    }

    public function anonDelete($request, $response){
        parent::_delete($request, $response);
    }
}

