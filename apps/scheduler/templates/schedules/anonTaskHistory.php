<?php $this->inherits('layouts/default.php') ?>

<?php $this->block('heading'); ?>
    <h1>Task History :: <?php echo $job['Job']['job_name']?></h1>
<?php $this->endblock('heading'); ?>

    <?php $this->block('content'); ?>
    <?php if(isset($major_schedule_error)){?>
        <h4 style="color: red; font-size: 14px; margin: 0 auto"><?php echo $major_schedule_error;?></h4>
    <?php }?>
<div class="index" style='margin-top:4px;'>
    <table id="problems">
        <thead>
            <tr>
                <th width="15%" class="tc">Job Name</th>
                <th width="12%" class="tc">Job Type</th>
                <th width="16%" class="tc">Start Date Time</th>
                <th width="16%" class="tc">End Date</th>
                <th width="11%" class="tc">Status</th>
                
                <th width="30%" class="tc">Action</th>
            </tr>
        </thead>
        
        <tfoot>
            <tr>
                <td colspan="2"><?php // echo $html->link('Back to List',array('action'=>'task-list')) ?></td>
                <td colspan="4"><?php // echo $this->Pagination->renderPages($pagedata) ?></td>
            </tr>
        </tfoot>

        <tbody>
            <?php 

            foreach ($pagedata as $key=>$row): 
                if($row['JobHistory']['status'] == \scheduler\ProcessStatus::$Processing){
                    $process_running = $bp->check_process(array('job_id'=>$row['Job']['id'], 'job_history_id'=>$row['JobHistory']['id']));
                    $process_running = true;//temporary
                }else{
                    $process_running = false;
                }
            ?>
            <tr>
                <td class="tc"><?php echo $row['Job']['job_name']; ?></td>
                <td class="tc"><?php echo $row['Job']['job_type']; ?></td>
                <td class="tc"><?php echo $row['JobHistory']['start_date_time']; ?></td>
                <td class="tc"><?php echo $row['JobHistory']['end_date_time']; ?></td>
                <td class="tc"><?php echo ($row['JobHistory']['status'] == \scheduler\ProcessStatus::$Processing && !$process_running)? 'Process Stopped' : \scheduler\ProcessStatus::getStatusString($row['JobHistory']['status']); ?></td>
                <td class="tc">
                    <?php echo ($row['JobHistory']['status'] == \scheduler\ProcessStatus::$Processing && $process_running)? ($this->a('index.php/scheduler/schedules/stoptask/'.$row['JobHistory']['id'], 'stop-task')) . '&nbsp;|&nbsp;': ''; ?>
                    <?php //echo $html->link('Return Text',array(), array('alt'=>$row['JobHistory']['return_text'], 'class'=>'return_text')) ?>
                    <a class="return_text" alt="<?php echo ($row['JobHistory']['status'] == \scheduler\ProcessStatus::$Processing && !$process_running)? 'Process is Stopped' : $row['JobHistory']['return_text']?>" href="javascript:void(0);">Return Text</a>&nbsp;|&nbsp;
                    <?php echo ($this->a('index.php/scheduler/schedules/DeleteTaskHistory/' . $row['Job']['id'].'/'.$row['JobHistory']['id'], 'Delete',array("status"=>$row['JobHistory']['status'],"class"=>"delete","val"=>"id = {$row['JobHistory']['id']}, \nStart Date Time = {$row['JobHistory']['start_date_time']}")));
                    ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
</div>

<script type="text/javascript">
    $('.return_text').live('click', function(e){
        e.preventDefault();
        alert($(this).attr('alt'));
    });
    
    $('.delete').click(function(){        
        v = $(this).attr('val');
        if(window.confirm("Are you sure?"))
        {
            if( $(this).attr("status") == '<?php echo (int)\scheduler\ProcessStatus::$Processing; ?>'){
                alert("Please stop the process first");
                return false;
            }

            return true;
        }
        else{
            return false;
        }
    });
    
    $(document).ready(function(){
        
    });
</script>
  <?php $this->endblock('content'); ?>