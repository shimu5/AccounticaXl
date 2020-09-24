<?php
/**
 * Caution :: Do all cmds by using exec, otherwise PID id will not found by getPid()
 * 
 */
namespace scheduler\components;
use Furina\core\Component;
use scheduler\models;

class BProcessor extends Component {
    
    var $proc; 
    private $processes = array();
    var $asynchronous = true; //false = synchronous[depend on another process], true = asynchronous[not depend on another process]
    var $project_name;
    
    public function __construct(){
        require(dirname(dirname(__FILE__)).'/libs/ProcessStatus.php');
        $temp_project = dirname(dirname(dirname(dirname(__FILE__))));
        //$this->project_name = array_pop(explode('\\', $temp));    //only project name like, "project_name"        
        $this->project_name = str_replace(':', '__', str_replace('\\', '_', str_replace('/', '_', trim($temp_project))));     //with path like "Drive__dir_projectname"
    }
    
    public function __destruct(){
        
    }
    
    // Starting point
    function start(){
        $this->init_queue();
    }
    
    //Stop
    function stop(){
        $JobHistory = new JobHistory();
        $Job = new Job();
        $Job->bindModel(
            array(
                'hasOne' => array(
                    'JobHistory' => array(
                        'foreignKey' => 'schedule_id',
                        'localKey' => 'schedule_id',
                    )
                )
            )
        );
        $job_history_data = $Job->query()->where('JobHistory.status = %d', array(\scheduler\ProcessStatus::$Force_Stop_Request))->all();
        if(!empty($job_history_data)){
            foreach($job_history_data as $task){
                if (stristr(PHP_OS, 'WIN')) {
                    //$command = 'start /b '.$cmd.'  --cmdtag='.$this->project_name.'_j'.(isset($proc_data['job_id'])? $proc_data['job_id']:'').'_h'.(isset($proc_data['job_history_id'])? $proc_data['job_history_id']:'').' --cmdtype='.$this->project_name.'_'.(isset($proc_data['command_type'])? $proc_data['command_type']:'').' --get="'.$get.'"';
                                    
                }else{
                    //this shell file is in libs folder, use it
                    
                    /*Actual ShellCode is here
                     * 
                     *  #!/bin/bash
                        clear
                        kill $(ps aux | grep '[p]hp' | grep -r $1 | grep -r $2 | grep -v grep | awk '{print $2}') 
                     * 
                     * 
                     */
                    $command = '/var/www/html/test.sh '.$this->project_name.'_j'.(isset($task['Job']['id'])? $task['Job']['id']:'').'_h'.(isset($task['JobHistory']['id'])? $task['JobHistory']['id']:''). ' '. $this->project_name.'_'.'minor';
                    $this->set_process_status(array(array('job_history_id' => $task['JobHistory']['id'])), \scheduler\ProcessStatus::$Force_Stop, \scheduler\ProcessStatus::getStatusString(\scheduler\ProcessStatus::$Force_Stop));
                }
        
                if($command){
                    //to do proc open
                    //pclose(popen($command , "r"));
                    $path = dirname(dirname(__FILE__));
                    $logDir = $path . DIRECTORY_SEPARATOR."logs" ;
                        if(!file_exists($logDir))
                            mkdir($logDir, 0755);

                    $errorlog = $logDir. DIRECTORY_SEPARATOR ."log.txt";       
                    $descriptorspec = array(
                        0 => array("pipe","r"),
                        1 => array("pipe","w"),
                        2 => array("pipe","w")
                    ) ;

                    $process = proc_open($command, $descriptorspec, $pipes, $path) ;

                    if (is_resource($process)) {
                        $errorTxt = stream_get_contents($pipes[2]);
                        fclose($pipes[0]);
                        fclose($pipes[1]);
                        fclose($pipes[2]);
                        $return_value = proc_close($process);
                        if(strlen($errorTxt)>0){
                        //$this->set_process_status(array(array('job_history_id' => $task['JobHistory']['id'])), \scheduler\ProcessStatus::$Error, $errorTxt);
                            $fp = fopen( $errorlog, 'a');
                            fwrite($fp, date("Y-m-d H:i:s")."   ".str_replace("\n"," ",str_replace("\r\n"," ",$errorTxt))."\n");
                            fclose($fp);
                        }
                    }                   
                }               
            }
        }
    }
    
