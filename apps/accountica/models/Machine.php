<?php
namespace accountica\models;
use Furina\framework\core\Config;

class ResellersType {
    static $Resellers3 = 3;
    static $Resellers2 = 2;
    static $Resellers1 = 1;
    static $Resellers4 = 4;
    
    public static function getTableName($type) {
        switch ($type) {
            case self::$Resellers1 :
                return 'resellers1';
            case self::$Resellers2 :
                return 'resellers2';
            case self::$Resellers3 :
                return 'resellers3';
            case self::$Resellers4 :
                return 'resellers4';    
        }
    }

    public static function getFieldsName($type = false) {
        return array(
            'last_id' => 'res' . $type . '_last_id',
            'found_records' => 'res' . $type . '_found_records',
            'new_records' => 'res' . $type . '_new_records',
            'updated_records' => 'res' . $type . '_updated_records'
        );
    }
    
    public static $resellers = array(
        1=>'Reseller1',
        2=>'Reseller2',
        3=>'Reseller3'
    );
    
    public static function getResName($num = false) {
        if(defined('RESELLER4_ON') && RESELLER4_ON == true) ResellersType::$resellers[4] = 'Reseller4';
        
        if (isset(ResellersType::$resellers[$num]))
            return ResellersType::$resellers[$num];
        return 'Unknown';
    }

    /**
     * return array of all status messages
     * @param $empty string
     */
    public static function getAllReseller($empty=false) {
        if(defined('RESELLER4_ON') && RESELLER4_ON == true) ResellersType::$resellers[4] = 'Reseller4';
        
        if ($empty!==false) return array('' => $empty) + ResellersType::$resellers;
        return ResellersType::$resellers;
    }
}



class RemoteTables {
    static $tableNames = array(
                      0 => 'gateways',
                      1 => 'resellers1',
                      2 => 'resellers2',
                      3 => 'resellers3',
                      4 => 'resellerspayments',
                      5 => 'clientsip',
                      6 => 'payments'

    );

    static $reseller_table_type = 1;
    static $client_table_type = 2;
    
    /*** reseller4 and resellers4_payments table may not be available for
     * all machine that will cause an error
     * of "Table(s) Not Exists"***/
    
    static $tableNamesofC4R4 = array(
                      0 => 'resellers4',
                      1 => 'resellers4_payments',
    );

    public static function getTableList($type = false) {
                return RemoteTables::$tableNames;
    }
}

class ServerErrors {
    static $error = array(

                       1 => 'User/Pass Invalid',
                       2 => 'No Database Found',
                       3 => 'Table(s) Not Exists',
                       4 => 'didn\'t respond',
    );
}

class Machine extends \Furina\mvc\model\Model {
    var $primaryKey = 'ip';
    var $validate   = array('ip'=>array('required','ip','unique'),
                            'port'=>array('numeric','range'),
                            'ip_alias'=>array('required'),
                            'host'=>array('required'),
                            'db_name'=>array('required')
                           );


    var $sync_script_name = 'sync_process.php';
    var $php_command_name = 'php.exe';
    var $filepointer;

    function  init() {
        parent::init();
        
    }
    
    static $servers = null;
    function getName($ip_port = false) {
        if (empty($ip_port)) return '';
        if (Machine::$servers == null) {
            $machine = new Machine();
            Machine::$servers = $machine->query()->select(array("CONCAT(ip,':',port)" => "Concate","ip_alias"))->where('`flag` = 1 AND `type` = 1')->map("Concate","ip_alias");  //working
        }
        //Log::printSQLs();
        return Machine::$servers[$ip_port];
    }
    
    function getNameById($server_id = 0) {
        if (!$server_id) return '';
        if (Machine::$servers == null) {
            $machine = new Machine();
            Machine::$servers = $machine->query()->select(array("id","ip_alias"))->where('`flag` = 1 AND `type` = 1 AND id = %d', array($server_id))->map("id","ip_alias");  //working
        }
        //Log::printSQLs();
        return Machine::$servers[$server_id];
    }
    
    function getList(){
        $machine = new Machine();
        Machine::$servers = $machine->query()->select(array("CONCAT(ip,':',port)" => "Concate","ip_alias"))->where('`flag` = 1 AND `type` = 1')->map("Concate","ip_alias");
        return Machine::$servers;
    }
    
