<?php $this->inherits('layouts/default.php') ?>

<?php $this->block('heading'); ?>
<h1>Currency Rate</h1>
<?php $this->endblock('heading'); ?>

<?php $this->block('content'); ?>
<div class="index">
    <table>
        <thead>
            <tr>
                <th class="tcenter">&nbsp;</th>
                <?php foreach ($rate_chart as $currency): ?>
                    <th class="tcenter"><?php echo '1 ' . $currency['Base']['name']; ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>

        <tbody>
            <?php if (empty($rate_chart)): ?>
                <tr>
                    <td colspan="<?php echo $row_count; ?>" class="tcenter"><?php echo NO_RECORD_MSG; ?></td>
                </tr>
            <?php else: ?>
                <?php
                foreach ($rate_chart as $base_name => $currency):
                    ?>
                    <tr id="<?php echo ($base['Cur']['id'] == $base_name) ? 'main_base' : ''; ?>">
                        <td style="font-weight:bold;width: 50px;">
                        <?php echo $currency['Base']['name']; ?>
                        </td>
                        <?php foreach ($rate_chart as $currencies):?>
                            <td class="tright">
                                <div class="tgray left" style=""><?php echo '1 ' . $currencies['Base']['name'] . ' = '; ?></div>
                                <div class="left" style="width: 80px">
                                    <?php 
                                        if(!empty($currencies['Rate']) && array_key_exists($base_name, $currencies['Rate'])){
                                            echo number_format($currencies['Rate'][$base_name],CUR_DIGIT);
                                        } 
                                        else
                                            echo number_format(1, CUR_DIGIT);
                                        ?>
                                </div>
                                <div class="tgray left" style="width: 60px"><?php echo $currency['Base']['sign']; ?></div>
                            </td>

                        <?php endforeach; ?>

                    </tr>

    <?php endforeach; ?>
<!--                <tr>
                    <td>&nbsp;</td>
    <?php foreach ($rate_chart as $key => $currency): ?>

                        <td colspan="" class="tcenter"><?php // echo $html->link('Edit', array('controller' => 'curs', 'action' => 'add_rate', 'params' => $key)) ?></td>
                <?php endforeach; ?>
                </tr>-->
<?php endif; ?>
        </tbody>

    </table>
</div>
<?php $this->endblock('content'); ?>