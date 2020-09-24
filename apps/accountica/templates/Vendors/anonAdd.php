<?php $this->heading('h1', 'New Vendor') ?>
<?php $this->inherits('layouts/default.php') ?>

<?php $this->block('content') ?>
<div class="zform">
    <?php echo $this->form('Vendor'); ?>
    <div class="fieldset top">
        <div class="ig5">
            <div class="legend">Personal Information</div>
            <?php echo $this->text('name', 'Name', array('class' => ' w5')) ?>
            <?php echo $this->text('user_name', 'UserName', array('class' => ' w5')) ?>
            <?php echo $this->text('password', 'Password', array('type' => 'password', 'class' => ' w5')) ?>
            <?php echo $this->text('email', 'Email', array('class' => ' w5')) ?>
            <?php echo $this->text('phone', 'Phone', array('class' => ' w5')) ?>
            <?php echo $this->textarea('address', 'Address', array('class' => ' w5')) ?>
            <?php echo $this->select('country_id', 'Country', $country, array('class' => ' w5')) ?>
        </div>
        <div class="ig5"><div class="legend">Account Information</div>
            <?php //echo $this->select('Account.id', 'Bank Name', $banks, array('class' => ' w5')) ?>
            <?php echo $this->text('Account.opening_date', 'Opening Date', array('class' => 'date w5', 'value' => date('Y-m-d'))) ?>
            <?php echo $this->select('Account.cur_id', 'Currency', $currs, array('class' => ' w5')) ?>
            <?php echo $this->text('Account.opening_balance', 'Opening Balance', array('input'=>'spinner','class' => 'double_val w5')) ?>
            <?php //echo $this->text('last_balance', 'Vendor Limit', array('class' => ' w5')) ?>
        </div>
    </div>
    <div class="fieldset action">
        <?php echo $this->button('button', 'Save', array('class' => 'submit')); ?>
        <?php echo $this->button('button', 'Cancel'); ?>
    </div>
    <?php echo $this->endform(); ?>
 </div>
<script type="text/javascript">
    $('.submit').click(function(e){
        e.preventDefault();
        if($('#Account_id').val() != '')
            $('#Vendor').submit();
        else{
            alert('Please select a bank');
            return;
        }
    });
    $(document).ready(function(){

    });

</script>                
<?php $this->endblock('content') ?>