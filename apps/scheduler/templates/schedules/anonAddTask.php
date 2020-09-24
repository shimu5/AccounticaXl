<?php $this->inherits('layouts/default.php') ?>

<?php $this->block('heading'); ?>
    <h1>Task List</h1>
<?php $this->endblock('heading'); ?>

<?php $this->block('content'); ?>
    <?php if(isset($major_schedule_error)){?>
        <h4 style="color: red; font-size: 14px; margin: 0 auto"><?php echo $major_schedule_error;?></h4>
    <?php }?>
    <link rel="stylesheet" type="text/css" href="/accounticaxl/apps/scheduler/media/css/reset.css" /> 
    <link rel="stylesheet" type="text/css" href="/accounticaxl/apps/scheduler/media/css/base.css" /> 
    <link rel="stylesheet" type="text/css" href="/accounticaxl/apps/scheduler/media/css/zform.css" />   
    <link rel="stylesheet" type="text/css" href="/accounticaxl/apps/scheduler/media/css/toolbar.css" />   
    <link rel="stylesheet" type="text/css" href="/accounticaxl/apps/scheduler/media/css/table.css" />    
    <link rel="stylesheet" type="text/css" href="/accounticaxl/apps/scheduler/media/css/furina.css" />    
    <link rel="stylesheet" type="text/css" href="/accounticaxl/apps/scheduler/media/css/mainmenu.css" />   
    <link rel="stylesheet" type="text/css" href="/accounticaxl/apps/scheduler/media/mainmenu.simple.css" />	
    <link rel="stylesheet" type="text/css" href="/accounticaxl/apps/scheduler/media/css/icon.css" />	
    <link rel="stylesheet" type="text/css" href="/accounticaxl/apps/scheduler/media/css/fleximan.css" />
    <link rel="stylesheet" type="text/css" href="/accounticaxl/apps/scheduler/media/css/js/tools.dateinput-1.2.7.css" />    
    <link rel="stylesheet" type="text/css" href="/accounticaxl/apps/scheduler/media/css/jquery-ui.css" />   
    <link rel="stylesheet" type="text/css" href="/accounticaxl/apps/scheduler/media/css/jquery-ui-timepicker-addon.css" />

    <script type="text/javascript" src="/accounticaxl/apps/scheduler/media/js/jquery-1.7.1.min.js" ></script>        
    <script type="text/javascript" src="/accounticaxl/apps/scheduler/media/js/jquery-ui.min.js" ></script>                
    <script type="text/javascript" src="/accounticaxl/apps/scheduler/media/js/tools.dateinput-1.2.7.js" ></script>   
    <script type="text/javascript" src="/accounticaxl/apps/scheduler/media/js/zs.js" ></script>   
    <script type="text/javascript" src="/accounticaxl/apps/scheduler/media/js/jquery.bgiframe.js" ></script>    
    <script type="text/javascript" src="/accounticaxl/apps/scheduler/media/js/jquery.dimensions.js" ></script> 
    <script type="text/javascript" src="/accounticaxl/apps/scheduler/media/js/jquery.jdMenu.js" ></script>  
    <script type="text/javascript" src="/accounticaxl/apps/scheduler/media/js/jquery.positionBy.js" ></script>  
    <script type="text/javascript" src="/accounticaxl/apps/scheduler/media/js/jquery.form.js" ></script>    
    <script type="text/javascript" src="/accounticaxl/apps/scheduler/media/js/jquery-ui-timepicker-addon.js" ></script>
    <style type="text/css">
        #scheduler_main{
            /*width: 30%;*/
            width: 500px;
            margin: 10px auto;
            border: 0.1em #94BAE5 solid;
            display: block;
            background-color: lavender;
            font-size: 10px;
            font-weight: bold;
            font-family: sans-serif;
        }
        .scheduler_sub{
            width: 94%;
            margin: 1% 0em 0em 0em;
            display: block;
            padding: 2% 2% 0% 2%;
        }
        .scheduler_sub2{
            width: 94%;
            margin: 1% auto;
            display: block;
            padding: 2% 2% 0% 2%;
            border: 0.1em solid #999;
            background-color: #BAD8F7;
            border-radius: 6px;
        }
        .sc_left{
            float: left;
            width: 25%;
            display: inline-block;
            padding: 0px;
            margin: 0px;
        }
        .sc_right{
            float: left;
            width: 74%;
            padding: 0px;
            margin: 0px;
            margin-left: 1%;
            display: inline-block;
        }
        p{
            font-size: 12px;
            font-family: sans-serif;
            margin: 0px;
            padding: 3px 0px;
            color: #B01B1B;
            font-weight: bold;
        }
        .scheduler_sub2 p{
            color: #B01B1B;
            font-weight: bold;
            font-family: sans-serif;
        }
            
        .scheduler_sub ul{
            list-style-type: none; 
            width: 100%;
        }
        .scheduler_sub li{
            display: inline;
        }
        .long_input{
            width: 100%;
        }
        .medium_input{
            width: 50%;
        }
        .small_input{
                
        }
        select{
            padding: 2px;
            width: 80%;
        }
        .clear{
            clear: both;
        }
        .spacing{
            padding: 5px;
        }
        .div_hide{
            background-color: silver;
            display: none;
        }
        .grp_checkbox{
            display: inline-block;
            margin-left: 3%;
        }
            
        .frequency select{
            width: 100%;
        }
    </style>
    <?php if(isset($major_schedule_error)){?>
        <h4 style="color: red; font-size: 14px; margin: 0 auto"><?php echo $major_schedule_error;?></h4>
    <?php }?>
    <div class="index" style='margin-top:4px;'>
         <?php echo $this->form('Schedule');?>
        <div id="scheduler_main">
            <div class="scheduler_sub">
                <div class="sc_left">
                    <p>Job Name :</p>
                </div>
                <div class="sc_right">
                    <?php echo $this->hidden('id'); ?>
                    <?php echo $this->text('Job.job_name',false,array("class"=>"long_input")) ?>

                </div>
                <div class="clear spacing"></div>
                
                <div class="sc_left">
                    <p>Job Type :</p>
                </div>
                <div class="sc_right">
                    
                    <?php echo $this->select('Job.job_type',false,array("once"=>"Once","recurring"=>"Recurring"),array("style"=>"width:100%;")) ?>
                    
                </div>
                <div class="clear spacing"></div>
                
                <div class="sc_left">
                    <p>Enabled :</p>
                </div>
                <div class="sc_right">
                    
                    <?php echo $this->check('Job.is_deleted',false,array("value"=>"0")) ?>

                </div>
                <div class="clear"></div>
                
            </div>
            
            <div class="clear"></div>
            <div class="scheduler_sub2 once" >
                <p style="font-size: 11px;color: #fff;font-weight: bold;">One time occurance</p>
                <div class="clear spacing"></div>
                
                <div class="sc_left">
                    <p>Date :</p>
                </div>
                <div class="sc_right">
                    <?php echo $this->text('start_date',false,array("class"=>"date","style"=>"width:40%;","id"=>"Once_start_date")) ?>
