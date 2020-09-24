<?php $this->inherits('layouts/default.php') ?>

<?php $Machine  = new \accountica\models\Machine();?>

<?php $this->block('heading'); ?>
    <h1>Sync. History</h1>
<?php $this->endblock('heading'); ?>

<?php $this->block('content'); ?>
<div class="index">
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Date</th>
                <th>Server</th>
                <th>Performed By</th>
                <th>Records Found</th>
                <th>New Records</th>
            </tr>
        </thead>

        <tbody>
            <?php $count = 0; foreach ($rows as $row): ?>
            <tr>
                <td><?php $this->e(++$count) ?></td>
                <td><?php $this->e($row['Synchronization']['date']) ?></td>
                <td>
                    <?php $this->e(\accountica\models\Machine::$servers[$row['Synchronization']['ip'].":".$row['Synchronization']['port']]) ?>
                    <?php $this->e('('.$row["Synchronization"]["ip"].":".$row["Synchronization"]["port"].')');?>
                    <?php
                    if(!empty($row['Synchronization']['sync_from_date'])) : ?>    <br/>
                        <span> Sync From: </span><span ><?php echo $row['Synchronization']['sync_from_date']; ?></span>
                        <span> To: </span><span ><?php echo $row['Synchronization']['sync_to_date']; ?></span>
                    <?php endif; ?>
                </td>
                <td><?php $this->e($admin_list[$row['Synchronization']['admin_id']]) ?></td>
                <td>
                    <?php if(defined('RESELLER4_ON') && RESELLER4_ON){ ?><span>R4 : </span><span class="bold"><?php $this->e($row['Synchronization']['res4_found_records']); ?></span><br /><?php } ?>

                    <span>R3 : </span><span class="bold"><?php $this->e($row['Synchronization']['res3_found_records']); ?></span><br />
                    <span>R2 : </span><span class="bold"><?php $this->e($row['Synchronization']['res2_found_records']); ?></span><br />
                    <span>R1 : </span><span class="bold"><?php $this->e($row['Synchronization']['res1_found_records']); ?></span><br />

                    <?php if(defined('RESELLER4_ON') && RESELLER4_ON){ ?>
                    <span>RP : </span><span class="bold"><?php $this->e(($row['Synchronization']['res_payment_found_records']+$row['Synchronization']['c4_res4_payment_found_records'])); ?></span><br />
                    <?php }else{ ?>
                    <span>RP : </span><span class="bold"><?php $this->e($row['Synchronization']['res_payment_found_records']); ?></span><br />
                    <?php } ?>


                    <span>GW : </span><span class="bold"><?php $this->e($row['Synchronization']['gateway_found_records']); ?></span><br />
                    <?php if (GWSYNC === true): ?>
                    <span>GWC : </span><span class="bold"><?php $this->e($row['Synchronization']['gwclients_found_records']); ?></span><br />
                    <span>GP : </span><span class="bold"><?php $this->e($row['Synchronization']['gw_payments_found_records']); ?></span><br />
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($record['Synchronization']['status'] == 0): ?>

                    <?php if(defined('RESELLER4_ON') && RESELLER4_ON){ ?><span>R4 : </span><span class="bold"><?php $this->e($row['Synchronization']['res4_new_records']); ?></span><br /><?php } ?>

                    <span>R3 : </span><span class="bold"><?php $this->e($row['Synchronization']['res3_new_records']); ?></span><br />
                    <span>R2 : </span><span class="bold"><?php $this->e($row['Synchronization']['res2_new_records']); ?></span><br />
                    <span>R1 : </span><span class="bold"><?php $this->e($row['Synchronization']['res1_new_records']); ?></span><br />

                    <?php if(defined('RESELLER4_ON') && RESELLER4_ON){ ?>
                    <span>RP : </span><span class="bold"><?php $this->e(($row['Synchronization']['res_payment_new_records'] + $row['Synchronization']['c4_res4_payment_new_records'])); ?></span><br />
                    <?php }else{ ?>
                    <span>RP : </span><span class="bold"><?php $this->e($row['Synchronization']['res_payment_new_records']); ?></span><br />
                    <?php } ?>

                    <span>GW : </span><span class="bold"><?php $this->e($row['Synchronization']['gateway_new_records']); ?></span><br />
                    <?php if (GWSYNC === true) { ?>
                    <span>GWC : </span><span class="bold"><?php $this->e($row['Synchronization']['gwclients_new_records']); ?></span><br />
                    <span>GP : </span><span class="bold"><?php $this->e($row['Synchronization']['gw_payments_new_records']); ?></span><br />
                    <?php } ?>


                    <?php else: ?>
                    <?php echo SyncStatus::getStatus($row['Synchronization']['status']); ?>
                    <?php endif;?>
                </td>                
            </tr>
            <?php endforeach ?>
        </tbody>

    </table>
</div>
<?php $this->endblock('content'); ?>