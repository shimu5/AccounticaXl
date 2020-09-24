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
            <legend>Vendor</legend>
            <div class="clear">
                <?php echo $this->select('vendor', 'Vendor', array(''=>'select')) ?>
                <?php echo $this->select('date', 'Date', array(''=>'select')) ?>
                <?php echo $this->select('bank_acc', 'Bank Account', array(''=>'select')+ $bank_account) ?>
                <?php echo $this->text('product', 'Product', array('class'=>' w5')) ?>                
            </div>
            <fieldset class="fldset_left fldset_left_new">
                <legend>Usage</legend>
                <?php echo $this->text('usage', 'Usage', array('class'=>' w5')) ?>                
                <?php echo $this->text('from_date', 'From Date', array('class'=>' w5')) ?>
                <?php echo $this->text('to_date', 'To Date', array('class'=>' w5')) ?>
                <div class="fieldset action align_right">
                    <?php echo $this->button('submit', 'Calculate');?>
                </div>
                
            </fieldset>
            <?php echo $this->text('amount', 'Amount', array('class'=>' w5')) ?> 
            <?php echo $this->text('rate', 'Rate', array('class'=>' w5')) ?> 
            <?php echo $this->text('cost_amount', 'Cost Amount', array('class'=>' w5')) ?> 
        </fieldset>
        <fieldset class="fldset_middle">
            <span style="width: 100px">&#8592; Invoice</span><div class="clear"></div>
            <span style="width: 100px">&#8594; Bills Payment</span><div class="clear"></div>
        </fieldset>        
        <fieldset class="fldset_right">
            <legend>Reseller</legend>
            <div class="">
<!--                <div class="legend">Add Transaction</div>-->
                <?php echo $this->select('to', 'Gateway', array(''=>'gateway name')) ?>
        <div class="clear">
            <table>               
                <tbody>
                 <tr>
                    <td>Gateway</td>
                    <td>Name 1</td>
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
<?php $this->endblock('content') ?>