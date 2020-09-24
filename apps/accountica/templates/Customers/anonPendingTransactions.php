<?php $this->heading('h1', 'Pending Transactions') ?>
<?php $this->inherits('layouts/default.php') ?>
<?php $this->block('toolbar') ?>
<ul>
    <li><?php echo ($this->a('index.php/accountica/customers/addpayment', 'Add Payment')); ?></li>
    <li><?php echo ($this->a('index.php/accountica/customers/list', 'Customer List')); ?></li>
    <li><?php echo ($this->a('index.php/accountica/customers/transactions', 'Transaction LIST')); ?></li>
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

<script type="text/javascript">
    function formatter(row, cell, value, columnDef, dataContext) {
        return value;
    }
    
    var columns = [
        {id: "sel", name: "No.", field: "no", behavior: "select", cssClass: "cell-selection", width: 40, cannotTriggerInsert: true, resizable: false, selectable: false, sortable: true },
        {id: "tr_date", name: "Date", field: "tr_date",  sortable: true},
        {id: "type", name: "Type", field: "type", editor: Slick.Editors.Text, sortable: true},
        {id: "reseller_id", name: "Reseller", field: "reseller_id", editor: Slick.Editors.Text, sortable: true},
        {id: "dst_bank_id", name: "Dest. Bank", field: "dst_bank_id", editor: Slick.Editors.Text, sortable: true},
        {id: "description", name: "Description", field: "description", sortable: true },
        {id: "amount", name: "Amount", field: "amount", sortable: true},
        {id: "cur_id", name: "Currency", field: "cur_id", editor: Slick.Editors.Text, sortable: true},
//        {id: "category_id", name: "Balance", field: "category_id", editor: Slick.Editors.Text, sortable: true},
        {id: "deposit", name: "Deposit", field: "deposit", sortable: true},
        {id: "deposit_cur_id", name: "Deposit Currency", field: "deposit_cur_id", sortable: true},
//        {id: "rate", name: "Rate", field: "rate", sortable: true},
        {id: "created_by", name: "Posted by", field: "created_by", editor: Slick.Editors.Text, sortable: true},
        {id: "button", name: "Action", field: "button", formatter: formatter,sortable: false, width:150},
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