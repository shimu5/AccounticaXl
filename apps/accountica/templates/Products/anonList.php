<?php $this->inherits('layouts/default.php') ?>

<?php $this->block('heading'); ?>
    <h1>List of Products</h1>
<?php $this->endblock('heading'); ?>

<?php $this->block('content'); ?>
<div class="index">
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>Price</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            <?php $count = 0; foreach ($rows as $row): ?>
            <tr>
                <td><?php $this->e(++$count) ?></td>
                <td><?php $this->e($row['Product']['name']) ?></td>
                <td><?php $this->e($row['Product']['price']) ?></td>
                <td><?php $this->e(accountica\models\ProductStatus::getStatusString($row['Product']['is_active'])) ?></td>
                <td>
                    <?php echo ($this->a('index.php/accountica/products/edit/'.$row['Product']['id'], 'edit')); ?>
                    &nbsp;|&nbsp;
                    <?php echo ($this->a('index.php/accountica/products/delete/'.$row['Product']['id'], 'delete')); ?>
                </td>
                
            </tr>
            <?php endforeach ?>
        </tbody>

    </table>
</div>
<?php $this->endblock('content'); ?>