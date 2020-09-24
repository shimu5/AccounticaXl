<?php
namespace accountica\models;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of sync_reseller
 *
 * @author nur
 */
class SyncResellerStatus {
    public static $SyncResellerStatus = array(
        0=> 'Shnchronized',
        1=> 'Accepted'
    );      
}
$count = 0;
class SyncReseller extends \Furina\mvc\model\Model
{
    //var $name = 'Reseller';
    var $table = 'sync_resellers';
    var $validate = array('ip'=>array('ip'),
                          'login'=>array('required','unique'),
                          'password'=>array('required'),
                          //'Email'=>array('email'),
                          //'Phone'=>array('phone'),
                          'callsLimit'=>array('integer'));


    var $savedCount;
    var $updatedCount;
    var $errorCount;
    //var $last_id;

    function init(){
        parent::__init();
        $this->savedCount = 0;
        $this->updatedCount = 0;
        $this->errorCount = 0;
    }

    function save($data, $doValidation = true){
        if($this->insert($data, array('validate_with'=>$doValidation)) == true){
            $this->savedCount++;
            return true;
        }

        $this->errorCount++;
        return false;

    }

    function ip($data){
        return true;
    }
    
    function integer($data){
        if(strstr($data, 'INET_'))
        {
          return true;
        }
        else if(is_numeric($data))
        {
           return true;
        }
        return false;
    }

    function update($data,$doValidation = true) {
        $condition = 'old_id='.$data['SyncReseller']['old_id'] .
                     // ' AND login=\''.$data['Reseller']['login'].'\''.
                      ' AND level='. $data['SyncReseller']['level'].
                      ' AND ip='.$data['SyncReseller']['ip'].
                      ' AND port=' . $data['SyncReseller']['port'].
                      ' AND table_type = ' . $data['SyncReseller']['table_type'];
        
         $resellerXists = $this->query()->where($condition)->one();
         //pdebug($resellerXists);
         if(empty($resellerXists)) return $this->save($data, $doValidation);
         else {             
            $this->id = $resellerXists['Reseller']['id'];
            parent::update($data, array('validate_with'=>$doValidation), null);
            $this->updatedCount++;            
         }

        return true;
    }
    
    function updateToSyncReseller($data, $options=false){
        parent::update($data,$options);
    }

   function unique($msg = null){

       return true;
   }

}
?>
