<div class="frequency daily" style="<?php if(isset($data['Schedule']['schedule_type']) && ($data['Schedule']['schedule_type']=="weekly" || $data['Schedule']['schedule_type']=="monthly")){ echo "display:none";}?>" >
<div class="sc_left" style="" >
    <p>Recurs Every :</p>
</div>
<div class="sc_right">
    <div style="float: left; width:40%">
    <?php echo $this->select('Daily.recurs_in',false,array(1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10,11=>11,12=>12,13=>13,14=>14,15=>15,16=>16,17=>17,18=>18,19=>19,20=>20,21=>21,22=>22,23=>23,24=>24,25=>25,26=>26,27=>27,28=>28,29=>29,30=>30,31=>31),array("style"=>"")); ?>
    </div>
 
    <div style="float: left;">day(s)</div>
</div>
</div>