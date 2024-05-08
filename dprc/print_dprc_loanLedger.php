<?php
	require_once('../my_Classes/options.class.php');
	include_once("../conf/ucs.conf.php");
	require_once("dprc_options.class.php");
	
	$options=new options();	
	
	
	function getBeginningBalance($date,$application_id){	
		$result = mysql_query("
			select
				* 
			from 
				dprc_payment as p, dprc_ledger as l
			where 
				l.dprc_payment_id = p.dprc_payment_id
			and
				application_id = '$application_id'
			and
				or_date < '$date'
			order by outbal asc
			limit 0,1
		") or die(mysql_error());	
		
		$r = mysql_fetch_assoc($result);
		return $r['outbal'];
	}
	
	
	$application_id 	= $_REQUEST['application_id'];
	$from_date			= $_REQUEST['from_date'];
	$to_date			= $_REQUEST['to_date'];
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
<title>DPRC LOAN LEDGER REPORT</title>
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
	.table-content tr:last-child td{
		border-top:2px solid #000;
		border-bottom:2px solid #000;	
		font-weight:bold;
	}
</style>
</head>
<body>
<div class="container">
     <div><!--Start of Form-->
	<div style="text-align:left; margin-bottom:5px; font-size:16px;">
		<img id="head_logo" src="../images/chead.png" style="display:inline-block; margin-right:20px;" width="64" height="64" />
		    <p style="display:inline-block; margin:0px; vertical-align:top;">
		        <strong><?=$title?></strong><br />
		        <?=$company_tel_no?><br />
		        <?=$company_address?>
		   	</p>
	    <hr style="margin:5px 0px; border-top:1px solid #000; border-bottom:none;"  />
	</div>

     	
     	<div style="font-weight:bolder; border-bottom:1px solid #000;">
        	Loan Ledger
            <span style="float:right;"><?=date("F j, Y H:i:s")?></span> 
        </div>           
        <div>
        	<?php include_once('print_dprc_report_heading.php') ?>
        </div>
        <div class="content" style="">
        	<table class="table-content">
            	<?php
				$result = mysql_query("
					select
						* 
					from 
						dprc_payment as p, dprc_ledger as l
					where 
						l.dprc_payment_id = p.dprc_payment_id
					and
						application_id = '$application_id'
					order by
						period asc, or_date asc, or_no asc
				") or die(mysql_error());
				
				$records = mysql_num_rows($result);
				?>
            	<tr>
                	<td>OR Date</td>
                    <td>OR No.</td>
                	<td>Period</td>    
                    <td>Amount</td>
                    <td>Principal</td>
                    <td>Interest</td>
                    <td>Penalty</td>
                    <td>Late Days</td>
                    <td>Outbal</td>
               	</tr>
                
                <!--<tr>
	                <td colspan="8">Beginning Balance</td>
                	<td class="align-right"><?=number_format(getBeginningBalance($from_date,$application_id),2)?></td>
                </tr> -->
               
				<?php 
				$total_amount = $total_principal = $total_interest = $total_penalty = 0;
				while($r = mysql_fetch_assoc($result)){ 
					$total_amount 		+= $r['principal'] + $r['interest'] + $r['penalty'];
					$total_principal 	+= $r['principal'];
					$total_interest 	+= $r['interest'];
					$total_penalty 		+= $r['penalty'];
				
				?>
                <tr>
                	<td><?=dprc::mdy($r['or_date'])?></td>
                    <td><?=$r['or_no']?></td>
                    <td><?=dprc::displayPeriod($r['period'])?></td>
                    <td><?=number_format($r['principal'] + $r['interest'] + $r['penalty'],2)?></td>
					<td><?=number_format($r['principal'],2)?></td>
                    <td><?=number_format($r['interest'],2)?></td>
                    <td><?=number_format($r['penalty'],2)?></td>
                    <td><?=$r['late_days']?></td>
                    <td><?=number_format($r['outbal'],2)?></td>
                </tr>
                <?php } ?>
                <tr>
                	<td>Records : <?=$records?></td>
                    <td></td>
                    <td>Summary:</td>
                    <td><?=number_format($total_amount,2)?></td>
                    <td><?=number_format($total_principal,2)?></td>
                    <td><?=number_format($total_interest,2)?></td>
                    <td><?=number_format($total_penalty,2)?></td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
        </div><!--End of content-->
        
        <!--<table cellspacing="0" cellpadding="5" align="center" width="98%" style="border:none; text-align:center; margin-top:10px;" class="summary">
            <tr>
                <td>Prepared by:<p>
                    <input type="text" class="line_bottom" /><br>Collection/Cashier</p></td>
                <td>Checked by:<p>
                    <input type="text" class="line_bottom" /><br>Database Administrator</p></td>
                <td>Noted by:<p>
                    <input type="text" class="line_bottom" /><br>Database Administrator</p></td>
            </tr>
        </table> -->
        <?php include_once("print_dprc_signatories.php") ?>
    </div><!--End of Form-->
</div>
</body>
</html>