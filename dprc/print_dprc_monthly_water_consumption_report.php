<?php
	require_once('../my_Classes/options.class.php');
	include_once("../conf/ucs.conf.php");
	require_once("dprc_options.class.php");
	
	$options=new options();	
	$date	= $_REQUEST['date'];
	
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
	
	function getPrevWaterConsumption($date,$customer_id){
		$result = mysql_query("
			select
				volume
			from
				dprc_water_consumption as wc
			where
				wc.customer_id = '$customer_id'
			and date < '$date'
			order by date desc
		") or die(mysql_error());
		
		$r = mysql_fetch_assoc($result);

		return (empty($r['volume'])) ? 0 : $r['volume'];
	}
	
	function getPresentWaterConsumption($date){
		$result = mysql_query("
			select
				customer_last_name, customer_first_name, customer_middle_name, c.customer_id, volume, water_cost, prev_reading_date, present_reading_date, prev_reading, present_reading
			from
				dprc_water_consumption as wc, customer as c
			where
				wc.customer_id = c.customer_id
			and date = '$date'
			order by customer_last_name asc, customer_first_name asc, customer_middle_name asc
		") or die(mysql_error());

		$a = array();
		while($r = mysql_fetch_assoc($result)){
			$aLoc                    = getLotAndBlock($r['customer_id']);
			$r['lot']                = $aLoc['lot'];
			$r['block']              = $aLoc['block'];
			$r['monthly_cosumption'] = $r['volume'];
			$r['bill']               = $r['water_cost'] * $r['monthly_cosumption'];
			$a[]                     = $r;
		}
	
		return $a;
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
		margin-top:5px;
		width:100%;	
	}
	.table-content tr:nth-child(1) td{
		border-top:1px solid #000;
		border-bottom:1px solid #000;
	}
	
	.table-content tr td:nth-child(n+5){
		text-align:right;
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
        	 MONTHLY WATER CONSUMPTION REPORT<br />
			<?=date("dS \of F Y",strtotime($date))?>
            <span style="float:right;"><?=date("F j, Y H:i:s")?></span> 
        </div>           
        <div class="content" style="">
        	<table class="table-content">
            	<tr>
                	<td width="10">#</td>
                    <td>CUSTOMER</td>
                    <td>BLOCK</td>
                    <td>LOT</td>
                    <td>PREV READING</td>
                    <td>PRESENT READING</td>
                    <td>MONTHLY COSUMPTION</td>
                    <td>COST PER CU.M.</td>
                    <td>WATER BILL</td>
                </tr>
                <?php
				$i = 1;
				$t_monthly_consumption = $t_bill = 0;
				foreach(getPresentWaterConsumption($date) as $r):
					$t_monthly_consumption += $r['monthly_cosumption'];
					$t_bill += $r['bill'];
					echo "
						<tr>
							<td>".($i++)."</td>
							<td>$r[customer_last_name], $r[customer_first_name] $r[customer_middle_name] </td>
							<td>$r[block]</td>
							<td>$r[lot]</td>
							<td>".number_format($r['prev_reading'],2)." cu.m.</td>
							<td>".number_format($r['present_reading'],2)." cu.m.</td>
							<td>".number_format($r['monthly_cosumption'],2)." cu.m.</td>
							<td>".number_format($r['water_cost'],2)."</td>
							<td>".number_format($r['bill'],2)."</td>
						</tr>
					";
				
                endforeach; ?>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><?=number_format($t_monthly_consumption,2)?> cu.m.</td>
                    <td></td>
                    <td><?=number_format($t_bill,2)?></td>
                </tr>
            </table>
        </div><!--End of content-->
        <table cellspacing="0" cellpadding="5" align="center" width="98%" style="border:none; text-align:center; margin-top:10px;" class="summary">
            <tr>
                <td>Read by:<p>
                    <input type="text" class="line_bottom" /><br>Project Utility</p></td>
                <td>Prepared by:<p>
                    <input type="text" class="line_bottom" /><br>Office Engineer</p></td>
                <td>Noted by:<p>
                    <input type="text" class="line_bottom" /><br>P.I.C.</p></td>
            </tr>
        </table>
    </div><!--End of Form-->
</div>
</body>
</html>