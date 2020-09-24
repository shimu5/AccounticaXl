<?php
namespace accountica\models;
class Countries extends \Furina\mvc\model\Model
{
    public $table = 'countries';
    
    public function getCountryList($intro){
        $c = $this->query()->orderby('country ASC')->map('iso3', 'country');
        return ($intro) ? array('' => $intro) + $c : $c;
    }
}
?>