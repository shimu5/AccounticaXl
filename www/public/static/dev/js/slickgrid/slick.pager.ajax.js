(function ($) {
  function SlickGridPager(dataView, grid, $container,op) {
    var $status;

    function init() {
        if(op == undefined){
            op = [];
        }
      dataView.onPagingInfoChanged.subscribe(function (e, pagingInfo) {
        updatePager(pagingInfo);
      });

      constructPagerUI();
      updatePager(dataView.getPagingInfo());
    }

    function getNavState() {
      var cannotLeaveEditMode = !Slick.GlobalEditorLock.commitCurrentEdit();
      var pagingInfo = dataView.getPagingInfo();
      var lastPage = pagingInfo.totalPages - 1;

      return {
        canGotoFirst: !cannotLeaveEditMode && pagingInfo.pageSize != 0 && pagingInfo.pageNum > 0,
        canGotoLast: !cannotLeaveEditMode && pagingInfo.pageSize != 0 && pagingInfo.pageNum != lastPage,
        canGotoPrev: !cannotLeaveEditMode && pagingInfo.pageSize != 0 && pagingInfo.pageNum > 0,
        canGotoNext: !cannotLeaveEditMode && pagingInfo.pageSize != 0 && pagingInfo.pageNum < lastPage,
        pagingInfo: pagingInfo
      }
    }

    function setPageSize(n) {
      dataView.setRefreshHints({
        isFilterUnchanged: true
      });
      dataView.setPagingOptions({pageSize: n});
    }

    function gotoPage(n) {
        if(dataView.getPagingInfo().pageNum != n);
            dataView.setPagingOptions({pageNum: n});

    }
    function gotoFirst() {
      if (getNavState().canGotoFirst) {
        dataView.setPagingOptions({pageNum: 0});
      }
    }

    function gotoLast() {
      var state = getNavState();
      if (state.canGotoLast) {
        dataView.setPagingOptions({pageNum: state.pagingInfo.totalPages - 1});
      }
    }

    function gotoPrev() {
      var state = getNavState();
      if (state.canGotoPrev) {
        dataView.setPagingOptions({pageNum: state.pagingInfo.pageNum - 1});
      }
    }

    function gotoNext() {
      var state = getNavState();
      if (state.canGotoNext) {
        dataView.setPagingOptions({pageNum: state.pagingInfo.pageNum + 1});
      }
    }

    function constructPagerUI() {
        
        if(op.pagerType==undefined || op.pagerType!="link"){
            pagerType = "select";
        }
        else{
            pagerType = "link";
        }
        if(op.pageSize==undefined || op.pageSize.length==0){
            pagerNum = [10,20,50,100,500];
        }
        else{
            pagerNum = op.pageSize;
        }

        if(op.defaultPagesize==undefined || pagerNum.indexOf(op.defaultPagesize)==-1){
            defaultSelected = pagerNum[0];
        }
        else{
            defaultSelected = op.defaultPagesize;
        }

        
        dataView.defaultPagesize(defaultSelected);
        
        

        
      $container.empty();

      var $nav = $("<span class='slick-pager-nav' />").appendTo($container);
      var $settings = $("<span class='slick-pager-settings' />").appendTo($container);
      $status = $("<span class='slick-pager-status' />").appendTo($container);
      $gotopage = $("<span class='slick-pager-goto' style='padding: 2px;' />").appendTo($container);
      
      

      if(pagerType == "link")
      {
        var linkItem ="";
        for(op = 0; op<pagerNum.length; op++){
            linkItem = linkItem+"<a data="+pagerNum[op]+">"+pagerNum[op]+"</a>";
        }

          
        $settings
            .append("<span class='slick-pager-settings-expanded'>Show: "+linkItem+"</span>");
//$settings.append("<span class='slick-pager-settings-expanded'>Show: <a data=0>All</a><a data='-1'>Auto</a><a data=25>25</a><a data=50>50</a><a data=100>100</a></span>");

        $settings.find("a[data]").click(function (e) {
          var pagesize = $(e.target).attr("data");
          if (pagesize != undefined) {
            if (pagesize == -1) {
              var vp = grid.getViewport();
              setPageSize(vp.bottom - vp.top);
            } else {
              setPageSize(parseInt(pagesize));
            }
          }
        });
      }
      if(pagerType == "select")
      {
        var selectOption ="";
        for(op = 0; op<pagerNum.length; op++){
            selectOption = selectOption+"<option data='"+pagerNum[op]+"' value='"+pagerNum[op]+"'>"+pagerNum[op]+"</option>";
        }
        $settings
            .prepend("<span class='slick-pager-settings-expanded'>Show: <select class='pagerTypeSelect'>"+selectOption+"</select></span>");


        $settings.find("select").change(function (e) {
          var pagesize = $(e.target).val();
          if (pagesize != undefined) {
            if (pagesize == -1) {
              var vp = grid.getViewport();
              setPageSize(vp.bottom - vp.top);
            } else {
              setPageSize(parseInt(pagesize));
            }
          }
        });
      }
      
        if(op.buttons != undefined && op.buttons==true){
        
      
              $settings .append("<span class='slick-pager-settings-expanded'><input type='button' class='savebtn' value='Save' style='color:#8C8A8C' /></span>");
              
              $container.on("click", ".savebtn", function(e) {
                    if(dataView.save())
                    {
                        $(".savebtn",$container).removeClass("savechanged");
                            
                    }
               });
               
               
               $settings .append("<span class='slick-pager-settings-expanded'><input type='button' class='reload' value='Reload' style='color:#000000' /></span>");
              
              $container.on("click", ".reload", function(e) {
                  if(confirm("All unsaved change will be deleted.\nDo You want to continue ?")){
                    if(dataView.cancelChange())
                    {
                        $(".savebtn",$container).removeClass("savechanged");
                    }
                  }
              });
      }
      

      var icon_prefix = "<span class='ui-state-default ui-corner-all ui-icon-container'><span class='ui-icon ";
      var icon_suffix = "' /></span>";

      $(icon_prefix + "ui-icon-lightbulb" + icon_suffix)
          .click(function () {
            $(".slick-pager-settings-expanded").toggle()
          })
          .appendTo($settings);

      $(icon_prefix + "ui-icon-seek-first" + icon_suffix)
          .click(gotoFirst)
          .appendTo($nav);

      $(icon_prefix + "ui-icon-seek-prev" + icon_suffix)
          .click(gotoPrev)
          .appendTo($nav);

      $(icon_prefix + "ui-icon-seek-next" + icon_suffix)
          .click(gotoNext)
          .appendTo($nav);

      $(icon_prefix + "ui-icon-seek-end" + icon_suffix)
          .click(gotoLast)
          .appendTo($nav);

      $container.find(".ui-icon-container")
          .hover(function () {
            $(this).toggleClass("ui-state-hover");
          });

      $container.children().wrapAll("<div class='slick-pager' />");
    }


    function updatePager(pagingInfo) {
      var state = getNavState();

      $container.find(".slick-pager-nav span").removeClass("ui-state-disabled");
      if (!state.canGotoFirst) {
        $container.find(".ui-icon-seek-first").addClass("ui-state-disabled");
      }
      if (!state.canGotoLast) {
        $container.find(".ui-icon-seek-end").addClass("ui-state-disabled");
      }
      if (!state.canGotoNext) {
        $container.find(".ui-icon-seek-next").addClass("ui-state-disabled");
      }
      if (!state.canGotoPrev) {
        $container.find(".ui-icon-seek-prev").addClass("ui-state-disabled");
      }

      if (pagingInfo.pageSize == 0) {
        var totalRowsCount = dataView.getItems().length;
        var visibleRowsCount = pagingInfo.totalRows;
        if (visibleRowsCount < totalRowsCount) {
          $status.text("Showing " + visibleRowsCount + " of " + totalRowsCount + " rows");
        } else {
          $status.text("Showing all " + totalRowsCount + " rows");
        }
        $status.text("Showing all " + pagingInfo.totalRows + " rows");
      } else {
        $status.text("Showing page " + (pagingInfo.pageNum + 1) + " of " + pagingInfo.totalPages);
        $gotopage.html("Go to Page: <input type='text' style='width:50px; height: 15px;' class='gttopage' />");
      }
    }
    
    $container.on("keyup", ".gttopage", function(e) {
        if(e.which==13){
            pagen = $.trim($(this).val());
            if(pagen!='' && parseInt(pagen)>0)
            {
                gotoPage(parseInt(pagen)-1);
            }
        }
    });

    init();
  }

  // Slick.Controls.Pager
  $.extend(true, window, { Slick:{ Controls:{ Pager:SlickGridPager }}});
})(jQuery);
