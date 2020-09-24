<?php $this->inherits('layouts/default.php') ?>

<?php $this->block('heading'); ?>
<h1>Transaction</h1>
<?php $this->endblock('heading'); ?>

<?php $this->block('content'); ?>



<div class="index">
<!-- grid Start -->
    <div style="position:relative">
        <div style="width:80%;margin:0 auto">
            <div class="grid-header" style="width:100%">
                <label>SlickGrid</label>
                <span style="float:right" class="ui-icon ui-icon-search" title="Toggle search panel"
                      onclick="toggleFilterRow()"></span>
            </div>
            <div id="myGrid" style="width:100%;min-height:300px;"></div>
            <div id="pager" style="width:100%;min-height:20px;"></div>
            <div >
                <input id="save" type="button" value="save">
            </div>
        </div>
    </div>

    <div id="inlineFilterPanel" style="display:none;background:#dddddd;padding:3px;color:black;">
        Show tasks with title including <input type="text" id="txtSearch2">
    </div>
    <!-- following input used to get url of grid data -->
    <input class="grid_url" type="hidden" value="http://localhost/accounticaxl/0.1/dev/www/index.php/accountica/banks/GridData">
    <!-- grid end -->
</div>


<!-- grid Start -->
<script type="text/javascript">

    var columns = [
        {id: "sel", name: "#", field: "id", behavior: "select", cssClass: "cell-selection", width: 40, cannotTriggerInsert: true, resizable: false, selectable: false, sortable: true },
        {id: "reseller_id", name: "Reseller", field: "reseller_id", width: 120, minWidth: 120, cssClass: "cell-title", editor: Slick.Editors.Text,  sortable: true},
        {id: "amount", name: "Amount", field: "amount", editor: Slick.Editors.Text, sortable: true},
        {id: "amount", name: "Amount", field: "amount", editor: Slick.Editors.Text, sortable: true},
    ];

    var options = {
        editable: true,
        enableAddRow: true,
        enableCellNavigation: true,
        asyncEditorLoading: true,
        forceFitColumns: true,
        topPanelHeight: 25
    };

    function myFilter(item, args) {
        if (item["percentComplete"] < args.percentCompleteThreshold) {
            return false;
        }

        if (args.searchString != "" && item["amount"].indexOf(args.searchString) == -1) {
            return false;
        }

        return true;
    }
  
</script>
<!-- grid Start -->
<?php $this->endblock('content'); ?>