    // initialize queue from db
    function init_queue(){
        while(1){
            $this->processes = $this->get_process_queue(0);
            $this->process_queue();
        }
    }
    
    public function fetch(& $result) {
        $return = array();
        $alias = null;
        if ($result) while ( ($row = $result->fetchRow()) ) {
            if (! $alias) {
                foreach ($row as $field => $value) {
                    $field = explode('.', $field, 2);

                    if (count($field) == 2)
                        $alias[] = array('model' => $field[0], 'field' => $field[1]);
                    else $alias[] = array('model' => &$this->name, 'field' => $field[0]);
                }
                $count = count($alias);
            }

            $i = 0;
            $aliasRow = array();

            foreach ($row as $field => $value) {
                $aliasRow[$alias[$i]['field']] = $value;
                $i ++;
            }
            $return[] = $aliasRow;
        }
        $result->free();
        return $return;
    }
        
    // get processess from db
    function get_process_queue($limit = 0, $is_priority = false){
        $processes = array();      
        $time=date("Y-m-d H:i:00");
        // To do [limit = 0(unlimited), is_priority = false(no_priority counts)]        
        $Q = "select job.id as job_id, job_name as job_name, command_text, command_type, schedule_id from bp_job as job where job.is_deleted=0";       
        if($limit > 0){
            $Q.=" limit {$limit}";
        }
      
        $Job = new \scheduler\models\Job();
        $procsQ = $Job->execute($Q);
        $procs = $this->fetch($procsQ);
        
        if(count($procs)){
            if(is_array($procs)){
                if(!isset($procs['0'])){
                    $procs = array($procs);
                }else{
                    $procs = $procs;
                }
            }
            foreach($procs as $k=>$v){
                
                //$Q = "select id as job_history_id, start_date_time from bp_job_history where schedule_id={$v['schedule_id']} and status = ". \scheduler\ProcessStatus::$Waiting." and start_date_time='{$time}' order by start_date_time asc limit 1";               
                $Q = "select id as job_history_id, start_date_time from bp_job_history where schedule_id={$v['schedule_id']} and status = ". \scheduler\ProcessStatus::$Waiting." order by start_date_time asc limit 1";
                
                $v1Q = $Job->execute($Q);
                $v1 = $this->fetch($v1Q);
                
                if(is_array($v1)){
                    if(count($v1)){
                        $processes[]= array_merge($v,$v1['0']) ;
                    }
                }
            }
        }
        else{
            return array();
        }
        
        return $processes;
    }
    
    // change process status [0 = waiting, 1 = in queue, 2 = processing, 3 = done, 4 =errr] in db. if "proocessing" then set the PID in db and remove after "done"
    function set_process_status1($processes = array(), $status = 0){
        $JobHistory = new JobHistory();
        if(!empty($processes)){
            $temp = array();
            foreach($processes as $vals){
                $temp[] = $vals['job_history_id'];
            }
            if(!empty($temp)){
                $procs_in_queue = implode(',', $temp);
                // To Do :: Do query to set all in queue
                $qry = 'update job_history set `status` = '. $status. ' where id IN ('. $procs_in_queue . ')';
                $JobHistory->execute($qry);
            }
        }
    }
    
    function set_process_status($processes = array(), $status = 0, $return_text=""){       
        if(!empty($processes)){
            foreach($processes as $vals)
            {                
                $this->update_job_history($vals['job_history_id'], $status, $return_text);               
                if($status != \scheduler\ProcessStatus::$Waiting && $status != \scheduler\ProcessStatus::$On_Queue)
                {
                   $this->update_job_last_status($vals['job_history_id'], $status);
                }       
            }
        }
    }
    
