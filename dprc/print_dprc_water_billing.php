<?php
	ob_start();
	session_start();
	require_once('../my_Classes/options.class.php');
	include_once("../conf/ucs.conf.php");
	require_once("dprc_options.class.php");
	
	$options=new options();	
	$date			= $_REQUEST['date'];
	$customer_id	= $_REQUEST['customer_id'];
	
	function getPeriod($date){
		return date("M j, Y",strtotime("-1 month +1 day",strtotime($date)))." - ".date("M j, Y",strtotime($date));
		
	}
	
	function getLotAndBlock($customer_id){
		#lot
		$result = mysql_query("
			select DISTINCT(lot) as lot from application where customer_id = '$customer_id' 
		") or die(mysql_error());
		$aLot = array();
		while($r = mysql_fetch_assoc($result)){
			$aLot[] = $r['lot'];
		}
		
		#block
		$result = mysql_query("
			select DISTINCT(block) as block from application where customer_id = '$customer_id' 
		") or die(mysql_error());
		$aBlock = array();
		while($r = mysql_fetch_assoc($result)){
			$aBlock[] = $r['block'];
		}
		
		$a          = array();
		$a['lot']   = implode(" & ",$aLot);
		$a['block'] = implode(" & ",$aBlock);
		
		return $a;		
	}
	
	function getBalance($customer_id,$date){
		
		#BALANCE
		$result = mysql_query("
			select
				customer_last_name, customer_first_name, customer_middle_name, c.customer_id, volume, water_cost,date, prev_reading, present_reading
			from
				dprc_water_consumption as wc, customer as c
			where
				wc.customer_id = c.customer_id
			and wc.customer_id = '$customer_id'
			and date < '$date'
		") or die(mysql_error());

		$t_amount = 0;
		while($r = mysql_fetch_assoc($result)){
			$aLoc                    = getLotAndBlock($r['customer_id']);
			$r['lot']                = $aLoc['lot'];
			$r['block']              = $aLoc['block'];			
			$r['monthly_cosumption'] = $r['volume'];			
			$r['bill']               = $r['water_cost'] * $r['monthly_cosumption'];
			$t_amount                += $r['bill'];			
		}
		
		#PAYMENTS
		$result = mysql_query("
			select sum(amount) as amount from dprc_receipt where customer_id = '$customer_id' and date < '$date' and status != 'C'
		") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		$t_payments = $r['amount'];		
	
		return $t_amount - $t_payments;
	}
	
	function getPresentWaterConsumption($date,$customer_id){
		$result = mysql_query("
			select
				customer_last_name, customer_first_name, customer_middle_name, c.customer_id, volume, water_cost
			from
				dprc_water_consumption as wc, customer as c
			where
				wc.customer_id = c.customer_id
			and date = '$date'
			and wc.customer_id = '$customer_id'
		") or die(mysql_error());

		$r                       = mysql_fetch_assoc($result);
		$aLoc                    = getLotAndBlock($r['customer_id']);
		$r['lot']                = $aLoc['lot'];
		$r['block']              = $aLoc['block'];		
		$r['monthly_cosumption'] = $r['volume'];
		$r['bill']               = $r['water_cost'] * $r['monthly_cosumption'];
	
		return $r['bill'];
	}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>DPRC MONTHLY WATER CONSUMPTION REPORT</title>
<script>

function printPage() { print(); } //Must be present for Iframe printing
</script>
<link rel="stylesheet" type="text/css" href="../css/dprc_print.css"/>
<style type="text/css">
	.table-content{
		width:100%;	
		margin:auto;
	}
	.table-content tr:nth-child(1) td{
		border-top:1px solid #000;
		border-bottom:1px solid #000;
	}
	
	.table-content tr td:nth-child(n+5){
		text-align:right;
	}
	
	.table-content td{
		padding:3px;	
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
     	
     	<div style="text-align:left; margin-bottom:5px; font-size:16px; vertical-align:top;">
            <img id="head_logo" src="../images/chead.png" style="display:inline-block; margin-right:20px;" width="64" height="64" />
            <p style="display:inline-block; margin:0px; vertical-align:top;">
                <strong><?=$title?></strong><br />
                <?=$company_tel_no?><br />
                <?=$company_address?><br />
                <strong>WATER BILL</strong>
            </p>            
            <hr style="margin:5px 0px; border-top:1px solid #000; border-bottom:none;"  />
        </div>
        <table class="table-content">
        	<tr>
            	<td>HOME OWNER</td>
                <td>BLK</td>
                <td>LOT</td>
                <td>PERIOD</td>
                <td>AMOUNT</td>
            </tr>
            <?php
			$prev_balance = getBalance($customer_id,$date);
            ?>
            <tr>
            	<td style="font-style:italic;">PREV. BALANCE</td>
                <td></td>
                <td></td>
                <td></td>
                <td><?=number_format($prev_balance,2)?></td>
            </tr>
            <?php 
			$result = mysql_query("
				select * from customer where customer_id = '$customer_id'
			") or die(mysql_error());
			$r = mysql_fetch_assoc($result);
			$aLoc            = getLotAndBlock($r['customer_id']); 
			$present_balance = getPresentWaterConsumption($date,$customer_id);
			echo "
				<tr>
					<td>$r[customer_first_name] $r[customer_middle_name] $r[customer_last_name]</td>
					<td>$aLoc[block]</td>
					<td>$aLoc[lot]</td>
					<td>".getPeriod($date)."</td>
					<td>".number_format($present_balance,2)."</td>
				</tr>
				
				<tr>
					<td colspan='4'>TOTAL AMOUNT DUE</td>
					<td style='text-align:right;'>".number_format($prev_balance + $present_balance,2)."</td>
				</tr>
			";
			?>
        </table>
        <div style="font-style:italic;">
        	*PLEASE DISREGARD IF PAYMENT WAS ALREADY MADE.
        </div>
        
        <table cellspacing="0" cellpadding="5" align="center" width="98%" style="border:none; text-align:center; margin-top:10px;" class="summary">
            <tr>
                <td>Prepared by:<p>
                    <input type="text" class="line_bottom" style="width:200px; text-align:center;" value="<?=$options->getUserName($_SESSION['userID'])?>" /><br>Collection Officer</p></td>
            </tr>
        </table>
        
        
       
    </div><!--End of Form-->
</div>
</body>
</html>