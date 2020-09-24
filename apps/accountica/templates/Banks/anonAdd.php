<?php $this->heading('h1', 'New Bank Account') ?>
<?php $this->inherits('layouts/default.php') ?>

<?php $this->block('content') ?>
<div class="zform">
    <?php echo $this->form('Bank');?>
    <div class="fieldset top">
        <div class="ig5">
            <div class="legend">Bank Information</div>
            <?php echo $this->text('acc_name', 'Account Name', array('class'=>' w5')) ?>
            <?php echo $this->text('acc_no', 'Account No', array('class'=>' w5')) ?>
            <?php echo $this->text('bank_name', 'Bank Name', array('class'=>' w5')) ?>
            <?php echo $this->text('branch', 'Branch', array('class'=>' w5')) ?>
        </div>

        <div class="ig5">
            <div class="legend">Account Information</div>
            <?php echo $this->text('Account.opening_date', 'Opening Date', array('class'=>'date w5')) ?>
            <?php echo $this->select('Account.cur_id', 'Currency', array(''=>'select') + $currencies) ?>
            <?php 
            $options = array('input'=>'spinner','class'=>'double_val');
            if(isset ($data['Bank']['id']) && $data['Bank']['id'] != '')
                $options = array_merge($options, array('readonly'=>true));
            echo $this->text('Account.opening_balance', 'Balance',$options);
            ?>             
        </div>
    </div>
    <div class="fieldset action">
        <?php echo $this->button('submit', 'Save');?>
        <?php echo $this->button('button', 'Cancel');?>
    </div>
    <?php echo $this->endform(); ?>
</div>
<?php $this->endblock('content') ?>