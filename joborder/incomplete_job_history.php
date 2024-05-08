<?php
define("REPORT_FILE", basename(dirname(__FILE__))."/print_".basename(__FILE__));
?>
<script type="text/javascript">
function printIframe(id)
{
    var iframe = document.frames ? document.frames[id] : document.getElementById(id);
    var ifWin = iframe.contentWindow || iframe;
    iframe.focus();
    ifWin.printPage();
    return false;
}
</script>

<form name="_form" action="" method="post">
<div class=form_layout>
    <div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">       
                    
        <div style="display:inline-block;">
            From Date: <br />
            <input type="text" class="datepicker textbox3" title="Please enter date"  name="from_date" readonly='readonly'  value="<?=$_REQUEST['from_date']?>">
        </div>
        
        <div style="display:inline-block;">
            To Date : <br />
            <input type="text" class="datepicker textbox3" title="Please enter date"  name="to_date" readonly='readonly'  value="<?=$_REQUEST['to_date']?>">
        </div>

        <div style="display:inline-block;">
            Job : <br>
            <?=lib::getTableAssoc($_REQUEST['job_id'],'job_id',"Select Job","select * from ".DB_HE.".jobs order by job asc ",'job_id','job')?>
        </div>                     

        <div style="display:inline-block;">
            Conducted by : <br>
            <input type="text" class="textbox ac-employee" name="employee_name" value="<?=$_REQUEST['employee_name']?>">
            <input type="hidden" name="conducted_by" value="<?=( ($_REQUEST['employee_name']) ? $_REQUEST['conducted_by'] : '' )?>" > 
        </div>                           
    </div>
    <div class="module_actions">
        <input type="submit" value="Generate Report"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
    <?php if( !empty($_REQUEST['from_date']) ) { ?>
        <iframe id="JOframe" name="JOframe" frameborder="0" src="<?=REPORT_FILE?>?
        job_id=<?=$_REQUEST['job_id']?>&
        from_date=<?=$_REQUEST['from_date']?>&
        to_date=<?=$_REQUEST['to_date']?>&
        conducted_by=<?=( ($_REQUEST['employee_name']) ? $_REQUEST['conducted_by'] : '' )?>&
        " width="100%" height="500">
        </iframe>
    <?php } ?>
    </div>
</div>
</form>
<script type="text/javascript">
jQuery(".ac-employee").autocomplete({
    source: "autocomplete/employees.php",
    minLength: 2,
    select: function(event, ui) {
        jQuery(this).val(ui.item.value);
        jQuery(this).next().val(ui.item.id);
    }
});
</script>