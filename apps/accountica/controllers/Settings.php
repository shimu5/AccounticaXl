<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Settings
 *
 * @author Jakir Hosen Khan
 */

namespace accountica\controllers;

use Furina\mvc\controller\Response;

class Settings extends CrudeController {

    public $model = 'Setting';

    public function anonPageSetting($request, $response) {
        $model = $this->m->{$this->model};
        $pay_field = array('2' => 'Exchange Rate', '3' => 'Deposit Amount');
        $response->set('pay_field', $pay_field);
        $results = $model->query()->map('key', 'value');
        if (!empty($request->data)) {
            foreach ($request->data['Setting'] as $key => $value) {
                $res = $model->query()->where('`key` = "%s"', array($key))->one;
                $data_save['Setting']['key'] = $key;
                $data_save['Setting']['value'] = $value;
                if (isset($res)) {
                    $data_save['Setting']['value'] = $res['Setting']['id'];
                }
                $model->save($data_save, array('validate_with' => false));
                unset($data_save);
                unset($res);
            }
            $url = $this->c->Router->root . 'index.php/accountica/settings/pagesetting';
            redirect($url);
        }

        $dataset = array();
        foreach ($results as $key => $value) {
            $key = str_replace('.', '__', $key);
            $dataset['Setting'][$key] = $value;
        }
        $response->set('data', $dataset);
    }

}

?>
