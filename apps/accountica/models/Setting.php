<?php

namespace accountica\models;

class TimeStampOption {

    const UTC_TIMESTAMP_OPTION = 1;
    const LOCAL_TIMESTAMP_OPTION = 2;
    const DEFAULT_TIMESTAMP_OPTION = 3;
    const SERVER_TIMESTAMP_OPTION = 4;

    static $names = array(
        1 => 'UTC',
        2 => 'LOCAL',
        3 => 'DEFAULT',
        4 => 'SEVER'
    );

    static function toString($type) {
        return TimeStampOption::$names[$type];
    }

}

class TimeZone {

    const ALLOW = 1;
    const DENY = 2;
    const APPLIED = 0;

    public static $timezones = array(
        'Pacific/Midway' => "(GMT-11:00) Midway Island",
        'US/Samoa' => "(GMT-11:00) Samoa",
        'US/Hawaii' => "(GMT-10:00) Hawaii",
        'US/Alaska' => "(GMT-09:00) Alaska",
        'US/Pacific' => "(GMT-08:00) Pacific Time (US &amp; Canada)",
        'America/Tijuana' => "(GMT-08:00) Tijuana",
        'US/Arizona' => "(GMT-07:00) Arizona",
        'US/Mountain' => "(GMT-07:00) Mountain Time (US &amp; Canada)",
        'America/Chihuahua' => "(GMT-07:00) Chihuahua",
        'America/Mazatlan' => "(GMT-07:00) Mazatlan",
        'America/Mexico_City' => "(GMT-06:00) Mexico City",
        'America/Monterrey' => "(GMT-06:00) Monterrey",
        'Canada/Saskatchewan' => "(GMT-06:00) Saskatchewan",
        'US/Central' => "(GMT-06:00) Central Time (US &amp; Canada)",
        'US/Eastern' => "(GMT-05:00) Eastern Time (US &amp; Canada)",
        'US/East-Indiana' => "(GMT-05:00) Indiana (East)",
        'America/Bogota' => "(GMT-05:00) Bogota",
        'America/Lima' => "(GMT-05:00) Lima",
        'America/Caracas' => "(GMT-04:30) Caracas",
        'Canada/Atlantic' => "(GMT-04:00) Atlantic Time (Canada)",
        'America/La_Paz' => "(GMT-04:00) La Paz",
        'America/Santiago' => "(GMT-04:00) Santiago",
        'Canada/Newfoundland' => "(GMT-03:30) Newfoundland",
        'America/Buenos_Aires' => "(GMT-03:00) Buenos Aires",
        'Greenland' => "(GMT-03:00) Greenland",
        'Atlantic/Stanley' => "(GMT-02:00) Stanley",
        'Atlantic/Azores' => "(GMT-01:00) Azores",
        'Atlantic/Cape_Verde' => "(GMT-01:00) Cape Verde Is.",
        'Africa/Casablanca' => "(GMT) Casablanca",
        'Europe/Dublin' => "(GMT) Dublin",
        'Europe/Lisbon' => "(GMT) Lisbon",
        'Europe/London' => "(GMT) London",
        'Africa/Monrovia' => "(GMT) Monrovia",
        'Europe/Amsterdam' => "(GMT+01:00) Amsterdam",
        'Europe/Belgrade' => "(GMT+01:00) Belgrade",
        'Europe/Berlin' => "(GMT+01:00) Berlin",
        'Europe/Bratislava' => "(GMT+01:00) Bratislava",
        'Europe/Brussels' => "(GMT+01:00) Brussels",
        'Europe/Budapest' => "(GMT+01:00) Budapest",
        'Europe/Copenhagen' => "(GMT+01:00) Copenhagen",
        'Europe/Ljubljana' => "(GMT+01:00) Ljubljana",
        'Europe/Madrid' => "(GMT+01:00) Madrid",
        'Europe/Paris' => "(GMT+01:00) Paris",
        'Europe/Prague' => "(GMT+01:00) Prague",
        'Europe/Rome' => "(GMT+01:00) Rome",
        'Europe/Sarajevo' => "(GMT+01:00) Sarajevo",
        'Europe/Skopje' => "(GMT+01:00) Skopje",
        'Europe/Stockholm' => "(GMT+01:00) Stockholm",
        'Europe/Vienna' => "(GMT+01:00) Vienna",
        'Europe/Warsaw' => "(GMT+01:00) Warsaw",
        'Europe/Zagreb' => "(GMT+01:00) Zagreb",
        'Europe/Athens' => "(GMT+02:00) Athens",
        'Europe/Bucharest' => "(GMT+02:00) Bucharest",
        'Africa/Cairo' => "(GMT+02:00) Cairo",
        'Africa/Harare' => "(GMT+02:00) Harare",
        'Europe/Helsinki' => "(GMT+02:00) Helsinki",
        'Europe/Istanbul' => "(GMT+02:00) Istanbul",
        'Asia/Jerusalem' => "(GMT+02:00) Jerusalem",
        'Europe/Kiev' => "(GMT+02:00) Kyiv",
        'Europe/Minsk' => "(GMT+02:00) Minsk",
        'Europe/Riga' => "(GMT+02:00) Riga",
        'Europe/Sofia' => "(GMT+02:00) Sofia",
        'Europe/Tallinn' => "(GMT+02:00) Tallinn",
        'Europe/Vilnius' => "(GMT+02:00) Vilnius",
        'Asia/Baghdad' => "(GMT+03:00) Baghdad",
        'Asia/Kuwait' => "(GMT+03:00) Kuwait",
        'Europe/Moscow' => "(GMT+03:00) Moscow",
        'Africa/Nairobi' => "(GMT+03:00) Nairobi",
        'Asia/Riyadh' => "(GMT+03:00) Riyadh",
        'Europe/Volgograd' => "(GMT+03:00) Volgograd",
        'Asia/Tehran' => "(GMT+03:30) Tehran",
        'Asia/Baku' => "(GMT+04:00) Baku",
        'Asia/Muscat' => "(GMT+04:00) Muscat",
        'Asia/Tbilisi' => "(GMT+04:00) Tbilisi",
        'Asia/Yerevan' => "(GMT+04:00) Yerevan",
        'Asia/Kabul' => "(GMT+04:30) Kabul",
        'Asia/Yekaterinburg' => "(GMT+05:00) Ekaterinburg",
        'Asia/Karachi' => "(GMT+05:00) Karachi",
        'Asia/Tashkent' => "(GMT+05:00) Tashkent",
        'Asia/Kolkata' => "(GMT+05:30) Kolkata",
        'Asia/Kathmandu' => "(GMT+05:45) Kathmandu",
        'Asia/Almaty' => "(GMT+06:00) Almaty",
        'Asia/Dhaka' => "(GMT+06:00) Dhaka",
        'Asia/Novosibirsk' => "(GMT+06:00) Novosibirsk",
        'Asia/Bangkok' => "(GMT+07:00) Bangkok",
        'Asia/Jakarta' => "(GMT+07:00) Jakarta",
        'Asia/Krasnoyarsk' => "(GMT+07:00) Krasnoyarsk",
        'Asia/Chongqing' => "(GMT+08:00) Chongqing",
        'Asia/Hong_Kong' => "(GMT+08:00) Hong Kong",
        'Asia/Irkutsk' => "(GMT+08:00) Irkutsk",
        'Asia/Kuala_Lumpur' => "(GMT+08:00) Kuala Lumpur",
        'Australia/Perth' => "(GMT+08:00) Perth",
        'Asia/Singapore' => "(GMT+08:00) Singapore",
        'Asia/Taipei' => "(GMT+08:00) Taipei",
        'Asia/Ulaanbaatar' => "(GMT+08:00) Ulaan Bataar",
        'Asia/Urumqi' => "(GMT+08:00) Urumqi",
        'Asia/Seoul' => "(GMT+09:00) Seoul",
        'Asia/Tokyo' => "(GMT+09:00) Tokyo",
        'Asia/Yakutsk' => "(GMT+09:00) Yakutsk",
        'Australia/Adelaide' => "(GMT+09:30) Adelaide",
        'Australia/Darwin' => "(GMT+09:30) Darwin",
        'Australia/Brisbane' => "(GMT+10:00) Brisbane",
        'Australia/Canberra' => "(GMT+10:00) Canberra",
        'Pacific/Guam' => "(GMT+10:00) Guam",
        'Australia/Hobart' => "(GMT+10:00) Hobart",
        'Australia/Melbourne' => "(GMT+10:00) Melbourne",
        'Pacific/Port_Moresby' => "(GMT+10:00) Port Moresby",
        'Australia/Sydney' => "(GMT+10:00) Sydney",
        'Asia/Vladivostok' => "(GMT+10:00) Vladivostok",
        'Asia/Magadan' => "(GMT+11:00) Magadan",
        'Pacific/Auckland' => "(GMT+12:00) Auckland",
        'Pacific/Fiji' => "(GMT+12:00) Fiji",
        'Asia/Kamchatka' => "(GMT+12:00) Kamchatka",
    );

