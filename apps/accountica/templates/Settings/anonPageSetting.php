<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 *
 * @author Jakir Hosen Khan
 */

        
?>
<?php $this->heading('h1', 'Page Setting') ?>
<?php $this->inherits('layouts/default.php') ?>

<?php $this->block('content') ?>
<div class="zform">
    <?php echo $this->form('Setting'); ?>
    <div class="fieldset top">
        <div class="ig5">
            <div class="legend">Fields Info</div>
            <?php echo $this->select('pay_amt', 'Amount',$pay_field,array('input'=>'w6s')); ?>
            <?php echo $this->text('profit_loss__base__view', 'Discount Rate(%)',array('input'=>'w4')); ?>
            <?php echo $this->text('profit_loss__base__text', 'Profit(%)',array('input'=>'w4')); ?>
            <?php echo $this->check('ledger__showpostedby', 'Posted By',array('input'=>'w8s checkbox','value'=>'1','hidden'=>'0','class'=>'checkbox' )); ?>
        </div>
    </div>
    <div class="fieldset action">
        <?php echo $this->button('submit', 'Save');?>
    </div>
    <?php echo $this->endform(); ?>
</div>
<?php $this->endblock('content') ?>