    function getListByID(){
        $machine = new Machine();
        Machine::$servers = $machine->query()->select(array("id","ip_alias"))->where('`flag` = 1 AND `type` = 1')->map("id","ip_alias");
        return Machine::$servers;
    }
    
    function syncStart($ip, $port = '3306'){
        /****temporary set, take in config****/
        if(!defined('RESELLER4_ON')) define('RESELLER4_ON', true);
        
        set_time_limit(1500);
        $db_sessions = rand(1,100);
        try{
            
            $sync = new Synchronization();
            $ipaddress = $this->execute_query(' SELECT INET_ATON(\''.$ip.'\' ) AS `Machine.ip`;');
            $server = $this->query()->select(array('id', 'ip_alias','INET_NTOA(ip)' => 'ip', 'port','db_name','host', 'password','status','last_admin_id','sync_start_date','start_status'))->where('ip=%d', array($ipaddress[0]['Machine']['ip']))->one();
            $server['Machine']['ipaddress'] = $ipaddress[0]['Machine']['ip'];
            
            $no_error_flag = true;
            $core4voip_reseller4_exists = false;
            
            // find status
            if (($server['Machine']['status'] == SyncStatus::$Syncing)) {
                // if Syncing DO Nothing
                
            }else {
                // if the server responds
                $status = $this->__checkServerStatus($ip, $server['Machine']['port']);
                
                // Prepare new history
                $new_sync_data = array();
                $data_found = array();

                // make db_session unique
                $db_sessions = $db_sessions.'_'.$server['Machine']['ipaddress'];

                // error checking
                if ($status->status) {
                    \Furina\mvc\model\database\DatabaseManager::add('remote_' . $db_sessions, array(
                        'database'=>'Furina\\mvc\\model\\database\\mysql',
                        'host' => $server['Machine']['ip'].':'.$server['Machine']['port'],
                        'user' => $server['Machine']['host'],
                        'pass' => $server['Machine']['password'],
                        'name' => $server['Machine']['db_name']
                    ));
                    
                    $query_status = $this->__checkQueryStatus($db_sessions);
                    
                    if ($query_status->status == false) {
                        $no_error_flag = false;
                        $server_status = $query_status->errno;
                    }
                    else {
                        // prepare found data
                        if(defined('RESELLER4_ON') && RESELLER4_ON == true){
                                if($query_status->r4status) $core4voip_reseller4_exists = true;
                        }
                    }
                }else { // error
                    $no_error_flag = false;
                    $server_status = SyncStatus::$ServerNoResponse;
                }

                // end or error checking
                
                // last history available
                $condition = '`ip` = '.$ipaddress[0]['Machine']['ip'].' AND `port` = '.$server['Machine']['port']. ' AND `status` = 0 ';
                $last_sync_status = $sync->query()->where($condition)->orderby('id desc')->one();
                
                $new_sync_data['Synchronization'] = array(
                    'date' => date('Y-m-d h:i:s'),
                    'admin_id' => $server['Machine']['last_admin_id'],
                    'ip' => $ipaddress[0]['Machine']['ip'],
                    'port' => $server['Machine']['port'],
                );
                
                // update with last available history
                if (!empty($last_sync_status)) {
                    $sync_data = array();
                    $sync_data['Synchronization'] = array(
                        'res1_last_id' =>  $last_sync_status['Synchronization']['res1_last_id'],
                        'res2_last_id' =>  $last_sync_status['Synchronization']['res2_last_id'],
                        'res3_last_id' =>  $last_sync_status['Synchronization']['res3_last_id'],
                        'res_payment_last_id' =>  $last_sync_status['Synchronization']['res_payment_last_id'],
                        'gateway_last_id' =>  $last_sync_status['Synchronization']['gateway_last_id'],
                        'gwclients_last_id' =>  $last_sync_status['Synchronization']['gwclients_last_id'],
                        'gw_payments_last_id' =>  $last_sync_status['Synchronization']['gw_payments_last_id']
                    );

                    /***New added for res4***/
                    if(isset($last_sync_status['Synchronization']['res4_last_id'])){
                        $sync_data['Synchronization']['res4_last_id'] = $last_sync_status['Synchronization']['res4_last_id'];
                    }
                    if(isset($last_sync_status['Synchronization']['c4_res4_payment_last_id'])){
                        $sync_data['Synchronization']['c4_res4_payment_last_id'] = $last_sync_status['Synchronization']['c4_res4_payment_last_id'];
                    }

                    $new_sync_data['Synchronization'] = array_merge($sync_data['Synchronization'],$new_sync_data['Synchronization']);

                }else {
                    if (!empty($server['Machine']['sync_start_date'])) {
                        $new_sync_data['Synchronization'] = array_merge( array('sync_from_date' => $server['Machine']['sync_start_date'],'sync_to_date' => date('Y-m-d H:i:s') ),$new_sync_data['Synchronization']);
                    }
                }

                // prepare for error history
                if ($no_error_flag == false) {
                    $new_sync_data['Synchronization']['status'] = $server_status;
                }// end of prepare for error history
                
                // add new history
                $sync->id = null;
                $sync->save($new_sync_data, array('validate_with'=>false));     //TO DO :: cant understand why validation fails
                $new_sync_id = $sync->lastInsertId();
                $new_sync_data['Synchronization']['id'] = $new_sync_id;
                
                if (($no_error_flag == true) && ($server['Machine']['status'] != SyncStatus::$Syncing))  {
 
                    $server_status = SyncStatus::$Syncing;
                    $conditions = sprintf('`ip`=%d AND `status`=%d', $ipaddress[0]['Machine']['ip'], SyncStatus::$OnQueue);
                    $runQuerySuccess = $this->update(array('Machine'=>array('status'=>$server_status)), array('validate_with'=>false), $conditions);

                    $number_of_affected_rows = $runQuerySuccess->numAffectedRows();
                    if ($number_of_affected_rows == 1) {
                        $this->__syncData($server,$db_sessions,$new_sync_id, $core4voip_reseller4_exists);
                        $server_status = SyncStatus::$Done;
                    }else {
                        die('SomethingWrongHappenWhileSync');
                    }

                    $conditions = sprintf('`ip`=%d', $ipaddress[0]['Machine']['ip']);
                    $runQuerySuccess = $this->update(array('Machine'=>array('status'=>$server_status)), array('validate_with'=>false), $conditions);
                    
                    if ($runQuerySuccess) {
                    // msg
                        
                    }else {
                        
                        die('SomethingWrongHappen');
                    }
                    // END OF PROCESS
                    
                } // end of processing
                else {
                    // error
                    $conditions = sprintf('`ip`=%d', $ipaddress[0]['Machine']['ip']);
                    $runQuerySuccess = $this->update(array('Machine'=>array('status'=>$server_status)), array('validate_with'=>false), $conditions);
                    
                    return;
                }
            }
        }catch(\Furina\mvc\exceptions $e){
            print_r($e->getMessage());
        }
    }
    
