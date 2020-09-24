<?php $this->heading('h1', 'Add Transaction Form') ?>
<?php $this->inherits('layouts/default.php') ?>

<?php $this->block('content') ?>
<div class="zform">
    <?php echo $this->form('PendingLedger');?>
    <div class="fieldset top">
        <fieldset class="fldset_new">
            <legend>Transaction</legend>
            <?php echo $this->select('type', 'Transaction type', accountica\models\PType::$vendorTransType) ?>
        </fieldset>
        <fieldset class="fldset_left">
            <legend>Vendor</legend>
            <div class="clear">
                <?php echo $this->select('account_id', 'Vendor', array(''=>'select')+$vendorList) ?>
                <?php echo $this->text('tr_date', 'Date', array('class'=>'w5 datetime')) ?>        
            </div>
            <?php echo $this->text('amount', 'Amount', array('class'=>' w5')) ?> 
            <?php echo $this->text('rate', 'Rate', array('class'=>' w5','readonly'=>'readonly')) ?> 
            <?php echo $this->text('deposit', 'Cost Amount', array('class'=>' w5')) ?> 
        </fieldset>
        <fieldset class="fldset_middle">
            <span style="width: 100px">&#8592; Invoice</span><div class="clear"></div>
            <span style="width: 100px">&#8594; Bills Payment</span><div class="clear"></div>
        </fieldset>        
        <fieldset class="fldset_right">
            <legend>Gateway</legend>
            <div class="">
                <?php echo $this->select('reseller_id', 'Gateway', array(''=>'gateway')) ?>
        <div class="clear">
            <table>
                <tbody>
                 <tr>
                    <td>Gateway</td>
                    <td>Rate</td>
                 </tr>                        
                </tbody>
                <tbody id="reseller_info"><tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                 </tr> </tbody>
           </table>
        </div>
            </div>
        </fieldset>
        <div class="clear"></div>
        
        <div class="fldset_new fldset_bottom"> 
            <?php echo $this->textarea('description', 'Description', array('class'=>' w5')) ?>
        </div>
    </div>
    <div class="fieldset action align_right">
        <?php echo $this->button('submit', 'OK');?>
        <?php echo $this->button('button', 'Cancel');?>
    </div>
    <?php echo $this->endform(); ?>
</div>
<style type="text/css"> 
    legend{
        color:#3DB3EB;
        padding: 0 5px 0 5px;
        font-size: 15px;
    }
    .fldset_new{
        border:1px solid #CCC;
        width: 600px;
        margin: 0 auto;
        border-radius: 5px;
        min-height:100px;
    }
    .fldset_bottom{
        border:0px solid #CCC;
        margin: 20px auto;
    }
    .fldset_left{
        border:1px solid #CCC;
        width: 450px;
        float:left;
        border-radius: 5px;
        padding: 10px;
        margin: 10px 0 0 20px;
    }
    .fldset_left_new{
        margin: 10px;
        width: 405px;
    }
    .fldset_middle{
        border:1px solid #CCC;
        width: 150px;
        float:left;
        padding: 10px;
        margin: 20px 0 0 20px;
    }
    .fldset_right{
        border:1px solid #CCC;
        width: 450px;
        border-radius: 5px;
        padding: 10px;
        float:right;
        margin: 10px 20px 0 0px;
    }
    
    table{
        margin: 8px;
    }
    table tr td{
        width: 600px;
        border: 1px solid #DAD0C4;
        padding: 5px;
        text-align: left;
    }
    table thead tr th{       
        border: 1px solid #DAD0C4;
        background-color: #CCC;
        width: 600px;
        padding: 5px;
        text-align: center;
    }
    .align_right{ text-align: right; padding-right: 20px; }
</style>
<script type="text/javascript">    
    $("#PendingLedger_account_id").change(function(){
        var acc_id = $(this).val();     
        if(acc_id=="")
            alert('select customer');
        else{
            $.ajax({
                url:'<?php echo $url; ?>',
                type: "POST",
                dataType: 'json',
                data: "vendor_id="+acc_id,
                success:function(data){
                    var str_opt = "<option selected>select</option>"
                    var tab_row = ""
                    $.each(data, function(idx, row){
                        
                        str_opt +="<option value='"+row['Gateway'].id+"'>"+row['Gateway'].ip_number+"</option>";
                        tab_row +="<tr><td>"+row['Gateway'].ip_number+"</td><td>"+row['Gateway'].rate+"</td></tr>";                        
                    });
                    $("#PendingLedger_reseller_id").html(str_opt);
                    $("#reseller_info").html(tab_row);                    
                }

            })
        }
    });

    $("#PendingLedger_amount").live('change',function(){
        var deposit = isNaN((parseFloat($(this).val())) * (parseFloat($("#PendingLedger_rate").val()))) ? 0 : (parseFloat($(this).val())) * (parseFloat($("#PendingLedger_rate").val()));
        $("#PendingLedger_deposit").val(deposit)
        
    });

    $("#PendingLedger_reseller_id").live('change',function(){
        var get_id = $(this).val();
        if(get_id=="")
            alert('select reseller');
        else{
            $.ajax({
                url:'<?php echo $url_rate; ?>',
                type: "POST",
                data: "gateway_id="+get_id,
                success:function(rate){
                          $("#PendingLedger_rate").val(rate);
                }

            })
        }
    })
</script>

<?php $this->endblock('content') ?>