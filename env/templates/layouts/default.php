<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>AccounticaXL 0.1</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
        <!-- Common CSS  -->
        <?php echo $this->css('reset.css'); ?>
        <?php echo $this->css('base.css'); ?>
        <?php echo $this->css('zform.css'); ?>
        <?php //echo $this->css('toolbar');?>
        <?php echo $this->css('table.css'); ?>
        <?php echo $this->css('furina.css'); ?>
        <?php //echo $this->css('mainmenu');?>
        <?php //echo $this->css('mainmenu.simple');?>
        <?php echo $this->css('icon.css'); ?>
        <?php echo $this->css('fleximan.css'); ?>
        <?php //echo $this->css('jquery-ui-1.8.16.custom.css');?>

        <!-- CSS for date pick  -->
        <?php echo $this->css('jquery-ui.css'); ?>
        <?php echo $this->css('jquery-ui-timepicker-addon.css'); ?>



        <!-- Css for slick grid  -->
        <?php echo $this->css('slickgrid/slick.grid.css'); ?>
        <?php echo $this->css('slickgrid/slick.pager.css'); ?>
        <?php echo $this->css('slickgrid/slick.columnpicker.css'); ?>
        <?php echo $this->css('slickgrid/slick_common.css'); ?>

        <!-- Common JS  -->
        <?php echo $this->js('jquery-1.7.1.min.js'); ?>
        <?php //echo $this->js('slickgrid/jquery-ui-1.8.16.custom.min.js');?>

        <!-- JS for date pick  -->
        <?php echo $this->js('jquery-ui.min.js'); ?>
        <?php echo $this->js('jquery-ui-timepicker-addon.js'); ?>
        <!-- JS for spiner -->
        <?php echo $this->js('jquery.mousewheel.js'); ?>
        <!-- JS for slick grid  -->
        <?php echo $this->js('slickgrid/firebugx.js'); ?>
        <?php echo $this->js('slickgrid/jquery.event.drag-2.2.js'); ?>
        <?php echo $this->js('slickgrid/slick.core.js'); ?>
        <?php echo $this->js('slickgrid/slick.formatters.js'); ?>
        <?php echo $this->js('slickgrid/slick.editors.js'); ?>
        <?php echo $this->js('slickgrid/slick.rowselectionmodel.js'); ?>
        <?php echo $this->js('slickgrid/slick.grid.ajax.js'); ?>
        <?php echo $this->js('slickgrid/slick.dataview.ajax.js'); ?>
        <?php echo $this->js('slickgrid/slick.pager.ajax.js'); ?>
        <?php echo $this->js('slickgrid/slick.columnpicker.ajax.js'); ?>
        <?php echo $this->js('slickgrid/slick_common.js'); ?>



        <script src="js/vendor/modernizr-2.6.2.min.js"></script>
    </head>
    <body>
        <!--[if lt IE 7]>
        <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <div class="header clearfix">
            <div class="bluebar">&nbsp;</div>

            <div class="logo">
                <h1>Accountica XL</h1>
            </div>

            <div class="userinfo">
                <?php $this->block('userinfo') ?>
                <div>
                    <div class="icon-notification"></div>
                    <div class="icon-balance">2115.00</div>
                    <div class="icon-user">zubair1416</div>
                </div>
                <div>
                    <div class="icon-preferences">PREFERENCES</div>
                    <div class="icon-usertype">RESELLER LOGOUT</div>
                </div>
                <?php $this->endblock('userinfo') ?>
            </div>

            <div class="menu">
                <ul class="clearfix">
                    <?php $this->block('menu') ?>
                    <li>Home</li>
                    <li>Accounts</li>
                    <li>Transactions</li>
                    <li>Synchronization</li>
                    <li>Settings</li>
                    <li>About</li>
                    <?php $this->endblock('menu') ?>
                </ul>
            </div>


        </div>

        <div class="container">
            <div class="container-inner">
                <div class="heading">
                    <?php $this->block('heading') ?>
                    <?php $this->heading('h1') ?>
                    <?php $this->endblock('heading') ?>

                    <div class="toolbar">
                        <?php $this->block('toolbar') ?>
                        <ul>
                            <li>NEW</li>
                            <li>LIST</li>
                            <li>DELETE</li>
                        </ul>
                        <?php $this->endblock('toolbar') ?>
                    </div>
                </div>

                <div class="left_panel">
                    <?php $this->block('left_panel') ?>
                    <?php $this->block('flash') ?>
                    <?php
                        $flashs = isset($_SESSION['FLASH']) ? $_SESSION['FLASH'] : array();
                        //pr($flashs);
                        if (!empty($flashs)) {
                            $this->e('<div class="flash wrapper">');
                            foreach ($flashs as $type => $flash) {
                    ?>
                                <div class="<?php $this->e($type) ?>"><?php $this->e($flash) ?></div>
                    <?php
                                $this->e('</div>');
                            }
                        }
                    ?>
                    <?php $this->endblock('flash') ?>

                    <?php $this->endblock('left_panel') ?>
                    </div>

                    <div class="content">
                    <?php $this->block('content') ?>


                    <?php $this->endblock('content') ?>


                </div>
            </div>
        </div>
        <div class="footer">

        </div>        
        <script type="text/javascript">
            // Date/Time picker Start
            $('.date').datepicker({
                dateFormat: 'yy-mm-dd'
            });
            $('.datetime').datetimepicker({
                dateFormat: 'yy-mm-dd',
                timeFormat: 'HH:mm:ss'
            });
            $('.time').timepicker({
                timeFormat: 'HH:mm:ss'
            });
            // Date/Time picker end

            // Spiner Start
            $(".int_val").spinner({
                    increment: 'fast', step: 1
//                    spin: function(event, ui) {
//                        if (ui.value > 20) {
//                            $(this).spinner("value", -20);
//                            return false;
//                        } else if (ui.value < -10) {
//                            $(this).spinner("value", 10);
//                            return false;
//                        }
//                    }
                });
                $('.double_val').spinner({ increment: 'fast', step: 0.01});
                $('.pos_double_val').spinner({min:0, increment: 'fast', step: 0.01});
                // // Spiner End
        </script>
        
        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <script type="text/javascript">window.jQuery || document.write('<script src="js/vendor/jquery-1.10.2.min.js"><\/script>')</script>
        <script type="text/javascript" src="js/plugins.js"></script>
        <script type="text/javascript" src="js/main.js"></script>
    </body>
</html>
