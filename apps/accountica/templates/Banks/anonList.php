<?php $this->inherits('layouts/default.php') ?>

<?php $this->block('heading'); ?>
    <h1>List of Banks</h1>
<?php $this->endblock('heading'); ?>

<?php $this->block('content'); ?>
<?php //pr($rows);             ?>
<div class="index">
    <!-- grid Start -->
    <?php echo $this->e($grid_url2)?>
    <div style="position:relative">
        <div style="width:80%;margin:0 auto">
            <div style="margin:10px;border: 1px solid black;">
                <div class="grid-header" style="width:100%">
                    <label>List of Banks</label>
                    <span style="float:right" class="ui-icon ui-icon-search" title="Search data" onclick="toggleFilterRow()"></span>
                </div>
                <div id="myGrid" style="width:100%;min-height:300px;"></div>
                <div id="pager" style="width:100%;min-height:20px;"></div>
                <!--<div >
                    <input id="save" type="button" value="save">
                </div>-->
            </div>
        </div>
    </div>

    <div id="inlineFilterPanel" style="display:none;background:#dddddd;padding:3px;color:black;">
        Search Account Name <input type="text" id="txtSearch2">
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
        {id: "acc_name", name: "Account Name", field: "acc_name", width: 120, minWidth: 120, cssClass: "cell-title", editor: Slick.Editors.Text,  sortable: true},
        {id: "acc_no", name: "Account No", field: "acc_no", editor: Slick.Editors.Text, sortable: true},
        {id: "bank_name", name: "Bank Name", field: "bank_name", editor: Slick.Editors.Text, sortable: true},
        {id: "branch", name: "Branch", field: "branch", editor: Slick.Editors.Text, sortable: true},
        {id: "opening_date", name: "Opening Date", field: "opening_date", editor: Slick.Editors.Text, sortable: true},
        {id: "cur_id", name: "Currency", field: "cur_id", editor: Slick.Editors.Text, sortable: true},
        {id: "opening_balance", name: "Opening Balance", field: "opening_balance", editor: Slick.Editors.Text, sortable: true},
        {id: "edit", name: "Edit", field: "edit", editor: Slick.Editors.Text, sortable: false, formatter: formatter},
        {id: "delete", name: "Delete", field: "delete", editor: Slick.Editors.Text, sortable: false, formatter: formatter},
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

        if (args.searchString != "" && item["acc_name"].indexOf(args.searchString) == -1) {
            return false;
        }
        
        return true;
    }
  
</script>
<!-- grid Start -->
<?php $this->endblock('content'); ?>