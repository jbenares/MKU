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
                  

        <fieldset style="border:1px solid #c0c0c0; display:inline-block;">
            <legend>Payroll Period #1</legend>
            <div style="display:inline-block;">
                Payroll Period From Date: <br />
                <input type="text" class="datepicker textbox3" title="Please enter date"  name="from_date" readonly='readonly'  value="<?=$_REQUEST['from_date']?>">
            </div>
        
            <div style="display:inline-block;">
                Payroll Period to Date : <br />
                <input type="text" class="datepicker textbox3" title="Please enter date"  name="to_date" readonly='readonly'  value="<?=$_REQUEST['to_date']?>">
            </div>
        </fieldset>
        
        <fieldset style="border:1px solid #c0c0c0; display:inline-block;">
            <legend>Payroll Period #2</legend>
            <div style="display:inline-block;">
                Payroll Period From Date: <br />
                <input type="text" class="datepicker textbox3" title="Please enter date"  name="from_date_2" readonly='readonly'  value="<?=$_REQUEST['from_date_2']?>">
            </div>
            
            <div style="display:inline-block;">
                Payroll Period to Date : <br />
                <input type="text" class="datepicker textbox3" title="Please enter date"  name="to_date_2" readonly='readonly'  value="<?=$_REQUEST['to_date_2']?>">
            </div>
        </fieldset>

        <div style="display:inline-block;">
        	Project : <br>
        	<?=lib::getTableAssoc($_REQUEST['project_id'],'project_id','Select Project',"select * from projects order by project_name asc",'project_id','project_name')?>
        </div>
        <div style="display:inline-block;">
            EMPLOYEE TYPE : <br>
            <?=lib::getTableAssoc($_REQUEST['employee_type_id'],'employee_type_id','Select Employee Type',"select * from employee_type where employee_type_void = '0' order by employee_type asc",'employee_type_id','employee_type')?>
        </div>

        <br>

        <div style="display:inline-block">
        	<input type="checkbox" name="display_sss" id="display_sss" value="1" <?php if($_REQUEST['display_sss']) echo "checked" ?>  > <label for="display_sss">Display SSS</label>
        </div>
        <div style="display:inline-block">
        	<input type="checkbox" name="display_pagibig" id="display_pagibig" value="1" <?php if($_REQUEST['display_pagibig']) echo "checked" ?>  > <label for="display_pagibig">Display Pagibig</label>
        </div>

        <div style="display:inline-block">
        	<input type="checkbox" name="display_philhealth" id="display_philhealth" value="1" <?php if($_REQUEST['display_philhealth']) echo "checked" ?>  > <label for="display_philhealth">Display Philhealth</label>
        </div>    

        <div style="display:inline-block">
        	<input type="checkbox" name="display_tax" id="display_tax" value="1" <?php if($_REQUEST['display_tax']) echo "checked" ?>  > <label for="display_tax">Display Taxes</label>
        </div>
                
      	<input type="submit" value="Generate Report"  />
        <input type="button" value="Print" onclick="printIframe('JOframe');" />
    </div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
    <?php if(!empty($_REQUEST['from_date']) && !empty($_REQUEST['to_date']) ) { ?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="payroll/print_report_contributions.php?       
        from_date=<?=$_REQUEST['from_date']?>&
        to_date=<?=$_REQUEST['to_date']?>&
        from_date_2=<?=$_REQUEST['from_date_2']?>&
        to_date_2=<?=$_REQUEST['to_date_2']?>&
        display_sss=<?=$_REQUEST['display_sss']?>&
        display_pagibig=<?=$_REQUEST['display_pagibig']?>&
        display_philhealth=<?=$_REQUEST['display_philhealth']?>&
        display_sss_loan=<?=$_REQUEST['display_sss_loan']?>&
        display_pagibig_loan=<?=$_REQUEST['display_pagibig_loan']?>&
        display_tax=<?=$_REQUEST['display_tax']?>&
        project_id=<?=$_REQUEST['project_id']?>&
        employee_type_id=<?=$_REQUEST['employee_type_id']?>
        " width="100%" height="500">
        </iframe>
    <?php } ?>
    </div>
</div>
</form>