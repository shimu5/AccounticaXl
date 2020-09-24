<?php $this->heading('h1', 'Bank Balance Summary') ?>
<?php $this->inherits('layouts/default.php') ?>

<?php $this->block('content') ?>
<div class="zform">
    <?php echo $this->form('Bank');?>
    <div class="fieldset top">
        <div class="ig5">
            <div class="legend">Bank Balance Summary</div>
            <table>
                <thead>
                 <tr>
                    <th>Bank Name</th>
                    <th>Balance</th>
                    <th>Ex Rate</th>
                    <th class="align_right">In Case (BDT)</th>
                 </tr>
                </thead>
                <tfoot>
                 <tr>
                    <td colspan="3" class="border_remove_left border_remove_bottom">&nbsp;</td>
                    <td class="align_right text_bold">4800.00 BDT</td>
                 </tr>
                </tfoot>
                <tbody>
                 <tr>
                    <td>Asia Bank Ltd</td>
                    <td>2000.00 BDT</td>
                    <td rowspan="4">1.00</td>
                    <td class="align_right">2000.00 BDT</td>
                 </tr>                 
                 <tr>
                    <td>Asia Bank Ltd</td>
                    <td>500.00 BDT</td>
                    <td class="align_right">500.00 BDT</td>
                 </tr>                 
                 <tr>
                    <td>Asia Bank Ltd</td>
                    <td>2000.00 BDT</td>
                    <td class="align_right">2000.00 BDT</td>
                 </tr>                 
                 <tr>
                    <td>Asia Bank Ltd</td>
                    <td>300.00 BDT</td>
                    <td class="align_right">300.00 BDT</td>
                 </tr>                 
                </tbody>
           </table>
            
            <table>                
                <tfoot>
                 <tr>
                    <td colspan="3" class="border_remove_left border_remove_bottom">&nbsp;</td>
                    <td class="align_right text_bold">4000.00 BDT</td>
                 </tr>
                </tfoot>
                <tbody>
                 <tr>
                    <td>Jupitar Bank Ltd</td>
                    <td>200.00 BDT</td>
                    <td rowspan="4">80.00</td>
                    <td class="align_right">1600.00 BDT</td>
                 </tr>                 
                 <tr>
                    <td>Tulip Bank Ltd</td>
                    <td>300.00 BDT</td>
                    <td class="align_right">2400.00 BDT</td>
                 </tr>                                 
                </tbody>
           </table>
        </div>
    </div>    
    <?php echo $this->endform(); ?>
</div>
<style type="text/css">
    table{
        margin: 8px;
    }
    table tr td{
        width: 600px;
        border: 1px solid #DAD0C4;
        padding: 5px;
        text-align: center;
    }
    table thead tr th{       
        border: 1px solid #DAD0C4;
        background-color: #CCC;
        width: 600px;
        padding: 5px;
        text-align: center;
    }
    .border_remove_left{    border-left: none;  }
    .border_remove_bottom{    border-bottom: none;  }
    .align_right{ text-align: right; }
    .text_bold{ font-weight: bold; }
</style>

<?php $this->endblock('content') ?>