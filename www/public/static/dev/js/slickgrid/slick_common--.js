


var getData;
var dataView;
var grid;
var data = [];
var datainfo =[];


var columns = [
{
    id: "sel",
    name: "#",
    field: "num",
    behavior: "select",
    cssClass: "cell-selection",
    width: 40,
    cannotTriggerInsert: true,
    resizable: false,
    selectable: false,
    sortable: true
},
{
    id: "title",
    name: "Title",
    field: "title",
    width: 120,
    minWidth: 120,
    cssClass: "cell-title",
    editor: Slick.Editors.Text,
    sortable: true
},
{
    id: "duration",
    name: "Duration",
    field: "duration",
    editor: Slick.Editors.Text,
    sortable: true
},
{
    id: "%",
    defaultSortAsc: false,
    name: "% Complete",
    field: "percentComplete",
    width: 80,
    resizable: false,
    formatter: Slick.Formatters.PercentCompleteBar,
    editor: Slick.Editors.PercentComplete,
    sortable: true,
    hide:true
},
{
    id: "start",
    name: "Start",
    field: "start",
    minWidth: 60,
    editor: Slick.Editors.Date,
    sortable: true
},
{
    id: "finish",
    name: "Finish",
    field: "finish",
    minWidth: 60,
    editor: Slick.Editors.Date,
    sortable: true
},
{
    id: "effort-driven",
    name: "Effort Driven",
    width: 80,
    minWidth: 20,
    maxWidth: 80,
    cssClass: "cell-effort-driven",
    field: "effortDriven",
    formatter: Slick.Formatters.Checkmark,
    editor: Slick.Editors.Checkbox,
    cannotTriggerInsert: true,
    sortable: true,
    hide:true
}
];

var options = {
    editable: true,
    enableAddRow: true,
    enableCellNavigation: true,
    asyncEditorLoading: true,
    forceFitColumns: true,
    topPanelHeight: 25
};

var sortcol = "title";
var sortdir = 1;
var percentCompleteThreshold = 0;
var searchString = "";

//
//function myFilter(item, args) {
//  if (item["percentComplete"] < args.percentCompleteThreshold) {
//    return false;
//  }
//
//  if (args.searchString != "" && item["title"].indexOf(args.searchString) == -1) {
//    return false;
//  }
//
//  return true;
//}

//function percentCompleteSort(a, b) {
//  return a["percentComplete"] - b["percentComplete"];
//}
//
//function comparer(a, b) {
//  var x = a[sortcol], y = b[sortcol];
//  return (x == y ? 0 : (x > y ? 1 : -1));
//}

function toggleFilterRow() {
    grid.setTopPanelVisibility(!grid.getOptions().showTopPanel);
}


$(".grid-header .ui-icon")
    .addClass("ui-state-default ui-corner-all")
    .mouseover(function (e) {
        $(e.target).addClass("ui-state-hover")
    })
    .mouseout(function (e) {
        $(e.target).removeClass("ui-state-hover")
    });

