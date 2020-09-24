<?php $this->heading('h1', 'New Admin') ?>
<?php $this->inherits('layouts/default.php') ?>

<?php $this->block('content') ?>
<div class="zform">
    <?php echo $this->form('Product'); ?>
    <div class="fieldset top">
        <div class="ig5">
            <div class="legend">Product Information</div>
            <?php echo $this->text('name', 'Name', array('class' => ' w5')) ?>
            <?php echo $this->text('price', 'Price', array('input'=>'spinner','class' => 'double_val w5')) ?>
            <?php echo $this->select('is_active', 'Status', accountica\models\ProductStatus::getAllStatus('select')); ?>
        </div>
        <div class="ig5">            
        </div>
    </div>
    <div class="fieldset action">
        <?php echo $this->button('submit', 'Save'); ?>
        <?php echo $this->button('button', 'Cancel'); ?>
        </div>

    <?php echo $this->endform(); ?>

        </div>
<?php $this->endblock('content') ?>