    /*** added **/
   function __checkQueryStatus($index) {
        global $DATABASE;
        $conn = $DATABASE[DATABASE_SET]['remote_'.$index];

        $host = $conn['host'];
        $user = $conn['user'];
        $pass = $conn['pass'];
        $name = $conn['name'];
        $result = new \stdClass();
        $result->status = true;
        $conn1 = @mysql_connect($host, $user, $pass);

        if ($conn1 == false) {
           $result->errno = 1;
           $result->status = false;
        }
        else {
           mysql_select_db($name, $conn1);
           if (mysql_error()) {
              $result->errno = 2;
              $result->status = false;
           }
           else {
              $no_tables = '';

                 foreach( RemoteTables::$tableNames as $table){
                 $table_result = $this->mysql_table_exists($table, $conn1);
                 if ($table_result === false) {
                    $no_tables .= $table.' ';
                 }
                 else {
                     $found_row = mysql_fetch_assoc($table_result);
                     $result->$table = $found_row['total'];
                 }
              }

              /***For Core4VoIP Check if Reseller4 Available***/
              if(defined('RESELLER4_ON') && RESELLER4_ON == true){
                   $result->r4status = $this->check_reseller4_exists($conn1, $name, &$result, &$no_tables);
              }
              /***End Check***/

              if (!empty($no_tables)) {
                   $result->errno = 3;
                   $result->status = false;
                   $result->tableNames = $no_tables;
              }
           }
        }
        return $result;
   }
   
