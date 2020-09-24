<?php

namespace accountica\controllers;

use Furina\mvc\controller\Response;

class Curs extends CrudeController {

    public $model = 'Cur';
    public $uses = array(
    );

//    function beforeRender(){
//      $base = $this->__baseCurrency();
//      
//   }

    public function anonList($request, $response) {
        $model = $this->m->{$this->model};
        $Rate = new \accountica\models\Rate();
        $base_currency = $this->__baseCurrency();
//        pr($base_currency);die;

        $join_conditions = null;
        if (!empty($base_currency)):
            $join_conditions = ' `Rate`.`base_cur_id` = ' . $base_currency['Cur']['id'] . ' AND `Rate`.`end_date` = \'0000-00-00\' ';
        endif;

        if (!empty($this->data)) {
            $condition = ' `Cur`.`name` LIKE \'';
            $condition .= trim($this->data['Cur']['search_text']);
            $condition .= '%\' ';
        } else {
            $condition = " `Rate`.`base_cur_id` = '" . $base_currency['Cur']['name'] . "' AND `Rate`.`end_date` = '0000-00-00' ";
        }

        $rate_details = $Rate->query()->where($condition)->all();

        $rate_datas = array();
        foreach ($rate_details as $key => $rate_data) {
            $rate_datas[$rate_data['Rate']['cur_id']] = $rate_data['Rate']['rate'];
        }
//        pr($rate_datas);
//        $model->bindModel(array(
//            'hasOne' => array(
//                'accountica\models\Rate' => array(
//                    'localKey'   => 'name',
//                    'foreignKey' => 'cur_id',
////                    'conditions' => $join_conditions
//                    )
//                )
//            ));

        $currencies = $model->query()->all();
//      pr($rate_details);
        $response->set('currency_lyst', $currencies); //pr($currencies);die;
        $response->set('base', $base_currency);
        $response->set('rate_details', $rate_datas);
        parent::_list($request, $response);
    }

    function __baseCurrency() {
        $model = $this->m->{$this->model};
        $base = $model->query()->where('`Cur`.`base` = 1 ')->one();
        return $base;
    }

    public function anonAdd($request, $response) {
        parent::_add($request, $response);
    }

    public function anonAddCurrency($request, $response) {
        $model = $this->m->{$this->model};
        $Rate = new \accountica\models\Rate();

        if ($request->isPost()) {
            try {
                $request->data['Cur']['base'] = 0;
                $model->insert($request->data, array('validate_with' => false));
                $save_data['Rate'] = array(
                    'rate' => 1,
                    'base_cur_id' => $request->data['Cur']['name'],
                    'cur_id' => $request->data['Cur']['name'],
                    'start_date' => date('Y-m-d'),
                );
                $Rate->save($save_data);
            } catch (\Furina\mvc\model\exceptions\InvalidException $e) {
                $response->set('error', $e->getErrors());
            }
            $url = $this->c->Router->root . 'index.php/accountica/curs/list';
            redirect($url);
        }
    }

    public function anonEdit($request, $response) {
        parent::_edit($request, $response);
        $response->render('template', 'accountica/templates/Products/anonAdd.php');
    }

    public function anonDelete($request, $response) {
        parent::_delete($request, $response);
    }

    public function anonAddRate($request, $response) {
        list($cur_name) = $request->params(null);
        $model = $this->m->{$this->model};
        $Rate = new \accountica\models\Rate();
        $currency_list = $model->getCurrencyList();

        if ($request->isPost()) {
            foreach ($request->data['Cur'] as $key => $value) {
                $currency_value = end($value);  // Taking the lasr array value
                $currency_name = key($value);   // Taking the last array index

                array_pop($value);

                $base_currency_value = end($value);
                $base_currency_name = key($value);
                echo '1st data';
                $this->_CalculateCurrencyAndSave($base_currency_name, $base_currency_value, $currency_name, $currency_value);
                echo '2nd data';
                $this->_CalculateCurrencyAndSave($currency_name, $currency_value, $base_currency_name, $base_currency_value);

                $url = $this->c->Router->root . 'index.php/accountica/curs/list';
                redirect($url);
            }
        }

        $currency_preview_rate = $Rate->query()->where(sprintf("base_cur_id = '%s' AND end_date = '0000-00-00'", $cur_name))->all();

        $response_data = array();

        foreach ($currency_preview_rate as $id => $rate_value) {


            $response_data[$rate_value['Rate']['base_cur_id'] . '_' . $rate_value['Rate']['cur_id']][$rate_value['Rate']['base_cur_id']] = 1;

            $response_data[$rate_value['Rate']['base_cur_id'] . '_' . $rate_value['Rate']['cur_id']][$rate_value['Rate']['cur_id']] = $rate_value['Rate']['rate'];
        }

        $response->data['Cur'] = $response_data;

        $response->set('data', array('Cur' => $response_data));
        $response->set('currency_list', $currency_list);
        $response->set('cur_name', $cur_name);
    }

    function _CalculateCurrencyAndSave($base_currency, $base_value, $currency, $currency_value) {
        $Rate = new \accountica\models\Rate();
        if (!empty($base_value) || $base_value > 0) {
            $rate = number_format(($currency_value / $base_value), 4);
            $previous_rate_data = $Rate->query()->where(sprintf("base_cur_id = '%s' AND cur_id = '%s' AND end_date = '0000-00-00'", $base_currency, $currency))->one();

            $request_data["Rate"] = array(
                'rate' => $rate,
                'base_cur_id' => $base_currency,
                'cur_id' => $currency,
                'start_date' => date("Y-m-d")
            );
            $update_date['Rate'] = array(
                'end_date' => date("Y-m-d")
            );
            $condetion = sprintf("base_cur_id = '%s' AND cur_id ='%s' AND end_date = 0000-00-00", $base_currency, $currency);
            if ($rate != $previous_rate_data['Rate']['rate']) {

                $Rate->update($update_date, null, $condetion);

                $Rate->save($request_data, array('validate_with' => false));
            }
        }
    }

    public function anonCurrencyChart($request, $response) {
        if (!defined('RESELLER4_ON')) {  // ToDo:: Setting Config;
            define("RESELLER4_ON", true);
        }
        $model = $this->m->{$this->model};
        $Rate = new \accountica\models\Rate();
        
        $currency_list = $model->query()->all();
        
        $rate_list = $Rate->query()->where(sprintf("end_date = '0000-00-00' AND base_cur_id != cur_id"))->all();
        
        $rate_chart = array();
        foreach($rate_list as $key=>$values){
            if(!isset($rate_chart[$values['Rate']['base_cur_id']])){
                $rate_chart[$values['Rate']['base_cur_id']] = array();
            }
            $rate_chart[$values['Rate']['base_cur_id']][$values['Rate']['cur_id']] = $values['Rate']['rate'];
        }
        
        $rate_main_chart = array();
        foreach($currency_list as $keys=>$value){
                $rate_main_chart[$value['Cur']['name']]["Base"] = $value['Cur'];
                $rate_main_chart[$value['Cur']['name']]['Rate'] = $rate_chart[$value['Cur']['name']];     
        }
        $response->set('rate_chart', $rate_main_chart);
    }

}