    //processing status
    function update_job_last_status($job_history_id = 0, $status = 0){
        $data1 = array();            
        //unset($this->Job);
        $Job = new \scheduler\models\Job();
        $Job->bindModel(
            array(
                'hasOne' => array(
                    '\scheduler\models\JobHistory' => array(
                        'foreignKey' => 'schedule_id',
                        'localKey' => 'schedule_id',
                    )
                )
            )
        );
        
        if($status == \scheduler\ProcessStatus::$Processing)
        {
            $data1['Job']['last_runtime']=date("Y-m-d H:i:s");
        }

        $job_data = $Job->query()->where('JobHistory.id = %d', array($job_history_id))->one();

        if(!empty($job_data)){
            $data1['Job']['id']=$job_data['Job']['id'];
            $data1['Job']['last_status']=$status;
            $Job->save($data1,array("validate_with"=>false));
        }                    
    }
    
    function update_job_history($job_history_id = 0, $status = 0, $return_text=""){
        $data1=array();
        $JobHistory = new \scheduler\models\JobHistory();
        
        $data1['JobHistory']['id'] = $job_history_id;
        $data1['JobHistory']['status'] = $status;
        
        if($return_text!==""){
            $data1['JobHistory']['return_text'] = $return_text;  
        }
        
        try{
            $JobHistory->save($data1,array("validate_with"=>false));
        }
        catch(\Furina\mvc\model\exceptions\InvalidException $e){
            return false;
        }
        
        return true; 
    }
    
    //queue processer
    function process_queue(){
        if(!empty($this->processes)){
            $this->set_process_status($this->processes, \scheduler\ProcessStatus::$On_Queue);
            for($proc = 0; $proc < count($this->processes); $proc++){
                $process = $this->processes[$proc];
                while(isset($process) && !empty($process)){
                    while(1){
                        if(!$this->is_any_process_running()){   //Only if any process not running, execept asynchronous
                            try{
                                if($this->run_process($process)){
                                    
                                    //TO DO :: Set this status from the process function
                                    //
                                    //
                                    //                                    
                                    unset($process);//loop breaking point
                                }
                            }catch(\Furina\mvc\model\exceptions\InvalidException $e){
                                $this->set_process_status(array($process), \scheduler\ProcessStatus::$Error, 'Error in Processing the Command'); //error in processing
                                unset($process);//loop breaking point
                                file_put_contents(dirname(dirname(__FILE__)).'/logs/log.txt', $e->getMessage());
                            }
                            break;
                        }else{
                            $this->wait(3);
                        }
                    }
                }
            }
//            $this->init_queue();
        }else{
            $this->wait(1); //require to give 60 secs
//            die('died after a turn');
//            $this->init_queue();
        }
        return true;
    }
    
    // halt for a while
    function wait($seconds = 1){
        sleep($seconds);
    }
    
    // find if there is any process running previously
    function is_any_process_running(){
        if(!$this->asynchronous){
            $opts = array(
                'cmd_type'=>$this->project_name.'_minor',
                'name'=>'php.exe',
            );
            $return = $this->process_check($opts);
            if(!empty($return)){
                return 1;
            }
            return 0;//no
        }
        return 0;//asynchronous
    }
    