   function __syncData($server,$db_session,$history_id, $core4voip_reseller4_exists = false) {
        /***set these in config***/
        if(!defined('SYNC_GATEWAY')) define('SYNC_GATEWAY', true);
        if(!defined('SYNC_RESELLER_PAYMENT')) define('SYNC_RESELLER_PAYMENT', true);
        if(!defined('SYNC_RESELLER')) define('SYNC_RESELLER', true);
        /***end***/
        
        $sync = new Synchronization();       
        $history_data = $sync->query()->where('`id` = '.$history_id)->one();
        $update_sync_data['Synchronization'] = array();

        if ( SYNC_GATEWAY === true ) $this->__syncGateway($db_session,$server,$history_data);
        if ( SYNC_RESELLER_PAYMENT === true ) $this->__syncPayments($db_session,$server,$history_data);
        
        /***Added for res4***/
        if ( SYNC_RESELLER_PAYMENT === true ){
            if($core4voip_reseller4_exists) $this->__syncPayments($db_session,$server,$history_data, false, true);
        }

        if ( SYNC_RESELLER === true ) $this->__syncResellers($db_session,$server,$history_data,ResellersType::$Resellers1);
        if ( SYNC_RESELLER === true ) $this->__syncResellers($db_session,$server,$history_data,ResellersType::$Resellers2);
        if ( SYNC_RESELLER === true ) $this->__syncResellers($db_session,$server,$history_data,ResellersType::$Resellers3,$core4voip_reseller4_exists);
        
        
        /***Added for res4***/
        if ( SYNC_RESELLER === true ){
            if($core4voip_reseller4_exists){
                $this->__syncResellers($db_session,$server,$history_data,ResellersType::$Resellers4);
            }
        }

//        if ( SYNC_GW_CLIENT === true ) $this->__syncGWClients($db_session,$server,$history_data);
//        if ( SYNC_CLIENT_PAYMENT === true ) $this->__syncClientsPayments($db_session,$server,$history_data);
        
   }
    
    
   // Sync reseller start
   function __syncResellers($db_session,$server,$history_data,$resellerType, $core4voip_reseller4_exists = false) {
        
        $Synchronization = new Synchronization();
        $RemoteReseller = new RemoteReseller();
        $SyncReseller = new SyncReseller();

        // update part
        $Setting = new Setting();

        $reseller_update = $Setting->get('reseller.update');

        $RemoteReseller->conn = 'remote_' . $db_session;
        $RemoteReseller->table = ResellersType::getTableName($resellerType);
        //$RemoteReseller->fields = null;
        //$RemoteReseller->fieldTypes = null;
        $RemoteReseller->__init();
        $SyncReseller->init();

        $fields  = ResellersType::getFieldsName($resellerType);
        
        $level   = $resellerType;
        $last_id = $history_data['Synchronization'][$fields['last_id']] ;
        $reseller3_parents = array();
        $reseller3_condition = '';
        
        if($last_id === '0'){
            $condition = ' `id` > 0 ';
        }else{
            $condition = !empty($last_id)?' `id` > '.$last_id:null;
        }
        
        $reseller3_condition = !empty($last_id)?' `reseller3_id` > '.$last_id:null;  //for reseller3 parents(if required)

        
        if(($reseller_update['reseller.update'] == 1) && !empty($last_id)) {
            $update_condition = ' `id` <= '.$last_id;
            $reseller3_update_condition = ' `reseller3_id` <= '.$last_id;
            
            if($level == ResellersType::$Resellers3 && $core4voip_reseller4_exists){
                $reseller3_parents = $this->__getReseller3Parents($db_session, $reseller3_update_condition);                
            }
                      
            $resellers_update = $RemoteReseller->query()->where($update_condition)->orderby('`id` ASC ')->all();

            foreach ($resellers_update as &$reseller) {
                // remote data
                $reseller['SyncReseller']          = $reseller['RemoteReseller'];
                $reseller['SyncReseller']['level'] = $level;
                $reseller['SyncReseller']['ip']    = $server['Machine']['ipaddress'];
                $reseller['SyncReseller']['port']  = $server['Machine']['port'];
                $reseller['SyncReseller']['old_id']= $reseller['RemoteReseller']['id'];
                
                if($level == ResellersType::$Resellers3 && $core4voip_reseller4_exists){
                    if($reseller3_parents->status){
                        $reseller['SyncReseller']['idReseller'] = isset($reseller3_parents->data[$reseller['RemoteReseller']['id']])?
                                                   $reseller3_parents->data[$reseller['RemoteReseller']['id']]: null;
                    }
                }else{
                    $reseller['SyncReseller']['idReseller'] = isset($reseller['RemoteReseller']['idReseller'])?
                                                   $reseller['RemoteReseller']['idReseller']: null;
                }
                $reseller['SyncReseller']['MaxClients'] = isset($reseller['RemoteReseller']['MaxClients'])?
                                                   $reseller['RemoteReseller']['MaxClients'] : null;
                unset($reseller['RemoteReseller']);
                unset($reseller['SyncReseller']['id']);

                $reseller['SyncReseller']['table_type'] = RemoteTables::$reseller_table_type;
                // save in the current db

                if ($SyncReseller->update($reseller, false)){

                } else {

                }

            }// end of foreach
        }

        unset($reseller);

        if($level == ResellersType::$Resellers3 && $core4voip_reseller4_exists){
            $reseller3_parents = $this->__getReseller3Parents($db_session, $reseller3_condition);            
        }
        
        $resellers = $RemoteReseller->query()->where($condition)->orderby('`id` ASC ')->all();
        
        foreach($resellers as &$reseller) {
            // remote data
            $reseller['SyncReseller']          = $reseller['RemoteReseller'];
            $reseller['SyncReseller']['level'] = $level;
            $reseller['SyncReseller']['ip']    = $server['Machine']['ipaddress'];
            $reseller['SyncReseller']['port']  = $server['Machine']['port'];
            $reseller['SyncReseller']['old_id']= $reseller['RemoteReseller']['id'];
            
            if($level == ResellersType::$Resellers3 && $core4voip_reseller4_exists){
                if($reseller3_parents->status){
                    $reseller['SyncReseller']['idReseller'] = isset($reseller3_parents->data[$reseller['RemoteReseller']['id']])?
                                               $reseller3_parents->data[$reseller['RemoteReseller']['id']]: null;
                }
            }else{
                $reseller['SyncReseller']['idReseller'] = isset($reseller['RemoteReseller']['idReseller'])?
                                               $reseller['RemoteReseller']['idReseller']: null;
            }
            
            $reseller['SyncReseller']['MaxClients'] = isset($reseller['RemoteReseller']['MaxClients'])?
                                               $reseller['RemoteReseller']['MaxClients'] : 0;
            unset($reseller['RemoteReseller']);
            unset($reseller['SyncReseller']['id']);

            $reseller['SyncReseller']['table_type'] = RemoteTables::$reseller_table_type;
            // save in the current db

            if($SyncReseller->save($reseller, false)){
                $last_id = $reseller['SyncReseller']['old_id'];
            }else{

            }
        } // end of foreach

        // update part

        $found_records = count($resellers);
        $new_records = $SyncReseller->savedCount;
        $updated_records = $SyncReseller->updatedCount;
        $update_sync_data['Synchronization'] = array(
                $fields['found_records'] => $found_records,
                $fields['last_id'] => $last_id,
                $fields['new_records'] => $new_records,
                $fields['updated_records'] => $updated_records
            );

        $Synchronization->id = $history_data['Synchronization']['id'];
        if(!empty($update_sync_data)) $Synchronization->save($update_sync_data, array('validate_with'=>false));

    }
    
