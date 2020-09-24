<div class="frequency weekly" style=" width: 100%; margin: 0; padding: 0;<?php if(!isset($data['Schedule']['schedule_type']) || $data['Schedule']['schedule_type']!="weekly"){ echo "display:none";}?>">
<div class="sc_left">
    <p>Recurs Every :</p>
</div>
<div class="sc_right">
        <div style="float: left; width:40%">
    <?php echo $this->select('Weekly.recurs_in',false,array(1=>1,2=>2,3=>3,4=>4,5=>5),array("style"=>"")); ?>
    </div>
 
    <div style="float: left;">week(s)</div>
    <div style="float: left; clear: both">On these days :</div>
    <br />
    <div style="width: 100%; clear: both">

        <div style="float: left; width: 20px;">sat</div>
        <div style="float: left; width: 30px;">    
        <?php echo $this->check('Weekly.sat',false,array("value"=>"sat")); ?>
        </div> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    
    
        <div style="float: left;width: 20px;">sun</div>
        <div style="float: left;width: 30px;">    
        <?php echo $this->check('Weekly.sun',false,array("value"=>"sun")); ?>
        </div>
        
        <div style="float: left;width: 20px;">mon</div>
        <div style="float: left;width: 30px;">    
        <?php echo $this->check('Weekly.mon',false,array("value"=>"mon")); ?>
        </div>
        
        <div style="float: left;width: 20px;">tue</div>
        <div style="float: left;width: 30px;">    
        <?php echo $this->check('Weekly.tue',false,array("value"=>"tue")); ?>
        </div>
        
        <div style="float: left;width: 20px; clear: both">wed</div>
        <div style="float: left;width: 30px;">    
        <?php echo $this->check('Weekly.wed',false,array("value"=>"wed")); ?>
        </div>
        
        <div style="float: left;width: 20px;">thu</div>
        <div style="float: left;width: 30px;">    
        <?php echo $this->check('Weekly.thu',false,array("value"=>"thu")); ?>
        </div>
        
        <div style="float: left;width: 20px;">fri</div>
        <div style="float: left;width: 30px;">    
        <?php echo $this->check('Weekly.fri',false,array("value"=>"fri")); ?>
        </div>

    </div>
</div>
    
</div>
                
                