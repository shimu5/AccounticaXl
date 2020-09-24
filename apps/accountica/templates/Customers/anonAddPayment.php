<?php $this->heading('h1', 'Add Payment Form') ?>
<?php $this->inherits('layouts/default.php') ?>
<?php $this->block('toolbar') ?>
<ul>
    <li><?php echo ($this->a('index.php/accountica/customers/list', 'Customer List')); ?></li>
    <li><?php echo ($this->a('index.php/accountica/customers/transactions', 'Transaction LIST')); ?></li>
    <li><?php echo ($this->a('index.php/accountica/customers/pendingtransactions', 'Pending Transaction LIST')); ?></li>
</ul>
<?php $this->endblock('toolbar') ?>

<?php $this->block('content') ?>
<div class="zform">
    <?php echo $this->form('PendingLedger');?>
    <div class="fieldset top">
        <fieldset class="fldset_new">
            <legend>Transaction</legend>
            <?php echo $this->select('type', 'Transaction type', accountica\models\PType::$customerTransType) ?>
        </fieldset>
        <fieldset class="fldset_left">
            <legend>Customer</legend>
            <div class="">
                <!--<div class="legend">Add Transaction</div>-->
                <?php echo $this->select('account_id', 'Customer', array(''=>'select')+ $customerList) ?>
                <?php echo $this->select('dst_bank_id', 'Bank Account Name', array(''=>'select')+$banks, array('disabled'=>'disabled')) ?>
                <?php echo $this->text('tr_date', 'Date', array('class'=>'w5 datetime')) ?>
                <?php echo $this->text('amount', 'Server Amount', array('class'=>' w5')) ?>
                <?php echo $this->text('rate', "Exchange Rate ", array('class'=>' w5','readonly'=>'readonly')) ?>
                <?php echo $this->text('deposit', 'Deposit Amount', array('class'=>' w5')) ?>
            </div>
        </fieldset>
        <fieldset class="fldset_middle">
            <span style="width: 100px">&#8594; Credit Allow</span><div class="clear"></div>
            <span style="width: 100px">&#8592; Credit Return</span><div class="clear"></div>
            <span style="width: 100px">&#8592; Receive Payment</span><div class="clear"></div>
        </fieldset>
        <fieldset class="fldset_right">
            <legend>Reseller</legend>
            <div class="">
<!--                <div class="legend">Add Transaction</div>-->
                <?php echo $this->select('reseller_id', 'Reseller', array(''=>'select')) ?>
        <div class="clear">
            <table>
                <tbody>
                 <tr>
                    <td>Reseller Name</td>
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
            <!--<div>Description</div>-->
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
    
    $(document).ready(function(){
        change_payment_type($("#PendingLedger_type").val());
    }(jQuery));
    
    $("#PendingLedger_type").change(function(){
        change_payment_type($(this).val());
    });
    
    function change_payment_type(type){
        rp_type = '<?php echo \accountica\models\PType::Payment?>'
        if(rp_type == type){
            $('#PendingLedger_dst_bank_id').removeAttr('disabled');
        }else{
            $('#PendingLedger_dst_bank_id').attr('disabled', 'disabled');
        }
    }
    
    $("#PendingLedger_account_id").change(function(){
        var acc_id = $(this).val();     
        if(acc_id=="")
            alert('select customer');
        else{
            $.ajax({
                url:'<?php echo $url; ?>',
                type: "POST",
                dataType: 'json',
                data: "customer_id="+acc_id,
                success:function(data){
                    var str_opt = "<option selected>select</option>"
                    var tab_row = ""
                    $.each(data, function(idx, row){
                        str_opt +="<option value='"+row['Reseller'].id+"'>"+row['Reseller'].login+"</option>";
                        tab_row +="<tr><td>"+row['Reseller'].login+"</td><td>"+row['Reseller'].rate+"</td></tr>";
                        
                    });
                    $("#PendingLedger_reseller_id").html(str_opt);
                    $("#reseller_info").html(tab_row);                    
                }

            })
        }
    });

    $("#PendingLedger_amount").live('change',function(){
       // var deposit = isNaN((parseFloat($(this).val())) * (parseFloat($("#PendingLedger_rate").val()))) ? 0 : (parseFloat($(this).val())) * (parseFloat($("#PendingLedger_rate").val()));
       // $("#PendingLedger_deposit").val(deposit);
        depositAmntCalc();
        
    });

    function depositAmntCalc(){
        var deposit = isNaN((parseFloat($("#PendingLedger_amount").val())) * (parseFloat($("#PendingLedger_rate").val()))) ? 0 : (parseFloat($("#PendingLedger_amount").val())) * (parseFloat($("#PendingLedger_rate").val()));
        $("#PendingLedger_deposit").val(deposit);        
    }

    $("#PendingLedger_reseller_id").live('change',function(){
        var res_id = $(this).val();
        if(res_id=="")
            alert('select reseller');
        else{
            $.ajax({
                url:'<?php echo $url_rate; ?>',
                type: "POST",
                data: "reseller_id="+res_id,
                success:function(rate){                       
                          $("#PendingLedger_rate").val(rate);
                          if($("#PendingLedger_rate").val()!="")
                            depositAmntCalc();
                }

            })
        }
    })
</script>
<?php $this->endblock('content') ?>
