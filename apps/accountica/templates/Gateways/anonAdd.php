<?php $this->heading('h1', 'Edit Gateways') ?>
<?php $this->inherits('layouts/default.php') ?>

<?php $this->block('content') ?>
<?php //pr($data); ?>
<div class="zform">
    <?php echo $this->form('Gateway');?>
    <div class="fieldset top">
        <div class="ig5">
            <div class="legend">Bank Account Information</div>
            <?php //echo $this->select('level', 'Resellers Type', array(''=>'select')+ $reseller_type) ?>
            <?php echo $this->text('server_id','Server',array('class'=>' w5')) ?>
            <?php echo $this->text('description','Description',array('class'=>' w5')) ?>
            <?php echo $this->text('ip_number','IP Number',array('class'=>' w5')) ?>
            <?php echo $this->text('ip_port','IP Port',array('class'=>' w5')) ?>
            <?php echo $this->text('type','Type',array('class'=>' w5')) ?>
            <?php echo $this->text('call_limit','Call Limit',array('input'=>'spinner','class'=>'int_val w5')) ?>
            <?php echo $this->text('vendor_id','Vendor',array('class'=>' w5')) ?>
            <?php echo $this->text('rate','Rate',array('input'=>'spinner','class'=>'double_val w5')) ?>
        </div>
    </div>
    <div class="fieldset action">
        <?php echo $this->button('submit', 'Save');?>
        <?php echo $this->button('button', 'Cancel');?>
    </div>
    <?php echo $this->endform(); ?>
</div>
<?php $this->endblock('content') ?>