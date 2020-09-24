<?php $this->inherits('layouts/default.php') ?>

<?php $this->block('heading'); ?>
    <h1>Sync. History</h1>
<?php $this->endblock('heading'); ?>

<?php $this->block('content'); ?>
<div class="index">
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Currency Name</th>
                <th>Symbol</th>
                <th>Current Rate</th>
            </tr>
        </thead>

        <tbody>
            <?php $count = 0; foreach ($currency_lyst as $row): ?>
            <?php if($row['Cur']['id'] != $base['Cur']['id']) : ?>
            <tr>
                <td><?php $this->e(++$count) ?></td>
                <td><?php echo $this->link($row['Cur']['name'],'add-rate/'.$row['Cur']['name']); ?></td>
                <td><?php $this->e($row['Cur']['sign']) ?></td>
                <td><?php echo '1 '.$base['Cur']['name'].' = '?></span>
                    <?php $this->e(number_format(array_key_exists($row['Cur']['name'], $rate_details)? $rate_details[$row['Cur']['name']]:1,CUR_DIGIT)); ?>
                    <?php // $this->e(number_format(!empty($row['Rate']['rate'])?$row['Rate']['rate']:1,CUR_DIGIT)); ?>
                    <span class="tgray">&nbsp;<?php echo $row['Cur']['name'] ?></span>
                </td>                             
            </tr>
            <?php endif; ?>
            <?php endforeach ?>
        </tbody>

    </table>
</div>
<?php $this->endblock('content'); ?>