<?php $this->inherits('layouts/default.php') ?>

<?php $this->block('heading'); ?>
    <h1>Bank Transactions</h1>
<?php $this->endblock('heading'); ?>

<?php $this->block('content'); ?>
<div class="index">
    <!-- grid Start -->
    <?php echo $this->e($grid_url2)?>
    <div style="position:relative">
        <div style="width:80%;margin:0 auto">
            <div style="margin:10px;border: 1px solid black;">
                <div class="grid-header" style="width:100%">
                    <label>List of Servers</label>
                    <span style="float:right" class="ui-icon ui-icon-search" title="Search data" onclick="toggleFilterRow()"></span>
                </div>
                <div id="myGrid" style="width:100%;min-height:300px;"></div>
                <div id="pager" style="width:100%;min-height:20px;"></div>
            </div>
        </div>
    </div>

    <div id="inlineFilterPanel" style="display:none;background:#dddddd;padding:3px;color:black;">
        Search Credit Type<input type="name" id="txtSearch2">
    </div>
    <!-- following input used to get url of grid data -->
    <input class="grid_url" type="hidden" value="<?php $this->e($grid_url)?>">

    <!-- grid end -->
</div>
<div class="chart">
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
<!-- grid Start -->
<script type="text/javascript">
    function formatter(row, cell, value, columnDef, dataContext) {
        return value;
    }
    var columns = [
        {id: "sel", name: "No.", field: "no", behavior: "select", cssClass: "cell-selection", width: 40, cannotTriggerInsert: true, resizable: false, selectable: false, sortable: true },
        {id: "tr_date", name: "Date", field: "tr_date",  sortable: true},
        {id: "type", name: "Type", field: "type", editor: Slick.Editors.Text, sortable: true},
        {id: "acc_name", name: "Transaction With", field: "acc_name", editor: Slick.Editors.Text, sortable: true},
        {id: "description", name: "Description", field: "description", sortable: true },
        {id: "deposit", name: "Amount", field: "deposit", sortable: true},
        {id: "cur_id", name: "Currency", field: "cur_id", editor: Slick.Editors.Text, sortable: true},
        {id: "bank_balance_after", name: "Balance", field: "bank_balance_after", editor: Slick.Editors.Text, sortable: false, formatter: formatter},
        {id: "cur_id", name: "Currency", field: "cur_id", editor: Slick.Editors.Text, sortable: true},
        {id: "created_by", name: "Posted by", field: "created_by", editor: Slick.Editors.Text, sortable: true},
    ];

    var options = {
        editable: false,
        enableAddRow: false,
        enableCellNavigation: true,
        asyncEditorLoading: true,
        forceFitColumns: true,
        topPanelHeight: 25
    };

    function myFilter(item, args) {
        if (item["percentComplete"] < args.percentCompleteThreshold) {
            return false;
        }

        if (args.searchString != "" && item["type"].indexOf(args.searchString) == -1) {
            return false;
        }

        return true;
    }

</script>
<!-- grid Start -->
<?php $this->endblock('content'); ?>