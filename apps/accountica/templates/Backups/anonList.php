<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<?php $this->heading('h1', 'Backup list') ?>
<?php $this->inherits('layouts/default.php') ?>

<?php $this->block('content') ?>
<div class="zform">
    <table id="categories_list" class="tablecontent tablecontentaltrow ww50p">
        <thead>
            <tr>
                <th class="tcenter">No.</th>
                <th class="tleft">Name</th>
                <th class="tcenter" colspan="2">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $count = 1; ?>
            <?php if (!empty($files)) : ?>
            <?php foreach ($files as $file) : ?>
                    <tr>
                        <td class="tcenter"><?php $this->e($count++); ?>.</td>
                        <td><?php echo ($this->a('index.php/accountica/backups/download/' . $file, $file)); ?></td>
                        <td colspan="">
                            <?php echo ($this->a('index.php/accountica/backups/restore/' . $file, 'Restore')); ?>
                            <?php echo ($this->a('index.php/accountica/backups/delete/' . $file, 'Delete')); ?>
                        </td>
                    </tr>
            <?php endforeach; ?>
            <?php else: ?>
                        <tr><td colspan="4" class="tcenter"><?php $this->e('No File exist'); ?></td></tr>
            <?php endif; ?>
            </tbody>
        </table>
</div>
<?php $this->endblock('content') ?>
