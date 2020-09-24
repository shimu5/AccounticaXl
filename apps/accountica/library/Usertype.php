<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Usertype
 *
 * @author RoniPHP
 */
namespace accountica;
Class Usertype {
    const ADMIN = 1;
    
    const RESELLER1 = 1;
    const RESELLER2 = 2;
    const RESELLER3 = 3;
    const RESELLER4 = 4;
    const GWClients = 1;
    
    const Gateways = 1;
    
    const Customer = 1;
    const Vendor = 2;
    const Agent = 3;

    public static $resellers = array(
        Usertype::RESELLER1=>'Reseller1',
        Usertype::RESELLER2=>'Reseller2',
        Usertype::RESELLER3=>'Reseller3',
        Usertype::RESELLER4=>'Reseller4',       
    );
    
    public static $admins = array(
        Usertype::ADMIN=>'ADMIN',    
    );
    
    public static $users = array(
        Usertype::Customer=>'Customer',    
        Usertype::Vendor=>'Vendor',    
        Usertype::Agent=>'Agent',    
    );

    public static function getUser($type) {
        if (isset(Usertype::$users[$type]))
            return Usertype::$users[$type];
        return 'Unknown';
    }

    /**
     * return array of all status messages
     * @param $empty string
     */
    public static function getAllUser($empty=false) {
        if ($empty!==false) return array('' => $empty) + Usertype::$users;
        return Usertype::$users;
    }
}
?>