    function __getReseller3Parents($index, $condition = null) {

        global $DATABASE;
        $conn = $DATABASE[DATABASE_SET]['remote_'.$index];
        $host = $conn['host'];
        $user = $conn['user'];
        $pass = $conn['pass'];
        $name = $conn['name'];
        $result = new \stdClass();
        $result->status = true;
        $conn1 = @mysql_connect($host, $user, $pass);
        $machine_tables = array();
        $table = 'resellers4_child';

        //$this->__writeLog('mysql error--'.mysql_error().'----condition er baire');
        if ($conn1 == false) {
           //$this->__writeLog('host--'.$host.'--user--'.$user.'--pass'.$pass);
           $this->__writeLog('mysql error--'.mysql_error().'----conn1 false'.mysql_errno());
           $result->errno = 1;
           $this->__writeLog('result status--'.$result->errno);
           $result->status = false;
        }
        else {
           mysql_select_db($name, $conn1);
           if (mysql_error()) {
              $this->__writeLog('mysql error--'.mysql_error().'----');
              $result->errno = 2;
              $result->status = false;
           }
           else {

                 $table_result = $this->mysql_table_exists($table, $conn1);
                 if ($table_result === false) {
                    $result->errno = 3;
                    $result->status = false;
                 }
                 else {
                     $query = "SELECT * FROM `resellers4_child`";
                     if($condition != null) $query .= " WHERE ".$condition;

                     $table_result = mysql_query($query, $conn1);
                     $found_row = array();
                     while($row = mysql_fetch_array($table_result))
                     {
                        $found_row[$row['reseller3_id']] = $row['reseller4_id'];
                     }
                     $result->data = $found_row;
                 }
           }
        }
        return $result;

    }
    
