<?php
namespace accountica\models;
class Rate extends \Furina\mvc\model\Model {
   var $validate = array(
                     'rate' =>array('decimal_10' ,'positive')
                    );
  
   function positive($value){     
      if($value>=0.0)
         return true;
      return 'Value must be positive.';
   }
}
?>