<!--                    <input type="text" name="date1" style="width: 40%" class="date" />-->
                </div>
                <div class="clear spacing"></div>
                
                <div class="sc_left">
                    <p>Time :</p>
                </div>
                <div class="sc_right">
                    
                    <?php echo $this->text('start_time',false,array("class"=>"time","style"=>"width:40%;","id"=>"Once_start_time")) ?>

                </div>
                <div class="clear spacing"></div>
                
            </div>
            
            <div class="clear"></div>
            <div class="scheduler_sub2 recurring">
                <p style="font-size: 11px;color: #fff;font-weight: bold;">Frequency</p>
                <div class="clear spacing"></div>
                
                <div class="sc_left">
                    <p>Occurs :</p>
                </div>
                <div class="sc_right">
                    
                    <?php echo $this->select('schedule_type',false,array("daily"=>"Daily","weekly"=>"Weekly","monthly"=>"Monthly")); ?>
                    
                </div>
                <div class="clear spacing"></div>
                
                <div id="frequency">
                    <?php 
                    
                        include(dirname(__FILE__).'/weekly_frequency.php');
                        include(dirname(__FILE__).'/daily_frequency.php');
                        include(dirname(__FILE__).'/monthly_frequency.php');
                    ?>
                </div>
                
                <div class="clear spacing"></div>
                
            </div>                      
            
            <div class="clear"></div>
            <div class="scheduler_sub2 recurring">
                <p style="font-size: 11px;color: #fff;font-weight: bold;">Daily Frequency</p>
                <div class="clear spacing"></div>
                
                <div class="sc_left" style="width: 45%;">
                    
                        <div style="float: left;">
                           <input type="radio" name="data[Occurs][start_time]" value="once" class="Occurs_start_time"  <?php if(isset($data['Occurs']['start_time'])&& $data['Occurs']['start_time']=="once"){echo "checked='checked'"; $Occurs="1";}else{$Occurs="disabled";}?> />
                        </div>

                        <div style="float: left;">&nbsp;&nbsp;Occurs once at&nbsp;&nbsp;</div>
                    <div style="float: left;">
                        <?php echo $this->text('Occurs_once.start_time',false,array("style"=>"width: 80px;margin-left: 2%;","class"=>"time Occurs_once")); ?>
                        </div>

                </div>
                <div class="sc_right" style="width: 54%;">                    
                                     
                </div>
                
                <div class="clear spacing"></div>
                
                <div class="sc_left" style="width: 55%;">
                     <div style="float: left;">
                            <input type="radio" name="data[Occurs][start_time]" value="every" class="Occurs_start_time"  <?php if(isset($data['Occurs']['start_time'])&& $data['Occurs']['start_time']=="every"){echo "checked='checked'"; $Occurs="1";}else{$Occurs="disabled";}?> />
                        </div>

                        <div style="float: left;">&nbsp;&nbsp;Occurs every&nbsp;&nbsp;</div>

                    <div class='hour hour_minite' style="width:45px;float: left;<?php if(isset($data['Occurs_every']['repeat_in_type'])&& $data['Occurs_every']['repeat_in_type']=="minite"){echo "display:none;";}?>">
                        <?php echo $this->select('Occurs_every.repeat_in_num_hour',false,array(1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10,11=>11,12=>12,13=>13,14=>14,15=>15,16=>16,17=>17,18=>18,19=>19,20=>20,21=>21,22=>22,23=>23,24=>24),array("style"=>"width:44px;", "class"=>"Occurs_every")); ?>
                        &nbsp;
                    </div>
                        
                    <div class='minite hour_minite' style="width:45px;float: left;<?php if(!isset($data['Occurs_every']['repeat_in_type'])|| $data['Occurs_every']['repeat_in_type']!="minite"){echo "display:none;";}?>">
                        <?php echo $this->select('Occurs_every.repeat_in_num_minite',false,array(1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10,11=>11,12=>12,13=>13,14=>14,15=>15,16=>16,17=>17,18=>18,19=>19,20=>20,21=>21,22=>22,23=>23,24=>24,25=>25,26=>26,27=>27,28=>28,29=>29,30=>30,31=>31,32=>32,33=>33,34=>34,35=>35,36=>36,37=>37,38=>38,39=>39,40=>40,41=>41,42=>42,43=>43,44=>44,45=>45,46=>46,47=>47,48=>48,49=>49,50=>50,51=>51,52=>52,53=>53,54=>54,55=>55,56=>56,57=>57,58=>58,59=>59),array("style"=>"width:44px;", "class"=>"Occurs_every")); ?>&nbsp;
                    </div>
                    <div style="float: left;">
                        <?php echo $this->select('Occurs_every.repeat_in_type',false,array("hour"=>"Hour","minite"=>"Minite"),array("style"=>"","class"=>"Occurs_every")); ?>
                    </div>

                </div>
                <div class="sc_right" style="width: 44%;float: left;">                    
                    <div style="padding: 3%;">
                        <div style="float: left;">&nbsp;&nbsp;Starting at :&nbsp;&nbsp;</div> 
                        <div style="float: left; width:70px">
                        <?php echo $this->text('Occurs_every.start_time',false,array("style"=>"width: 70px;margin-left: 2%;","class"=>"time Occurs_every")); ?>
                        </div>
                    
                    </div>
                    <div style="padding: 3%; clear: both">
                        <div style="float: left;  width:70px">&nbsp;&nbsp;Ending at :&nbsp;&nbsp;</div> 
                        <div style="float: left;">
                        <?php echo $this->text('Occurs_every.end_time',false,array("style"=>"width: 70px;margin-left: 2%;","class"=>"time Occurs_every")); ?>
                        </div>
                    
                    </div>                        
                </div>
                <div class="clear spacing"></div>
                
            </div>
            
            <div class="clear"></div>
            <div class="scheduler_sub2 recurring">
                <p style="font-size: 11px;color: #fff;font-weight: bold;">Duration</p>
                <div class="clear spacing"></div>
                
                <div class="sc_left" style="width: 45%;">                    

                    
                    <div style="float: left;">&nbsp;&nbsp;Start Date :&nbsp;&nbsp;</div> 
                        <div style="float: left; width: 30%">
                        <?php echo $this->text('Recurring.start_date',false,array("style"=>"width:80px;","class"=>"date")); ?>
                        </div>
                    
                    
                </div>
                <div class="sc_right" style="width: 54%;float: left;">                    
                    <div style="float: left;">
                        <input type="radio" name="data[Recurring][end_date_radio]" value="Y" <?php if(isset($data['Recurring']['end_date_radio'])&& $data['Recurring']['end_date_radio']=="Y"){echo "checked=checked";}?> />
                        &nbsp;&nbsp;End Date :&nbsp;&nbsp;
                    </div> 
                        <div style="float: left;">
                        <?php echo $this->text('Recurring.end_date',false,array("style"=>"width:80px;","class"=>"date")); ?>
                        </div>

                        <div style="float: left; clear: both">
                        <input type="radio" name="data[Recurring][end_date_radio]" value="N" <?php if(!isset($data['Recurring']['end_date_radio'])|| $data['Recurring']['end_date_radio']!="Y"){echo "checked=checked";}?> />
                        &nbsp;&nbsp;No End Date
                    </div>
                                           
                </div>
                <div class="clear spacing"></div>
                
            </div>
            <div class="clear"></div>
            <div class="scheduler_sub2">
                
                <div style="float: left;">Command Type&nbsp;&nbsp;</div>

                    <div style="float: left;">
                        <?php echo $this->select('Job.command_type',false,array("minor"=>"Minor","major"=>"Major"),array("style"=>"width:100px;")); ?>
                        &nbsp;
                    </div>
                
                
                <div style="width: 100%; clear: both">
                    Command Text
                    <?php echo $this->textarea('Job.command_text',false,array("style"=>"width: 98%;margin: 1% auto;border-radius: 5;")) ?>
                </div>
                <div class="clear spacing"></div>
                <div style="width:30%;display: inline-block;float: right;">
                    <?php echo $this->button('submit',"OK",array("style"=>"width: 50px;","id"=>"submit")) ?>
                    
                    <input type="button" name="name" value="CANCEL" id="cancel_button" style="margin-left:2%;" onclick="javascript:location.href='<?php $this->e($cancel_url)?>'" />
                </div>
                <div class="clear spacing"></div>
            </div>
            
        </div>
    <?php echo $this->endform() ?>
 
