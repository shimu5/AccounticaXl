<?php $this->inherits('layouts/default.php') ?>

<?php $this->block('heading'); ?>
    <h1>Bank Transactions</h1>
<?php $this->endblock('heading'); ?>

<?php $this->block('content'); ?>
<?php //pr($rows);             ?>
<div class="index">
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Date</th>
                <th>Type</th>
                <th>Dest. Bank</th>
                <th>Src. Bank</th>
                <th>Description</th>
                <th>Category</th>
                <th>Amount </th>                
                <th>Currency</th>
                <th>Rate</th>
                <th>Deposit</th>
                <th>Deposit Currency</th>
                <th>By</th>
                <th>Actions</th>
                
            </tr>
        </thead>

        <tbody>
            <?php $count = 0; foreach ($rows as $key=>$row): ?>
            <tr>
                <td><?php $this->e(++$count) ?></td>
                <td><?php $this->e($row['PendingLedger']['tr_date']) ?></td>
                <td><?php $this->e(\accountica\models\PType::toString($row['PendingLedger']['type'])) ?></td>
                <td><?php $this->e($bank[$row['PendingLedger']['dst_bank_id']]) ?></td>
                <td><?php $this->e($bank[$row['PendingLedger']['src_bank_id']]) ?></td>
                <td><?php $this->e($row['PendingLedger']['description']) ?></td>             
                <td><?php $this->e(\accountica\models\PType::getCatName($row['PendingLedger']['category_id'])) ?></td>               
                <td><?php $this->e($row['PendingLedger']['amount']) ?></td>
                <td><?php $this->e($row['PendingLedger']['cur_id']) ?></td>
                <td><?php $this->e($row['PendingLedger']['rate']) ?></td>
                <td><?php $this->e($row['PendingLedger']['deposit']) ?></td>
                <td><?php $this->e($row['PendingLedger']['deposit_cur_id']) ?></td>
                <td><?php $this->e($admins[$row['PendingLedger']['created_by']]) ?></td>
                 <td>
                    <?php echo $this->button('button', 'Accept',array('onclick'=>'return ledger(1,'.$row['PendingLedger']['id'].');'));?>
                    <?php echo $this->button('button', 'Reject', array('onclick'=>'return ledger(2,'.$row['PendingLedger']['id'].');'));?>
                </td>
                
                
            </tr>
            <?php endforeach ?>
        </tbody>

    </table>
</div>
<script>
   function ledger(val,id){
       if((val==1 || val ==2 ) && id > 0){
           var _queryString = 'postdata=' + val + '&id=' + id;
                      
            $.ajax({
                url: "<?php echo $url; ?>",
                type: "POST",
                data: _queryString
            }).done(function( msg ) {
                window.location.reload();
            }).fail(function( jqXHR, textStatus ) {
                alert('error=Somthing went wrong!');
            });
       }
   }
</script>
<div class="index">
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable(<?php echo $data ?>);

        var options = {
          title: 'Bank Transactions Summary',
          hAxis: {title: 'Tr. Date',  titleTextStyle: {color: 'red'}},
          orientation: 'horizontal',
          width: 600,
          height: 400
        };

        var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>
   
    <div id="chart_div" style="width: 700px; height: 500px; margin: 0 auto;"></div>
</div>
<?php $this->endblock('content'); ?>