    public static function getTimeZone($zone) {
        if (isset(TimeZone::$timezones[$zone]))
            return TimeZone::$timezones[$zone];
        return 'Unknown';
    }

    /**
     * return array of all status messages
     * @param $empty string
     */
    public static function getAllTimeZones($empty = false) {
        if ($empty !== false)
            return array('' => $empty) + TimeZone::$timezones;
        return TimeZone::$timezones;
    }

}

class Setting extends \Furina\mvc\model\Model {

    static public $rate_chart = array();

    function init(){
        parent::__init();
        if (!defined('RESELLER4_ON'))
            $this->checkReseller4();
        $this->rateSheetData();
        //$this->setTimeZone();
    }

    function checkReseller4() {
        $is_reseller4 = $this->get('reseller4.use');
        if (!defined('RESELLER4_ON'))
            define('RESELLER4_ON', (isset($is_reseller4['reseller4.use']) ? $is_reseller4['reseller4.use'] : false));
    }

    function getExchangeRate($default = 'default') {
        $condition = '`Rate`.`end_date` = \'0000-00-00\'';
        switch ($default) {
            case 'default':
                $condition .= ' AND `Cur`.`base` = 1';
                break;
        }

        $cur = new Cur();
        $rate = new Rate();

        $cur->bindModel(array(
            'hasMany' => array(
                'Rate' => array(
                    'localKey' => 'id',
                    'foreignKey' => 'cur_id'
            ))
        ));
        $currency_rate = $cur->where($condition)->map('Rate.base_cur_id', 'Rate.rate');
        return $currency_rate;
    }

