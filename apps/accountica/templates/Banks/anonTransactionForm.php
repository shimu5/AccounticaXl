<?php $this->heading('h1', 'Add Transaction Form') ?>
<?php $this->inherits('layouts/default.php') ?>

<?php $this->block('content') ?>
<div class="zform">
    <?php echo $this->form('Bank');?>
    <div class="fieldset top">
        <fieldset class="fldset_new">
            <legend>Transaction</legend>
            <?php echo $this->select('type_transaction', 'Transaction type', array(''=>'select')) ?>
        </fieldset>
        <fieldset class="fldset_left">
            <legend>Customer</legend>
            <div class="">
                <!--<div class="legend">Add Transaction</div>-->
                <?php echo $this->select('to', 'customer', array(''=>'select')) ?>
                <?php echo $this->text('dep_amount', 'date', array('class'=>' w5')) ?>
                <?php echo $this->select('bank_account', 'Bank Account', array(''=>'select')+$bank_account) ?>
                <?php echo $this->select('category', 'Product', array(''=>'select')) ?>            
                <?php echo $this->text('ex_rate', 'Payment Method', array('class'=>' w5')) ?>
                <?php echo $this->text('amount', 'Exchange Rate', array('class'=>' w5')) ?>            
                <?php echo $this->text('date', 'Deposit Amount', array('class'=>' w5')) ?>
                <?php //echo $this->textarea('description', 'Description', array('class'=>' w5')) ?>
            </div>
        </fieldset>
        <fieldset class="fldset_middle">
            <span style="width: 100px">&#8592; Credit Allow</span><div class="clear"></div>
            <span style="width: 100px">&#8594; Credit Return</span><div class="clear"></div>
            <span style="width: 100px">&#8594; Receive Payment</span><div class="clear"></div>
        </fieldset>        
        <fieldset class="fldset_right">
            <legend>Reseller</legend>
            <div class="">
<!--                <div class="legend">Add Transaction</div>-->
                <?php echo $this->select('to', 'Reseller', array(''=>'select')) ?>
        <div class="clear">
            <table>               
                <tbody>
                 <tr>
                    <td>Reseller Name</td>
                    <td>Name</td>
                 </tr>                 
                 <tr>
                    <td>Rate</td>
                    <td>0.12 BDT</td>
                 </tr>                                 
                 <tr><td>&nbsp;</td><td>&nbsp;</td></tr>                                 
                 <tr><td>&nbsp;</td><td>&nbsp;</td></tr>                                 
                 <tr><td>&nbsp;</td><td>&nbsp;</td></tr>                                 
                </tbody>
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
<?php $this->endblock('content') ?>