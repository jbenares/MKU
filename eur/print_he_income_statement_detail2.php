<?php
require_once('../my_Classes/options.class.php');
include_once("../conf/ucs.conf.php");

$options=new options();	

$from_date		= $_REQUEST['from_date'];
$to_date		= $_REQUEST['to_date'];


function getRepair($stock_id,$from_date,$to_date){
	
	$sql = mysql_query("		
		select 
			sum(amount) as amount, eq_name, p.stock_id
		from
			issuance_header as h, issuance_detail as d, equipment as p, equipment_categories as eq
		where
			h.issuance_header_id = d.issuance_header_id
		and d.equipment_id = p.eqID
		and p.eq_catID = eq.eq_catID
		and h.status != 'C'
		and date between '$from_date' and '$to_date'
		and p.stock_id = '$stock_id'
		and d.stock_id != '669'
		group by p.eqID
		order by eq_name asc") or die (mysql_error());
		
		while($r = mysql_fetch_assoc($sql)){
			$sum += $r['amount'];
		}
	
	return $sum;
}

function getFuel($stock_id,$from_date,$to_date){
	$sql = mysql_query("		
		select 
			sum(amount) as amount, eq_name, p.stock_id
		from
			issuance_header as h, issuance_detail as d, equipment as p, equipment_categories as eq
		where
			h.issuance_header_id = d.issuance_header_id
		and d.equipment_id = p.eqID
		and p.eq_catID = eq.eq_catID
		and h.status != 'C'
		and date between '$from_date' and '$to_date'
		and p.stock_id = '$stock_id'
		and d.stock_id = '669'
		group by p.eqID
		order by eq_name asc") or die (mysql_error());
		
		while($r = mysql_fetch_assoc($sql)){
			$sum += $r['amount'];
		}
	
	return $sum;		
}

function getOtherFuel($eq_id,$from_date,$to_date){
	$sql = mysql_query("	
		select 
			sum(amount) as amount, eq_name, p.stock_id, p.eqID
		from
			issuance_header as h, issuance_detail as d, equipment as p, equipment_categories as eq
		where
			h.issuance_header_id = d.issuance_header_id
		and d.equipment_id = p.eqID
		and p.eq_catID = eq.eq_catID
		and h.status != 'C'
		and date between '$from_date' and '$to_date'
		and p.eqID = '$eq_id'		
		and d.stock_id = '669'
		group by p.eqID
		order by eq_name asc") or die (mysql_error());
		
	$r = mysql_fetch_assoc($sql);
	
	$sum = $r['amount'];
	
	return $sum;
}

function getOtherRepair($eq_id,$from_date,$to_date){
	$sql = mysql_query("	
		select 
			sum(amount) as amount, eq_name, p.stock_id, p.eqID
		from
			issuance_header as h, issuance_detail as d, equipment as p, equipment_categories as eq
		where
			h.issuance_header_id = d.issuance_header_id
		and d.equipment_id = p.eqID
		and p.eq_catID = eq.eq_catID
		and h.status != 'C'
		and date between '$from_date' and '$to_date'
		and p.eqID = '$eq_id'		
		and d.stock_id != '669'
		group by p.eqID
		order by eq_name asc") or die (mysql_error());
		
	$r = mysql_fetch_assoc($sql);
	
	$sum = $r['amount'];
	
	return $sum;
}

	$result = mysql_query("
		select 
			p.stock , sum(h.rate_per_hour * computed_time) as income, p.stock_id, sum(d.computed_time) as c_time
		from
			eur_header as h, eur_detail as d, productmaster as p, equipment_categories as eq
		where
			h.eur_header_id = d.eur_header_id
		and h.stock_id = p.stock_id
		and p.eq_catID = eq.eq_catID
		and h.`status` != 'C'
		and d.eur_void = '0'
		and d.released_date between '$from_date' and '$to_date'
		group by h.stock_id
		order by p.stock asc
	") or die(mysql_error());

	
set_time_limit(500);		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>JOB ORDER</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<style type="text/css">
*{
	font-size:11px;	
	font-family:Arial, Helvetica, sans-serif;
}
table{ border-collapse:collapse; }
table tr td:nth-child(2){ text-align:right; }
td{ vertical-align:top; padding:3px; }	
.subtotal td{ border-top:1px solid #000; font-weight:bold;}

.grandtotal td{ border-bottom:3px double #000; font-weight:bold;}

</style>
</head>
<body>
<div class="container">
     	<?php
			require("form_heading.php");
        ?>
	
     <div><!--Start of Form-->
     	<div style="font-weight:bolder; text-align: center;">
            HEAVY EQUIPMENT - INCOME STATEMENT <br />
			<?php echo date("m/d/Y",strtotime($from_date))." - ".date("m/d/Y",strtotime($to_date)); ?>
            <br />
			<br />
        </div>          
	<table style="width: 100%;" border="1">
	<tr>
		<td style="font-weight: bold;">Heavy Equipment</td>
		<td style="font-weight: bold; text-align: right;">Rental Revenue</td>
		<td style="font-weight: bold; text-align: right;">Salary</td>
		<td style="text-align: right; font-weight: bold;">Fuel Expense</td>
		<td style="text-align: right; font-weight: bold;">Repair and Maintenance Expense</td>
		<td style="text-align: right; font-weight: bold;">Net Income (Losses)</td>
	</tr>
	<?php
	while($r = mysql_fetch_assoc($result)){
	$arr_stock[] = $r['stock_id'];			
	$income = 0;
	$stock_id = $r['stock_id'];
	$repair_amount = getRepair($stock_id,$from_date,$to_date);	
	$fuel_amount = getFuel($stock_id,$from_date,$to_date);	
	$salary = $r['c_time'] * ( 298.5 / 8 );
	$income = $r['income'] - ($fuel_amount + $repair_amount + $salary);
	
	//totals
	$total_rev += $r['income'];
	$total_repair += $repair_amount;
	$total_fuel += $fuel_amount;
	$total_salary += $salary;
	?>
	<tr>
		<td><?=$r['stock']?></td>
		<td style="text-align: right;"><?=number_format($r['income'],2)?></td>
		<td style="text-align: right;"><?=number_format($salary,2)?></td>
		<td style="text-align: right;"><?=number_format($fuel_amount,2)?></td>
		<td style="text-align: right;"><?=number_format($repair_amount,2)?></td>
		<td style="text-align: right;"><?=number_format($income,2)?></td>
	</tr>
	<?php } 
	$total_income = $total_rev - ($total_fuel + $total_repair + $total_salary);
	?>
	<tr>
		<td style="text-align: left; font-weight: bold; font-size: 13px;">SUBTOTAL</td>
		<td style="text-align: right; font-weight: bold; font-size: 13px;"><?=number_format($total_rev,2)?></td>
		<td style="text-align: right; font-weight: bold; font-size: 13px;"><?=number_format($total_salary,2)?></td>
		<td style="text-align: right; font-weight: bold; font-size: 13px;"><?=number_format($total_fuel,2)?></td>
		<td style="text-align: right; font-weight: bold; font-size: 13px;"><?=number_format($total_repair,2)?></td>
		<td style="text-align: right; font-weight: bold; font-size: 13px;"><?=number_format($total_income,2)?></td>
	</tr>	
	<tr>
		<td colspan="6">&nbsp;<td>
	</tr>
	<tr>
		<td colspan="6" style="font-weight: bold;">Other Equipments<td>
	</tr>
	<?php

	$result2 = mysql_query("
		select 
			p.eqID, p.eq_name, p.stock_id
		from
			issuance_header as h, issuance_detail as d, equipment as p, equipment_categories as eq
		where
		p.stock_id not in ('" . implode($arr_stock, "', '") . "')
		and h.issuance_header_id = d.issuance_header_id
		and d.equipment_id = p.eqID
		and p.eq_catID = eq.eq_catID
		and h.status != 'C'
		and date between '$from_date' and '$to_date'
		and (d.stock_id = '669' or d.stock_id != '669')
		group by p.eqID
		order by eq_name asc
	") or die(mysql_error());
		
	
	
	while($r2 = mysql_fetch_assoc($result2)){
	$fuel2 = getOtherFuel($r2['eqID'],$from_date,$to_date);
	$repair2 = getOtherRepair($r2['eqID'],$from_date,$to_date);
	
	$total_repair2 += $repair2;
	$total_fuel2 += $fuel2;
	
	$income2 = 0 - ($fuel2 + $repair2);
	$total_income2 += $income2;
	?>	
	<tr>
		<td><?=$r2['eq_name']?></td>
		<td></td>
		<td></td>
		<td style="text-align: right;"><?=number_format($fuel2,2)?></td>
		<td style="text-align: right;"><?=number_format($repair2,2)?></td>
		<td style="text-align: right;"><?=number_format($income2,2)?></td>
	</tr>
	<?php } ?>
	<tr>
		<td style="text-align: left; font-weight: bold; font-size: 13px;">SUBTOTAL</td>
		<td style="text-align: right; font-weight: bold; font-size: 13px;"></td>
		<td style="text-align: right; font-weight: bold; font-size: 13px;"></td>
		<td style="text-align: right; font-weight: bold; font-size: 13px;"><?=number_format($total_fuel2,2)?></td>
		<td style="text-align: right; font-weight: bold; font-size: 13px;"><?=number_format($total_repair2,2)?></td>
		<td style="text-align: right; font-weight: bold; font-size: 13px;"><?=number_format($total_income2,2)?></td>
	</tr>
	<tr>
		<td colspan="6">&nbsp;</td>
	</tr>
	<?php 
	$net = $total_rev - ($total_fuel + $total_fuel2 + $total_repair + $total_repair2 + $total_salary);
	?>
	<tr>
		<td style="font-weight: bold; font-size: 13px; text-align: left;">TOTAL</td>
		<td style="font-weight: bold; font-size: 13px; text-align: right;"><?=number_format($total_rev,2)?></td>
		<td style="font-weight: bold; font-size: 13px; text-align: right;"><?=number_format($total_salary,2)?></td>
		<td style="font-weight: bold; font-size: 13px; text-align: right;"><?=number_format($total_fuel+$total_fuel2,2)?></td>
		<td style="font-weight: bold; font-size: 13px; text-align: right;"><?=number_format($total_repair+$total_repair2,2)?></td>
		<td style="font-weight: bold; font-size: 13px; text-align: right;"><?=number_format($net,2)?></td>
	</tr>
	</table>
    </div><!--End of Form-->
</div>
</body>
</html>