$(function () {


    dataView = new Slick.Data.DataView({
        inlineFilters: true
    });
    grid = new Slick.Grid("#myGrid", dataView, columns, options);
    grid.setSelectionModel(new Slick.RowSelectionModel());
    dataView.setPagingOptions({
        pageSize: 10
    });
    var pager = new Slick.Controls.Pager(dataView, grid, $("#pager"));




    /*

  /////////// Custom Configuration For pager ///////
  op = {
      pagerType : "select",
      pageSize : [10,20,50,100],
      defaultPagesize : 50,
      buttons:true
  }
  var pager = new Slick.Controls.Pager(dataView, grid, $("#pager"),op);


  */


    var columnpicker = new Slick.Controls.ColumnPicker(columns, grid, options);


    // move the filter panel defined in a hidden div into grid top panel
    $("#inlineFilterPanel")
    .appendTo(grid.getTopPanel())
    .show();

    //  grid.onCellChange.subscribe(function (e, args) {
    //    dataView.updateItem(args.item.id, args.item);
    //  });

    grid.onColumnVisibilityChange.subscribe(function (e, args) {
        alert(args.field +"  "+ args.showHide);
    });

    //  grid.onAddNewRow.subscribe(function (e, args) {
    //    var item = {"num": data.length, "id": "new_" + (Math.round(Math.random() * 10000)), "title": "New task", "duration": "1 day", "percentComplete": 0, "start": "01/01/2009", "finish": "01/01/2009", "effortDriven": false};
    //    $.extend(item, args.item);
    //    dataView.addItem(item);
    //  });

    grid.onKeyDown.subscribe(function (e) {
        // select all rows on ctrl-a
        if (e.which != 65 || !e.ctrlKey) {
            return false;
        }

        var rows = [];
        for (var i = 0; i < dataView.getLength(); i++) {
            rows.push(i);
        }

        grid.setSelectedRows(rows);
        e.preventDefault();
    });


    grid.onSort.subscribe(function (e, args) {
        sortdir = args.sortAsc ? "asc" : "desc";
        sortcol = args.sortCol.field;
        dataView.sortColumn({
            "field":sortcol,
            "order":sortdir
        });
    });



    // wire up model events to drive the grid
    dataView.onRowCountChanged.subscribe(function (e, args) {
        grid.updateRowCount();
        grid.render();
    });

    dataView.onRowsChanged.subscribe(function (e, args) {
        grid.invalidateRows(args.rows);
        grid.render();
    });

    dataView.onPagingInfoChanged.subscribe(function (e, pagingInfo) {
        var isLastPage = pagingInfo.pageNum == pagingInfo.totalPages - 1;
        var enableAddRow = isLastPage || pagingInfo.pageSize == 0;
        var options = grid.getOptions();

        if (options.enableAddRow != enableAddRow) {
            grid.setOptions({
                enableAddRow: enableAddRow
            });
        }
    });


    var h_runfilters = null;

    // wire up the slider to apply the filter to the model
    //  $("#pcSlider,#pcSlider2").slider({
    //    "range": "min",
    //    "slide": function (event, ui) {
    //      Slick.GlobalEditorLock.cancelCurrentEdit();
    //
    //      if (percentCompleteThreshold != ui.value) {
    //        window.clearTimeout(h_runfilters);
    //        h_runfilters = window.setTimeout(updateFilter, 10);
    //        percentCompleteThreshold = ui.value;
    //      }
    //    }
    //  });


    // wire up the search textbox to apply the filter to the model
    $("#txtSearch,#txtSearch2").keyup(function (e) {
        Slick.GlobalEditorLock.cancelCurrentEdit();

        // clear on Esc
        if (e.which == 27) {
            this.value = "";
        }

        searchString = this.value;
        updateFilter();
    });

    function updateFilter() {
        dataView.setFilterArgs({
            searchString: searchString
        });
        dataView.refresh();
    }

    $("#btnSelectRows").click(function () {
        if (!Slick.GlobalEditorLock.commitCurrentEdit()) {
            return;
        }

        var rows = [];
        for (var i = 0; i < 10 && i < dataView.getLength(); i++) {
            rows.push(i);
        }

        grid.setSelectedRows(rows);
    });


    // initialize the model after all the events have been hooked up
    //dataView.setPagingOptions({pageSize: 100, pageNum: 1});


    dataView.getData($('.grid_url').val());


    dataView.beginUpdate();
    // dataView.setItems(data);
    dataView.setFilterArgs({
        searchString: searchString
    });
  
    dataView.endUpdate();

    // if you don't want the items that are not visible (due to being filtered out
    // or being on a different page) to stay selected, pass 'false' to the second arg
    dataView.syncGridSelection(grid, true);

    //$("#gridContainer").resizable();

    $(window).resize(function() {
        grid.resizeCanvas()
    });

    $(".slick-viewport").resize(function(){

        });

})