<?php

	$b 			= $_REQUEST['b'];
	$creditcardID		= $_REQUEST['creditcardID'];
	$employee_keyword 	= $_REQUEST['employee_keyword'];
	$fdate 		= $_REQUEST['fdate'];
	$tdate 		= $_REQUEST['tdate'];
	$client		= $_REQUEST['client'];
	$search_keyword	= $_REQUEST['search_keyword'];
	$filter 		= $_REQUEST['filter'];
	
?>
<style>

.prev_table {
	padding: 3px;
	width: 480px;
}

.prev_table td {
	border-bottom: 1px #C0C0C0 dashed;
	padding: 3px;
	color: #5e6977;
}

</style>
<script language="JavaScript" src="scripts/cwcalendar/calendar.js"></script>
<link rel="stylesheet" href="scripts/cwcalendar/cwcalendar.css"></link>
<script>
function printIframe(id)
{
    var iframe = document.frames ? document.frames[id] : document.getElementById(id);
    var ifWin = iframe.contentWindow || iframe;
    iframe.focus();
    ifWin.printPage();
    return false;
}
</script>
<form name="_form" id="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/application_cascade.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">
    	<!--
    	Employee : (Leave blank to show all)<br />
    	<input type="text" autocomplete="off" id="employee_keyword" name="employee_keyword" class="textbox" onclick="this.select();" onkeyup="xajax_show_employees(document.getElementById('employee_keyword').value);toggleBox('demodiv',1);" onmouseover="Tip('Type a keyword to search employee.');" value="<?=$employee_keyword;?>" style="color:#0000CC" />
            <div id='demodiv3' class='demo3'><a style='cursor: pointer' onclick="toggleBox('demodiv3',0);">
            <img src='images/close.gif' style='position:absolute;right:-4px;top:-4px;'></a><br />
            <div id='employeediv' style='overflow-y:scroll;overflow-x:hidden;height:250px;border-top:1px #C0C0C0 dashed;'></div></div> 
            <input type="hidden" name="empID" id="empID" value="<?=$empID;?>" />    
        -->
        <?php //echo $options->client_options($client) ?>                                         
            <input type="text" name="search_keyword" class="textbox" value="<?=$search_keyword;?>">
            <select name="filter" class="select">
	            <option value="e.employee_lname" <?php if($filter == "e.employee_lname") echo "selected='selected'"?>>Last Name</option>
                <option value="e.employeeNUM" <?php if($filter == "e.employeeNUM") echo "selected='selected'"?>>Employee Number</option>
                <option value="project_name" <?php if($filter == "project_name") echo "selected='selected'"?>>Project Name</option>
           </select>

            <div class="inline">
                Hired From Date :<br>
                <input type="text" class="textbox datepicker" name="hired_from_date" id="hired_from_date" value="<?=$_REQUEST['hired_from_date']?>">
            </div>

            <div class="inline">
                Hired To Date :<br>
                <input type="text" class="textbox datepicker" name="hired_to_date" id="hired_to_date" value="<?=$_REQUEST['hired_to_date']?>">
            </div>

            <input type="submit" name="b" class="buttons" value="Display">
        	<input type="button" name="b" value="Print" onclick="printIframe('JOframe');" class="buttons" />
	</div>
    <?php 
    if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; 
    else if ($b=='Display'){
    ?>
    <div style="padding:3px; text-align:center;">
    	<iframe id="JOframe" name="JOframe" frameborder="0" src="payroll/print_employee_record.php?search_keyword=<?=$search_keyword;?>&filter=<?=$filter;?>&hired_from_date=<?=$_REQUEST['hired_from_date']?>&hired_to_date=<?=$_REQUEST['hired_to_date']?>" width="100%" height="500">
        </iframe>
    </div>
    <?php } ?>
</div>
</form>