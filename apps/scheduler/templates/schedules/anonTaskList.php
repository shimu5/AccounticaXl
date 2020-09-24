<?php $this->inherits('layouts/default.php') ?>

<?php $this->block('heading'); ?>
    <h1>Task List</h1>
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
                    <th width="10%" class="tc">Job Type</th>
                    <th width="15%" class="tc">Start Date</th>
                    <th width="15%" class="tc">Last Runtime</th>
                    <th width="10%" class="tc">Last Status</th>

                    <th width="7%" class="tc">Active</th>
                    <th width="28%" class="tc">Action</th>
                </tr>
            </thead>

            <tfoot>
                <tr>
                    <td colspan=""><input type="button" value="Add New Task" onclick="javascript:location.href='<?php echo $add_new_task_url?>'" /></td>
                    <td colspan=""><input type="button" value="Refresh Queue" onclick="javascript:refreshQueue();" /></td>
                    <td colspan="5"><?php //echo $this->Pagination->renderPages($pagedata) ?></td>
                </tr>
            </tfoot>

            <tbody>
                <?php foreach ($pagedata as $key=>$row):?>
                <tr>
                    <td class="tc"><?php $this->e($row['Job']['job_name']); ?></td>
                    <td class="tc"><?php $this->e($row['Job']['job_type']); ?></td>
                    <td class="tc"><?php $this->e($row['Schedule']['start_date'].' '.$row['Schedule']['start_time']); ?></td>
                    <td class="tc"><?php $this->e($row['Job']['last_runtime']); ?></td>
                    <td class="tc"><?php  echo \scheduler\ProcessStatus::getStatusString($row['Job']['last_status']); ?></td>
                    <td class="tc"><?php if($row['Job']['is_deleted']==1){ $this->e("No");}else{$this->e("Yes");} ?></td>
                    <td class="tc">
                        <?php echo ($this->a('index.php/scheduler/schedules/task-history/' . $row['Job']['id'], 'History')); ?>
                            &nbsp;|&nbsp;
                            <?php echo ($this->a('index.php/scheduler/schedules/AddTask/' . $row['Schedule']['id'], 'Edit')); ?>
                            &nbsp;|&nbsp;
                            <?php echo ($this->a('index.php/scheduler/schedules/DeleteTask/' . $row['Schedule']['id'], 'Delete', array('onclick' => 'return deleteit("' . $row['Job']['job_name'] . '")'))); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>

        </table>
    </div>


<script type="text/javascript">
    
    function deleteit(val){
       
        if(window.confirm("Do you want to delete the job '"+val+"'?"))
        {
            return true;
        }
       return false;
   }
  
   function refreshQueue(){
            $.ajax({
                url:'/accounticaxl/www/index.php/scheduler/schedules/AddQueue'  ,
                data:'',
                type:"post",
                success:function(data){
                    alert("Queue Refreshed");
                }
            });
   }

</script>

<?php $this->endblock('content'); ?>