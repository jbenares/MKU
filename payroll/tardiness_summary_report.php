<?php
require_once(dirname(__FILE__).'/../library/lib.php');
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
            Employee : <br />
            <?=lib::getTableAssoc($_REQUEST['employeeID'],'employeeID','All Employees',"select * from employee order by employee_lname , employee_fname",'employeeID','employee_lname',array('employee_lname','employee_fname'))?>
        </div>        

		<div style="display:inline-block;">
            Company : <br />
            <?=lib::getTableAssoc($_REQUEST['companyID'],'companyID','All Companies',"select * from companies where company_void = '0' order by company_name asc",'companyID','company_name')?>
        </div>        

        <div style="display:inline-block;">
        	Project : <br>
        	<?=lib::getTableAssoc($_REQUEST['project_id'],'project_id',"All Projects","select * from projects order by project_name asc",'project_id','project_name')?>
       	</div>               
    </div>
    <div class="module_actions">
    	<input type="submit" value="Generate Report"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
    <?php if(!empty($_REQUEST['from_date']) && !empty($_REQUEST['to_date']) ) { ?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="payroll/print_tardiness_summary_report.php?        
        from_date=<?=$_REQUEST['from_date']?>&
        to_date=<?=$_REQUEST['to_date']?>&
        companyID=<?=$_REQUEST['companyID']?>&
        project_id=<?=$_REQUEST['project_id']?>&
        employeeID=<?=$_REQUEST['employeeID']?>
        " width="100%" height="500">
        </iframe>
    <?php } ?>
    </div>
</div>
</form>
<script type="text/javascript">
jQuery(".accountability").autocomplete({
    source: "list_accountability.php",
    minLength: 1,
    select: function(event, ui) {
        jQuery(this).val(ui.item.value);
        jQuery(this).next().val(ui.item.rr_detail_id);        
    }
});
</script>