    function rateSheetData() {
        if (Setting::$rate_chart == null) {
            $cur = new Cur();
            $rate = new Rate();
            $currency = $cur->query()->all();
            $cur = new Cur();
            $cur->bindModel(array(
                'hasMany' => array(
                    'accountica\models\Rate' => array(
                        'localKey' => 'name',
                        'foreignKey' => 'base_cur_id',
                        'conditions' => '`Rate`.`end_date` = \'0000-00-00\' AND `Rate`.`base_cur_id` != `Rate`.`cur_id`'
                    )
                )
            ));
            $currencies = $cur->query()->orderby('`Cur`.`id` ASC')->all();
            
            $rate_chart = array();
            $rate_data = array();
            foreach ($currency as $base) {
                $base_cur_id = $base['Cur']['name'];

                $temp = array();
                foreach ($currency as $cur) {
                    $cur_id = $cur['Cur']['name'];
                    if ($cur_id == $base_cur_id)
                        $temp[$cur_id] = 1.00;
                    else {
                        $rate = $this->__findRate($base_cur_id, $cur_id, $currencies);
                        if (!empty($rate))
                            $temp[$cur_id] = $rate;
                        else
                            $temp[$cur_id] = -1;
                    }
                }
                $rate_data[$base_cur_id]['Rate'] = $temp;
                $rate_data[$base_cur_id]['Base'] = $base['Cur'];
            }
            

            $cur_digit = CUR_DIGIT;

            $rate_chart = $rate_data;
            foreach ($rate_chart as $key1 => $value) {
                foreach ($value['Rate'] as $key2 => $index) {
                    if (($index == -1)):
                        if (($rate_chart[$key2]['Rate'][$key1] == -1))
                            $rate_chart[$key1]['Rate'][$key2] = 1.00;
                        else
                            $rate_chart[$key1]['Rate'][$key2] = sprintf("%.{$cur_digit}lf", 1 / $rate_chart[$key2]['Rate'][$key1]);
                    endif;
                }
            }
            
            Setting::$rate_chart = $rate_chart;
        }

        //$this->rate_chart = Setting::$rate_chart;
    }

    function __findRate($base_cur_id, $cur_id, $currencies) {
        foreach ($currencies as $cur) {
            $base_index = $cur['Cur']['name'];
            if ($base_index == $cur_id) {
                $currency_index = $cur['Rate']['cur_id'];
                if (!empty($currency_index) && $base_cur_id == $currency_index) {
                    $rate = $cur['Rate']['rate'];
                    return $rate;
                }
            }
        }

        return null;
    }

    function check_cur($base, $cur, $chart) {
        if (empty($chart[$cur]))
            return null;
        foreach ($chart[$cur]['Rate'] as $key => $rate) {
            if ($key == $base)
                return $rate;
        }
    }

