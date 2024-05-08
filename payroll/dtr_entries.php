<?php

	$b 			= $_REQUEST['b'];
	$fdate 		= $_REQUEST['fdate'];
	$tdate 		= $_REQUEST['tdate'];
	$companyID		= $_REQUEST['companyID'];
	$projects		= $_REQUEST[projects];
	$employeeID 	= $_REQUEST['employeeID'];
	$employee_keyword = $_REQUEST['employee_keyword'];
	$ltr		= $_REQUEST['ltr'];
	$counta		= $_REQUEST['counta'];
	$countb		= $_REQUEST['countb'];
	$employee_type_id = $_REQUEST[employee_type_id];
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
<script language="JavaScript" src="scripts/cwcalendar/calendar.js"></script>
<?php
$ops = $_REQUEST['ops'];
?>
<link rel="stylesheet" href="scripts/cwcalendar/cwcalendar.css"></link>
<form name="_form" id="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/application_cascade.png'><?=$transac->getMname($view);?></div>
    <div class="module_actions">
	<!--
	<img src="images/user_orange.png" />
            <input type="text" autocomplete="off" id="employee_keyword" name="employee_keyword" class="textbox" onclick="this.select();" onkeyup="xajax_show_employees(document.getElementById('employee_keyword').value);toggleBox('demodiv',1);" onmouseover="Tip('Type a keyword to search employee.');" value="<?=$employee_keyword;?>" style="color:#0000CC" />
		-->
            <div id='demodiv3' class='demo3'><a style='cursor: pointer' onclick="toggleBox('demodiv3',0);">
            <img src='images/close.gif' style='position:absolute;right:-4px;top:-4px;'></a><br />
            <div id='employeediv' style='overflow-y:scroll;overflow-x:hidden;height:250px;border-top:1px #C0C0C0 dashed;'></div></div> 
            <input type="hidden" name="employeeID" id="employeeID" value="<?=$employeeID;?>" />
    </div>
    <div class="module_actions">
    	Date Range
    	<input type="text" name="fdate" id="fdate" class="textbox" onmouseover="Tip('Choose a date.');" value="<?=$fdate;?>" onclick="fPopCalendar('fdate')" readonly="readonly" />       
        To        
       <input type="text" name="tdate" id="tdate" class="textbox" onmouseover="Tip('Choose a date.');" value="<?=$tdate;?>" onclick="fPopCalendar('tdate')" readonly="readonly" />

	<!--<?php echo $options->getTableAssoc($companyID,'companyID','Select Company',"select * from companies where company_void = '0' order by company_name asc",'companyID','company_abbrevation'); ?>-->

	<?php echo $randy_options->projects_options($projects); ?>
	<?phpecho $options->getTableAssoc($employee_type_id,'employee_type_id','Select Employee Type',"select * from employee_type where employee_type_void = '0' order by employee_type asc",'employee_type_id','employee_type')?></td>
        </tr>
	<p>
	Last Name : <input type="text" name="ltr" value="<?=$ltr;?>" class="textbox3" />
	Employee Start : <input type="text" name="counta" value="<?=$counta;?>" class="textbox3" />
	Count : <input type="text" name="countb" value="<?=$countb;?>" class="textbox3" />
	Print Type: 
	<select name="ops" class="textbox">
		<?php if($_REQUEST['ops'] == '1'){ ?>
		<option selected value="1">Default Printing</option>
		<option value="2" >Night Shift Print - Type 1</option>
		<option value="3" >Night Shift Print - Type 2</option>
		<?php }else if($_REQUEST['ops'] == '2'){ ?>
		<option value="1">Default Printing</option>
		<option selected value="2" >Night Shift Print - Type 1</option>
		<option value="3" >Night Shift Print - Type 2</option>		
		<?php }else if($_REQUEST['ops'] == '3'){ ?>	
		<option value="1">Default Printing</option>
		<option value="2" >Night Shift Print - Type 1</option>
		<option selected value="3" >Night Shift Print - Type 2</option>	
		<?php }else{ ?>
		<option selected value="1">Default Printing</option>
		<option value="2" >Night Shift Print - Type 1</option>
		<option value="3" >Night Shift Print - Type 2</option>	
		<?php } ?>
	</select>
	</div>	
	<div class="module_actions"> 
		<input type="submit" name="b" class="buttons" value="Display" />
		<input type="button" name="b" value="Print" onclick="printIframe('JOframe');" class="buttons" />
	</div>
    <?php 
    if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; 
    if($ops == '1'){
    ?>
    <div style="padding:3px; text-align:center;">
    	<iframe id="JOframe" name="JOframe" frameborder="0" src="payroll/print_dtr_entries.php?fdate=<?=$fdate;?>&tdate=<?=$tdate;?>&projects=<?=$projects;?>&companyID=<?=$companyID;?>&employeeID=<?=$employeeID;?>&ltr=<?=$ltr;?>&counta=<?=$counta;?>&countb=<?=$countb;?>&employee_type_id=<?=$employee_type_id?>" width="100%" height="500">
        </iframe>
    </div>
    <?php }else if($ops == '2'){ ?>
    <div style="padding:3px; text-align:center;">
    	<iframe id="JOframe" name="JOframe" frameborder="0" src="payroll/print_dtr_entries_night.php?fdate=<?=$fdate;?>&tdate=<?=$tdate;?>&projects=<?=$projects;?>&companyID=<?=$companyID;?>&employeeID=<?=$employeeID;?>&ltr=<?=$ltr;?>&counta=<?=$counta;?>&countb=<?=$countb;?>&employee_type_id=<?=$employee_type_id?>" width="100%" height="500">
        </iframe>
    </div>		
	<?php }else if($ops == '3'){ ?>
    <div style="padding:3px; text-align:center;">
    	<iframe id="JOframe" name="JOframe" frameborder="0" src="payroll/print_dtr_entries_night2.php?fdate=<?=$fdate;?>&tdate=<?=$tdate;?>&projects=<?=$projects;?>&companyID=<?=$companyID;?>&employeeID=<?=$employeeID;?>&ltr=<?=$ltr;?>&counta=<?=$counta;?>&countb=<?=$countb;?>&employee_type_id=<?=$employee_type_id?>" width="100%" height="500">
        </iframe>
    </div>		
	<?php } ?>
</div>
</form>