<?php $this->heading('h1', 'New Admin') ?>
<?php $this->inherits('layouts/default.php') ?>

<?php $this->block('content') ?>
<div class="zform">
    <?php echo $this->form('Admin');?>
    <div class="fieldset top">
        <div class="ig5">
            <div class="legend">Admin Information</div>
            <?php echo $this->text('name', 'Name', array('class'=>' w5')) ?>
            <?php echo $this->text('user_name', 'User name', array('class'=>' w5')) ?>
            <?php echo $this->text('password', 'Password') ?>
            <?php //echo $this->hidden('parent_id', array('value'=>'0')) ?>
        </div>

        <div class="ig5">
            <div class="legend">Contact Information</div>
            <?php echo $this->text('email', 'E-mail', array()) ?>
            <?php echo $this->text('phone', 'Phone', array()) ?>
            <?php echo $this->textarea('address', 'Address', array()) ?>
            <?php echo $this->select('country_id', 'Country', array('BAN'=>'Bangladesh', 'CAN'=>'Canada')) ?>
            <?php //echo $this->select('status', 'State', array('0'=>'Active', '1'=>'Inactive', '2'=>'Disabled')); ?>
        </div>
    </div>
    <div class="fieldset action">
        <?php echo $this->button('submit', 'Save');?>
        <?php echo $this->button('button', 'Cancel');?>
    </div>

    <?php echo $this->endform(); ?>

</div>
<?php $this->endblock('content') ?>