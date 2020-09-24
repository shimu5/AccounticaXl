<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace accountica\models;
/**
 * Description of remote_gateway
 *
 * @author nur
 */
class Gateway extends \Furina\mvc\model\Model
{
   var $table = 'gateways';
   
   static $gatRate = null;
   
   function getRate($id) {
        if( empty($id)) return '';
        if(Gateway::$gatRate === null) {
           Gateway::$gatRate = $this->query()->where(sprintf("id = %d",$id))->map('id', 'rate');
        }
        return Gateway::$gatRate[$id];
    }

    function getGatewayList(){
        $g_list = $this->query()->where('status = 0')->map('id','ip_number');
        return $g_list;
    }
}
?>
