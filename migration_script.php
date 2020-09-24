<?php
//$exportDb = "accounticavl";
class Migration {
    
    public $dh1 = null;
    public static $exportDb= "accounticavl";
    public $migration = array(
        'admins'=>array(
            'query'=>array(
                    /*'TRUNCATE `%s`.admins',
                    "INSERT into `%s`.admins (user_name, password, name) SELECT user_name, password, name FROM `accounticavl`.admins",
                    //"INSERT into `%s`.admins (user_name, password, name) select user_name, password, name from ".static::$exportDb.".admins"
                    'TRUNCATE `%s`.users',
                    'INSERT into `%s`.users (user_name, password, name, phone,	email, address,	country_id, usertype, flag, status) SELECT user_name, password, name, phone,email, address, country_id, usertype, flag, status from `accounticavl`.users',
                    'TRUNCATE `%s`.synchronizations',
                    'INSERT into `%s`.synchronizations (ip, port, admin_id, date, res1_last_id, res2_last_id, res3_last_id, res4_last_id, res4_found_records, res4_new_records, res4_updated_records, res_payment_last_id, c4_res4_payment_last_id, c4_res4_payment_found_records, c4_res4_payment_new_records, c4_res4_payment_updated_records, gateway_last_id, gwclients_last_id, gw_payments_last_id, res1_found_records, res2_found_records, res3_found_records, res_payment_found_records, gateway_found_records, gwclients_found_records, gw_payments_found_records, res1_new_records, res2_new_records, res3_new_records, res_payment_new_records, gateway_new_records, gwclients_new_records, gw_payments_new_records, res1_updated_records, res2_updated_records, res3_updated_records, res_payment_updated_records, gateway_updated_records, gwclients_updated_records, gw_payments_updated_records, sync_from_date, sync_to_date, queue, status )
                        SELECT ip, port, admin_id, date, res1_last_id, res2_last_id, res3_last_id, res4_last_id, res4_found_records, res4_new_records, res4_updated_records, res_payment_last_id, c4_res4_payment_last_id, c4_res4_payment_found_records, c4_res4_payment_new_records, c4_res4_payment_updated_records, gateway_last_id, gwclients_last_id, gw_payments_last_id, res1_found_records, res2_found_records, res3_found_records, res_payment_found_records, gateway_found_records, gwclients_found_records, gw_payments_found_records, res1_new_records, res2_new_records, res3_new_records, res_payment_new_records, gateway_new_records, gwclients_new_records, gw_payments_new_records, res1_updated_records, res2_updated_records, res3_updated_records, res_payment_updated_records, gateway_updated_records, gwclients_updated_records, gw_payments_updated_records, sync_from_date, sync_to_date, queue, status from `accounticavl`.synchronizations',
                    'TRUNCATE `%s`.curs',
                    'INSERT into `%s`.curs (name, sign, base ) SELECT name, sign, base from `accounticavl`.curs',
                    'TRUNCATE `%s`.rates',
                    'INSERT into `%s`.rates (rate, base_cur_id, cur_id, start_date, end_date ) SELECT rate, base_cur_id, cur_id, start_date, end_date FROM `accounticavl`.rates',
                   */ 'TRUNCATE `%s`.accounts',
                    'INSERT into `%s`.accounts (type,cur_id, opening_balance,user_id,opening_date,last_balance,last_update ) SELECT type, cur_id, opening_balance, user_id, opening_date, last_balance, last_update FROM `accounticavl`.accounts',
                    /*'TRUNCATE `%s`.banks',
                    'INSERT into `%s`.banks ( account_id,bank_name, branch, acc_no, acc_name) SELECT account_id, bank_name, branch, acc_no, acc_name FROM `accounticavl`.banks',
                    //'TRUNCATE `%s`.categorys',
                    //'INSERT into `%s`.categorys (  name, desc, type, parent_id, is_active) SELECT  name, desc, type, parent_id, is_active FROM `accounticavl`.categorys',
                    'TRUNCATE `%s`.countries',
                    'INSERT into `%s`.`countries` ( country, iso2, iso3, noc) SELECT country, iso2, iso3, noc FROM `accounticavl`.`countrys`',
                    'TRUNCATE `%s`.`gateways`',
                    'INSERT into `%s`.`gateways` ( id_route, server_id, description, ip_number, ip_port, type, call_limit)
                         SELECT id_route,  h323_id, description, ip_number, ip_port, type, call_limit FROM `accounticavl`.`gateways`',
                    'TRUNCATE `%s`.`gateway_rate_history`',
                    'INSERT into `%s`.`gateway_rate_history` (  user_id, gateway_id, rate, last_date ) SELECT  user_id, gateway_id, rate, last_date FROM `accounticavl`.`gateway_rate_history`',

                    'TRUNCATE `%s`.`ledgers`',
                    "INSERT into `%s`.`ledgers` (reseller_id, account_id, src_bank_id, dst_bank_id, amount, balance_after, cur_id, rate, product_id, category_id, tr_date, description, `type`, created_by, product_rate, reseller_rate, deposit, deposit_cur_id, is_posted, bank_balance_after, invoice_id, res_payment_id, res_old_amount, sync_flag, keep )
                        SELECT  reseller_id, account_id, src_bank_id, dst_bank_id, amount, balance_after, cur_id, rate, product_id, category_id, if(((SELECT `COLUMN_NAME` FROM `information_schema`.`COLUMNS` WHERE `TABLE_SCHEMA` = 'accounticavl' AND `TABLE_NAME` = 'ledgers' AND `COLUMN_NAME` = 'time') IS NULL), (CONCAT(`date`,' 00:00:00')), (CONCAT(`date`,'',`time`))) as tr_date,
                            description, `type`, created_by, product_rate, reseller_rate, deposit, deposit_cur_id,is_posted, bank_balance_after, invoice_id, res_payment_id, res_old_amount,sync_flag, `keep` FROM `accounticavl`.`ledgers`",

                    'TRUNCATE `%s`.`machines`',
                    'INSERT into `%s`.`machines`( ip, ip_alias, server_type, port, db_name, host, password, flag, type, status, last_admin_id, sync_start_date, start_status ) SELECT ip, ip_alias, server_type, port, db_name, host, password, flag, type, status, last_admin_id, sync_start_date, start_status FROM `accounticavl`.`machines`',
                    'TRUNCATE `%s`.`pending_ledgers`',
                    "INSERT into `%s`.`pending_ledgers`(reseller_id, account_id, src_bank_id, dst_bank_id, amount, balance_after, cur_id, rate, product_id, category_id, tr_date, description, type, created_by, product_rate, reseller_rate, deposit, deposit_cur_id, is_posted, bank_balance_after, invoice_id, res_payment_id, res_old_amount, sync_flag, keep) SELECT  reseller_id, account_id, src_bank_id, dst_bank_id, amount, balance_after, cur_id, rate, product_id, category_id,
                        if((SELECT `COLUMN_NAME` FROM `information_schema`.`COLUMNS` WHERE `TABLE_SCHEMA` = 'accounticavl' AND `TABLE_NAME` = 'pending_ledgers' AND `COLUMN_NAME` = 'time') IS NULL, CONCAT(`date`,' 00:00:00'), CONCAT(`date`,' ',`time`)) as tr_date, description, type, created_by, product_rate, reseller_rate, deposit, deposit_cur_id, is_posted, bank_balance_after, invoice_id, res_payment_id, res_old_amount, sync_flag, keep FROM `accounticavl`.`pending_ledgers`",
                    'TRUNCATE `%s`.`products`',
                    'INSERT into `%s`.`products` ( name, price, is_active ) SELECT name, price, is_active FROM `accounticavl`.`products`',
                    'TRUNCATE `%s`.`resellers`',
                    'INSERT into `%s`.`resellers` ( old_id, idReseller, login, password, level, client_type, type, callsLimit, fullname, address, city, zipcode, country, phone, email, table_type, account_state) SELECT old_id, idReseller, login, password, level, client_type, type, callsLimit, Fullname, Address, City, ZipCode, Country, Phone, Email, table_type, account_state FROM `accounticavl`.`resellers`',
                    'TRUNCATE `%s`.`reseller_payments`',
                    'INSERT into `%s`.`reseller_payments` ( old_id, id_reseller,resellerlevel,client_type, amount, tr_date, type, description, status, pending_ledger_id ) SELECT old_id, id_reseller, resellerlevel, client_type, money, data, type, description, status, pending_ledger_id FROM `accounticavl`.`resellerspayments`',
                    'TRUNCATE `%s`.`reseller_rate_history`',
                    'INSERT into `%s`.`reseller_rate_history` ( user_id, res_id, res_level, rate, last_date ) SELECT user_id, res_id, res_level, rate, last_date FROM `accounticavl`.`res_rate_history`',
                    'TRUNCATE `%s`. `settings`',
                    'INSERT into `%s`.`settings` ( `id`, `key`, `value`,`type` ) SELECT `id`, `key`, `value`,`type` FROM `accounticavl`.`settings`',
                    */
                     
                    
                )
        )
    );

    function __construct($infos = array()){
        if(!empty($infos)){
            //connection to the database
            $this->dh1 = mysql_connect($infos['hostname'], $infos['username'], $infos['password']) or die("Unable to connect to MySQL");
            mysql_select_db($infos['database'], $this->dh1) or die(mysql_error());
        }
    }

    function migration($infos = array()){
        if(!empty($this->migration)){
            foreach($this->migration as $temp_name=>$temp_value){
                foreach($temp_value['query'] as $no=>$query){
                    $Q = sprintf($query, $infos['database']);
                    mysql_query($Q, $this->dh1) or die(mysql_error());
                }
            }
        }
    }
    
    function db_close(){
        if($this->dh1 != null) mysql_close($this->dh1);
    }

}

//Database should be in same server, if not then dump the from_db and import as "temp_accounticavl_db" in the AccounticaXL server, and migrate
$infos = array(
    'hostname'=>'localhost',
    'port'=>'3306',
    'username'=>'root',
    'password'=>'',
    'database'=>'accounticaxl'
);

$mig = new Migration($infos);
$mig->migration($infos);
$mig->db_close();
?>