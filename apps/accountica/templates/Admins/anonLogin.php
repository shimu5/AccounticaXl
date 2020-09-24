<?php $this->heading('h1', 'Login') ?>
<?php $this->inherits('layouts/default.php') ?>

<?php $this->block('menu') ?>
&nbsp;
<?php $this->endblock('menu') ?>

<?php $this->block('userinfo') ?>
<div>
    <div class="icon-notification"></div>
    <div class="icon-balance">0.00</div>
    <div class="icon-user">Anonymous</div>
</div>
<div>
    <div class="icon-preferences">PREFERENCES</div>
    <div class="icon-usertype">ADMIN LOGIN</div>
</div>
<?php $this->endblock('userinfo') ?>

<?php $this->block('toolbar') ?>
&nbsp;
<?php $this->endblock('toolbar') ?>

<?php $this->block('content') ?>
<div class="zform">
    <?php echo $this->form('User');?>
    <div class="fieldset top">
        <div class="ig5">
            <div class="legend">Admin Authentication</div>
            <?php echo $this->text('user_name', 'User name', array('class'=>' w5')) ?>
            <?php echo $this->text('password', 'Password') ?>
            <?php echo $this->hidden('status', array('value'=>'1')) ?>
        </div>

        <div class="ig5">
            
        </div>
    </div>
    <div class="fieldset action">
        <?php echo $this->button('submit', 'Login');?>
        <?php echo $this->button('button', 'Cancel');?>
    </div>

    <?php echo $this->endform(); ?>

</div>
<?php $this->endblock('content') ?>