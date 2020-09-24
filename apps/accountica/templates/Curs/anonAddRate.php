<?php $this->heading('h1', 'Add Currency Rate') ?>
<?php $this->inherits('layouts/default.php') ?>

<?php $this->block('content') ?>
<div class="zform">
    <?php echo $this->form('Cur'); ?>
    <div class="fieldset top">
        <div class="ig5">
            <div class="currency_box">
            <div class="legend">Add Currency Rate</div>
            <table>
                <tbody>
            <?php $count = 0; foreach ($currency_list as $name) {
                if($name !== $cur_name){ ?>
                    <tr>
                        <td><?php
                            if($data){
                                echo $this->text('Cur.'.$cur_name.'_'.$name.'.'.$cur_name, $cur_name, array('class' => 'pos_double_val w5'));
                                } 
                            ?>
                        </td>
                        <td><span> = </span></td>                        
                        <td><?php echo $this->text('Cur.'.$cur_name.'_'.$name.'.'.$name, $name, array('class' => 'pos_double_val w5')) ?></td>
                    </tr>
                        <?php $count++; ?>
                <?php }
            }?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
    <div class="fieldset action">
        <?php echo $this->button('submit', 'Save'); ?>
        <?php echo $this->button('button', 'Cancel'); ?>
        </div>

    <?php echo $this->endform(); ?>
</div>

<style type="text/css">
    div.input .iw{
        margin-left: 5% !important ;
    }
    .ig5{
        margin-left: 30% !important ;
/*        padding-left: 20% !important ;*/
    }
</style>
<?php $this->endblock('content') ?>