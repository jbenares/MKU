<style type="text/css">
.align-right{
	text-align:right;
}
.cp_table{
	width:50%;
	border-collapse:collapse;
}
.cp_table tr th {
	border-top:1px solid #000;
	border-bottom:1px solid #000;
}
.payroll-table{
	border-collapse:collapse;	
}
.payroll-table tr:first-child{
	border-top:1px solid #000;
	border-bottom:1px solid #000;	
	font-weight:bold;
}
.payroll-table td{
	padding:3px 10px;
}
</style>
<?php
	$b						= $_REQUEST['b'];
	$user_id				= $_SESSION['userID'];	
	$from_date				= $_REQUEST['from_date'];
	$to_date				= $_REQUEST['to_date'];
	$companyID				= $_REQUEST['companyID'];
	$project_id				= $_REQUEST['project_id'];
	$employee_type_id		= $_REQUEST['employee_type_id'];
	$payroll_sequence_id	= $_REQUEST['payroll_sequence_id'];	
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
<?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
<form name="header_form" id="header_form" action="" method="post">
<div class="module_actions">	
	<table>
    	<tr>
        	<td>FROM DATE</td>
            <td><input type="text" class="textbox datepicker" name="from_date" value="<?=$from_date?>"  /></td>
        </tr>
        <tr>
        	<td>TO DATE</td>
            <td><input type="text" class="textbox datepicker" name="to_date" value="<?=$to_date?>" /></td>
        </tr>
        <tr>
        	<td>COMPANY</td>
            <td><?=$options->getTableAssoc($companyID,'companyID','Select Company',"select * from companies where company_void = '0' order by company_name asc",'companyID','company_name')?></td>
        </tr>
        <tr>
        	<td>PROJECT</td>
            <td><?=$options->getTableAssoc($project_id,'project_id','Select Project',"select * from projects order by project_name asc",'project_id','project_name')?></td>
        </tr>
    </table>
</div>
<div class="module_actions">
	<input type="submit" name="b" value="Generate Report" />
    <input type="button" value="Print" onclick="printIframe('JOframe');" />
</div>
<div style="padding:3px; text-align:center;" id="content">
     <?php	if($b == "Generate Report"){ ?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="payroll/print_payslip.php?
        	from_date=<?=$from_date?>&
            to_date=<?=$to_date?>&
            project_id=<?=$project_id?>&
            companyID=<?=$companyID?>
            " width="100%" height="500">
        </iframe>
    <?php } ?>
    </div>
</form>

<script type="text/javascript">
jQuery(function(){	

});
</script>
	