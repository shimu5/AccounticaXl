<?php
/**
 * Created by JetBrains PhpStorm.
 * User: zubair
 * Date: 4/18/14
 * Time: 12:43 AM
 * To change this template use File | Settings | File Templates.
 */
namespace accountica\models;

class Admin extends \Furina\mvc\model\Model {
    var $validate_insert = array(
        //'name' => array('_required_'),
        'username' => array('_required_'),
        //'email' => array('_required_', 'email'),
        'password' => array('_required_', /*'hash'*/),
        //'retype_password' => array('hash'),
        //'_post_' => array('password_match'),

    );
    
    function admins_list(){
        return $this->query()->where('status = %d', array('1'))->map('id', 'name');
    }
}
