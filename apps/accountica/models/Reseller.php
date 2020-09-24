<?php
namespace accountica\models;
class Reseller extends \Furina\mvc\model\Model
{
   
   var $validate = array('login'=>array('required',
                                        'betweenMinMax_2_15','unique'),
                         'password'=>array('required'),
                         
                         'identifier'=>array(
                                             'betweenMinMax_1_15'
                                            ),
                         'clientsLimit'=>array('decimal_4'),
                         'MaxClients'=>array('integer')
                         );


   function unique($msg = null){
       if(empty($this->id)){
           $condition = sprintf(' `login` = \'%s\' AND `level` = %d AND `ip` = %d AND `port` = %d ',$this->data['Reseller']['login'],$this->data['Reseller']['level'],$this->data['Reseller']['ip'],$this->data['Reseller']['port']);
           $info = $this->find($condition,null);
           if(!empty($info)){
               return 'Already Exsists';
           }
       }

       return true;
   }

   function resellersByCustomerId($id, $empty=false,$ip = null,$port = null){      
        $cusResRelModel = App::loadModel('CustomerResRel',Furina::Dir(Furina::Models));
        $cusResRelModel->bindModel(array('hasOne' => array('Reseller' => array(
                                     'localKey' => 'reseller_id',
                                     'foreignKey' => 'id'
                ) )));

       $serverModel = App::loadModel('Machine',Furina::Dir(Furina::Models));
       //pdebug($serverModel);
       $allServersList = $serverModel->findList('`Machine`.`flag` = 1 AND `Machine`.`type` = 1',array('CONCAT(`ip`,":",`port`)'=>'server','ip_alias'));
       //pdebug($allServersList);die();


       $condition = '`CustomerResRel`.`customer_id`='.$id;
       if(!empty($ip) && !empty($port)){
           $condition .= ' AND `Reseller`.`ip` = '.$ip.' AND `Reseller`.`port` = '.$port;
       }
       $resellers = $cusResRelModel->findAll($condition, array('Reseller.id','Reseller.login','Reseller.ip','Reseller.port','Reseller.table_type','Reseller.level'));
       $resellerss = array();
       if($empty) $resellerss[''] = '';

       foreach($resellers as &$reseller){
          $level = null;
          if($reseller['Reseller']['table_type'] == 2)
              $level = 'GWC ';
          else
              $level = 'R'.$reseller['Reseller']['level'];
          $resellerss[$reseller['Reseller']['id']] = $reseller['Reseller']['login'].' ('.$allServersList[$reseller['Reseller']['ip'].':'.$reseller['Reseller']['port']].') -'.$level;
       }

       return $resellerss;
   }


   function allAssignedRes($level = null){
       $this->loadModel('CustomerResRel');
       $condition = null;
       if(!empty($level)){
           $condition .= ' CustomerResRel.reseller_level = '.$level;
       }
       $resellers = $this->CustomerResRel->findList($condition,array('id','reseller_id'));
       
       return $resellers;
   }

   static function getName($id) {
        if (empty($id)) return '';
        $model = App::loadModel('Reseller', Furina::Dir(Furina::Models));
        $reseller = $model->findList('id='.$id, array('id','login'));
         return $reseller[$id];
    }


   static $resRate = null;
   function getRate($id) {
        if( empty($id)) return '';
        if( Reseller::$resRate === null) {
           Reseller::$resRate = $this->query()->where(sprintf("id = %d",$id))->map('id', 'rate');
        }
        return Reseller::$resRate[$id];
    }
    
    function getResellerList(){
        $res_list = $this->query()->where('status = 0')->map('id','login');
        return $res_list;        
    }

    


}
?>