<?php $this->inherits('layouts/default.php') ?>

<?php $this->block('heading'); ?>
    <h1>List of Gateways</h1>
<?php $this->endblock('heading'); ?>

<?php $this->block('content'); ?>
<?php //pr($rows);             ?>
<?php echo $this->form('Gateway');?>
    
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
                <th>Server</th>
                <th>description</th>
                <th>ip_number</th>
                <th>ip_port</th>
                <th>type</th>
                <th>call_limit</th>
                <th>Status</th>                                
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="7">&nbsp;</td>
                <td>
                 <?php   echo $this->button('submit', 'Accepted', array('onclick'=>'swap_gateways()')); ?>
                </td>
            </tr>
          </tfoot>
        <tbody>
            <?php $count = 0; foreach ($rows as $row): ?>
            <tr>
                <td>
                    <?php
                    if($row['SyncGateway']['status']==0){
                    echo $this->check('Gateway.select.', false, array('value' => $row['SyncGateway']['id'],'class'=>'multi_checkbox', 'id' => 'User_select_' . $row['SyncGateway']['id']));
                    }else{
                    echo $this->check('Gateway.select.', false, array('disabled'=>'disabled','value' => $row['SyncGateway']['id'],'class'=>'multi_checkbox', 'id' => 'User_select_' . $row['SyncGateway']['id']));    
                    }
                    ?>
                    <?php $this->e(++$count) ?>
                </td>
                <td><?php $this->e($row['SyncGateway']['server_id']) ?></td>
                <td><?php $this->e($row['SyncGateway']['description']) ?></td>
                <td><?php $this->e($row['SyncGateway']['ip_number']) ?></td>
                <td><?php $this->e($row['SyncGateway']['ip_port']) ?></td>
                <td><?php $this->e($row['SyncGateway']['type']) ?></td>
                <td><?php $this->e($row['SyncGateway']['call_limit']) ?></td> 
                <td id="status_<?php echo $row['SyncGateway']['id'];?>">
                    <?php $this->e($syncResellerStatus[$row['SyncGateway']['status']]) ?>
                </td>
            </tr>
            <?php endforeach ?>
        </tbody>

    </table>
</div>
    
<script type="text/javascript">
    /****** GET Multiple Value From checkbox to insert data to resellers *******/    
    function swap_gateways(){
        var val = [];
        $(':checkbox:checked').each(function(i){
          val[i] = $(this).val();
        });
        //alert(val);
        var _method = 'POST';
        var _url = '<?php echo $url; ?>';
        var _queryStr = {gateways_id:val};
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