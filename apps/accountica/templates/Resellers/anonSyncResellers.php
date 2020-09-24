<?php $this->inherits('layouts/default.php') ?>

<?php $this->block('heading'); ?>
    <h1>List of Resellers</h1>
<?php $this->endblock('heading'); ?>

<?php $this->block('content'); ?>
<?php //pr($rows);             ?>
<?php echo $this->form('Reseller');?>
    
<div class="searchform top clearfix" >
        <div class="fields">
            <?php echo $this->select('status', 'Status', array(''=>'select')+$syncResellerStatus) ?>
        </div>
    <?php echo $this->button('submit', 'Search');?>
</div>
<?php echo $this->endform(); ?>    
<div class="index">
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Server </th>
                <th>Login </th>
                <th>Password </th>
                <th>Level</th>
                <th>parent</th>
                <th>Balance</th>
                <th>Status</th>
                                
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="7">&nbsp;</td>
                <td>
                 <?php   echo $this->button('submit', 'Accept', array('onclick'=>'swap_resellers()')); ?>
                </td>
            </tr>
          </tfoot>
        <tbody>
            <?php $count = 0; foreach ($rows as $row): ?>
            <tr>
                <td>
                    <?php
                    if($row['SyncReseller']['status']==0){
                    echo $this->check('Reseller.select.', false, array('value' => $row['SyncReseller']['id'],'class'=>'multi_checkbox', 'id' => 'User_select_' . $row['SyncReseller']['id']));
                    }else{
                    echo $this->check('Reseller.select.', false, array('disabled'=>'disabled','value' => $row['SyncReseller']['id'],'class'=>'multi_checkbox', 'id' => 'User_select_' . $row['SyncReseller']['id']));    
                    }
                    ?>
                    <?php $this->e(++$count) ?>
                </td>
                <td><?php $this->e($ipNameList[$row['SyncReseller']['server_id']]) ?></td>
                <td><?php $this->e($row['SyncReseller']['login']) ?></td>
                <td><?php $this->e(/*$row['SyncReseller']['password']*/"*********") ?></td>
                <td><?php $this->e($row['SyncReseller']['level']) ?></td>
                <td><?php $this->e($row['SyncReseller']['idReseller']) ?></td>
                <td><?php $this->e('0.00') ?></td> 
                <td id="status_<?php echo $row['SyncReseller']['id'];?>">
                    <?php $this->e($syncResellerStatus[$row['SyncReseller']['status']]) ?>
                </td>
            </tr>
            <?php endforeach ?>
        </tbody>

    </table>
</div>
    
<script type="text/javascript">
    /****** GET Multiple Value From checkbox to insert data to resellers *******/    
    function swap_resellers(){
        var val = [];
        $(':checkbox:checked').each(function(i){
          val[i] = $(this).val();
        });
        //alert(val);
        var _method = 'POST';
        var _url = '<?php echo $url; ?>';
        var _queryStr = {resellers_id:val};
        $.ajax({
            type:_method,
            url:_url,
            data:_queryStr,
            success:function (msg) {
                //alert(val);
                $(':checkbox:checked').each(function(i){                    
                    $('#User_select_'+$(this).val()).attr("disabled",'disabled');
                    $('#User_select_'+$(this).val()).removeAttr("checked");
                    $("#status_"+$(this).val()).text("Accepted");
                  });                               
                alert(msg);
            }
        });
    };
    
</script>    
<?php $this->endblock('content'); ?>