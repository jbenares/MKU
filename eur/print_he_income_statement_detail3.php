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
		group by p.eqID") or die (mysql_error());
		
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

function getIncome($stock_id,$from_date,$to_date){
	$sql = mysql_query("
		select 
			sum(h.rate_per_hour * computed_time) as income
		from
			eur_header as h, eur_detail as d
		where
			h.eur_header_id = d.eur_header_id
		and h.`status` != 'C'
		and d.eur_void = '0'
		and h.stock_id = '$stock_id'
		and d.released_date between '$from_date' and '$to_date'") or die(mysql_error());
		
		$r = mysql_fetch_assoc($sql);
		if($r['income']){
			return $r['income'];
		}else{
			return 0;
		}
			
}

function getSalary($stock_id,$from_date,$to_date){
	$sql = mysql_query("select 
		sum(d.computed_time) as c_time
		from eur_header as h,
		eur_detail as d
		where 
		h.eur_header_id = d.eur_header_id and 
		h.stock_id = '$stock_id' and
		h.`status` != 'C' and
		d.eur_void = '0' and
		d.released_date between '$from_date' and '$to_date'") or die (mysql_error());
		
		$r = mysql_fetch_assoc($sql);
		$salary = $r['c_time'] * (298.5/8);
		
		return $salary;
}

	
function category($categ){

		$sql = mysql_query("select * from heavy_equipment_categories as hec where he_type_id = '$categ'") or die (mysql_error());
		while($r = mysql_fetch_assoc($sql)){
			$arr[] = $r['stock_id'];
		}

	return $arr;
}

function getStock($stock_id){
	$sql = mysql_query("Select stock from productmaster where stock_id = '$stock_id'") or die (mysql_error());
	$r = mysql_fetch_assoc($sql);
	
	return $r['stock'];
}
	
set_time_limit(500);		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>HE - INCOME STATEMENT</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<style type="text/css">
*{
	font-size:11px;	
	font-family:Arial, Helvetica, sans-serif;
}
table{ 

}
table tr td:nth-child(2){ 
	text-align:right; 
}
td{ 
	vertical-align:top; padding:3px;
}	
.subtotal td{ 
	border-top:1px solid #000; font-weight:bold;
}

.grandtotal td{ 
	border-bottom:3px double #000; font-weight:bold;
}

.he_sub{
	border-top: 1px solid black;
}

.totals{
	font-weight: bold;
	text-align: right;
	border-top: 2px solid #000000;
}
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
	<table style="width: 100%;" border="0">
	<tr>
		<td style="width: 200px;font-weight: bold; text-align: center;" class="he_header">Equipment</td>
		<td colspan="2" style="width: 200px;font-weight: bold; text-align: center;" class="he_header">Rental Revenue</td>
		<td colspan="2" style="width: 200px;font-weight: bold; text-align: center;" class="he_header">Driver/Operator Salary</td>
		<td colspan="2" style="width: 200px;font-weight: bold; text-align: center;" class="he_header">Labor Charge</td>
		<td colspan="2" style="width: 200px;text-align: center; font-weight: bold;" class="he_header">Fuel Expense</td>
		<td colspan="2" style="width: 200px;text-align: center; font-weight: bold;" class="he_header">Spare Parts and Oil Expense</td>
		<td colspan="2" style="width: 200px;text-align: center; font-weight: bold;" class="he_header">Total Expense</td>
		<td colspan="2" style="width: 200px;text-align: center; font-weight: bold;" class="he_header">Net Income (Losses)</td>
	</tr>
	<tr>
		<td colspan="15" style="border-bottom: 1px solid black;"></td>
	</tr>
	<?php 
	$arr13 = category(13);
	$sub_net = $sub_repair = $sub_fuel = $sub_salary = $sub_income = $sub_labor = 0;	
	foreach($arr13 as $value13){
		$stock 	= getStock($value13);
		$income = getIncome($value13,$from_date,$to_date);
		$salary = getSalary($value13,$from_date,$to_date);
		$fuel 	= getFuel($value13,$from_date,$to_date);
		$repair = getRepair($value13,$from_date,$to_date);
		$labor = $repair * (20/100);
		
		$sub_income += $income;
		$sub_salary += $salary;
		$sub_fuel 	+= $fuel;
		$sub_repair += $repair;
		$sub_labor += $labor;
		
		//calc
		$net = $income - ($salary + $fuel + $repair + $labor);	
		if($net != 0){
		?>
		<tr>
			<td><?=$stock?></td>
			<td style="text-align: right;"><?=number_format($income,2)?></td>	
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($salary,2)?></td>		
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($labor,2)?></td>		
			<td style="width: 50px;">&nbsp;</td>			
			<td style="text-align: right;"><?=number_format($fuel,2)?></td>		
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($repair,2)?></td>
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($salary+$fuel+$repair+$labor,2)?></td>
			<td style="width: 50px;">&nbsp;</td>			
			<td style="text-align: right; "><?php
			if($net < 0){
				echo '('.number_format(abs($net),2).')';
			}else{
				echo number_format($net,2);
			}			
			?></td>		
		</tr>
		<?php
		}
	}
	$total_income += $sub_income;
	$total_salary += $sub_salary;
	$total_fuel += $sub_fuel;
	$total_repair += $sub_repair;
	$total_labor += $sub_labor;
	$sub_net = $sub_income - ($sub_salary + $sub_fuel + $sub_repair + $sub_labor);
	if($sub_net != 0){ ?>
	<tr>
		<td>&nbsp;</td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_income,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_salary,2)?></td>
		<td></td>		
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_labor,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_fuel,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_repair,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_salary+$sub_fuel+$sub_repair+$sub_labor,2)?></td>
		<td></td>		
		<td class="he_sub" style="text-align: right; font-weight: bold;">
		<?php
			if($sub_net < 0){
				echo '('.number_format(abs($sub_net),2).')';
			}else{
				echo number_format($sub_net,2);
			}			
			?>		
		</td>
	</tr>
	<tr>
		<td colspan="11"><br /></td>
	</tr>	
	<?php 
	}
	$arr1 = category(1);
	$sub_net = $sub_repair = $sub_fuel = $sub_salary = $sub_income = $sub_labor = 0;	
	foreach($arr1 as $value1){
		$stock 	= getStock($value1);
		$income = getIncome($value1,$from_date,$to_date);
		$salary = getSalary($value1,$from_date,$to_date);
		$fuel 	= getFuel($value1,$from_date,$to_date);
		$repair = getRepair($value1,$from_date,$to_date);
		$labor  = $repair * (20/100);
		
		$sub_income += $income;
		$sub_salary += $salary;
		$sub_fuel 	+= $fuel;
		$sub_repair += $repair;
		$sub_labor  += $labor;
		
		//calc
		$net = $income - ($salary + $fuel + $repair + $labor);	
		if($net != 0){
		?>
		<tr>
			<td><?=$stock?></td>
			<td style="text-align: right;"><?=number_format($income,2)?></td>	
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($salary,2)?></td>		
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($fuel,2)?></td>		
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($repair,2)?></td>
			<td style="width: 50px;">&nbsp;</td>			
			<td style="text-align: right;"><?=number_format($labor,2)?></td>
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($salary+$fuel+$repair+$labor,2)?></td>
			<td style="width: 50px;">&nbsp;</td>			
			<td style="text-align: right; "><?php
			if($net < 0){
				echo '('.number_format(abs($net),2).')';
			}else{
				echo number_format($net,2);
			}			
			?></td>		
		</tr>
		<?php
		}
	}
	$total_income += $sub_income;
	$total_salary += $sub_salary;
	$total_fuel += $sub_fuel;
	$total_repair += $sub_repair;
	$total_labor += $sub_labor;
	$sub_net = $sub_income - ($sub_salary + $sub_fuel + $sub_repair + $sub_labor);
	if($sub_net != 0){ ?>
	<tr>
		<td>&nbsp;</td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_income,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_salary,2)?></td>
		<td></td>		
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_labor,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_fuel,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_repair,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_salary+$sub_fuel+$sub_repair+$sub_labor,2)?></td>
		<td></td>		
		<td class="he_sub" style="text-align: right; font-weight: bold;">
		<?php
			if($sub_net < 0){
				echo '('.number_format(abs($sub_net),2).')';
			}else{
				echo number_format($sub_net,2);
			}			
			?>		
		</td>
	</tr>
	<tr>
		<td colspan="11"><br /></td>
	</tr>
	<?php
	}
	$arr2 = category(2);
	$sub_net = $sub_repair = $sub_fuel = $sub_salary = $sub_income = $sub_labor = 0;	
	foreach($arr2 as $value2){
		$stock 	= getStock($value2);
		$income = getIncome($value2,$from_date,$to_date);
		$salary = getSalary($value2,$from_date,$to_date);
		$fuel 	= getFuel($value2,$from_date,$to_date);
		$repair = getRepair($value2,$from_date,$to_date);
		$labor  = $repair * (20/100);
		
		$sub_income += $income;
		$sub_salary += $salary;
		$sub_fuel 	+= $fuel;
		$sub_repair += $repair;
		$sub_labor  += $labor;
		//calc
		$net = $income - ($salary + $fuel + $repair + $labor);
		if($net != 0){
		?>
		<tr>
			<td><?=$stock?></td>
			<td style="text-align: right;"><?=number_format($income,2)?></td>	
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($salary,2)?></td>		
			<td style="width: 50px;">&nbsp;</td>			
			<td style="text-align: right;"><?=number_format($labor,2)?></td>		
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($fuel,2)?></td>		
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($repair,2)?></td>
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($salary+$fuel+$repair+$labor,2)?></td>
			<td style="width: 50px;">&nbsp;</td>			
			<td style="text-align: right;"><?php
			if($net < 0){
				echo '('.number_format(abs($net),2).')';
			}else{
				echo number_format($net,2);
			}			
			?></td>		
		</tr>
		<?php
		}
	}
	$total_income += $sub_income;
	$total_salary += $sub_salary;
	$total_fuel += $sub_fuel;
	$total_repair += $sub_repair;
	$total_labor += $sub_labor;
	$sub_net = $sub_income - ($sub_salary + $sub_fuel + $sub_repair + $sub_labor);
	if($sub_net != 0){ ?>
	<tr>
		<td>&nbsp;</td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_income,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_salary,2)?></td>
		<td></td>	
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_labor,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_fuel,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_repair,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_salary+$sub_fuel+$sub_repair+$sub_labor,2)?></td>
		<td></td>		
		<td class="he_sub" style="text-align: right; font-weight: bold;">
		<?php
			if($sub_net < 0){
				echo '('.number_format(abs($sub_net),2).')';
			}else{
				echo number_format($sub_net,2);
			}			
			?>		
		</td>
	</tr>
	<tr>
		<td colspan="11"><br /></td>
	</tr>
	<?php }
	$arr3 = category(3);
	$sub_net = $sub_repair = $sub_fuel = $sub_salary = $sub_income = 0;
	foreach($arr3 as $value3){
		$stock 	= getStock($value3);
		$income = getIncome($value3,$from_date,$to_date);
		$salary = getSalary($value3,$from_date,$to_date);
		$fuel 	= getFuel($value3,$from_date,$to_date);
		$repair = getRepair($value3,$from_date,$to_date);
		$labor  = $repair * (20/100);
		
		$sub_income += $income;
		$sub_salary += $salary;
		$sub_fuel 	+= $fuel;
		$sub_repair += $repair;
		$sub_labor += $labor;
		//calc
		$net = $income - ($salary + $fuel + $repair + $labor);	
		if($net != 0){
		?>
		<tr>
			<td><?=$stock?></td>
			<td style="text-align: right;"><?=number_format($income,2)?></td>	
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($salary,2)?></td>		
			<td style="width: 50px;">&nbsp;</td>		
			<td style="text-align: right;"><?=number_format($labor,2)?></td>		
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($fuel,2)?></td>		
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($repair,2)?></td>
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($salary+$fuel+$repair+$labor,2)?></td>
			<td style="width: 50px;">&nbsp;</td>			
			<td style="text-align: right;"><?php
			if($net < 0){
				echo '('.number_format(abs($net),2).')';
			}else{
				echo number_format($net,2);
			}			
			?></td>		
		</tr>
		<?php
		}
	}
	$total_income += $sub_income;
	$total_salary += $sub_salary;
	$total_fuel += $sub_fuel;
	$total_repair += $sub_repair;	
	$total_labor += $sub_labor;	
	$sub_net = $sub_income - ($sub_salary + $sub_fuel + $sub_repair + $sub_labor);
	if($sub_net != 0){ ?>
	<tr>
		<td>&nbsp;</td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_income,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_salary,2)?></td>
		<td></td>		
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_labor,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_fuel,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_repair,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_salary+$sub_fuel+$sub_repair+$sub_labor,2)?></td>
		<td></td>		
		<td class="he_sub" style="text-align: right; font-weight: bold;">
		<?php
			if($sub_net < 0){
				echo '('.number_format(abs($sub_net),2).')';
			}else{
				echo number_format($sub_net,2);
			}			
			?>		
		</td>
	</tr>
	<tr>
		<td colspan="11"><br /></td>
	</tr>
	<?php } 
	$arr4 = category(4);
	$sub_net = $sub_repair = $sub_fuel = $sub_salary = $sub_income = $sub_labor = 0;
	foreach($arr4 as $value4){
		$stock 	= getStock($value4);
		$income = getIncome($value4,$from_date,$to_date);
		$salary = getSalary($value4,$from_date,$to_date);
		$fuel 	= getFuel($value4,$from_date,$to_date);
		$repair = getRepair($value4,$from_date,$to_date);
		$labor  = $repair * (20/100);
		
		$sub_income += $income;
		$sub_salary += $salary;
		$sub_fuel 	+= $fuel;
		$sub_repair += $repair;
		$sub_labor  += $labor;
		//calc
		$net = $income - ($salary + $fuel + $repair + $labor);		
		if($net != 0){
		?>
		<tr>
			<td><?=$stock?></td>
			<td style="text-align: right;"><?=number_format($income,2)?></td>	
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($salary,2)?></td>		
			<td style="width: 50px;">&nbsp;</td>		
			<td style="text-align: right;"><?=number_format($labor,2)?></td>		
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($fuel,2)?></td>		
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($repair,2)?></td>
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($salary+$fuel+$repair+$labor,2)?></td>
			<td style="width: 50px;">&nbsp;</td>			
			<td style="text-align: right;"><?php
			if($net < 0){
				echo '('.number_format(abs($net),2).')';
			}else{
				echo number_format($net,2);
			}			
			?></td>		
		</tr>
		<?php
		}
	}
	$total_income += $sub_income;
	$total_salary += $sub_salary;
	$total_fuel += $sub_fuel;
	$total_repair += $sub_repair;	
	$total_labor += $sub_labor;	
	$sub_net = $sub_income - ($sub_salary + $sub_fuel + $sub_repair + $sub_labor);
	if($sub_net != 0){ 
		?>
	<tr>
		<td>&nbsp;</td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_income,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_salary,2)?></td>
		<td></td>	
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_labor,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_fuel,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_repair,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_salary+$sub_fuel+$sub_repair+$sub_labor,2)?></td>
		<td></td>		
		<td class="he_sub" style="text-align: right; font-weight: bold;">
		<?php
			if($sub_net < 0){
				echo '('.number_format(abs($sub_net),2).')';
			}else{
				echo number_format($sub_net,2);
			}			
			?>		
		</td>
	</tr>
	<tr>
		<td colspan="11"><br /></td>
	</tr>
	<?php } 
	$arr5 = category(5);
	$sub_net = $sub_repair = $sub_fuel = $sub_salary = $sub_income = $sub_labor = 0;
	foreach($arr5 as $value5){
		$stock 	= getStock($value5);
		$income = getIncome($value5,$from_date,$to_date);
		$salary = getSalary($value5,$from_date,$to_date);
		$fuel 	= getFuel($value5,$from_date,$to_date);
		$repair = getRepair($value5,$from_date,$to_date);
		$labor  = $repair * (20/100);
		
		$sub_income += $income;
		$sub_salary += $salary;
		$sub_fuel 	+= $fuel;
		$sub_repair += $repair;
		$sub_labor += $labor;
		//calc
		$net = $income - ($salary + $fuel + $repair + $labor);		
		if($net != 0){
		?>
		<tr>
			<td><?=$stock?></td>
			<td style="text-align: right;"><?=number_format($income,2)?></td>	
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($salary,2)?></td>		
			<td style="width: 50px;">&nbsp;</td>	
			<td style="text-align: right;"><?=number_format($labor,2)?></td>		
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($fuel,2)?></td>		
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($repair,2)?></td>
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($salary+$fuel+$repair+$labor,2)?></td>
			<td style="width: 50px;">&nbsp;</td>			
			<td style="text-align: right;"><?php
			if($sub_net < 0){
				echo '('.number_format(abs($net),2).')';
			}else{
				echo number_format($net,2);
			}			
			?></td>		
		</tr>
		<?php
		}
	}
	$total_income += $sub_income;
	$total_salary += $sub_salary;
	$total_fuel += $sub_fuel;
	$total_repair += $sub_repair;	
	$total_labor  += $sub_labor;	
	$sub_net = $sub_income - ($sub_salary + $sub_fuel + $sub_repair + $sub_labor);
	if($sub_net != 0){ 
		?>
	<tr>
		<td>&nbsp;</td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_income,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_salary,2)?></td>
		<td></td>	
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_labor,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_fuel,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_repair,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_salary+$sub_fuel+$sub_repair+$sub_labor,2)?></td>
		<td></td>		
		<td class="he_sub" style="text-align: right; font-weight: bold;">
		<?php
			if($sub_net < 0){
				echo '('.number_format(abs($sub_net),2).')';
			}else{
				echo number_format($sub_net,2);
			}			
			?>		
		</td>
	</tr>
	<tr>
		<td colspan="11"><br /></td>
	</tr>
	<?php }
	$arr6 = category(6);
	$sub_net = $sub_repair = $sub_fuel = $sub_salary = $sub_income = $sub_labor = 0;
	foreach($arr6 as $value6){
		$stock 	= getStock($value6);
		$income = getIncome($value6,$from_date,$to_date);
		$salary = getSalary($value6,$from_date,$to_date);
		$fuel 	= getFuel($value6,$from_date,$to_date);
		$repair = getRepair($value6,$from_date,$to_date);
		$labor  = $repair * (20/100);
		
		$sub_income += $income;
		$sub_salary += $salary;
		$sub_fuel 	+= $fuel;
		$sub_repair += $repair;
		$sub_labor += $labor;
		//calc
		$net = $income - ($salary + $fuel + $repair + $labor);		
		if($net != 0){
		?>
		<tr>
			<td><?=$stock?></td>
			<td style="text-align: right;"><?=number_format($income,2)?></td>	
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($salary,2)?></td>		
			<td style="width: 50px;">&nbsp;</td>		
			<td style="text-align: right;"><?=number_format($labor,2)?></td>		
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($fuel,2)?></td>		
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($repair,2)?></td>
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($salary+$fuel+$repair+$labor,2)?></td>
			<td style="width: 50px;">&nbsp;</td>			
			<td style="text-align: right;"><?php
			if($net < 0){
				echo '('.number_format(abs($net),2).')';
			}else{
				echo number_format($net,2);
			}			
			?></td>		
		</tr>
		<?php
		}
	}
	$total_income += $sub_income;
	$total_salary += $sub_salary;
	$total_fuel += $sub_fuel;
	$total_repair += $sub_repair;	
	$total_labor += $sub_labor;	
	$sub_net = $sub_income - ($sub_salary + $sub_fuel + $sub_repair + $sub_labor);
	if($sub_net != 0){ 
		?>
	<tr>
		<td>&nbsp;</td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_income,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_salary,2)?></td>
		<td></td>		
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_labor,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_fuel,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_repair,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_salary+$sub_fuel+$sub_repair+$sub_labor,2)?></td>
		<td></td>		
		<td class="he_sub" style="text-align: right; font-weight: bold;">
		<?php
			if($sub_net < 0){
				echo '('.number_format(abs($sub_net),2).')';
			}else{
				echo number_format($sub_net,2);
			}			
			?>		
		</td>
	</tr>
	<tr>
		<td colspan="11"><br /></td>
	</tr>
	<?php 	
	}
	$arr7 = category(7);
	$sub_net = $sub_repair = $sub_fuel = $sub_salary = $sub_income = $sub_labor = 0;
	foreach($arr7 as $value7){
		$stock 	= getStock($value7);
		$income = getIncome($value7,$from_date,$to_date);
		$salary = getSalary($value7,$from_date,$to_date);
		$fuel 	= getFuel($value7,$from_date,$to_date);
		$repair = getRepair($value7,$from_date,$to_date);
		$labor  = $repair * (20/100);
		
		$sub_income += $income;
		$sub_salary += $salary;
		$sub_fuel 	+= $fuel;
		$sub_repair += $repair;
		$sub_labor += $labor;
		//calc
		$net = $income - ($salary + $fuel + $repair + $labor);		
		if($net != 0){
		?>
		<tr>
			<td><?=$stock?></td>
			<td style="text-align: right;"><?=number_format($income,2)?></td>	
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($salary,2)?></td>		
			<td style="width: 50px;">&nbsp;</td>		
			<td style="text-align: right;"><?=number_format($labor,2)?></td>		
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($fuel,2)?></td>		
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($repair,2)?></td>
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($salary+$fuel+$repair+$labor,2)?></td>
			<td style="width: 50px;">&nbsp;</td>			
			<td style="text-align: right;"><?php
			if($net < 0){
				echo '('.number_format(abs($net),2).')';
			}else{
				echo number_format($net,2);
			}			
			?></td>		
		</tr>
		<?php
		}
	}
	
	$total_income += $sub_income;
	$total_salary += $sub_salary;
	$total_fuel += $sub_fuel;
	$total_repair += $sub_repair;	
	$total_labor += $sub_labor;	
	$sub_net = $sub_income - ($sub_salary + $sub_fuel + $sub_repair + $sub_labor);
	if($sub_net != 0){ 
		?>
	<tr>
		<td>&nbsp;</td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_income,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_salary,2)?></td>
		<td></td>	
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_labor,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_fuel,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_repair,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_salary+$sub_fuel+$sub_repair+$sub_labor,2)?></td>
		<td></td>		
		<td class="he_sub" style="text-align: right; font-weight: bold;">
		<?php
			if($sub_net < 0){
				echo '('.number_format(abs($sub_net),2).')';
			}else{
				echo number_format($sub_net,2);
			}			
			?>		
		</td>
	</tr>
	<tr>
		<td colspan="11"><br /></td>
	</tr>
	<?php 	
	}
	$arr8 = category(8);
	$sub_net = $sub_repair = $sub_fuel = $sub_salary = $sub_income = $sub_labor = 0;
	foreach($arr8 as $value8){
		$stock 	= getStock($value8);
		$income = getIncome($value8,$from_date,$to_date);
		$salary = getSalary($value8,$from_date,$to_date);
		$fuel 	= getFuel($value8,$from_date,$to_date);
		$repair = getRepair($value8,$from_date,$to_date);
		$labor  = $repair * (20/100);
		
		$sub_income += $income;
		$sub_salary += $salary;
		$sub_fuel 	+= $fuel;
		$sub_repair += $repair;
		$sub_labor  += $labor;
		//calc
		$net = $income - ($salary + $fuel + $repair + $labor);		
		if($net != 0){
		?>
		<tr>
			<td><?=$stock?></td>
			<td style="text-align: right;"><?=number_format($income,2)?></td>	
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($salary,2)?></td>		
			<td style="width: 50px;">&nbsp;</td>	
			<td style="text-align: right;"><?=number_format($labor,2)?></td>		
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($fuel,2)?></td>		
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($repair,2)?></td>
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($salary+$fuel+$repair+$labor,2)?></td>
			<td style="width: 50px;">&nbsp;</td>			
			<td style="text-align: right;"><?php
			if($net < 0){
				echo '('.number_format(abs($net),2).')';
			}else{
				echo number_format($net,2);
			}			
			?></td>		
		</tr>
		<?php
		}
	}
	$total_income += $sub_income;
	$total_salary += $sub_salary;
	$total_fuel += $sub_fuel;
	$total_repair += $sub_repair;	
	$total_labor += $sub_labor;	
	$sub_net = $sub_income - ($sub_salary + $sub_fuel + $sub_repair + $sub_labor);
	if($sub_net != 0){ 
		?>
	<tr>
		<td>&nbsp;</td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_income,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_salary,2)?></td>
		<td></td>	
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_labor,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_fuel,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_repair,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_salary+$sub_fuel+$sub_repair+$sub_labor,2)?></td>
		<td></td>		
		<td class="he_sub" style="text-align: right; font-weight: bold;">
		<?php
			if($sub_net < 0){
				echo '('.number_format(abs($sub_net),2).')';
			}else{
				echo number_format($sub_net,2);
			}			
			?>		
		</td>
	</tr>
	<tr>
		<td colspan="11"><br /></td>
	</tr>
	<?php 	
	}
	$arr9 = category(9);
	$sub_net = $sub_repair = $sub_fuel = $sub_salary = $sub_income = $sub_labor = 0;
	foreach($arr9 as $value9){
		$stock 	= getStock($value9);
		$income = getIncome($value9,$from_date,$to_date);
		$salary = getSalary($value9,$from_date,$to_date);
		$fuel 	= getFuel($value9,$from_date,$to_date);
		$repair = getRepair($value9,$from_date,$to_date);
		$labor = $repair * (20/100);
		
		$sub_income += $income;
		$sub_salary += $salary;
		$sub_fuel 	+= $fuel;
		$sub_repair += $repair;
		$sub_labor += $labor;
		//calc
		$net = $income - ($salary + $fuel + $repair + $labor);		
		if($net != 0){
		?>
		<tr>
			<td><?=$stock?></td>
			<td style="text-align: right;"><?=number_format($income,2)?></td>	
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($salary,2)?></td>		
			<td style="width: 50px;">&nbsp;</td>		
			<td style="text-align: right;"><?=number_format($labor,2)?></td>		
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($fuel,2)?></td>		
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($repair,2)?></td>
			<td style="width: 50px;">&nbsp;</td>	
			<td style="text-align: right;"><?=number_format($salary+$fuel+$repair+$labor,2)?></td>
			<td style="width: 50px;">&nbsp;</td>			
			<td style="text-align: right;"><?php
			if($net < 0){
				echo '('.number_format(abs($net),2).')';
			}else{
				echo number_format($net,2);
			}			
			?></td>		
		</tr>
		<?php
		}
	}
	$total_income += $sub_income;
	$total_salary += $sub_salary;
	$total_fuel += $sub_fuel;
	$total_repair += $sub_repair;	
	$total_labor += $sub_labor;	
	$sub_net = $sub_income - ($sub_salary + $sub_fuel + $sub_repair + $sub_labor);
	if($sub_net != 0){ 
		?>
	<tr>
		<td>&nbsp;</td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_income,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_salary,2)?></td>
		<td></td>		
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_labor,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_fuel,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_repair,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_salary+$sub_fuel+$sub_repair+$sub_labor,2)?></td>
		<td></td>		
		<td class="he_sub" style="text-align: right; font-weight: bold;">
		<?php
			if($sub_net < 0){
				echo '('.number_format(abs($sub_net),2).')';
			}else{
				echo number_format($sub_net,2);
			}			
			?>		
		</td>
	</tr>
	<tr>
		<td colspan="11"><br /></td>
	</tr>
	<?php 	
	}
	$arr10 = category(10);
	$sub_net = $sub_repair = $sub_fuel = $sub_salary = $sub_income = $sub_labor = 0;
	foreach($arr10 as $value10){
		$stock 	= getStock($value10);
		$income = getIncome($value10,$from_date,$to_date);
		$salary = getSalary($value10,$from_date,$to_date);
		$fuel 	= getFuel($value10,$from_date,$to_date);
		$repair = getRepair($value10,$from_date,$to_date);
		$labor  = $repair * (20/100);
		
		$sub_income += $income;
		$sub_salary += $salary;
		$sub_fuel 	+= $fuel;
		$sub_repair += $repair;
		$sub_labor += $labor;
		//calc
		$net = $income - ($salary + $fuel + $repair + $labor);		
		if($net != 0){
		?>
		<tr>
			<td><?=$stock?></td>
			<td style="text-align: right;"><?=number_format($income,2)?></td>	
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($salary,2)?></td>		
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($labor,2)?></td>		
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($fuel,2)?></td>		
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($repair,2)?></td>
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($salary+$fuel+$repair+$labor,2)?></td>
			<td style="width: 50px;">&nbsp;</td>			
			<td style="text-align: right;"><?php
			if($net < 0){
				echo '('.number_format(abs($net),2).')';
			}else{
				echo number_format($net,2);
			}			
			?></td>		
		</tr>
		<?php
		}
	}
	$total_income += $sub_income;
	$total_salary += $sub_salary;
	$total_fuel += $sub_fuel;
	$total_repair += $sub_repair;	
	$total_labor += $sub_labor;	
	$sub_net = $sub_income - ($sub_salary + $sub_fuel + $sub_repair + $sub_labor);
	if($sub_net != 0){ 
		?>
	<tr>
		<td>&nbsp;</td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_income,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_salary,2)?></td>
		<td></td>		
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_labor,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_fuel,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_repair,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_salary+$sub_fuel+$sub_repair+$sub_labor,2)?></td>
		<td></td>		
		<td class="he_sub" style="text-align: right; font-weight: bold;">
		<?php
			if($sub_net < 0){
				echo '('.number_format(abs($sub_net),2).')';
			}else{
				echo number_format($sub_net,2);
			}			
			?>		
		</td>
	</tr>
	<tr>
		<td colspan="11"><br /></td>
	</tr>
	<?php 	
	}
	$arr11 = category(11);
	$sub_net = $sub_repair = $sub_fuel = $sub_salary = $sub_income = $sub_labor = 0;
	foreach($arr11 as $value11){
		$stock 	= getStock($value11);
		$income = getIncome($value11,$from_date,$to_date);
		$salary = getSalary($value11,$from_date,$to_date);
		$fuel 	= getFuel($value11,$from_date,$to_date);
		$repair = getRepair($value11,$from_date,$to_date);
		$labor = $repair * (20/100);
		
		$sub_income += $income;
		$sub_salary += $salary;
		$sub_fuel 	+= $fuel;
		$sub_repair += $repair;
		$sub_labor += $labor;
		//calc
		$net = $income - ($salary + $fuel + $repair + $labor);		
		if($net != 0){
		?>
		<tr>
			<td><?=$stock?></td>
			<td style="text-align: right;"><?=number_format($income,2)?></td>	
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($salary,2)?></td>		
			<td style="width: 50px;">&nbsp;</td>			
			<td style="text-align: right;"><?=number_format($labor,2)?></td>		
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($fuel,2)?></td>		
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($repair,2)?></td>
			<td style="width: 50px;">&nbsp;</td>	
			<td style="text-align: right;"><?=number_format($salary+$fuel+$repair+$labor,2)?></td>
			<td style="width: 50px;">&nbsp;</td>			
			<td style="text-align: right;"><?php
			if($net < 0){
				echo '('.number_format(abs($net),2).')';
			}else{
				echo number_format($net,2);
			}			
			?></td>		
		</tr>
		<?php
		}
	}
	$total_income += $sub_income;
	$total_salary += $sub_salary;
	$total_fuel += $sub_fuel;
	$total_repair += $sub_repair;	
	$total_labor += $sub_labor;	
	$sub_net = $sub_income - ($sub_salary + $sub_fuel + $sub_repair + $sub_labor);
	if($sub_net != 0){ 
		?>
	<tr>
		<td>&nbsp;</td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_income,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_salary,2)?></td>
		<td></td>		
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_labor,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_fuel,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_repair,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_salary+$sub_fuel+$sub_repair+$sub_labor,2)?></td>
		<td></td>		
		<td class="he_sub" style="text-align: right; font-weight: bold;">
		<?php
			if($sub_net < 0){
				echo '('.number_format(abs($sub_net),2).')';
			}else{
				echo number_format($sub_net,2);
			}			
			?>		
		</td>
	</tr>
	<tr>
		<td colspan="11"><br /></td>
	</tr>
	<?php 	
	}
	$arr12 = category(12);
	$sub_net = $sub_repair = $sub_fuel = $sub_salary = $sub_income = $sub_labor = 0;
	foreach($arr12 as $value12){
		$stock 	= getStock($value12);
		$income = getIncome($value12,$from_date,$to_date);
		$salary = getSalary($value12,$from_date,$to_date);
		$fuel 	= getFuel($value12,$from_date,$to_date);
		$repair = getRepair($value12,$from_date,$to_date);
		$labor = $repair * (20/100);
		
		$sub_income += $income;
		$sub_salary += $salary;
		$sub_fuel 	+= $fuel;
		$sub_repair += $repair;
		$sub_labor += $labor;
		//calc
		$net = $income - ($salary + $fuel + $repair + $labor);		
		if($net != 0){
		?>
		<tr>
			<td><?=$stock?></td>
			<td style="text-align: right;"><?=number_format($income,2)?></td>	
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($salary,2)?></td>		
			<td style="width: 50px;">&nbsp;</td>		
			<td style="text-align: right;"><?=number_format($labor,2)?></td>		
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($fuel,2)?></td>		
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($repair,2)?></td>
			<td style="width: 50px;">&nbsp;</td>
			<td style="text-align: right;"><?=number_format($salary+$fuel+$repair+$labor,2)?></td>
			<td style="width: 50px;">&nbsp;</td>			
			<td style="text-align: right;"><?php
			if($net < 0){
				echo '('.number_format(abs($net),2).')';
			}else{
				echo number_format($net,2);
			}			
			?></td>		
		</tr>
		<?php
		}
	}
	$total_income += $sub_income;
	$total_salary += $sub_salary;
	$total_fuel += $sub_fuel;
	$total_repair += $sub_repair;	
	$total_labor += $sub_labor;	
	$sub_net = $sub_income - ($sub_salary + $sub_fuel + $sub_repair + $sub_labor);
	if($sub_net != 0){ 
		?>
	<tr>
		<td>&nbsp;</td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_income,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_salary,2)?></td>
		<td></td>		
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_labor,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_fuel,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_repair,2)?></td>
		<td></td>
		<td class="he_sub" style="text-align: right; font-weight: bold;"><?=number_format($sub_salary+$sub_fuel+$sub_repair+$sub_labor,2)?></td>
		<td></td>		
		<td class="he_sub" style="text-align: right; font-weight: bold;">
		<?php
			if($sub_net < 0){
				echo '('.number_format(abs($sub_net),2).')';
			}else{
				echo number_format($sub_net,2);
			}			
			?>		
		</td>
	</tr>
	<?php $total_net = $total_income - ($total_salary + $total_fuel + $total_repair + $total_labor)?>
	<tr>
		<td><br /></td>
	</tr>
	<tr>
		<td></td>
		<td class="totals"><?=number_format($total_income,2)?></td>
		<td></td>
		<td class="totals"><?=number_format($total_salary,2)?></td>
		<td></td>		
		<td class="totals"><?=number_format($total_labor,2)?></td>
		<td></td>
		<td class="totals"><?=number_format($total_fuel,2)?></td>
		<td></td>
		<td class="totals"><?=number_format($total_repair,2)?></td>
		<td></td>
		<td class="totals"><?=number_format($total_salary+$total_fuel+$total_repair+$total_labor,2)?></td>
		<td></td>		
		<td class="totals">
		<?php
			if($total_net < 0){
				echo '('.number_format(abs($total_net),2).')';
			}else{
				echo number_format($total_net,2);
			}		
		?>
		</td>
	</tr>
	<?php } ?>
	</table>
	<br />
    <table align="center" width="98%" style=" text-align:center; margin-top:10px;" >
		<tr>
			<td style="text-align: center;">Prepared By:</td>
			<td style="text-align: center;">Checked & Verified By:</td>
			<td style="text-align: center;">Noted By:</td>
		</tr>
		<tr>
			<td style="text-align: center;"><br /></td>
			<td style="text-align: center;"><br /></td>
			<td style="text-align: center;"><br /></td>
		</tr>		
		<tr>
			<td style="text-align: center;"><div style="border-top: 1px solid black; width: 200px; text-align: center; margin: 0 auto;">&nbsp;</div></td>
			<td style="text-align: center;"><div style="border-top: 1px solid black; width: 200px; text-align: center; margin: 0 auto;">Finance Manager</div></td>
			<td style="text-align: center;"><div style="border-top: 1px solid black; width: 200px; text-align: center; margin: 0 auto; ">General Manager / President</div></td>
		</tr>		
    </table>	
    </div><!--End of Form-->
</div>
</body>
</html>