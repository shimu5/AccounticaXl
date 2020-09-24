<?php $this->heading('h1', 'Assign Resellers') ?>
<?php $this->inherits('layouts/default.php') ?>

<?php $this->block('toolbar') ?>
<ul>
    <li><?php echo ($this->a('index.php/accountica/customers/list', 'Customer List')); ?></li>
    <li><?php echo ($this->a('index.php/accountica/customers/add', 'New Customer')); ?></li>
</ul>
<?php $this->endblock('toolbar') ?>
    

<?php $this->block('content'); ?>
 
<div class="index">
    
     <!-- grid Start -->
    <?php echo $this->e($grid_url2)?>
    <div style="position:relative">
        <div style="width:100%;margin:0 auto">
            <div style="margin:10px;border: 1px solid black;">
                <div class="grid-header" style="width:100%">
                    <label>List of Transactions</label>
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

<style type="text/css">
    .head_table table{
        width: 700px;
    }
    .head_table table tbody tr:hover td {
        border:none;
        border-bottom: 1px solid #fff;
        border-top: 1px solid #fff;
        border-right: 1px solid #fff;
    }
</style>
<script type="text/javascript">
    $(document).ready(function() {
        $(".customer").each(function(i, name) {
            $(this).val($(this).attr('customer'));
        });
    }(jQuery));

    function reseller_update(val) {
        var rate = $("#rate_" + val).val();
        var customer = $("#customer_" + val).val();
        var _method = 'POST';
        var _url = '<?php echo $url; ?>';
        var _queryStr = {reseller_id: val, rate: rate, customer_id: customer};
        $.ajax({
            type: _method,
            url: _url,
            data: _queryStr,
            success: function(msg) {
                //alert(val);
                $("#rate_" + val).attr("disabled", 'disabled');
                $("#customer_" + val).attr("disabled", 'disabled');
                $("#assignid_" + val).val("Assigned");
                $("#assignid_" + val).attr("disabled", 'disabled');
                alert(msg);
            }
        });
    }
</script>

<script type="text/javascript">
    function formatter(row, cell, value, columnDef, dataContext) {
        return value;
    }

    var columns = [
        {id: "sel", name: "No.", field: "no", behavior: "select", cssClass: "cell-selection", width: 40, cannotTriggerInsert: true, resizable: false, selectable: false, sortable: true},
        {id: "server_id", name: "Server Name", field: "server_id", sortable: true},
        {id: "login", name: "Reseller Name", field: "login", editor: Slick.Editors.Text, sortable: true},
        {id: "level", name: "Level", field: "level", editor: Slick.Editors.Text, sortable: true},
        {id: "rate", name: "Rate", field: "rate", editor: Slick.Editors.Text, sortable: false, formatter: formatter},
        {id: "customer", name: "Customer", field: "customer", sortable: false, formatter: formatter},
        {id: "button", name: "Actions", field: "button", sortable: false, formatter: formatter}
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
<?php $this->endblock('content'); ?>