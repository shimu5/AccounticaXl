<?php $this->inherits('layouts/default.php') ?>

<?php $this->block('heading'); ?>
<h1>List of Menus</h1>
<?php $this->endblock('heading'); ?>

<?php $this->block('content'); ?>
<?php //pr($rows);             ?>
<div class="index">
    <table>
        <thead>
            <tr>
                <th colspan="2">Menu List</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($URL as $key => $row):  ?>
                <tr>
                    <th class='tc' style="width: 100px;"><?php echo $row['title']; ?></th>
                    <td style="">
                        <?php
                        foreach ($row['menu'] as $k => $v):
                            ?>
                     <?php echo ($this->a($v, $k,array('target'=>'_blank'))) ?> <hr/> 
                            <?php
                        endforeach;
                        ?>
</td>
                <?php endforeach ?>
            </tr>
        </tbody>
        <tfoot><tr><td colspan="0"><?php echo $pages ?></td></tr></tfoot>
    </table>
</div>
<?php $this->endblock('content'); ?>