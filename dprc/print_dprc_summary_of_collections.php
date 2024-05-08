<?php
	require_once('../my_Classes/options.class.php');
	include_once("../conf/ucs.conf.php");
	require_once("dprc_options.class.php");
	
	$options=new options();	
	$from_date		= $_REQUEST['from_date'];
	$to_date		= $_REQUEST['to_date'];
	$postcode		= $_REQUEST['postcode'];
	
	function getCustomer($application_id){
		$result = mysql_query("
			select
				*
			from
				application as a, customer as c
			where
				a.customer_id = c.customer_id
			and
				a.application_id = '$application_id'
		") or die(mysql_error());
		
		$r = mysql_fetch_assoc($result);
		return htmlentities("$r[customer_last_name], $r[customer_first_name] $r[customer_middle_name] $r[customer_appel]");
	}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>DPRC STATEMENT OF PAYMENTS REPORT</title>
<script>

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
</style>
</head>
<body>
<div class="container">
     <div><!--Start of Form-->
     	
     	<div style="font-weight:bolder; border-bottom:1px solid #000;">
        	SUMMARY OF COLLECTIONS<br />
			<?=date("m/d/Y",strtotime($from_date))?> to <?=date("m/d/Y",strtotime($to_date))?>
            <span style="float:right;"><?=date("F j, Y H:i:s")?></span> 
        </div>           
        <div class="content" style="">
        	<table class="table-content">
            	<tr>
                	<td>Application #</td>
                	<td>OR Date</td>
                    <td>OR No.</td>
                    <td>Post Code</td>
                    <td>Amount</td>
                    <td>Date Encoded</td>
                    <td>Penalize</td>
                    <td>Customer</td>
                    <td>Remarks</td>
                </tr>
                <?php
				$sql = "
					select	
						* 
					from 
						dprc_payment as p, application as a
					where
						p.application_id = a.application_id
					and or_date between '$from_date' and '$to_date'
					and	a.application_void = '0'
				";
				if($postcode){
				$sql .= "and postcode = '$postcode'";	
				}
				$sql .= "
					order by or_date asc, or_no asc
				";
				$result = mysql_query($sql) or die(mysql_error());
				$records = mysql_num_rows($result);
				$total_amount = 0;
				while($r = mysql_fetch_assoc($result)){
					$total_amount += $r['payment_amount'];
                ?>
                <tr>
                	<td><?=str_pad($r['application_id'],7,0,STR_PAD_LEFT)?></td>
                	<td><?=dprc::mdy($r['or_date'])?></td>
                    <td><?=$r['or_no']?></td>
                    <td><?=$options->getAttribute('dprc_post_codes','postcode',$r['postcode'],'postcode_desc')?></td>
                    <td class="align-right"><?=number_format($r['payment_amount'],2)?></td>
                    <td><?=dprc::mdy($r['date_encoded'])?></td>
                    <td><?=($r['penalized']) ? "Yes" : "No" ?></td>
                    <td><?=getCustomer($r['application_id'])?></td>
                    <td><?=$r['remarks']?></td>
                </tr>
                <?php } ?>
                <tr>
                	<td>Records : <?=$records?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="align-right"><?=number_format($total_amount,2)?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
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