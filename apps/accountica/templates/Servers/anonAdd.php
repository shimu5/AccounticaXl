<?php $this->heading('h1', 'New Server') ?>
<?php $this->inherits('layouts/default.php') ?>

<?php $this->block('content') ?>
<div class="zform">
    <?php echo $this->form('Server');?>
    <div class="fieldset top">
        <div class="ig5">
            <div class="legend">Server Information</div>
            <?php echo $this->text('ip', 'IP', array('class'=>' w5')) ?>
            <?php echo $this->text('ip_alias', 'Alias', array('class'=>' w5')) ?>
            <?php echo $this->text('host', 'DB User') ?>
            <?php echo $this->text('port', 'Port') ?>
            <?php echo $this->text('password', 'DB Password') ?>
            <?php echo $this->text('db_name', 'Database') ?>
            <?php echo $this->select('flag', 'Enabled ?',array(1=>'Yes',0=>'No')) ?>
            <?php echo $this->select('type', 'Type ?',array(1=>'Server')) ?>
            <?php //echo $this->hidden('parent_id', array('value'=>'0')) ?>
        </div>

        <div class="ig5">
            <div class="legend">Help Tips</div>
            <div class="main_box">
                <div class="inner_panel" id="inner_panel_1">
                    <p>If you want to synchronize data with this server, you have to permit accounticaVL server ip (assume 192.168.1.1) into this server's mysql ( also require to permit accounticaVL ip into firewall to access mysql server port e.g 3306).</p>
                </div>
                <div class="inner_panel" id="inner_panel_2">
                    <p>Assume you have entered following info into the current page :</p>
                </div>
                <div class="inner_panel" id="inner_panel_3">
                    <p>IP: 192.168.1.2<br/>alias: server2<br/>db user: accounts<br/>password : accvl<br/>port:3306<br/>database: voipswitch</p>
                </div>
                <div class="inner_panel" id="inner_panel_4">
                    <p>Then, you have to run following command into the mysql of 192.168.1.2 </p>
                </div>
                <div class="inner_panel" id="inner_panel_4">
                    <p>use mysql;<br/>GRANT all ON *.* TO accounts@'192.168.1.1' IDENTIFIED BY 'accvl';<br/>FLUSH privileges;</p>
                </div>
            </div>
        </div>
    </div>
    <div class="fieldset action">
        <?php echo $this->button('submit', 'Save');?>
        <?php echo $this->button('button', 'Cancel');?>
    </div>

    <?php echo $this->endform(); ?>

</div>
<style type="text/css">
    .inner_panel p{
        padding: 5px !important;
    }
    .main_box{
        width: auto;
        margin: 0px 8px;
        padding: 0px 4px 4px;
        background-color: whitesmoke;
        border: 0px none;
    }
</style>
<?php $this->endblock('content') ?>

