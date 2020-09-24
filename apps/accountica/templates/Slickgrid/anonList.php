<?php $this->inherits('layouts/default.php') ?>

<?php $this->block('heading'); ?>
    <h1>List of Customers</h1>
<?php $this->endblock('heading'); ?>

<?php $this->block('content'); ?>
<div class="index">
    <!-- grid Start -->
    <?php echo $this->e($grid_url2)?>
    <div style="position:relative">
        <div style="width:80%;margin:0 auto">
            <div style="margin:10px;border: 1px solid black;">
                <div class="grid-header" style="width:100%">
                    <label>List of Customers</label>
                    <span style="float:right" class="ui-icon ui-icon-search" title="Search data" onclick="toggleFilterRow()"></span>
                </div>
                <div id="myGrid" style="width:100%;min-height:300px;"></div>
                <div id="pager" style="width:100%;min-height:20px;"></div>
            </div>
        </div>
    </div>

    <div id="inlineFilterPanel" style="display:none;background:#dddddd;padding:3px;color:black;">
        Search Customer<input type="text" id="txtSearch2">
    </div>
    <!-- following input used to get url of grid data -->
    <input class="grid_url" type="hidden" value="<?php $this->e($grid_url)?>">

    <!-- grid end -->

</div>
    <div id="mine"></div>



<!-- grid Start -->
<script type="text/javascript">
    
    function formatter(row, cell, value, columnDef, dataContext) {
        return value;
    }
    var columns = [
        {id: "sel", name: "#", field: "no", behavior: "select", cssClass: "cell-selection", width: 40, cannotTriggerInsert: true, resizable: false, selectable: false, sortable: true },
        {id: "acc_name", name: "Customer Name", field: "name", width: 120, minWidth: 120, cssClass: "cell-title", editor: Slick.Editors.Text,  sortable: true},
        {id: "acc_no", name: "Customer No", field: "user_name", editor: Slick.Editors.Text, sortable: true},
        {id: "bank_name", name: "Phone", field: "phone", editor: Slick.Editors.Text, sortable: true},
        {id: "last_balance", name: "Balance", field: "last_balance", editor: Slick.Editors.Text, sortable: true,formatter: formatter},
        {id: "transaction", name: "Transaction", field: "transaction",  sortable: false, formatter: formatter},
       // {id: "edit", name: "Edit", field: "edit", editor: Slick.Editors.Text, sortable: false, formatter: formatter},
       // {id: "delete", name: "Delete", field: "delete", editor: Slick.Editors.Text, sortable: false, formatter: formatter},
       {id: "Save", name:"Save", field:"save",formatter: function (r,c,id,def,datactx){ return "<a href='#'>Save</a>"; }},
       {id: "delete", name:"Delete", field:"delete",formatter: function (r,c,id,def,datactx){ return "<a href='#'>x</a>"; }},
       
       /* {id: "delete", name:"Delete", field:"delete",formatter: function (r,c,id,def,datactx) {
               // return "<a href='#' onclick='editClick(" + id + "," + r + ")'>edit</a>&nbsp;&nbsp;"+
                return " <a href='#' id='saverow' onclick='saveGrid(" + r + ")'>Save</a>&nbsp;&nbsp;"+
                      "<a href='#'>x</a>";} },
                      //  "</a><a href='#' id='' reloadUrl ='' onclick='removeClick(" + id + "," + r + ")'>x</a>";} },*/
    ];


    var options = {
        editable: true,
        enableAddRow: true,
        enableCellNavigation: true,
        asyncEditorLoading: true,
        autoEdit: true,
        forceFitColumns: true,
        topPanelHeight: 25
    };

    function myFilter(item, args) {
        if (item["percentComplete"] < args.percentCompleteThreshold) {
            return false;
        }

        if (args.searchString != "" && item["name"].indexOf(args.searchString) == -1) {
            return false;
        }

        return true;
    }
    function dataViweCallback(d){
        console.log(d);
    }
</script>
<!-- grid Start -->

<script type="text/javascript">
    //var grid;
    //alert(window.location);
    /*$.get(window.location, function(data) {
      $('#mine').html(data[rows]);
     // alert(data["page"])
    });*/

     //datas = new Slick.Grid();
     

     //dataView = new Slick.Data.DataView({ inlineFilters: true });
  //grid = new Slick.Grid("#myGrid", dataView, columns, options);
  
  //grid.setSelectionModel(new Slick.RowSelectionModel());

  //var pager = new Slick.Controls.Pager(dataView, grid, $("#pager"));
     
     
    /*   function removeClick(id,row){
        dataView.deleteItem(id);
        //        console.log(dataView);
//        console.log(row);
//        console.log(dataView.getItem(row));
    }
 function saveGrid(row) {
         console.log(dataView.getItem(row));
        $.ajax({
            url: "add",
            type: "POST",
            data: dataView.getItem(row),
            dataType:'json',
            success: function(msg){
              location.reload();
            },
            error:function( event, jqxhr, settings, exception){
                alert("saveError");
               
               grid = new Slick.Grid($('#myGrid'), data, columns, options);
               //dataView.deleteItem(row);
              //grid.updateFilter();
              dataView.refresh();
            }
        });

     
        //$("input[name='mydata']").val(jQuery.parseJSON(grid.getData()));
    }

     function saveRow(newid,row){
      alert(newid);
 //       var selectRow = gridInstance.getSelectedRows();
 //       alert(gridInstance.getDataItem(selectRow).columnName)
//        grid.onCellChange.subscribe(function (e, args) {
//                alert(args);
//                if (typeof(args.item.id)=='undefined')
//                    $.post("/mygrid/data/insert", args.item);
//                else
//                    $.post("/mygrid/data/update", args.item);
//            });

    }

     
            
        
function requiredFieldValidator(value) {
    if (value == null || value == undefined || !value.length) {
      return {valid: false, msg: "This is a required field"};
    } else {
      return {valid: true, msg: null};
    }
  }

     /* $.getJSON("www/index.php/accountica/slickgrid/list", success = function (data) {
            //grid = new Slick.Grid("#myGrid", data, columns, options);
            grid.onCellChange.subscribe(function (e, args) {
                if (typeof(args.item.id)=='undefined')
                    $.post("www/index.php/accountica/slickgrid/insert", args.item);
                else
                    $.post("www/index.php/accountica/slickgrid/update", args.item);
            });

            // Handle new row

            grid.onAddNewRow.subscribe(function(e, args) {
                var item = args.item;
                var column = args.column;
                grid.invalidateRow(data.length);
                data.push(item);
                console.log(data);
                grid.updateRowCount();
                grid.render();
            });

            $("#myGrid").show();
        });*/

</script>

<?php $this->endblock('content'); ?>