    function rateSheet() {
        if (Setting::$rate_chart == null) {

            $cur = new Cur();
            $rate = new Rate();
            $currencies = $cur->query->all();

            $rate_chart = array();
            foreach ($currencies as $base) {
                $base_index = $base['Cur']['id'];
                foreach ($currencies as $currency) {
                    $currency_index = $currency['Cur']['id'];
                    $condition = '`Rate`.`end_date` = \'0000-00-00\'';
                    $condition .= ' AND `Rate`.`base_cur_id` = ' . $base['Cur']['id'];
                    $condition .= ' AND `Rate`.`cur_id` = ' . $currency['Cur']['id'];

                    $rates = $rate->query()->where($condition)->one();

                    if (empty($rates)) {
                        $rates['Rate']['rate'] = -1;
                    }

                    $rate_chart[$base_index]['Rate'][$currency_index] = $rates['Rate']['rate'];
                }

                $rate_chart[$base_index]['Base'] = $base['Cur'];
                //echo '<br>-------------------------<br />';
            }


            $cur_digit = CUR_DIGIT;

            foreach ($rate_chart as $key1 => $value) {
                foreach ($value['Rate'] as $key2 => $index) {

                    if (($index == -1)):
                        if (($rate_chart[$key2]['Rate'][$key1] == -1))
                            $rate_chart[$key1]['Rate'][$key2] = 1.00;
                        else
                            $rate_chart[$key1]['Rate'][$key2] = sprintf("%.{$cur_digit}lf", 1.00 / $rate_chart[$key2]['Rate'][$key1]);
                    endif;
                }
            }

            Setting::$rate_chart = $rate_chart;
        }

        $this->rate_chart = Setting::$rate_chart;
    }

    function getBaseCurrency() {
        $cur = new Cur();
        $base = $cur->query()->where('`Cur`.`base` = 1')->one();
        return $base['Cur']['name'];
    }

    function getBase() {
        $this->loadModel('Cur');
        $base = $this->Cur->find('`Cur`.`base` = 1');
        return $base;
    }

    function getBaseId() {
        $this->loadModel('Cur');
        $base = $this->Cur->find('`Cur`.`base` = 1');
        return $base['Cur']['id'];
    }

    function getBaseSign() {
        $this->loadModel('Cur');
        $base = $this->Cur->find('`Cur`.`base` = 1');
        return $base['Cur']['sign'];
    }

    function getDepositCurrency() {
        $Cur = new Cur();
        $deposit_currency = $Cur->query()->where('`name` = "USD"')->one();
        $cur['Cur']['id'] = $deposit_currency['Cur']['id'];
        $cur['Cur']['name'] = $deposit_currency['Cur']['name'];
        $cur['Cur']['sign'] = $deposit_currency['Cur']['sign'];

        return $cur;
    }

    function defaultFieldControlValue() {
        return '332';
    }

    function getFieldControlValue() {
        // read from file
        $fileName = '../su/config/field_setting.php';
        if (file_exists($fileName)) {
            $content = @file_get_contents($fileName);
            return $content;
        } else {
            return $this->defaultFieldControlValue();
        }
    }

    function setFieldControlValue($value) {
        // write to file
        $fileName = '../su/config/field_setting.php';
        if (file_exists($fileName)) {
            @file_put_contents($fileName, $value);
            return true;
        }

        return false;
    }

    function getTimeStamp($field, $option = 4) {

        switch ($option) {
            case TimeStampOption::DEFAULT_TIMESTAMP_OPTION :

            // break;
            case TimeStampOption::LOCAL_TIMESTAMP_OPTION :

            //break;
            case TimeStampOption::SERVER_TIMESTAMP_OPTION :
                $field = str_replace('`', '', $field);
                break;
            case TimeStampOption::UTC_TIMESTAMP_OPTION :
                $field = 'UNIX_TIMESTAMP(' . $field . ')';
                break;
        }
        return $field;
    }

    function setTimeZone() {
        $time_zone = $this->getConfig('datetime.zone');
        if (isset($time_zone['datetime.zone']) && $time_zone['datetime.zone'] != '')
            date_default_timezone_set($time_zone['datetime.zone']);
    }

    function set($key = null, $value = 0) {
        $this->execute_query('UPDATE `settings` SET `value` = ' . $value . ' WHERE `key` = \'' . $key . '\'');
    }

    function get($key = null) {
        $condition = null;
        if (!empty($key)) {
            $condition = ' `key` = \'' . $key . '\'';
        }

        return $this->query()->select('key', 'value')->where($condition)->map('key', 'value');
    }

    function setConfig($key = null, $value = 0) {
        $this->table = 'configs';
        $this->execute_query('UPDATE `configs` SET `value` = \'' . $value . '\' WHERE `key` = \'' . $key . '\'');
        $this->table = 'settings';
    }

    function getConfig($key = null) {
        $this->table = 'configs';
        $condition = null;
        if (!empty($key)) {
            $condition = ' `setting`.`key` = \'' . $key . '\'';
        }

        $result = $this->findList($condition, array('key', 'value'));
        $this->table = 'settings';
        return $result;
    }

}

?>