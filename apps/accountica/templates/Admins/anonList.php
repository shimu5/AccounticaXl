<?php $this->inherits('layouts/default.php') ?>

<?php $this->block('heading'); ?>
    <h1>List of Admins</h1>
<?php $this->endblock('heading'); ?>

<?php $this->block('content'); ?>
<div class="index">
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>Username</th>
                <th>E-mail</th>
                <th>Phone</th>
                <th>Country</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            <?php $count = 0; foreach ($rows as $row): ?>
            <tr>
                <td><?php $this->e(++$count) ?></td>
                <td><?php $this->e($row['Admin']['name']) ?></td>
                <td><?php $this->e($row['Admin']['username']) ?></td>
                <td><?php $this->e($row['Admin']['email']) ?></td>
                <td><?php $this->e($row['Admin']['phone']) ?></td>
                <td><?php $this->e($row['Admin']['country_id']) ?></td>
                <td>
                    <?php echo ($this->a('index.php/accountica/admins/edit/'.$row['Admin']['id'], 'edit')); ?>
                    &nbsp;|&nbsp;
                    <?php echo ($this->a('index.php/accountica/admins/delete/'.$row['Admin']['id'], 'delete')); ?>
                </td>
                
            </tr>
            <?php endforeach ?>
        </tbody>

    </table>
</div>
<?php $this->endblock('content'); ?>