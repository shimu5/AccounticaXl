<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of process_controller
 *
 * @author RoniPHP
 */
namespace scheduler\controllers;
use Furina\mvc\controller\Response;

class Schedules extends CrudeController {
    public $model = 'Job';

    public function components() {
        return parent::components() + array('BProcessor');
    }
    
    public function anonTest($request, $response){
        sleep(60);
        file_put_contents($this->c->Router->root.'ronitest.txt', 'hallo world');
        $BP = new \scheduler\components\BProcessor();
        $BP->set_process_status(array(array('job_history_id'=>$request->get['job_history_id'])), \scheduler\ProcessStatus::$DONE, 'Done Successfully');
        die('halloo24 world');
    }
    
//    
//    public function cliStart($request, $response){
//        $this->Scheduler->start();die;
//    }
//    
//    //not working
//    public function cliStop($request, $response){
//        $this->Scheduler->stop();die;
//    }
//    
    public function anonStart($request, $response){
        $BP = new \scheduler\components\BProcessor();
        $BP->start();die;
    }
    
    public function anonStop($request, $response){
        $BP = new \scheduler\components\BProcessor();
        $BP->stop();die;
    }
    
    public function anonIndex($request, $response){
//	$command = 'php /var/www/html/cmd.php -c=scheduler.scheduler -a=stop';
//        pclose(popen($command , "r"));

//        $this->Scheduler->temp();
        $BP = new \scheduler\components\BProcessor();
//        $BP->temp();
//        die('c:done');
        echo \scheduler\ProcessStatus::$DONE;
    }

    
    //demo
    function cliTestProcess($request, $response){
        sleep(60);
        file_put_contents(dirname(dirname(__FILE__)).'/logs/tp.txt', 'Hallo World '. date('Y-m-d H:i:s') );
        $this->Scheduler->set_process_status(array(array('job_history_id'=>$request->get['job_history_id'])), ProcessStatus::$DONE, 'Done Successfully');
        die();
    }
    
    //demo
    function adminDbBackups($request, $response){
        $dir = "/tmp";
        $dh  = opendir($dir);
        while (false !== ($filename = readdir($dh))) {
            $files[] = $filename;
        }

        sort($files);
    }

    function cliDbDump($request, $response){
        //sleep(60);
        $cmd = 'D:\xampp\mysql\bin\mysqldump -u root ngo_v2_bp > D:\webroot\bproc\backup\database\db_backup_'.date('Y_m_d_H_i_s').'.sql';
        exec($cmd, $output);
        $this->Scheduler->set_process_status(array(array('job_history_id'=>$request->get['job_history_id'])), ProcessStatus::$DONE, 'Done Successfully');
        die();
    }
    
    function anonAddQueue($request, $response){
        $this->__add_task_in_process_queue();
    }
    
    function cliAddQueue($request, $response){
        $this->__add_task_in_process_queue();die;
    }
    
    function __add_task_in_process_queue(){
        $bp = new \scheduler\components\BProcessor();
        $bp->add_task_in_process_queue();
    }
    
    public function adminGetProcessQueue($request, $response){
        $bp = new \scheduler\components\BProcessor();
        $bp->get_process_queue();      
    }
    
    public function anonTaskHistory($request, $response){
        $this->ChkMajorProcess($request, $response);
        list($job_id) = $request->params();
        $Job = new \scheduler\models\Job();
        $jobdata = $Job->read($job_id);
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

        $query = $Job->query()->where('Job.id = %d', array($job_id))->all();
//        $this->Pagination->limit = 15;
//        $pagedata = $this->Pagination->paginate($query);
        
        $response->set('pagedata', $query);
        $response->set('job', $jobdata);
        $response->set('stop_task_url', $this->c->Router->root . 'index.php/scheduler/schedules/stoptask');
    }
    
    private function ChkMajorProcess($request, $response) {
        $options = array(
            'cmd_tags' => "start_{$this->Scheduler->project_name}",
            'cmd_type' => "{$this->Scheduler->project_name}_major"
        );

        $bp = new \scheduler\components\BProcessor();
        $return = $bp->process_check($options);

        if (empty($return)) {
            $response->set("major_schedule_error", "Background Process is not running. Please contact to Administrator");
        }
        $response->set('bp', $bp);
    }
    
