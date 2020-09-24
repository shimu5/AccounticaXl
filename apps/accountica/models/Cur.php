<?php
namespace accountica\models;

class Cur extends \Furina\mvc\model\Model {
    var $validate = array(
        'name' => array(
            'required',
            'unique'
        )
    );

    function unique($value) {
        if( $this->id != null )
            return true;
        $result = $this->find("name='".$value."'");

        if( $result === NULL)
            return true;
        return "The Currency Already Exsists";
    }
    
    static $currencies = null;
    function getName($id) {
        if( empty($id)) return '';
        if( Cur::$currencies === null) {
//            $cur_model = App::loadModel('Cur', Furina::Dir(Furina::Models));
//            Cur::$currencies = $cur_model->findList('', array('id', 'name'));
            Cur::$currencies = $this->query()->where("id = %d",array($id))->map('name', 'id');  
        }
        return Cur::$currencies[$id];
    }


    static $curs_id = null;
    function getId($name) {
        if( empty($name)) return '';
        if( Cur::$curs_id === null) {           
            Cur::$curs_id = $this->query()->where("name = '%s'",array($name))->map('name', 'id');           
        }
      
        return Cur::$curs_id[$name];
    }
    
    public function getCurrencyList($intro = false){
        $c = $this->query()->orderby('name ASC')->map('name', 'name');
        return ($intro) ? array('' => $intro) + $c : $c;
    }
    
    public function getBaseCurrency(){
        $base_name = $this->query()->select(array('name'))->where(sprintf('base = 1'))->one();
        return $base_name['Cur']['name'];
    }
}
?>