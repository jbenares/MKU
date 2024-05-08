<?php
	require_once('../my_Classes/options.class.php');
	include_once("../conf/ucs.conf.php");
	require_once("dprc_options.class.php");
	
	$options=new options();	
	$from_date		= $_REQUEST['from_date'];
	$to_date		= $_REQUEST['to_date'];
	$postcode		= $_REQUEST['postcode'];
	
	function getReceipts($from_date,$to_date){
		$result = mysql_query("
			select
				or_no,date,customer_last_name,customer_first_name,customer_middle_name,customer_appel,amount,r.remarks
			from
				dprc_receipt as r left join customer as c on r.customer_id = c.customer_id
			where
				date between '$from_date' and '$to_date'
			and status != 'C'
			order by or_no asc
		") or die(mysql_error());
		
		$a = array();
		while($r = mysql_fetch_assoc($result)){
			$a[] = $r;	
		}
		return $a;
		#return htmlentities("$r[customer_last_name], $r[customer_first_name] $r[customer_middle_name] $r[customer_appel]");
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
        	WATER RECEIPTS HISTORY<br />
			<?=date("m/d/Y",strtotime($from_date))?> to <?=date("m/d/Y",strtotime($to_date))?>
            <span style="float:right;"><?=date("F j, Y H:i:s")?></span> 
        </div>           
        <div class="content" style="">
        	<table class="table-content">
            	<tr>
                	<td>AR#</td>
                	<td>DATE</td>
                    <td>CUSTOMER</td>
                    <td>AMOUNT</td>
                    <td>REMARKS</td>
                </tr>
                <?php 
				$t_amount = 0;
				foreach(getReceipts($from_date,$to_date) as $r): 
				$t_amount += $r['amount'];
				?>
                <tr>
                	<td><?=$r['or_no']?></td>
                	<td><?=dprc::mdy($r['date'])?></td>
                    <td><?=htmlentities("$r[customer_last_name], $r[customer_first_name] $r[customer_middle_name] $r[customer_appel]")?></td>
                    <td style="text-align:right;"><?=number_format($r['amount'],2)?></td>
                    <td><?=$r['remarks']?></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                	<td></td>
                    <td></td>
                    <td></td>
                    <td style="text-align:right;"><?=number_format($t_amount,2)?></td>
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