    public function anonDeleteTaskHistory($request, $response){
        
        list($job_id,$history_id) = $request->params(null,null);
        $bp = new \scheduler\components\BProcessor();
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

        $query = $Job->query()->where('Job.id = %d and JobHistory.id=%d', array($job_id,$history_id))->one();
        
        if(!empty($query)){
            if($query['JobHistory']['status'] == \scheduler\ProcessStatus::$Processing ){
//                $response->setFlash("Please stop the process first");
            }
            else{
                $this->m->JobHistory->delete($history_id);  
            }            
        }
        else{

        }
        
        $url = $this->c->Router->root . 'index.php/scheduler/schedules/tasklist';
        redirect($url);
        return true;

    }
    
    public function anonTaskList($request, $response){
        $this->ChkMajorProcess($request, $response);
        $Schedule = new \scheduler\models\Schedule();
        
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
        $query = $Schedule->query()->all();  
    
        $response->set('add_new_task_url', $this->c->Router->root . 'index.php/scheduler/schedules/addtask');
        $response->set('pagedata', $query);
    }
    
    //Add a task in schedule list
    public function anonAddTask($request, $response){
                
        list($schedule_id) = $request->params(0);
                
        $Schedule = new \scheduler\models\Schedule();
        
        if(!empty($request->data)){
            $response->data = $request->data;
            $response->set('data', $response->data);
            
            $command = "insert"; 
            try{
                
                mysql_query("SET AUTOCOMMIT=0 SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE");
                mysql_query("START TRANSACTION");
                $job = new \scheduler\models\Job();
                $scheduledaily =new \scheduler\models\ScheduleDaily();
                $scheduleweekly =new \scheduler\models\ScheduleWeekly();
                $schedulemonthly =new \scheduler\models\ScheduleMonthly();
                $scheduletime = new \scheduler\models\ScheduleTime();
                
                
                if( isset($request->data['Schedule']['id']) && (int)$request->data['Schedule']['id']<=0 )
                {
                    unset($request->data['Schedule']['id']);
                    unset($request->data['Job']['id']);
                }
                else if( isset($request->data['Schedule']['id'])){                   
                    $command = "update";
                    
                    $request->data['Schedule']['id'] = (int)$request->data['Schedule']['id'];                   
                    $Schedule->execute("
                        update {$Schedule->table} set
                        schedule_type='',    
                        start_date=null, end_date=null,
                        start_time=null, end_time=null,
                        repeat_in_type=null 
                        where id={$request->data['Schedule']['id']}
                        ");
                        
                    $scheduledaily->delete("schedule_id='{$request->data['Schedule']['id']}'");
                    $scheduleweekly->delete("schedule_id='{$request->data['Schedule']['id']}'");
                    $schedulemonthly->delete("schedule_id='{$request->data['Schedule']['id']}'");    
                    $scheduletime->delete("schedule_id='{$request->data['Schedule']['id']}'");
                    
                }
               
                $request->data['Schedule']['schedule_name'] = $request->data['Job']['job_name'];
                
                if(!isset($request->data['Job']['is_deleted'])){
                    $request->data['Job']['is_deleted']=1;
                }
                else{
                    $request->data['Job']['is_deleted']=0;
                }
                
                if(!isset($request->data['Job']['command_type']) || $request->data['Job']['command_type']!="major")
                {
                    $request->data['Job']['command_type']="minor";
                }
                
              
                if($request->data['Job']['job_type']=="once")
                {
                    if($Schedule->save($request->data,array('validate_with'=>false))){

                    }
                    else{
                        throw new \Exception("Schedule Not Saved");
                    }
                    
                    
                    $request->data['Job']['next_run_in_day'] = $request->data['Schedule']['start_date'];
                        
                    if($command == "insert"){
                        $request->data['Job']['schedule_id']= $Schedule->lastInsertId();
                        
                        $job->save($request->data,array('validate_with'=>false));
                    }
                    else{                        
                        $request->data['Job']['schedule_id']= $request->data['Schedule']['id'];                     

                        $job->update($request->data,array(),"schedule_id={$request->data['Job']['schedule_id']}",array('validate_with'=>false));
                    }
                    
                    
                    /******* Save Schedule Time Once **********/
                    $scheduleTime['ScheduleTime']['schedule_id']=$request->data['Job']['schedule_id'];
                    
                    if(!isset($request->data['Schedule']['start_time'])){
                        $scheduleTime['ScheduleTime']['time'] = 0;
                    }
                    else{

                        $starttime=explode(":",$request->data['Schedule']['start_time']);
                        $scheduleTime['ScheduleTime']['time']= (($starttime[0]%24)*60) + ($starttime[1]%60);
                        $scheduletime->save($scheduleTime,array('validate_with'=>false));
                    }                                       
                }
                else if($request->data['Job']['job_type']=="recurring")
                {
                    $starttime=array();

                        $request->data['Schedule']['start_date'] = $request->data['Recurring']['start_date'];
                        
                        if($request->data['Recurring']['end_date_radio']=="Y"){
                            $request->data['Schedule']['end_date'] = $request->data['Recurring']['end_date'];
                        }
                      
                        if(!isset($request->data['Occurs']['start_time'])){
                            throw new \Exception("Please select Daily Frequency");
                        }
                        
                        if($request->data['Occurs']['start_time']=="once"){
                            
                            /***** add schedule start time array ********/
                            $starttime[]=$request->data['Occurs_once']['start_time'];
                            
                            
                            $request->data['Schedule']['start_time'] = $request->data['Occurs_once']['start_time'];
                        }
                        else{
                            if(trim($request->data['Occurs_every']['start_time'])!="")
                            {
                                $request->data['Schedule']['start_time'] = $request->data['Occurs_every']['start_time'];
                                
                                /***** add schedule start time array ********/
                             //   $starttime[0]=explode(":",$request->data['Occurs_every']['start_time']);
                                $starttime[0] = $request->data['Occurs_every']['start_time'];
                            }
                            
                            if(trim($request->data['Occurs_every']['end_time'])!=""){
                                $request->data['Schedule']['end_time'] = $request->data['Occurs_every']['end_time'];
                                $endTime = strtotime($request->data['Occurs_every']['end_time']); 
                            }
                            else{
                                $endTime = strtotime("23:59:59");
                            }
                            
                            $repeat_in_type = $request->data['Occurs_every']['repeat_in_type'];
                            $request->data['Schedule']['repeat_in_type'] = $repeat_in_type;
                            $request->data['Schedule']["repeat_in_num"] = $request->data['Occurs_every']["repeat_in_num_{$repeat_in_type}"];
                            
                            $multiplier=60*60;
                            if($repeat_in_type=="minite")
                                {
                                    $multiplier=60;
                                }
                                
                            $i=1;
                            while(count($starttime) && $i<=1440){
                                $newtime = strtotime($starttime[$i-1]) + ($request->data['Schedule']["repeat_in_num"]*$multiplier);

                                if($newtime<=$endTime){
                                    $starttime[$i]=date("H:i:s",$newtime);
                                }
                                else{
                                    break;
                                }
                                $i++;
                            }
                        }

                        if($Schedule->save($request->data,array('validate_with'=>false))){

                        }
                        else{
                            throw new\Exception("Schedule Not Saved");
                        }


                           if(strtotime($request->data['Schedule']['start_date'])< strtotime(date("Y-m-d")))
                            {
                                $request->data['Job']['next_run_in_day'] = date("Y-m-d");
                            }
                            else
                            {
                                $request->data['Job']['next_run_in_day'] = $request->data['Schedule']['start_date'];
                            }
                        
                        
                        if($command == "insert"){

                            $request->data['Job']['schedule_id']= $Schedule->lastInsertId();
                            $job->save($request->data,array('validate_with'=>false));
                        }
                        else{

                            $request->data['Job']['schedule_id']= $request->data['Schedule']['id'];

                            $job->update($request->data,array(),"schedule_id={$request->data['Job']['schedule_id']}",array('validate_with'=>false));
                        }
                        
                        if(count($starttime)){                           
                            $scheduleTime['ScheduleTime']['schedule_id']=$request->data['Job']['schedule_id'];
                            foreach($starttime as $st){
                                $st=explode(":",$st);
                                $scheduleTime['ScheduleTime']['time']= (($st[0]%24)*60) + ($st[1]%60);
                                $scheduletime->save($scheduleTime,array('validate_with'=>false));
                            }
                        }
  
                        if($request->data['Schedule']['schedule_type']=="daily"){
                            $request->data['ScheduleDaily']['schedule_id'] = $request->data['Job']['schedule_id'];
                            $request->data['ScheduleDaily']['recurs_in'] = $request->data['Daily']['recurs_in'];
                            $scheduledaily->save($request->data,array('validate_with'=>false));
                        }else if($request->data['Schedule']['schedule_type']=="weekly"){
                            $request->data['ScheduleWeekly']['schedule_id'] = $request->data['Job']['schedule_id'];
                            $request->data['ScheduleWeekly']['recurs_in'] = $request->data['Weekly']['recurs_in'];
                            unset($request->data['Weekly']['recurs_in']);
                            $request->data['ScheduleWeekly']['week_days'] = serialize($request->data['Weekly']);

                            $scheduleweekly->save($request->data,array('validate_with'=>false));
                        }else if($request->data['Schedule']['schedule_type']=="monthly"){
                        
                            if(!isset($request->data['MonthWeekDay']['radio'])){
                                throw new \Exception("Please select Monthly Frequency");
                            }

                            if($request->data['MonthWeekDay']['radio']=="day" || $request->data['MonthWeekDay']['radio']=="week")
                            {
                                $request->data['ScheduleMonthly']['schedule_id'] = $request->data['Job']['schedule_id'];

                                if($request->data['MonthWeekDay']['radio']=="day"){
                                    $request->data['ScheduleMonthly']['recurs_in'] = $request->data['MonthDay']['recurs_in'];
                                    unset($request->data['MonthDay']['recurs_in']);
                                    $request->data['ScheduleMonthly']['month_days'] = serialize($request->data['MonthDay']);
                                }
                                else{
                                    $request->data['ScheduleMonthly']['recurs_in'] = $request->data['MonthWeek']['recurs_in'];
                                    unset($request->data['MonthWeek']['recurs_in']);
                                    $request->data['ScheduleMonthly']['month_days'] = serialize($request->data['MonthWeek']);
                                }
                                $request->data['ScheduleMonthly']['schedule_id'] = $request->data['Job']['schedule_id'];
                                $schedulemonthly->save($request->data,array('validate_with'=>false));
                            }
                        }else{
                            throw new \Exception("Invalid Occurs Type Selected");
                        }                  
                }
                else{
                    throw new \Exception("Invalid Job Type Selected");
                }
                
                $JobHistory = new \scheduler\models\JobHistory();
                $BP = new \scheduler\components\BProcessor();
                                 
                $JobHistory->delete("schedule_id={$request->data['Job']['schedule_id']} and `status`=".   \scheduler\ProcessStatus::$Waiting);
                
               $BP->add_task_in_process_queue($request->data['Job']['schedule_id']);    
     
                    
                mysql_query("COMMIT");

                           
                $url = $this->c->Router->root . 'index.php/scheduler/schedules/tasklist';
                redirect($url);
                return;
                

            }
            catch(\Furina\mvc\model\exceptions\InvalidException $e){
                $msg = "";
                if(is_array($e->errors)){
                    foreach ($e->errors as $k ){
                        foreach ($k as $k1=>$v ){
                            $msg = ucwords(str_replace("_", " ", $k1))." is ".$v;
                        }
                        
                    }
                }
                else{
                    $msg = $e->errors;
                }
                return;
            }
                        
        }
        else if($schedule_id>0){
            
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
            
            
            $ScheduleData = $Schedule->read($schedule_id);
            
            if(!empty($ScheduleData)){
                $response->data = $ScheduleData;
                
                if($ScheduleData['Job']['job_type']=="once"){
                    
                }
                else{
                    unset($response->data['Schedule']['start_date']);
                    unset($response->data['Schedule']['start_time']);
                    $response->data['Recurring']['start_date'] = $ScheduleData['Schedule']['start_date'];
                    
                    $response->data['Recurring']['end_date_radio']="N";
                    
                    if(trim($ScheduleData['Schedule']['end_date'])!="" && $ScheduleData['Schedule']['end_date']!="0000-00-00") {
                            $response->data['Recurring']['end_date'] = $ScheduleData['Schedule']['end_date'];
                            $response->data['Recurring']['end_date_radio']="Y";                       
                    }
                        
                    if($ScheduleData['Schedule']['repeat_in_type']=="minite" || $ScheduleData['Schedule']['repeat_in_type']=="hour"){
                        $response->data['Occurs_every']['repeat_in_type'] = $ScheduleData['Schedule']['repeat_in_type'];
                        $response->data['Occurs_every']["repeat_in_num_{$ScheduleData['Schedule']['repeat_in_type']}"] = $ScheduleData['Schedule']['repeat_in_num'];
                        $response->data['Occurs']['start_time']="every";
                        $response->data['Occurs_every']['start_time']=trim($ScheduleData['Schedule']['start_time']);
                        $response->data['Occurs_every']['end_time']=trim($ScheduleData['Schedule']['end_time']);                       
                    }
                    else{
                        $response->data['Occurs']['start_time']="once";
                        $response->data['Occurs_once']['start_time']=trim($ScheduleData['Schedule']['start_time']);

                    }

                    
                    if($ScheduleData['Schedule']['schedule_type']=="daily")
                    {
                        $response->data['Daily']['recurs_in'] = $ScheduleData['ScheduleDaily']['recurs_in'];
                    }                    
                    else if($ScheduleData['Schedule']['schedule_type']=="weekly") {
                         $response->data['Weekly'] = unserialize($ScheduleData['ScheduleWeekly']['week_days']);
                         $response->data['Weekly']['recurs_in'] = $ScheduleData['ScheduleWeekly']['recurs_in'];
                    }                    
                    else if($ScheduleData['Schedule']['schedule_type']=="monthly"){                       
                        $month_days = unserialize($ScheduleData['ScheduleMonthly']['month_days']);
                        if(count($month_days)>1){
                            $response->data['MonthWeekDay']['radio']="week";
                            $response->data['MonthWeek']=$month_days;
                            $response->data['MonthWeek']['recurs_in']=$ScheduleData['ScheduleMonthly']['recurs_in'];
                        }
                        else{
                            $response->data['MonthWeekDay']['radio']="day";
                            $response->data['MonthDay']=$month_days;
                            $response->data['MonthDay']['recurs_in']=$ScheduleData['ScheduleMonthly']['recurs_in'];
                        }                        
                    }                   
                }
                $response->set('data', $response->data);
                $response->set('cancel_url', $this->c->Router->root . 'index.php/scheduler/schedules/tasklist');
            }
            else{
                $url = $this->c->Router->root . 'index.php/scheduler/schedules/tasklist';
                redirect($url);
            }   
        }
        
    }
    
    public function anonDeleteTask($request, $response)
    {
        list($schedule_id) = $request->params(0);
        
        $msg="";
        $schedule_id = (int) $schedule_id;
        
        if($schedule_id > 0){
            mysql_query("SET AUTOCOMMIT=0 SESSION TRANSACTION ISOLATION LEVEL SERIALIZABLE");
            mysql_query("START TRANSACTION");
            
            try
            {
                
                    $msg = $this->deleteSchedule($schedule_id);
                    if($msg === true){
                        $msg = "Schedule id={$schedule_id} deleted";
                        mysql_query("COMMIT");
                    }
            }
            catch(\Furina\mvc\model\exceptions\InvalidException $e)
            {
                $msg = "Schedule id={$schedule_id} not deleted";
            }
            
            
        }

        
        $url = $this->c->Router->root . 'index.php/scheduler/schedules/tasklist';
        redirect($url);
        return;
        
    }
    
    
    private function deleteSchedule($schedule_id){
        $BP = new \scheduler\components\BProcessor();
        $schedule = new \scheduler\models\Schedule();
        
     
        $schedule->bindModel(
            array(
                'hasOne' => array(
                    'scheduler\models\JobHistory' => array(
                        'foreignKey' => 'schedule_id',
                        'localKey' => 'id',
                    )
                )
            )
        );

        $query = $schedule->query()->where("Schedule.id = %d and JobHistory.status = %d", array($schedule_id, \scheduler\ProcessStatus::$Processing))->one();
        
        if(empty($query)){
        
            $schedule->delete("id='{$schedule_id}'");
            $this->m->Job->delete("schedule_id='{$schedule_id}'");
            $this->m->JobHistory->delete("schedule_id='{$schedule_id}'");    
            $this->m->ScheduleDaily->delete("schedule_id='{$schedule_id}'");
            $this->m->ScheduleWeekly->delete("schedule_id='{$schedule_id}'");
            $this->m->ScheduleMonthly->delete("schedule_id='{$schedule_id}'");   
            $this->m->ScheduleTime->delete("schedule_id='{$schedule_id}'");

            return true;
        }
        else{
            return "Please stop all the running process first.";
        }
    }
    
    
    //Kill the process, 
    function anonStopTask($request, $response) {
        list($history_id) = $request->params();
        $Job = new \scheduler\models\Job();
        $Scheduler = new \scheduler\components\BProcessor();
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

        $job = $Job->query()->select(array('Job.id'=>'job_id', 'JobHistory.id'=>'job_history_id'))->where('JobHistory.id = %d', array($history_id))->one();
        
        if($Scheduler->kill_process($job['Job'])){
            $Scheduler->set_process_status(array(array('job_history_id'=>$history_id)), \scheduler\ProcessStatus::$Force_Stop, 'Forcefully Stopped');
            //$response->setFlash("Job is Stopped");
        }else{
            //$response->setFlash("Job cannot be stopped");
        }
        
        $url = $this->c->Router->root . 'index.php/scheduler/schedules/TaskHistory/'.$job['Job']['job_id'];
        redirect($url);
    }
    
    public function adminChangeScheduleType($request, $response){
        list($type) = $request->params();
        $response->setLayout('ajax')->setTemplate($type . '_frequency');
    }
}