<?php $this->heading('h1', 'Withdrawal Form') ?>
<?php $this->inherits('layouts/default.php') ?>

<?php $this->block('content') ?>
<div class="zform">
    <?php echo $this->form('PendingLedger');?>
    <div class="fieldset top">
        <div class="ig5">
            <div class="legend">Withdrawal</div>
            <?php echo $this->select('dst_bank_id', 'From', array(''=>'select') + $bank_account) ?>
            <?php echo $this->text('deposit', 'Withdrawal Amount('.$base_cur.')', array('class'=>' w5 changeamount','id'=>'deposite_amount')) ?>
            <?php echo $this->text('rate', 'Exchange Rate', array('class'=>' w5 changeamount','id'=>'exchange_rate')) ?>
            <?php echo $this->text('amount', 'Amount', array('class'=>' w5','readonly'=>true,'id'=>'amount')); ?>
            <?php echo $this->select('category_id', 'Category', $category) ?>
            <?php echo $this->text('tr_date', 'Date', array('class'=>' w5 date')) ?>
            <?php echo $this->textarea('description', 'Description', array('class'=>' w5')) ?>
        </div>
    </div>
    <div class="fieldset action">
        <?php echo $this->button('submit', 'Save');?>
        <?php echo $this->button('button', 'Cancel');?>
    </div>
    <?php echo $this->endform(); ?>
</div>


<script type="text/javascript">
    $('.changeamount').on('input',function(){
        changeamount();
    });
    $('.changeamount').on('change',function(){
        changeamount();
    });
    function changeamount(){
        var deposite_rate = parseFloat($('#deposite_amount').val());
        var exchange_rate = parseFloat($('#exchange_rate').val());
        var amount = parseFloat(deposite_rate * exchange_rate);
        $('#amount').val(parseFloat(amount));
        if (isNaN( $('#amount').val())) 
             $('#amount').val(0);
    }

    $('#PendingLedger_dst_bank_id').change(function(){
        $.post('<?php echo $this->c->Router->root.'GetExchangeRate'?>', {bank:$(this).val()},function(data){
            $('#exchange_rate').val(data)
        });
    });
    
</script>


<?php $this->endblock('content') ?>