    function process_check($process_opts = array()) {

        $output = array();
        if (stristr(PHP_OS, 'WIN')) {
            /*
             Fix the Invalid XSL format (or) file name? Error ::

             If you use the /format switch you might encounter this error. This happens if your system�s locale setting differs from the language the operating system was
             originally installed in. Unfortunately the /locale switch does not seem to work so you have to manually fix the problem:-

             In Windows Explorer go to: %WINDIR%\System32\wbem\en-US (or your corresponding locale folder)
             Search for: *.xsl
             You should find 5 files; select and copy these
             Go to: %WINDIR%\System32\wbem\
             Create a new folder for your locale (i.e. �de-DE?? for Germany)
             Go into the new folder and paste the files.

             If not  solve in this way, 
             actually we need csv.xsl to get command resutl as csv. The comd "exec()" in default find this file in Appache directory.
             So we should copy the file from %WINDIR%\System32\wbem\en-US and paste it in place where exec can find it. 
            */

            $csv_opt = (defined('WIN_RECOMM_CSV'))?WIN_RECOMM_CSV:'csv';
//            $cmd = "WMIC process Where \"CommandLine Like '{$process_script_name_prefix}%' AND name='{$php_command_name}'\" get /FORMAT:".$csv_opt." ";//new
//            $cmd = "WMIC process where \"CommandLine LIKE '%Project_name_jobid_schhistory_id%' AND name= 'php.exe'\" get ProcessId /FORMAT:".$csv_opt." ";//new
            $options = array(
                'cmd_tags'=>"CommandLine LIKE '%%%s%%'",
                'cmd_type'=>"CommandLine LIKE '%%%s%%'",
                'name'=>"name = '%s'"
            );
            
            $cmd_options = array();
            if(!empty($process_opts)){
                foreach($process_opts as $tag=>$opt){
                    $cmd_options[] = sprintf($options[$tag], $opt);
                }
                $cmd_args = (count($cmd_options) > 1)?implode(' AND ', $cmd_options):$cmd_options['0'];
                $cmd_args = str_replace("\\", "\\\\", $cmd_args);
//                $cmd = 'WMIC process where "CommandLine LIKE \'%bproc_j%\' AND CommandLine LIKE \'%bproc_minor%\' AND NOT CommandLine LIKE \'%WMIC%\' AND name= \'php.exe\'" get ProcessId /FORMAT:'.$csv_opt.' ';//new
                $cmd = 'WMIC process where "Not Name LIKE \'%cmd.exe%\' AND NOT CommandLine LIKE \'%WMIC%\''.(($cmd_args!='')? ' AND '.$cmd_args : '').'" get ProcessId /FORMAT:'.$csv_opt.' ';//new
                exec($cmd, $output);
            }
        }
        else{
            
//            $cmd = 'ps -Ao pid,command | grep python | grep  "python server.py -i '.$server_id.'\$" | grep -v grep | sed "s/^[ ]*//" | cut -d\  -f1';
//            $proc_ls = proc_open($cmd,
//              array(
//                array("pipe","r"), //stdin
//                array("pipe","w"), //stdout
//                array("pipe","w")  //stderr
//              ),
//              $pipes);
//            $output_pid = stream_get_contents($pipes[1]);
//            fclose($pipes[0]);
//            fclose($pipes[1]);
//            fclose($pipes[2]);
//            $return_value_ls = proc_close($proc_ls);
//            echo $output_pid;
   
          
              $options = array(
                'cmd_tags'=>"%s",
                'cmd_type'=>"%s"
            );
            
            $cmd_options = array();
            if(!empty($process_opts)){
                foreach($process_opts as $tag=>$opt){
                    if(isset($options[$tag]) && $options[$tag] != ''){
                        $cmd_options[$tag] = sprintf($options[$tag], $opt);
                    }
                }
                $cmd = 'ps -Ao pid,command | grep php';
                if(isset($cmd_options['cmd_tags']) && $cmd_options['cmd_tags']!='') {
                    $cmd .= ' | grep -r \''.$cmd_options['cmd_tags'].'\'';
                }
                if(isset($cmd_options['cmd_type']) && $cmd_options['cmd_type']!=''){
                    $cmd .= ' | grep -r \''.$cmd_options['cmd_type'].'\'';
                }
                
                $cmd .= " | grep -v grep | sed 's/^[ ]*//' | cut -d' ' -f1";

	         $cmd1 = 'su - root -c "'.$cmd.'"';
echo $cmd12 = 'sudo '.$cmd.'';

                //echo $cmd1 ; 
                exec($cmd, $output);

/*
$proc_ls = proc_open($cmd12,
              array(
                array("pipe","r"), //stdin
                array("pipe","w"), //stdout
                array("pipe","w")  //stderr
              ),
              $pipes);
$output = stream_get_contents($pipes[0]);
print_r($output);
$output = stream_get_contents($pipes[1]);
print_r($output);
$output = stream_get_contents($pipes[2]);
print_r($output);
            fclose($pipes[0]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            $return_value_ls = proc_close($proc_ls);
            //echo $output_pid;
//print_r($return_value_ls);

print_r($output);  */

          }
            
        }
            
        /***start***/
        $count = 0;
        $processArray = array();
        $key = array();
        $key_count = 0;

        if(!empty($output)){
            foreach ($output as $process) {
                 $count++;
                 if (empty($process)) continue;
                 $params = explode(',',$process);

                 if ($count == 2) {// first row returns the column name
                     $key = $params;
                     $key_count = count($key);
                     continue;
                 }

                 for ($i = 0;$i<$key_count;$i++) {
                     $processArray[$key[$i]] = $params[$i];
                 }

                 if (!empty($params[0])) return $processArray;
            }
        }
        /**end, if not get the output array as windows then make this code diff for win/linux**/
        
        return array();
    }
    
