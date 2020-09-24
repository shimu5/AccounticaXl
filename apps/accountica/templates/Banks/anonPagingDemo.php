<?php $this->inherits('layouts/default.php') ?>

<?php $this->block('heading'); ?>
    <h1>List of Banks</h1>
<?php $this->endblock('heading'); ?>

<?php $this->block('content'); ?>
<?php //pr($rows);             ?>
<div class="index">
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Acc Name</th>
                <th>Acc No</th>
                <th>Bank Name</th>
                <th>Branch</th>
                <th>Opening Date</th>
                <th>Currency</th>
                <th>Opening Balance</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            <?php $count = 0; $rows = (isset($paginate['rows']))? $paginate['rows']: array(); foreach ($rows as $row): ?>
            <tr>
                <td><?php $this->e(++$count) ?></td>
                <td><?php $this->e($row['Bank']['acc_name']) ?></td>
                <td><?php $this->e($row['Bank']['acc_no']) ?></td>
                <td><?php $this->e($row['Bank']['bank_name']) ?></td>
                <td><?php $this->e($row['Bank']['branch']) ?></td>
                <td><?php $this->e($row['Account']['opening_date']) ?></td>
                <td><?php $this->e($row['Account']['cur_id']) ?></td>
                <td><?php $this->e($row['Account']['opening_balance']) ?></td>
                <td>
                    <?php echo ($this->a('index.php/accountica/banks/edit/'.$row['Bank']['id'], 'edit')); ?>
                    &nbsp;|&nbsp;
                    <?php echo ($this->a('index.php/accountica/banks/delete/'.$row['Bank']['id'], 'delete')); ?>
                    &nbsp;|&nbsp;
                    <?php echo ($this->a('index.php/accountica/banks/transactions/'.$row['Bank']['id'], 'transactions')); ?>
                </td>
                
            </tr>
            <?php endforeach ?>
        </tbody>
        <tfoot><tr><td colspan="0"><?php echo $pages ?></td></tr></tfoot>
    </table>   
</div>
<?php $this->endblock('content'); ?>