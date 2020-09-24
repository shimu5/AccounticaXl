<?php $this->heading('h1', 'Edit Resellers') ?>
<?php $this->inherits('layouts/default.php') ?>

<?php $this->block('content') ?>
<?php //pr($data); ?>
<div class="zform">
    <?php echo $this->form('Reseller');?>
    <div class="fieldset top">
        <div class="ig5">
            <div class="legend">Bank Account Information</div>
            <?php echo $this->select('level', 'Resellers Type', array(''=>'select')+ $reseller_type) ?>
            <?php echo $this->text('login', 'Login', array('class'=>' w5')) ?>
            <?php echo $this->password('password', 'Password', array('class'=>' w5')) ?>
            <?php echo $this->select('server_id', 'Server', array(''=>'select')+ $getIpNameList) ?>
            <?php echo $this->text('fullname', 'Full Name', array('class'=>' w5')) ?>
            <?php echo $this->text('email', 'Email', array('class'=>' w5')) ?>
            <?php echo $this->text('phone', 'Phone', array('class'=>' w5')) ?>
            <?php echo $this->text('address', 'Address', array('class'=>' w5')) ?>
            
            <?php echo $this->text('city', 'City', array('class'=>' w5')) ?>
            <?php echo $this->text('zipcode', 'Zip Code', array('class'=>' w5')) ?>            
            <?php echo $this->select('country', 'Country', array(''=>'select')+ $getCountryList) ?>
            <?php echo $this->text('rate', 'Rate', array('input'=>'spinner','class'=>'double_val w5')) ?>
        </div>
    </div>
    <div class="fieldset action">
        <?php echo $this->button('submit', 'Save');?>
        <?php echo $this->button('button', 'Cancel');?>
    </div>
    <?php echo $this->endform(); ?>
</div>
<?php $this->endblock('content') ?>