    // run the process
    function run_process($proc_data = array()){
        $this->set_process_status(array($proc_data), \scheduler\ProcessStatus::$Processing);
        //Add Environmental Variable for PHP Path or Add Path in here
        //$get = addslashes(serialize(array('job_history_id'=>(isset($proc_data['job_history_id'])? $proc_data['job_history_id']:'0'))));
        $get = isset($proc_data['job_history_id'])? 'job_history_id='.$proc_data['job_history_id']: 'job_history_id=0'; //check cmd_bootsrap.php for get set style
        
        $cmd = ($proc_data['command_text']!='')?$proc_data['command_text']:'';
        
        if (stristr(PHP_OS, 'WIN')) {
            //$command = 'start /b '.$cmd.'  --cmdtag='.$this->project_name.'_j'.(isset($proc_data['job_id'])? $proc_data['job_id']:'').'_h'.(isset($proc_data['job_history_id'])? $proc_data['job_history_id']:'').' --cmdtype='.$this->project_name.'_'.(isset($proc_data['command_type'])? $proc_data['command_type']:'').' --get="'.$get.'"';
            $command = 'start /b '.$cmd.'*'.$get.'  --cmdtag='.$this->project_name.'_j'.(isset($proc_data['job_id'])? $proc_data['job_id']:'').'_h'.(isset($proc_data['job_history_id'])? $proc_data['job_history_id']:'').' --cmdtype='.$this->project_name.'_'.(isset($proc_data['command_type'])? $proc_data['command_type']:'');
        }else{
            //$command = $cmd.'  --cmdtag='.$this->project_name.'_j'.(isset($proc_data['job_id'])? $proc_data['job_id']:'').'_h'.(isset($proc_data['job_history_id'])? $proc_data['job_history_id']:'').' --cmdtype='.$this->project_name.'_'.(isset($proc_data['command_type'])? $proc_data['command_type']:'').' --get="'.$get.'"' . ' /dev/null &';       
            $command = $cmd.'*'.$get.'  --cmdtag='.$this->project_name.'_j'.(isset($proc_data['job_id'])? $proc_data['job_id']:'').'_h'.(isset($proc_data['job_history_id'])? $proc_data['job_history_id']:'').' --cmdtype='.$this->project_name.'_'.(isset($proc_data['command_type'])? $proc_data['command_type']:''). ' /dev/null &';                   
        }
        
        //pclose(popen($command , "r"));
        $errorlog = $path . DIRECTORY_SEPARATOR ."error_{$proc_data['schedule_id']}_{$proc_data['job_history_id']}.log";       
        $descriptorspec = array(
            0 => array("pipe","r"),
            1 => array("pipe","w"),
            2 => array("file",$errorlog,"a")
        ) ;

        $process = proc_open($command, $descriptorspec, $pipes, $path) ;

        if (is_resource($process)) {
            $return_value = proc_close($process);
            if($return_value!=0){
                $errorTxt = file_get_contents($errorlog);
                $this->set_process_status(array(array('job_history_id' => $proc_data['job_history_id'])), \scheduler\ProcessStatus::Error, $errorTxt);
            }
        }

        return 1;
    }

    // kill the process;
    function kill_process($process_data = array()) {
        if(!empty($process_data)){
            $opts = array(
                'cmd_tags'=>$this->project_name.'_j'.(isset($process_data['job_id'])? $process_data['job_id']:'').'_h'.(isset($process_data['job_history_id'])? $process_data['job_history_id']:''),     
                'cmd_type'=>$this->project_name.'_minor',
                'name'=>'php.exe',
            );
            $process = $this->process_check($opts);
        }
        if ($process === null || empty($process)) {
            return false;
        }

        if (stristr(PHP_OS, 'WIN')) {
            $pid = $process['ProcessId'];
            exec("WMIC process Where ProcessId=".$pid.' call terminate',$output);
            return true;
            
        }else {
            $pid = $process['ProcessId'];
            exec("KILL -15 ".$pid, $output);
            return true;
        }
        
        return false;
    }
    
