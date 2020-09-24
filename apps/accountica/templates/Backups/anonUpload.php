<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<?php $this->inherits('layouts/default.php') ?>

<?php $this->block('heading'); ?>
    <h1>File Upload</h1>
<?php $this->endblock('heading'); ?>

<?php $this->block('content'); ?>
<div class="index">
     <?php echo $this->form('FileUpload', array('method' => 'file')); ?>
     <?php //echo $this->form('FileUpload',array('method'=>'file'));?>
    <div class="fieldset top">
        <div class="ig5">
            <div class="legend">File Upload</div>
            <?php echo $this->file('sql', 'Flle Name', array('class'=>'w5')) ?>
            <?php echo $this->button('submit', 'Upload');?>
        </div>
    </div>
    <?php echo $this->endform(); ?>
</div>
<?php $this->endblock('content'); ?>