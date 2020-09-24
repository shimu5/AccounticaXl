<div class="frequency monthly" style=" width: 100%; margin: 0; padding: 0;<?php if(!isset($data['Schedule']['schedule_type']) || $data['Schedule']['schedule_type']!="monthly"){ echo "display:none";}?>">
<div class="sc_left">
    <p>&nbsp;</p>
</div>
<div class="sc_right day">
    <div style="float: left;">

        <input type="radio" name="data[MonthWeekDay][radio]" class="MonthWeekDay"  value="day" <?php if(isset($data['MonthWeekDay']['radio'])&& $data['MonthWeekDay']['radio']=="day"){echo "checked='checked'"; $day="1";}else{$day="disabled";}?>  />
    Day&nbsp;&nbsp;
    </div>
    
    <div style="float: left;">
    <?php echo $this->select('MonthDay.day_number',false,array(1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10,11=>11,12=>12,13=>13,14=>14,15=>15,16=>16,17=>17,18=>18,19=>19,20=>20,21=>21,22=>22,23=>23,24=>24,25=>25,26=>26,27=>27,28=>28),array("style"=>"",$day=>$day)); ?>
    </div>
 
    <div style="float: left;">&nbsp;&nbsp;of every&nbsp;&nbsp;</div>
    
    <div style="float: left;">
    <?php echo $this->select('MonthDay.recurs_in',false,array(1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10,11=>11,12=>12),array("style"=>"",$day=>$day)); ?>
    </div>
 
    <div style="float: left;">&nbsp;&nbsp;month(s)&nbsp;&nbsp;</div>

</div>
<div class="clear spacing"></div>

<div class="sc_left">
    <p>&nbsp;</p>
</div>
<div class="sc_right week">
    <div style="float: left;">
        <input type="radio" name="data[MonthWeekDay][radio]" class="MonthWeekDay"  value="week" <?php if(isset($data['MonthWeekDay']['radio'])&& $data['MonthWeekDay']['radio']=="week"){echo "checked=checked"; $week="1";}else{$week="disabled";}?> />
    The&nbsp;&nbsp;
    </div>

    <div style="float: left;">
    <?php echo $this->select('MonthWeek.week_number',false,array(1=>"1st",2=>"2nd",3=>"3rd",4=>"4th"),array("style"=>"", $week=>$week)); ?>
        &nbsp;&nbsp;
    </div>
    <div style="float: left; margin-left: 5px;">
    <?php echo $this->select('MonthWeek.day',false,array("sat"=>"sat","sun"=>"sun","mon"=>"mon","tue"=>"tue","wed"=>"wed","thu"=>"thu","fri"=>"fri"),array("style"=>"", $week=>$week)); ?>
        
    </div>
    <div style="float: left;">&nbsp;&nbsp;day of every&nbsp;&nbsp;</div>

    <div style="float: left;">
    <?php echo $this->select('MonthWeek.recurs_in',false,array(1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10,11=>11,12=>12),array("style"=>"", $week=>$week)); ?>
    </div>
    <div style="float: left;">&nbsp;&nbsp;month(s)&nbsp;&nbsp;</div>
    
</div>

</div>