    // check running process; parameters[an array with job_id, job_history_id]
    function check_process($process_data = array()) {
        if(!empty($process_data)){
            $opts = array(
                'cmd_tags'=>$this->project_name.'_j'.(isset($process_data['job_id'])? $process_data['job_id']:'').'_h'.(isset($process_data['job_history_id'])? $process_data['job_history_id']:''),     
                'cmd_type'=>$this->project_name.'_minor',
                'name'=>'php.exe',
            );
            $process = $this->process_check($opts);
        }
        if ($process === null || empty($process)) {
            return false;
        }else{
            return true;
        }
        return false;
    }
    
    function add_task_in_process_queue($schedule_id = 0, $currentDate = '') {
        if ($currentDate == '') {
            $currentDate = date("Y-m-d");
        }

        $Schedule = new \scheduler\models\Schedule();
        $Job = new \scheduler\models\Job();
        $JobHistory = new \scheduler\models\JobHistory();
        $ScheduleTime = new \scheduler\models\ScheduleTime();        
        $Schedule->bindModel(
                array(
                    'hasOne' => array(
                        '\scheduler\models\Job' => array(
                            'foreignKey' => 'schedule_id',
                            'localKey' => 'id',
                        ),
                        '\scheduler\models\ScheduleDaily' => array(
                            'foreignKey' => 'schedule_id',
                            'localKey' => 'id',
                        ),
                        '\scheduler\models\ScheduleWeekly' => array(
                            'foreignKey' => 'schedule_id',
                            'localKey' => 'id',
                        ),
                        '\scheduler\models\ScheduleMonthly' => array(
                            'foreignKey' => 'schedule_id',
                            'localKey' => 'id',
                        )
                    )
                )
        );

        $ScheduleData = $Schedule->query()->where("Job.is_deleted=0 and Job.next_run_in_day='{$currentDate}'");

        if ($schedule_id > 0) {
            $ScheduleData = $ScheduleData->where("Job.schedule_id=" . (int) $schedule_id);
        }

        $ScheduleData = $ScheduleData->all();
        
        $week_days_name = array("sat" => "saturday", "sun" => "sunday", "mon" => "monday", "tue" => "tuesday", "wed" => "wednesday", "thu" => "thursday", "fri" => "friday");

        if(!empty($ScheduleData))
        foreach ($ScheduleData as $key => $val) {
            $nextDate = "";
            $start_date = array();

            $schedule_start_date = strtotime($val['Schedule']['start_date']);

            if (trim($val['Schedule']['end_date']) == "") {
                $schedule_end_date = strtotime(date("Y-m-d") . " + 15 months");
            } 
            else {
                $schedule_end_date = strtotime($val['Schedule']['end_date'] . " +1 day");
            }

            if ($val['Job']['job_type'] == "once") {
                $start_date[] = $currentDate;
            } 
            else if ($val['Schedule']['schedule_type'] == "daily") {
                $start_date[] = $currentDate;
                $nextDate = date("Y-m-d",strtotime($currentDate . " + {$val['ScheduleDaily']['recurs_in']} day"));
            } 
            else if ($val['Schedule']['schedule_type'] == "weekly") {
                $week_days = unserialize($val['ScheduleWeekly']['week_days']);
                $nextDate = date("Y-m-d",strtotime($currentDate . " + {$val['ScheduleWeekly']['recurs_in']} weeks"));
                if(count($week_days)){
                    $fromdate = date("Y-m-d", strtotime($currentDate . " -1 day"));
                    foreach ($week_days as $day) {
                        if (isset($week_days_name[$day])) {
                             $start_date[] = date('Y-m-d', strtotime("{$fromdate} next {$week_days_name[$day]}"));
                        }
                    }
                }
            }             
            else if($val['Schedule']['schedule_type'] == "monthly") {

                $month_days = unserialize($val['ScheduleMonthly']['month_days']);
                if (count($month_days) == 1 && isset($month_days['day_number'])) {
                    if ($month_days['day_number']>0 && $month_days['day_number'] <=28) 
                    {
                        if ($month_days['day_number'] == date('d', strtotime($currentDate))) 
                        {
                            $start_date[] = $currentDate;
                            $nextDate = $this->nextMonthDay($currentDate,$month_days['day_number'],$val['ScheduleMonthly']['recurs_in']);       
                        } 
                        else {
                            $nextDate = $this->nextMonthDay(date("Y-01-{$month_days['day_number']}", strtotime($currentDate)),$month_days['day_number']);                            
                        }
                    }
                } 
                else {

                    if (isset($month_days['week_number']) && isset($month_days['day']) && isset($week_days_name[$month_days['day']]) && $month_days['week_number']>0 && $month_days['week_number']<=4) 
                    {
                        $month_days['week_number']-=1;
                        
                        $pd = date("Y-m-1", strtotime("{$currentDate}"));
                        $pd = date("Y-m-d", strtotime("{$pd} -1 day"));

                        $pd1 = date("Y-m-d", strtotime("{$pd} +{$month_days['week_number']} weeks"));
                        $wd = date('Y-m-d', strtotime("{$pd1} next {$week_days_name[$month_days['day']]}"));

                        if(strtotime($wd)==strtotime($currentDate)){
                            $start_date[] = $wd;
                        }

                        if(strtotime($wd)>strtotime($currentDate) && date("Y-m",strtotime($wd))==date("Y-m",strtotime($currentDate)) ){
                            $nextDate = $wd;
                        }
                        else {

                            $i=$val['ScheduleMonthly']['recurs_in'];                               
                            do{
                                $cd = date("Y-m-1", strtotime("{$currentDate}"));
                                $cd = date("Y-m-1", strtotime("{$pd} +{$i} months"));
                                $pd = date("Y-m-d", strtotime("{$cd} -1 day"));
                                $pd1 = date("Y-m-d", strtotime("{$pd} +{$month_days['week_number']} weeks"));
                                $wd = date('Y-m-d', strtotime("{$pd1} next {$week_days_name[$month_days['day']]}"));

                                if(strtotime($wd)>strtotime($currentDate) && date("Y-m",strtotime($wd))==date("Y-m",strtotime($cd)) ){
                                    $nextDate = $wd;
                                    $cd=false;
                                    break;
                                }

                                $i++;
                              }while($cd!==false && $i<=100);
                        }
                   }                       
                }
            }
            if (!empty($start_date)) {

                $JobHistoryData = array();
                $JobHistoryData['JobHistory']['schedule_id'] = $val['Schedule']['id'];
                $JobHistoryData['JobHistory']['status'] = \scheduler\ProcessStatus::$Waiting;
                $ScheduleTimeData = $ScheduleTime->query()->where("schedule_id='{$val['Schedule']['id']}'")->all();

                foreach ($start_date as $k1 => $v1) {
                    if(strtotime($v1) >= $schedule_start_date && strtotime($v1) < $schedule_end_date) {
                        foreach ($ScheduleTimeData as $k => $v) {
                            $datatime = $v1 . " " . floor($v['ScheduleTime']['time'] / 60) . ":" . ($v['ScheduleTime']['time'] % 60) . ":00";
                            $JobHistoryData['JobHistory']['start_date_time'] = $datatime;
                            try {
                                $JobHistory->save($JobHistoryData, array("validate_with" => false));
                            } catch (\Furina\mvc\model\exceptions\InvalidException $e) {

                            }
                        }
                    }
                }
            }

            if( trim($nextDate)!="" && strtotime($nextDate) < $schedule_end_date ){
               
                $job['Job']['id']=$val['Job']['id'];
                $job['Job']['next_run_in_day']=$nextDate;
                $Job->save($job,array("validate_with"=>false));
            }
        }       
    }
    
