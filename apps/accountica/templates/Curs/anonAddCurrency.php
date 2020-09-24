<?php $this->heading('h1', 'New Currency') ?>
<?php $this->inherits('layouts/default.php') ?>

<?php $this->block('content') ?>
<div class="zform">
    <?php echo $this->form('Cur'); ?>
    <div class="fieldset top">
        <div class="ig5">
            <div class="legend">Information</div>            
            <?php echo $this->text('name', 'Name', array('class' => ' w5')) ?>
            <?php echo $this->text('sign', 'Symble', array('class' => ' w5')) ?>
            
        </div>
    </div>
    <div class="fieldset action">
        <?php echo $this->button('submit', 'Save'); ?>
        <?php echo $this->button('button', 'Cancel'); ?>
        </div>

    <?php echo $this->endform(); ?>
</div>
<?php $this->endblock('content') ?>