    function __syncPayments($db_session,$server,$history_data,$update=false, $reseller4 = false) {

        $Setting = new Setting();
        $Synchronization = new Synchronization();       
        $RemoteResellerPayment = new RemoteResellerPayment();    
        $SyncResellerPayment = new SyncResellerPayment();    
        $ResellerPayment = new ResellerPayment();    

        $utc_time_option = TimeStampOption::DEFAULT_TIMESTAMP_OPTION;

        $utc_time_flag = $Setting->get('sync.utc.time.option');
        $local_time_flag = $Setting->get('sync.local.time.option');

        if ($utc_time_flag['sync.utc.time.option'] == 1) {
            $utc_time_option = TimeStampOption::UTC_TIMESTAMP_OPTION;
        }

        if ($update == true) {
            $update_data = $Setting->get('resellerspayments.update');
        }
        $RemoteResellerPayment->conn = 'remote_' . $db_session;
        $RemoteResellerPayment->table = 'resellerspayments'; //core4voip table
        $RemoteResellerPayment->__init();
        $ResellerPayment->prepareAdditionalInfo();

        $start_date = null;

        $condition = null;
        $sync_condition_date = '';
        if (!empty($server['Machine']['sync_start_date']) ) {            
            $start_date = $server['Machine']['sync_start_date'];
            $end_date = date('Y-m-d H:i:s');
            $sync_condition_date = ''.$Setting->getTimeStamp('`data`',$utc_time_option).' >=  '.$Setting->getTimeStamp('\''.$start_date.'\'',$utc_time_option);
        }
                
        if($reseller4 == true){
            $condition .= !empty($history_data['Synchronization']['c4_res4_payment_last_id'])?'  `id` > '.$history_data['Synchronization']['c4_res4_payment_last_id']:$sync_condition_date;

            $last_id = $history_data['Synchronization']['c4_res4_payment_last_id'] ;

            $fieldset = array('id','id_reseller','resellerlevel','money',$Setting->getTimeStamp('`data`',$utc_time_option) => 'data','type');

            $RemoteResellerPayment->table = 'resellers4_payments';
            $RemoteResellerPayment->__init();

            $remoteResellerPayments = $RemoteResellerPayment->query()->where($condition)->select($fieldset)->orderby(' `id` ASC')->all();
        }else{
            $condition .= !empty($history_data['Synchronization']['res_payment_last_id'])?'  `id` > '.$history_data['Synchronization']['res_payment_last_id']:$sync_condition_date;

            $last_id = $history_data['Synchronization']['res_payment_last_id'] ;

            $fieldset = array('id','id_reseller','resellerlevel','money',$Setting->getTimeStamp('`data`',$utc_time_option) => 'data','type');

            $remoteResellerPayments = $RemoteResellerPayment->query()->where($condition)->select($fieldset)->orderby(' `id` ASC')->all();
        }

        $SyncResellerPayment->init();
        foreach($remoteResellerPayments as &$resellerPayment){

             if ($update == true)
                $resellerPayment['SyncResellerPayment']['update_data'] = $update_data;

             if ($utc_time_flag['sync.utc.time.option'] == 1) {
                $resellerPayment['RemoteResellerPayment']['data'] = date('Y-m-d H:i:s',$resellerPayment['RemoteResellerPayment']['data']);
             }

             $resellerPayment['SyncResellerPayment'] = $resellerPayment['RemoteResellerPayment'];
             $resellerPayment['SyncResellerPayment']['amount'] = $resellerPayment['RemoteResellerPayment']['money'];
             $resellerPayment['SyncResellerPayment']['tr_date'] = $resellerPayment['RemoteResellerPayment']['data'];
             $resellerPayment['SyncResellerPayment']['port'] = $server['Machine']['port'];
             $resellerPayment['SyncResellerPayment']['service'] = 0;
             $resellerPayment['SyncResellerPayment']['old_id'] = $resellerPayment['RemoteResellerPayment']['id'];
             $resellerPayment['SyncResellerPayment']['server_id'] = $server['Machine']['id'];
             
             // auto sync from = A.S.F. , Original Amount = O.A.
             //$resellerPayment['ResellerPayment']['description'] = ' A.S.F.: '.Machine::getName($server['Machine']['ipaddress'].':'.$server['Machine']['port']).'; O. A.: '.$resellerPayment['RemoteResellerPayment']['money'];
             $resellerPayment['SyncResellerPayment']['description'] = ' A.S.F.: '.($server['Machine']['ipaddress'].':'.$server['Machine']['port']).'; O. A.: '.$resellerPayment['RemoteResellerPayment']['money'];
             
             unset($resellerPayment['SyncResellerPayment']['id']);
             unset($resellerPayment['RemoteResellerPayment']);

             // newly added 25.08.2010
             $resellerPayment['SyncResellerPayment']['table_type'] = RemoteTables::$reseller_table_type;
             // end of newly added 25.08.2010

             $resellerPayment['SyncResellerPayment']['ip'] = $server['Machine']['ipaddress'];
             $resellerPayment['SyncResellerPayment']['admin_id'] = $server['Machine']['last_admin_id'];
             $resellerPayment['SyncResellerPayment']['sync_flag'] = 1;
             $resellerPayment['SyncResellerPayment']['sync_id'] = $history_data['Synchronization']['id']; // for which sync

             if($SyncResellerPayment->save($resellerPayment, false)){
                    $last_id = $resellerPayment['SyncResellerPayment']['old_id'];
                    // add new id to pending ledgers
                    $resellerPayment['SyncResellerPayment']['id'] = $SyncResellerPayment->lastInsertId();
                    if($ResellerPayment->insertToLedger(array('ResellerPayment'=>$resellerPayment['SyncResellerPayment'])) === true){
                         $resellerPayment['SyncResellerPayment']['pending_ledger_id'] = $ResellerPayment->access_temp_id();
                         $resellerPayment['SyncResellerPayment']['status'] = 1;//pdebug($resellerPayment);

                         // update resellerspayments
                         $update_query =  ' UPDATE `sync_reseller_payments` SET `pending_ledger_id` =  '.$ResellerPayment->access_temp_id().',`status` = 1 WHERE `id` = '.$SyncResellerPayment->lastInsertId();
                         $runQuerySuccess = $this->execute_query($update_query);
                     }else{
                        
                     }
             }
             else {
               
            }
        }

        $found_records = count($remoteResellerPayments);
        $new_records = $SyncResellerPayment->savedCount;
        $updated_records = $SyncResellerPayment->updatedCount;

        if($reseller4 == true){
            $update_sync_data['Synchronization'] = array(
                'c4_res4_payment_found_records' => $found_records,
                'c4_res4_payment_last_id' => $last_id,
                'c4_res4_payment_new_records' => $new_records,
                'c4_res4_payment_updated_records' => $updated_records
            );
        }else{
            $update_sync_data['Synchronization'] = array(
                'res_payment_found_records' => $found_records,
                'res_payment_last_id' => $last_id,
                'res_payment_new_records' => $new_records,
                'res_payment_updated_records' => $updated_records
            );
        }
        
        $Synchronization->id = $history_data['Synchronization']['id'];
        if (!empty($update_sync_data))
            $Synchronization->save($update_sync_data, array('validate_with'=>false));

    }
    