    public function fn_next_run_in_day($schedule_id = 0, $currentDate = '') {
        return true;

        if ($currentDate == '') {
            $currentDate = date("Y-m-d");
        }
        
        $Job = new \scheduler\models\Job();
        $ScheduleModel = \scheduler\models\Schedule();

        $ScheduleModel->bindModel(
                array(
                    'hasOne' => array(
                        'Job' => array(
                            'foreignKey' => 'schedule_id',
                            'localKey' => 'id',
                        ),
                        'ScheduleDaily' => array(
                            'foreignKey' => 'schedule_id',
                            'localKey' => 'id',
                        ),
                        'ScheduleWeekly' => array(
                            'foreignKey' => 'schedule_id',
                            'localKey' => 'id',
                        ),
                        'ScheduleMonthly' => array(
                            'foreignKey' => 'schedule_id',
                            'localKey' => 'id',
                        )
                    )
                )
        );

        $ScheduleData = $ScheduleModel->query()->where("Job.is_deleted=0 and Schedule.id=" . (int) $schedule_id)->one();

        try {

            if (!empty($ScheduleData)) {
                if ($ScheduleData['Job']['job_type'] == "once") {
                    
                } 
                else {
                    $currentDate = strtotime($currentDate);
                    $start_date = strtotime($ScheduleData['Schedule']['start_date']);

                    if (trim($ScheduleData['Schedule']['end_date']) == "") {
                        $end_date = strtotime(date("Y-m-d") . " + 15 months");
                    } 
                    else {
                        $end_date = strtotime($ScheduleData['Schedule']['end_date']);
                    }

                    if ($ScheduleData['Schedule']['schedule_type'] == "daily") {
                        $nextDate = strtotime($ScheduleData['Job']['next_run_in_day'] . " + {$ScheduleData['ScheduleDaily']['recurs_in']} day");
                    } 
                    else if ($ScheduleData['Schedule']['schedule_type'] == "weekly") {

                        $nextDate = strtotime($ScheduleData['Job']['next_run_in_day'] . " + {$ScheduleData['ScheduleWeekly']['recurs_in']} weeks");
                    } 
                    else if ($ScheduleData['Schedule']['schedule_type'] == "monthly") {
                        $nextDate = strtotime($ScheduleData['Job']['next_run_in_day'] . " + {$ScheduleData['ScheduleMonthly']['recurs_in']} months");
                    }

                    if ($start_date <= $nextDate && $end_date >= $nextDate) {
                        $ScheduleData['Job']['next_run_in_day'] = date('Y-m-d', $nextDate);
                        $Job->save($ScheduleData);
                    }
                }
            }

            return true;
        } catch (\Furina\mvc\model\exceptions\InvalidException $e) {
            return false;
        }
    }