<script type="text/javascript">
    $('.date').datepicker({
            dateFormat: 'yy-mm-dd'
    });
    $('.time').timepicker({
            timeFormat: 'HH:mm:ss'
    });
    
    $('#Schedule_schedule_type').live('change', function(){
                $(".frequency").hide();
        $("."+$('#Schedule_schedule_type').val()).show();
        $(".frequency").find('input').attr('disabled','disabled');
        $(".frequency").find('select').attr('disabled','disabled');
        $("."+$('#Schedule_schedule_type').val()).find('input').removeAttr('disabled');
        $("."+$('#Schedule_schedule_type').val()).find('select').removeAttr('disabled');
        change_month_week_day();
    });   
    
    change_job_type();
    $('#Job_job_type').live('change', change_job_type);
    function change_job_type(){
        job_type = $('#Job_job_type').val();
        if(job_type == 'once'){
            once_class = $('.once').attr('class');
            once_class = once_class.replace(' div_hide', '');
            $('.once').attr('class', once_class);
            $('.once input').removeAttr('disabled');
            
            $('.recurring').each(function(i){
                recurring_class = $(this).attr('class');
                $(this).attr('class', recurring_class + ' div_hide');
                $(this).find('input').attr('disabled','');
                $(this).find('select').attr('disabled','');
            });
        }else{
            once_class = $('.once').attr('class');
            $('.once').attr('class', once_class + ' div_hide');
            $('.once input').attr('disabled','');
            
            $('.recurring').each(function(i){
                recurring_class = $(this).attr('class');
                recurring_class = recurring_class.replace(' div_hide', '');
                $(this).attr('class', recurring_class);
                $(this).find('input').removeAttr('disabled');
                $(this).find('select').removeAttr('disabled');
            });
            
            $(".frequency").find('input').attr('disabled','disabled');
            $(".frequency").find('select').attr('disabled','disabled');
            $("."+$('#Schedule_schedule_type').val()).find('input').removeAttr('disabled');
            $("."+$('#Schedule_schedule_type').val()).find('select').removeAttr('disabled');
            DailyFrequencyChange();
            change_month_week_day();
            
            
        }
    }
    
    $('.MonthWeekDay').live('click', change_month_week_day);
    function change_month_week_day(){
        if($('#Schedule_schedule_type').val()=="monthly"){
            $(".day").find('select').attr('disabled','disabled');
            $(".week").find('select').attr('disabled','disabled');
            $("."+$(".MonthWeekDay:checked").val()).find('select').removeAttr('disabled');

        }
    }
    
    $(".Occurs_start_time").change(function(){
        DailyFrequencyChange();
    });
    function DailyFrequencyChange(){
        $(".Occurs_once").attr('disabled','disabled');
        $(".Occurs_every").attr('disabled','disabled');
        $(".Occurs_"+$(".Occurs_start_time:checked").val()).removeAttr('disabled');
    }
    
    $("#Occurs_every_repeat_in_type").change(function(){
        $(".hour_minite").hide();
        $("."+$('#Occurs_every_repeat_in_type').val()).show();
    });
       
    $(document).ready(function(){
        change_job_type();
    });
    
    <?php if(isset($data['Schedule']['id']) && $data['Schedule']['id']>0){ ?>
        $("#submit").click(function(){
            if(window.confirm("All pending schedule task will be deleted for this schedule. \n\nDo you want to continue?"))
            {
                return true;
            }
            else{
                return false;
            }
        });
    <?php }?>
</script>
<?php $this->endblock('content'); ?>