    function __syncGateway($db_session,$server,$history_data=null) {
        
        $sync = new Synchronization();       
        $SyncGateway = new SyncGateway();
        $RemoteGateway = new RemoteGateway();
        // Get data from Gateway Table
        $RemoteGateway->conn = 'remote_' . $db_session;
        $condition = !empty($history_data['Synchronization']['gateway_last_id'])?' `id_route` > '.$history_data['Synchronization']['gateway_last_id']:null;
        $remoteGateways = $RemoteGateway->query()->select(array('id_route','description','SUBSTRING_INDEX(ip_number,":",1)'=>'ip_number','SUBSTRING_INDEX(ip_number,":",-1)'=>'ip_port','type','call_limit'))->where($condition)->orderby(' `id_route` ASC ')->all();

        $gateway_last_id = $history_data['Synchronization']['gateway_last_id'] ;
        $SyncGateway->init();
        foreach ($remoteGateways as &$gateway) {
            $tmp_data = $gateway;
            $gateway['SyncGateway'] = $gateway['accountica\models\RemoteGateway'];
            $gateway['SyncGateway']['server_ip']   = $server['Machine']['ipaddress'];
            $gateway['SyncGateway']['server_port'] = $server['Machine']['port'];

            if ($SyncGateway->save($gateway, false)) {
                $gateway_last_id = $gateway['SyncGateway']['id_route'];
            }
            else {
                
            }
        }

        $gateway_found_records = count($remoteGateways);
        $gateway_new_records = $SyncGateway->savedCount;
        $gateway_updated_records = $SyncGateway->updatedCount;
        $update_sync_data['Synchronization'] = array(
                'gateway_found_records' => $gateway_found_records,
                'gateway_last_id' => $gateway_last_id,
                'gateway_new_records' => $gateway_new_records,
                'gateway_updated_records' => $gateway_updated_records
            );
        $sync->id = $history_data['Synchronization']['id'];
        if (!empty($update_sync_data)) $sync->save($update_sync_data, array('validate_with'=>false));
   }