    public function nextMonthDay($datestr = "", $day,$i=1) {

        for(;$i<=100;$i++){
            $d = date("Y-m-d", strtotime("{$datestr} +{$i} month"));

            if (date("d", strtotime($d)) == $day && strtotime($d) > strtotime(date("Y-m-d"))) {
                return $d;
                break;
            }
        }

        return date("Y-m-d", strtotime("{$datestr} +1 month"));
    }
    
    function temp(){       
//        pr(\scheduler\ProcessStatus::$Force_Stop_Request);
        pr($this->get_process_queue());
//        die('died in temp, comp');
//        $data = array(
//            'cmd'=>'php D:\webroot\bproc\cmd.php -c=scheduler.scheduler -a=test3 --cmdtag=bproc_j2_h3 --cmdtype=bproc_minor'
//        );
//        $this->proc->run_process($data);
        
//        $data = array(
//                'cmd'=>'php D:\webroot\bproc\cmd.php -c=scheduler.scheduler -a=test3 --cmdtag=bproc_j3_h5 --cmdtype=bproc_minor'
//            );
//        $this->proc->run_process($data);
        
//$a = 'sudo ps -Ao pid,command | grep "php"';
//$a = 'kill -9 `pgrep php`';
        
        /*
$a = 'sudo sh ./test.sh';
$proc_ls = proc_open($a,
              array(
                array("pipe","r"), //stdin
                array("pipe","w"), //stdout
                array("pipe","w")  //stderr
              ),
              $pipes);
$output = stream_get_contents($pipes[0]);
print_r($output);
$output = stream_get_contents($pipes[1]);
print_r($output);
$output = stream_get_contents($pipes[2]);
print_r($output);
            fclose($pipes[0]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            $return_value_ls = proc_close($proc_ls);
            //echo $output_pid;
print_r($return_value_ls);die;
        */
        
        return 1;
    }
}
