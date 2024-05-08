<?php
	require_once('../my_Classes/options.class.php');
	include_once("../conf/ucs.conf.php");
	require_once("dprc_options.class.php");
	
	$options=new options();	
	
	$application_id = $_REQUEST['application_id'];
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
<title>DPRC DOWN PAYMENT LEDGER</title>
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
	.table-content td{
		padding:1px 5px;	
	}
	.table-content tr:last-child td{
		border-top:2px solid #000;
		border-bottom:2px solid #000;	
		font-weight:bold;
	}
	.table-content tr td:nth-child(n+3){
		text-align:right;	
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
        	Downpayment Ledger
            <span style="float:right;"><?=date("F j, Y H:i:s")?></span> 
        </div>           
        <div>
        	<?php include_once('print_dprc_report_heading.php') ?>
        </div>
        <div class="content" style="">
        	<table class="table-content">
            	<tr>
                	<td>OR Date</td>
                    <td>OR No.</td>
                    <td>Amount</td>
                    <td>Principal</td>
                    <td>Penalty</td>
                    <td>Late Days</td>
                    <td>Outbal</td>
                </tr>
                <tr>
                	<td colspan="6">Balance</td>
                    <td class="align-right"><?=number_format($r['dp_amount'],2)?></td>
                </tr>
                <?php
				$result = mysql_query("
					select
						or_date, dp.remarks, dp_principal, dp_penalty, dp_days, dp_outbal, dp.remarks, or_no, dprc_dp_id
					from
						dprc_dp as dp, application as a, dprc_payment as p
					where
						dp.dprc_payment_id = p.dprc_payment_id
					and
						a.application_id = p.application_id
					and				
						dp.application_id = '$application_id'
					order by
						or_date asc, or_no asc, dp_outbal desc
				") or die(mysql_error());
				
			
				$records = mysql_num_rows($result);
				$total_amount = $total_principal = $total_penalty = 0;
				while($r = mysql_fetch_assoc($result)){
					$total_amount += ($r['dp_principal'] +  $r['dp_penalty']);
					$total_principal += $r['dp_principal'];
					$total_penalty += $r['dp_penalty'];
					
					
                ?>
                <tr>
                	<td><?=$r['or_date']?></td>
                    <td><?=($r['remarks']) ? $r['remarks'] : $r['or_no']?></td>
                    <td class="align-right"><?=number_format($r['dp_principal'] +  $r['dp_penalty'],2)?></td>
                    <td class="align-right"><?=number_format($r['dp_principal'],2)?></td>
                    <td class="align-right"><?=number_format($r['dp_penalty'],2)?></td>
                    <td><?=$r['dp_days']?></td>
                    <td class="align-right"><?=number_format($r['dp_outbal'],2)?></td>
                </tr>
                <?php } ?>
                <tr>
                	<td>Records : <?=$records?></td>
                    <td></td>
                    <td></td>
                    <td class="align-right"><?=number_format($total_amount,2)?></td>
                    <td class="align-right"><?=number_format($total_principal,2)?></td>
                    <td class="align-right"><?=number_format($total_penalty,2)?></td>
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