   function mysql_table_exists($table, $link) {
        $exists = mysql_query("SELECT count(*) AS `total` FROM `$table` ", $link);
        if ($exists) return $exists;
        return false;
   }

   /***
    * This function check the triggers for
    * a particular trigger to decide r4 is available in server
    * or not and its also show tables not found error if r4 enable but tables are not
    * (Russel Sir, dont want to use[trigger system] it, also wants [enable or not] r4 data should come)
    ***/
   function check_reseller4_exists1($link, $db, $result, $no_tables) {

        $res4_tables = RemoteTables::$tableNamesofC4R4;

        $query = "SELECT trigger_schema, trigger_name, action_statement
        FROM information_schema.triggers
        WHERE trigger_name = 'calls_reseller4_insert_trigger'
        AND trigger_schema = '$db'";

        $table_result = mysql_query($query, $link);
        $found_row = mysql_fetch_assoc($table_result);

        if (!empty($found_row) && isset($found_row['action_statement'])){

            if(!empty($res4_tables)){
                foreach( $res4_tables as $table ){
                   $table_result = $this->mysql_table_exists($table, $link);
                   if ($table_result === false) {
                       $no_tables .= $table.' ';
                   }
                   else {
                       $found_row = mysql_fetch_assoc($table_result);
                       $result->$table = $found_row['total'];
                   }
               }
            }

            return true;
        }

        return false;

   }

   function check_reseller4_exists($link, $db, $result, $no_tables) {

        $res4_tables = RemoteTables::$tableNamesofC4R4;

        if(!empty($res4_tables)){
            foreach( $res4_tables as $table ){
               $table_result = $this->mysql_table_exists($table, $link);
               if ($table_result === false) {
                   return false;  //If any one table is not found, then it'll decide this server doesn't have r4
               }
               else {
                   $found_row = mysql_fetch_assoc($table_result);
                   $result->$table = $found_row['total'];
               }
           }
        }

        return true;
   }

   
   function __checkServerStatus($ip, $port) {
        $fp = @fsockopen("$ip", $port, $errno, $errstr, "1");
        $result = new \stdClass();
        $result->errno  = $errno;
        $result->errstr = $errstr;
        $result->status = false;
        $result->resourceId = $fp;
        if ($fp)
          $result->status = true;

        return $result;
   }
   
   public function getIpNameList($intro = false){
        return ($intro) ? array('' => $intro) + $this->query()->orderby('ip_alias ASC')->map('id', 'ip_alias') : $this->query()->orderby('ip_alias ASC')->map('id', 'ip_alias');
    }
   // for -> ServerName :: IP 
   public function getServerNameList($intro = false){
        return ($intro) ? array('' => $intro) + $this->query()->select(array('id','concat(ip_alias,"::",ip)'=>'ip_alias'))->orderby('ip_alias ASC')->map('id','ip_alias') : $this->query()->select(array('id','concat(ip_alias,"::",ip)'=>'ip_alias'))->orderby('ip_alias ASC')->map('id','ip_alias');
    }
    
}
?>