<?php
/**
 * Created by JetBrains PhpStorm.
 * User: zubair
 * Date: 4/21/14
 * Time: 10:34 PM
 * To change this template use File | Settings | File Templates.
 */
namespace scheduler\controllers;
use Furina\mvc\controller\Response;

class CrudeController extends AppController {

    public function handleRequest($request, $handler = null)
    {
        $response = new Response();
        $function = $this->__map($request, $handler);

        $template = str_replace('\\controllers\\', '\\templates\\', static::$__class__);
        $template .= '/' .$function.'.php';

        $output = $request->namedParam('output');
        if ( $output == 'json')
            $response->render('json');
        else
            $response->render('template', $template);

        $response->set('data', $request->data);
        $response->set('errors', $request->data);

        $this->__execute($request, $response, $function);
        return $response;
    }
    
    protected function _delete($request, $response) {
        list($id) = $request->params(null);
        $model = $this->m->{$this->model};

        if (!empty($id) && ($data=$model->read($id))!== null) {
              $model->delete($id);
              //$response->setFlash(sprintf($this->messages['DELETE_SUCCESS'], $this->model, $id));
              //$response->redirect(array('action'=>'index'));			
        }
        else {
              //$response->setFlash(sprintf($this->messages['NOT_FOUND'], $this->model, $id))->redirect(array('action'=>'index'));
        }
    }
    
    protected function _list($request, $response) {
        $model = $this->m->{$this->model};
        $rows = $model->query()->all();
        $response->set('rows', $rows);
    }

    protected function _add($request, $response) {  
        $model = $this->m->{$this->model};
        if ($request->isPost()) {   
            try {
                $model->save($request->data);
            }
            catch (\Furina\mvc\model\exceptions\InvalidException $e) {
                $response->set('error', $e->getErrors());
            }
        }     
    }
 
    protected function _edit($request, $response) {
        list($id) = $request->params(null);
        $model = $this->m->{$this->model};
        if (!empty($id) && ($row=$model->read($id))!== null) {
            if ($request->isPost()) {   
                try {
                    $request->data[$this->model][$model->primaryKey] = $id;
                    $model->save($request->data);
                    //$response->setFlash('saved');
                    //$response->redirect(array( 'action'=>'list', 'params'=>true ));
                }
                catch (\Furina\mvc\model\exceptions\InvalidException $e) {
                    $response->set('error', $e->getErrors());
                }
            } 
            $response->set('data', $row);          
        }else {
            throw new \Exception("Url Not Found!!!");
        }
    }
}
