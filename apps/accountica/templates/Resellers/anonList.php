<?php $this->inherits('layouts/default.php') ?>

<?php $this->block('heading'); ?>
    <h1>List of Resellers</h1>
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
        Search Login<input type="name" id="txtSearch2">
    </div>
    <!-- following input used to get url of grid data -->
    <input class="grid_url" type="hidden" value="<?php $this->e($grid_url)?>">

    <!-- grid end -->
    
</div>
<!-- grid Start -->
<script type="text/javascript">
    function formatter(row, cell, value, columnDef, dataContext) {
        return value;
    }
    var columns = [
        {id: "sel", name: "#", field: "no", behavior: "select", cssClass: "cell-selection", width: 40, cannotTriggerInsert: true, resizable: false, selectable: false, sortable: true },
        {id: "server_id", name: "Server", field: "server_id", sortable: true},
        {id: "login", name: "Login", field: "login", editor: Slick.Editors.Text, sortable: true},
        {id: "password", name: "Password", field: "password", editor: Slick.Editors.Text, sortable: true},
        {id: "level", name: "Level", field: "level", editor: Slick.Editors.Text, sortable: true},
        {id: "idReseller", name: "Parent", field: "idReseller", editor: Slick.Editors.Text, sortable: true},
        {id: "balance", name: "Balance", field: "balance", editor: Slick.Editors.Text, sortable: false, formatter: formatter},
        {id: "edit", name: "Edit", field: "edit", editor: Slick.Editors.Text, sortable: false, formatter: formatter},
        {id: "paymenthistory", name: "PaymentHistory", field: "paymenthistory", editor: Slick.Editors.Text, sortable: false, formatter: formatter},
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

        if (args.searchString != "" && item["login"].indexOf(args.searchString) == -1) {
            return false;
        }

        return true;
    }

</script>
<!-- grid Start -->
<?php $this->endblock('content'); ?>