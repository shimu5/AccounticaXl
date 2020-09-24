<?php $this->inherits('layouts/default.php') ?>

<?php $this->block('heading'); ?>
    <h1>Assign Gateways</h1>
<?php $this->endblock('heading'); ?>

<?php $this->block('content'); ?>
    
<?php echo $this->form('Vendor');?>
<div class="searchform top clearfix" >
        <div class="fields">
            <?php echo $this->select('server_id', 'Server', array(''=>'select')+$getServerNameList) ?>
            <?php echo $this->select('vendor_id', 'Vendor', array(''=>'All')+$getVendorNameList) ?>
        
            <?php echo $this->hidden('sort', array('value'=>'id', 'rel'=>'sort')); ?>
            <?php echo $this->hidden('sortd', array('value'=>'desc', 'rel'=>'sortd')); ?>
            <?php echo $this->hidden('page', array('rel'=>'page')); ?>
            <?php echo $this->hidden('limit', array('rel'=>'limit')); ?>
        </div>
    <?php echo $this->button('submit', 'Search');?>
</div>
<?php echo $this->endform(); ?>   
<div class="index">
    <table>
        <thead>
            <tr>
                <th>Server Name</th>
                <th>IP Number</th>
                <th>IP Port</th>
                <th>Rate</th>
                <th>Vendor</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            <?php $count = 0; foreach ($rows as $row): ?>
            <tr>
                <td><?php $this->e($row['Gateway']['server_id']) ?></td>
                <td><?php $this->e($row['Gateway']['ip_number']) ?></td>
                <td><?php $this->e($row['Gateway']['ip_port']) ?></td>
                <td>
                    
                    <?php 
                        $options = array('class'=>' w5','style'=>'width:100px;');                   
                        if($row['Gateway']['vendor_id'] > 0) $options = array_merge($options, array('disabled'=>'disabled', 'value'=>$row['Gateway']['rate']));                    
                        echo $this->text('rate'.'.'.$row['Gateway']['id'], '', $options); 
                    ?>
                </td>
                <td>
                    
                    <?php 
                        $options = array('class' => 'gateway','id'=>'gateway_'.$row['Gateway']['id'],'gateway'=>$row['Gateway']['vendor_id']);                   
                        if($row['Gateway']['vendor_id'] > 0) $options = array_merge($options, array('disabled'=>'disabled'));                    
                        echo $this->select('gateway'.'.'.$row['Gateway']['id'], '', array(''=>'select')+ $getVendorNameList, $options);          
                    ?>
                </td>
                <td>
        <?php 
        if($row['Gateway']['vendor_id']>0)
        echo $this->button('submit', 'Assigned', array('onclick'=>'gateway_update('.$row['Gateway']['id'].')','disabled'=>'disabled'));
        else
        echo $this->button('submit', 'Assign', array('onclick'=>'gateway_update('.$row['Gateway']['id'].')','id'=>'assignid_'.$row['Gateway']['id']));
        
        ?>
                </td>
            </tr>
            <?php endforeach ?>
        </tbody>

    </table>
</div>    
    <style type="text/css">
        .head_table table{
            width: 500px;
        }
        .head_table table tbody tr:hover td {
            border:none;
            border-bottom: 1px solid #fff;
            border-top: 1px solid #fff;
            border-right: 1px solid #fff;
        }
    </style>
    <script type="text/javascript">
        $(document).ready(function(){
            $(".gateway").each(function(i,name){
                $(this).val($(this).attr('gateway'));
            });
        }(jQuery));
        
        function gateway_update(val){
            var rate = $("#rate_"+val).val();
            var vendorid = $("#gateway_"+val).val();
            var _method = 'POST';
            var _url = '<?php echo $url; ?>';
            var _queryStr = {gateway_id:val,rate:rate,vendor_id:vendorid};
            $.ajax({
                type:_method,
                url:_url,
                data:_queryStr,
                success:function (msg) {
                    //alert(val);
                    $("#rate_"+val).attr("disabled",'disabled');
                    $("#gateway_"+val).attr("disabled",'disabled');
                    $("#assignid_"+val).val("Assigned");
                    $("#assignid_"+val).attr("disabled",'disabled');                    
                    alert(msg);
                }
            });
         }
    </script>
<?php $this->endblock('content'); ?>