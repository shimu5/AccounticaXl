<?php
/**
 * Created by JetBrains PhpStorm.
 * User: zubair
 * Date: 4/18/14
 * Time: 12:43 AM
 * To change this template use File | Settings | File Templates.
 */
namespace accountica\models;
class ProductStatus {
    const ACTIVE = 1;
    const DELETED = 0;

    public static $messages = array(
        ProductStatus::ACTIVE=>'Active',
        ProductStatus::DELETED=>'Deleted'    
    );

    public static function getStatusString($status) {
        if (isset(ProductStatus::$messages[$status]))
            return ProductStatus::$messages[$status];
        return 'Unknown';
    }

    /**
     * return array of all status messages
     * @param $empty string
     */
    public static function getAllStatus($empty=false) {
        if ($empty!==false) return array('' => $empty) + ProductStatus::$messages;
        return ProductStatus::$messages;
    }
}
class Product extends \Furina\mvc\model\Model {
    
}
