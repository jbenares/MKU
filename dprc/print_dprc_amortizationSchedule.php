<?php
	require_once('../my_Classes/options.class.php');
	include_once("../conf/ucs.conf.php");
	require_once("dprc_options.class.php");
	
	$options=new options();
	$application_id 	= $_REQUEST['application_id'];
	
	$result = mysql_query("
		select 
			* 
		from 
			application 
		where 
			application_id = '$application_id'
	") or die(mysql_error());
	$r = mysql_fetch_assoc($result);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>DPRC AMORTIZATION SCHEDULE REPORT</title>
<script>
print();
function printPage() { print(); } //Must be present for Iframe printing
</script>
<link rel="stylesheet" type="text/css" href="../css/dprc_print.css"/>
<style type="text/css">
	.table-content{
		margin-top:5px;
		width:100%;	
	}
	.table-content tr:nth-child(1) td{
		border-top:1px solid #000;
		border-bottom:1px solid #000;
	}
	.table-content td:nth-child(n+4){
		text-align:right;	
	}
	/*
	.table-content tr:last-child td{
		border-top:2px solid #000;
		border-bottom:2px solid #000;	
		font-weight:bold;
	}
	*/
</style>
</head>
<body>
<div class="container">
     <div><!--Start of Form-->
     	
     	<div style="font-weight:bolder; border-bottom:1px solid #000;">
        	AMORTIZATION SCHEDULE
            <span style="float:right;"><?=date("F j, Y H:i:s")?></span> 
        </div>           
        <div>
        	<?php include_once('print_dprc_report_heading.php') ?>
        </div>
        <div class="content" style="">
        	<?php $l = dprc::getAmortSched($application_id); ?>
        	<table class="table-content">
            	<tr>
                	<td>PERIOD</td>
                    <td>DUE DATE</td>
                    <td>PRINCIPAL</td>
                    <td>INTEREST</td>
                    <td>AMOUNT</td>
                </tr>
                <tr>
                	<td>BEG. BAL.</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><?=dprc::numform($r['net_loan'])?></td>
                </tr>
                <?php foreach($l as $ledger){ ?>
                <tr>
                	<td><?=$ledger['period']?></td>
                    <td><?=$ledger['due_date']?></td>
                    <td><?=dprc::numform($ledger['principal'])?></td>
                    <td><?=dprc::numform($ledger['interest'])?></td>
                    <td><?=dprc::numform($ledger['outbal'])?></td>
                </tr>
                <?php } ?>

           	</table>
        </div><!--End of content-->
        
        <table cellspacing="0" cellpadding="5" align="center" width="98%" style="border:none; text-align:center; margin-top:10px;" class="summary">
            <tr>
                <td>Prepared by:<p>
                    <input type="text" class="line_bottom" /><br>Collection/Cashier</p></td>
                <td>Checked by:<p>
                    <input type="text" class="line_bottom" /><br>Database Administrator</p></td>
                <td>Noted by:<p>
                    <input type="text" class="line_bottom" /><br>Database Administrator</p></td>
            </tr>
        </table>
    </div><!--End of Form-->
</div>
</body>
</html>