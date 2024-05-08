<?php
require_once('../my_Classes/options.class.php');
include_once("../conf/ucs.conf.php");
//require_once(dirname(__FILE__).'/library/lib.php');

$startingdate	= $_REQUEST['startingdate'];
$endingdate		= $_REQUEST['endingdate'];
$checklist		= $_REQUEST['checklist'];

function getProject($value){
	$sql = mysql_query("Select * from projects where project_id = '$value'") or die (mysql_error());
	$r = mysql_fetch_assoc($sql);
	
	return $r['project_name'];
}
$sql_taxation 		= mysql_query("Select * from gchart where sub_mclass = '5' and parent_gchart_id = '0'") or die (mysql_error());

function getBalanceOtherIncome($oi_gchart_id,$value,$startingdate,$endingdate,$oi_sub_mclass){
	
	$total_credit			 = 0;
	$total_debit 			 = 0;
	$other_income_balance    = 0;
	
	$sql_child = mysql_query("Select 
							sum(d.debit) as debit, sum(d.credit) as credit, p.project_name, p.project_id
							from
							gchart as g,
							gltran_header as h,
							gltran_detail as d,
							projects as p
							where g.parent_gchart_id != '0' and
							h.gltran_header_id = d.gltran_header_id and
							p.project_id = d.project_id and
							g.gchart_id = d.gchart_id and
							h.`status` != 'C' and
							h.date between '$startingdate' and '$endingdate' and
							g.parent_gchart_id = '$oi_gchart_id' and
							p.project_id = '$value' and
							g.sub_mclass = '$oi_sub_mclass'
							") or die (mysql_error());
	$r_child = mysql_fetch_assoc($sql_child);
	
	set_time_limit(100);
	
	$sql = mysql_query(" 
				select 
					sum(debit) as total_debit, sum(credit) as total_credit, p.project_id as project, g.gchart_id, g.gchart, g.beg_debit, g.beg_credit
				from  
					gltran_header as h,
					gltran_detail as d,
					projects as p,
					gchart as g
				where
					h.gltran_header_id = d.gltran_header_id
				and
					h.date between '$startingdate' and '$endingdate'
				and
					h.`status` != 'C'
				and
					d.project_id = p.project_id
				and 
					p.project_id = '$value'
				and
					g.gchart_id = d.gchart_id
				and
					d.gchart_id = '$oi_gchart_id'
				and
					g.sub_mclass = '$oi_sub_mclass'
				
	") or die (mysql_error());
	$r = mysql_fetch_assoc($sql);
	
	set_time_limit(100);
	$total_debit 	= $r['total_debit'] + $r_child['debit'];
	$total_credit 	= $r['total_credit'] + $r_child['credit'];
	
	if($oi_sub_mclass == '4' OR $oi_sub_mclass == '1'){
		$other_income_balance = $total_credit - $total_debit;
	}else if($oi_sub_mclass == '2' OR $oi_sub_mclass == '3' OR $oi_sub_mclass == '5'){
		$other_income_balance = $total_debit - $total_credit;
	}
	
	return $other_income_balance;
	
}

function getBalanceSales($s_gchart_id,$value,$startingdate,$endingdate,$s_sub_mclass){
	
	$total_credit = 0;
	$total_debit  = 0;
	$sales_balance	  = 0;
	
	$sql_child = mysql_query("Select 
							sum(d.debit) as debit, sum(d.credit) as credit, p.project_name, p.project_id
							from
							gchart as g,
							gltran_header as h,
							gltran_detail as d,
							projects as p
							where g.parent_gchart_id != '0' and
							h.gltran_header_id = d.gltran_header_id and
							p.project_id = d.project_id and
							g.gchart_id = d.gchart_id and
							h.`status` != 'C' and
							h.date between '$startingdate' and '$endingdate' and
							g.parent_gchart_id = '$s_gchart_id' and
							p.project_id = '$value' and
							g.sub_mclass = '$s_sub_mclass'
							") or die (mysql_error());
	$r_child = mysql_fetch_assoc($sql_child);
	set_time_limit(100);
	$sql = mysql_query(" 
				select 
					sum(debit) as total_debit, sum(credit) as total_credit, p.project_id as project, g.gchart_id, g.gchart, g.beg_debit, g.beg_credit
				from  
					gltran_header as h,
					gltran_detail as d,
					projects as p,
					gchart as g
				where
					h.gltran_header_id = d.gltran_header_id
				and
					h.date between '$startingdate' and '$endingdate'
				and
					h.`status` != 'C'
				and
					d.project_id = p.project_id
				and 
					p.project_id = '$value'
				and
					g.gchart_id = d.gchart_id
				and
					d.gchart_id = '$s_gchart_id'
				and
					g.sub_mclass = '$s_sub_mclass'
				
	") or die (mysql_error());
	$r = mysql_fetch_assoc($sql);
	set_time_limit(100);
	$total_debit 	= $r['total_debit'] + $r_child['debit'];
	$total_credit 	= $r['total_credit'] + $r_child['credit'];
	
	if($s_sub_mclass == '4' OR $s_sub_mclass == '1'){
		$sales_balance = $total_credit - $total_debit;
	}else if($s_sub_mclass == '2' OR $s_sub_mclass == '3' OR $s_sub_mclass == '5'){
		$sales_balance = $total_debit - $total_credit;
	}
	
	return $sales_balance;
}

function getBalanceCost($cos_gchart_id,$value,$startingdate,$endingdate,$cos_sub_mclass){
	
	$total_credit = 0;
	$total_debit  = 0;
	$cost_balance	  = 0;
	
	$sql_child = mysql_query("Select 
							sum(d.debit) as debit, sum(d.credit) as credit, p.project_name, p.project_id
							from
							gchart as g,
							gltran_header as h,
							gltran_detail as d,
							projects as p
							where g.parent_gchart_id != '0' and
							h.gltran_header_id = d.gltran_header_id and
							p.project_id = d.project_id and
							g.gchart_id = d.gchart_id and
							h.`status` != 'C' and
							h.date between '$startingdate' and '$endingdate' and
							g.parent_gchart_id = '$cos_gchart_id' and
							p.project_id = '$value' and
							g.sub_mclass = '$cos_sub_mclass'
							") or die (mysql_error());
	$r_child = mysql_fetch_assoc($sql_child);
	set_time_limit(100);
	$sql = mysql_query(" 
				select 
					sum(debit) as total_debit, sum(credit) as total_credit, p.project_id as project, g.gchart_id, g.gchart, g.beg_debit, g.beg_credit
				from  
					gltran_header as h,
					gltran_detail as d,
					projects as p,
					gchart as g
				where
					h.gltran_header_id = d.gltran_header_id
				and
					h.date between '$startingdate' and '$endingdate'
				and
					h.`status` != 'C'
				and
					d.project_id = p.project_id
				and 
					p.project_id = '$value'
				and
					g.gchart_id = d.gchart_id
				and
					d.gchart_id = '$cos_gchart_id'
				and
					g.sub_mclass = '$cos_sub_mclass'
	") or die (mysql_error());
	$r = mysql_fetch_assoc($sql);
	set_time_limit(100);
	$total_debit 	= $r['total_debit'] + $r_child['debit'];
	$total_credit 	= $r['total_credit'] + $r_child['credit'];
	
	if($cos_sub_mclass == '4' OR $cos_sub_mclass == '1'){
		$cost_balance = $total_credit - $total_debit;
	}else if($cos_sub_mclass == '2' OR $cos_sub_mclass == '3' OR $cos_sub_mclass == '5'){
		$cost_balance = $total_debit - $total_credit;
	}
	
	return $cost_balance;
}

function getBalanceExpenses($ex_gchart_id,$value,$startingdate,$endingdate,$ex_sub_mclass){
	
	$total_credit		  = 0;
	$total_debit  		  = 0;
	$expense_balance	  = 0;
	
	$sql_child = mysql_query("Select 
							sum(d.debit) as debit, sum(d.credit) as credit, p.project_name, p.project_id
							from
							gchart as g,
							gltran_header as h,
							gltran_detail as d,
							projects as p
							where g.parent_gchart_id != '0' and
							h.gltran_header_id = d.gltran_header_id and
							p.project_id = d.project_id and
							g.gchart_id = d.gchart_id and
							h.`status` != 'C' and
							h.date between '$startingdate' and '$endingdate' and
							g.parent_gchart_id = '$ex_gchart_id' and
							p.project_id = '$value' and
							g.sub_mclass = '$ex_sub_mclass'
							") or die (mysql_error());
	$r_child = mysql_fetch_assoc($sql_child);
	set_time_limit(100);
	$sql = mysql_query(" 
				select 
					sum(debit) as total_debit, sum(credit) as total_credit, p.project_id as project, g.gchart_id, g.gchart, g.beg_debit, g.beg_credit
				from  
					gltran_header as h,
					gltran_detail as d,
					projects as p,
					gchart as g
				where
					h.gltran_header_id = d.gltran_header_id
				and
					h.date between '$startingdate' and '$endingdate'
				and
					h.`status` != 'C'
				and
					d.project_id = p.project_id
				and 
					p.project_id = '$value'
				and
					g.gchart_id = d.gchart_id
				and
					d.gchart_id = '$ex_gchart_id'
				and
					g.sub_mclass = '$ex_sub_mclass'
	") or die (mysql_error());
	$r = mysql_fetch_assoc($sql);
	set_time_limit(100);
	$total_debit 	= $r['total_debit'] + $r_child['debit'];
	$total_credit 	= $r['total_credit'] + $r_child['credit'];
	
	if($ex_sub_mclass == '4' OR $ex_sub_mclass == '1'){
		$expense_balance = $total_credit - $total_debit;
	}else if($ex_sub_mclass == '2' OR $ex_sub_mclass == '3' OR $ex_sub_mclass == '5'){
		$expense_balance = $total_debit - $total_credit;
	}
	
	return $expense_balance;
}

function getBalanceTax($tax_gchart_id,$value,$startingdate,$endingdate,$tax_sub_mclass){
	
	$total_credit		  = 0;
	$total_debit  		  = 0;
	$tax_balance		  = 0;
	
	$sql_child = mysql_query("Select 
							sum(d.debit) as debit, sum(d.credit) as credit, p.project_name, p.project_id
							from
							gchart as g,
							gltran_header as h,
							gltran_detail as d,
							projects as p
							where g.parent_gchart_id != '0' and
							h.gltran_header_id = d.gltran_header_id and
							p.project_id = d.project_id and
							g.gchart_id = d.gchart_id and
							h.`status` != 'C' and
							h.date between '$startingdate' and '$endingdate' and
							g.parent_gchart_id = '$tax_gchart_id' and
							p.project_id = '$value' and
							g.sub_mclass = '$tax_sub_mclass'
							") or die (mysql_error());
	$r_child = mysql_fetch_assoc($sql_child);
	set_time_limit(100);
	$sql = mysql_query(" 
				select 
					sum(debit) as total_debit, sum(credit) as total_credit, p.project_id as project, g.gchart_id, g.gchart, g.beg_debit, g.beg_credit
				from  
					gltran_header as h,
					gltran_detail as d,
					projects as p,
					gchart as g
				where
					h.gltran_header_id = d.gltran_header_id
				and
					h.date between '$startingdate' and '$endingdate'
				and
					h.`status` != 'C'
				and
					d.project_id = p.project_id
				and 
					p.project_id = '$value'
				and
					g.gchart_id = d.gchart_id
				and
					d.gchart_id = '$tax_gchart_id'
				and
					g.sub_mclass = '$tax_sub_mclass'	
	") or die (mysql_error());
	$r = mysql_fetch_assoc($sql);
	set_time_limit(100);
	$total_debit 	= $r['total_debit'] + $r_child['debit'];
	$total_credit 	= $r['total_credit'] + $r_child['credit'];
	
	if($tax_sub_mclass == '4' OR $tax_sub_mclass == '1'){
		$tax_balance = $total_credit - $total_debit;
	}else if($tax_sub_mclass == '2' OR $tax_sub_mclass == '3' OR $tax_sub_mclass == '5'){
		$tax_balance = $total_debit - $total_credit;
	}
	
	return $tax_balance;
}

$total_other_income = 0;
$total_sales = 0;
$total_cost = 0;
$total_expense = 0;
$total_taxation = 0;
$gross = 0;
$befor_tax = 0;
$net_income = 0;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>INCOME STATEMENT - PER PROJECT</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<style type="text/css">
body
{
	font-family:Arial, Helvetica, sans-serif;
	font-size:11px;
	overflow-x: scroll;
    width: auto;
    white-space: nowrap;
}
.title{
	width: 100%;
	height: auto;
	text-align: left;
	font-weight: bold;
	font-size: 15px;
}
.container{
    width: auto;
    white-space: nowrap;
}
.accounts{
	text-align: left;
}
.amounts_title{
	text-align: center;
}
.amounts_detail{
	text-align: right;
	padding-right: 10px;
}
.account_header{
	text-align: left;
}
.account_header2{
	text-align: left;
	font-weight: bold;
	text-decoration: underline;
	padding-left: 10px;
}
.account_titles{
	text-align: left;
	padding-left: 15px;
}
.factors{
	text-align: left;
	font-weight: bold;
}
.totalities{
	text-align: right;
	padding-right: 10px;
}
.auto_adjust{
	height: 2000px;
}
.per_factor{
	text-align: right;
	padding-right: 10px;
	font-weight: bold;
}
li{
	display: inline;
}
</style>
</head>
<body>
<div class="container">
	<?php
		$i = 0;
		foreach($checklist as $value){
			$i++;
		}
	$cal_width = 700;
	$total_width = $i * $cal_width;	
	
	echo "<div class='auto_adjust' style='width:".$total_width."px;'>";
	?>
	<div class="title" style="backgroun-color: skyblue;">
		<span class="title">INCOME STATEMENT</span><br />
		<span class="title">FROM: <?=$startingdate?> TO: <?=$endingdate?></span>
	</div>
<ul>
	<li>
	<!-- start of HEADERS -->
	<div style="float: left;">
	<table style="margin: 10px;" width="250" height="1000px;">
		<tr>
			<td colspan="2"><br /></td>
		</tr>
		<tr>
			<td width="200" class="accounts">ACCOUNTS</td>
		</tr>
		<tr>
			<td colspan="2" class="account_header">REVENUE</td>
		</tr>
		<!-- OTHER INCOME -->
		<tr>
			<td class="account_header2">OTHER INCOME</td>
		</tr>
		<?php 
		$oi = mysql_query("Select * from gchart where sub_mclass = '4' and parent_gchart_id = '0'") or die (mysql_error());
		while($oi_r = mysql_fetch_assoc($oi)){ 
		$oi_gchart_id = $r_oi['gchart_id'];
		?>
		<tr>
			<td class="account_titles"><?=$oi_r['gchart']?></td>
		</tr>	
		<?php } ?>
		<tr>
			<td><br /></td>
		</tr>
		<!-- SALES -->
		<tr>
			<td class="account_header2">SALES</td>
		</tr>
		<?php 
		$s = mysql_query("Select * from gchart where sub_mclass = '1' and parent_gchart_id = '0'") or die (mysql_error());
		while($sr = mysql_fetch_assoc($s)){
		?>
		<tr>
			<td class="account_titles"><?=$sr['gchart']?></td>
		</tr>	
		<?php } ?>
		<tr>
			<td><br /></td>
		</tr>
		<!-- REVENUE -->
		<tr>
			<td class="factors">REVENUE</td>
		</tr>
		<tr>
			<td class="factors" >LESS: EXPENSES</td>
		</tr>
		<!-- COST OF SALES -->
		<tr>
			<td class="account_header2">COST OF SALES</td>
		</tr>
		<?php 
		$cos = mysql_query("Select * from gchart where sub_mclass = '2' and parent_gchart_id = '0'") or die (mysql_error());
		while($r_cos = mysql_fetch_assoc($cos)){ 
		?>
		<tr>
			<td class="account_titles"><?=$r_cos['gchart']?></td>
		</tr>	
		<?php } ?>
		<tr>
			<td><br /></td>
		</tr>
		<!-- GROSS -->
		<tr>
			<td class="factors">GROSS PROFIT</td>
		</tr>
		<!-- EXPENSES-->
		<tr>
			<td class="account_header2">EXPENSES</td>
		</tr>
		<?php 
		$e = mysql_query("Select * from gchart where sub_mclass = '3' and parent_gchart_id = '0'") or die (mysql_error());
		while($r_e = mysql_fetch_assoc($e)){ 
		?>
		<tr>
			<td class="account_titles"><?=$r_e['gchart']?></td>
		</tr>	
		<?php } ?>
		<tr>
			<td><br /></td>
		</tr>		
		<tr>
			<td class="factors">INCOME BEFORE TAXTATION</td>
		</tr>
		<!-- TAXATION-->
		<tr>
			<td class="account_header2">TAXATION</td>
		</tr>
		<?php 
		$t = mysql_query("Select * from gchart where sub_mclass = '5' and parent_gchart_id = '0'") or die (mysql_error());
		while($r_t = mysql_fetch_assoc($t)){ 
		?>
		<tr>
			<td class="account_titles"><?=$r_t['gchart']?></td>
		</tr>	
		<?php } ?>
		<tr>
			<td><br /></td>
		</tr>	
		<tr>
			<td><br /></td>
		</tr>		
		<!-- NET INCOME / LOSS-->
		<tr>
			<td>NET INCOME/LOSS</td>
		</tr>
	</table>
	</div>
	</li>
	<!-- end of HEADERS -->
	<?php foreach($checklist as $value){ 
	
	$total_other_income = 0;
	$total_sales = 0;
	$total_cost = 0;
	$total_expense = 0;
	$total_taxation = 0;
	$gross = 0;
	$befor_tax = 0;
	$net_income = 0;
	
	?>
	<li>
	<div style="float: left;">
	<table style="margin: 10px;" width="100" height="1000px;">
		<tr>
			<td><span><?php echo getProject($value);?></span></td>
		</tr>
		<tr>
			<td width="100" class="amounts_title">&nbsp;</td>
		</tr>
		<tr>
			<td class="account_header"><br /></td>
		</tr>
		<!-- OTHER INCOME -->
		<tr>
			<td class="account_header2"><br /></td>
		</tr>
		<?php 
		$sql_other_income 	= mysql_query("Select * from gchart where sub_mclass = '4' and parent_gchart_id = '0'") or die (mysql_error());
		while($r_oi = mysql_fetch_assoc($sql_other_income)){ 
		$other_income = 0;
		$oi_gchart_id = $r_oi['gchart_id'];
		$oi_sub_mclass = $r_oi['sub_mclass'];
		$other_income = getBalanceOtherIncome($oi_gchart_id,$value,$startingdate,$endingdate,$oi_sub_mclass);
		$total_other_income += $other_income;
		?>
		<tr>
			<?php if($other_income != 0){ ?>
			<td class="amounts_detail"><?=number_format($other_income,2);?></td>
			<?php }else{ ?>
			<td class="amounts_detail">-</td>
			<?php } ?>
		</tr>	
		<?php } ?>
		<tr>
			<td class="per_factor" style=" border-top: 1px solid black;"><?=number_format($total_other_income,2);?></td>
		</tr>
		<!-- SALES -->
		<tr>
			<td class="account_header2"><br /></td>
		</tr>
		<?php 
		$sql_sales = mysql_query("Select * from gchart where sub_mclass = '1' and parent_gchart_id = '0'") or die (mysql_error());
		while($rs = mysql_fetch_assoc($sql_sales)){
		$sales = 0;
		$s_gchart_id = $rs['gchart_id'];
		$s_sub_mclass = $rs['sub_mclass'];
		
		$sales = getBalanceSales($s_gchart_id,$value,$startingdate,$endingdate,$s_sub_mclass);
		$total_sales += $sales;
		?>
		<tr>
			<?php if($sales != 0){ ?>
			<td class="amounts_detail"><?=number_format($sales,2);?></td>
			<?php }else{ ?>
			<td class="amounts_detail">-</td>
			<?php } ?>
		</tr>	
		<?php } 
		$revenue = 0;
		$revenue = $total_sales + $total_other_income;
		?>
		<tr>
			<td class="per_factor" style=" border-top: 1px solid black;"><?=number_format($total_sales,2);?></td>
		</tr>
		<!-- REVENUE -->
		<tr>
			<td style="font-weight: bold; text-decoration: underline; text-decoration-style:double; text-align: right; padding-right: 10px;"><?=number_format($revenue,2);?></td>
		</tr>
		<tr>
			<td class="factors"><br /></td>
		</tr>
		<!-- COST OF SALES -->
		<tr>
			<td class="account_header2"><br /></td>
		</tr>
		<?php 
		$sql_cost = mysql_query("Select * from gchart where sub_mclass = '2' and parent_gchart_id = '0'") or die (mysql_error());
		while($cos_r = mysql_fetch_assoc($sql_cost)){ 
		$cost = 0;
		$cos_gchart_id = $cos_r['gchart_id'];
		$cos_sub_mclass = $cos_r['sub_mclass'];
		
		$cost = getBalanceCost($cos_gchart_id,$value,$startingdate,$endingdate,$cos_sub_mclass);
		$total_cost += $cost;
		?>
		<tr>
			<?php if($cost != 0){ ?>
			<td class="amounts_detail"><?=number_format($cost,2)?></td>
			<?php }else{ ?>
			<td class="amounts_detail">-</td>
			<?php }?>
		</tr>	
		<?php } 
			$gross = $revenue - $total_cost;
		?>
		<tr>
			<td class="per_factor" style=" border-top: 1px solid black;"><?=number_format($total_cost,2);?></td>
		</tr>
		<!-- GROSS -->
		<tr>
			<td style="font-weight: bold; text-decoration: underline; text-decoration-style:double; text-align: right; padding-right: 10px;"><?=number_format($gross,2);?></td>
		</tr>
		<!-- EXPENSES-->
		<tr>
			<td class="account_header2"><br /></td>
		</tr>
		<?php 
		$sql_expenses = mysql_query("Select * from gchart where sub_mclass = '3' and parent_gchart_id = '0'") or die (mysql_error());
		while($ex_r = mysql_fetch_assoc($sql_expenses)){ 
		$expense = 0;
		$ex_gchart_id = $ex_r['gchart_id'];
		$ex_sub_mclass = $ex_r['sub_mclass'];
		
		$expense = getBalanceExpenses($ex_gchart_id,$value,$startingdate,$endingdate,$ex_sub_mclass);
		$total_expense += $expense;
		?>
		<tr>
			<?php if($expense != 0){ ?>
			<td class="amounts_detail"><?=number_format($expense,2)?></td>
			<?php }else{ ?>
			<td class="amounts_detail">-</td>
			<?php }?>
		</tr>	
		<?php } 
		$befor_tax = $gross - $total_expense;
		?>
		<tr>
			<td class="per_factor" style=" border-top: 1px solid black;"><?=number_format($total_expense,2);?></td>
		</tr>		
		<tr>
			<td style="font-weight: bold; text-decoration: underline; text-decoration-style:double; text-align: right; padding-right: 10px;"><?=number_format($befor_tax,2);?></td>
		</tr>
		<!-- TAXATION-->
		<tr>
			<td class="account_header2"><br /></td>
		</tr>
		<?php 
		$sql_taxation = mysql_query("Select * from gchart where sub_mclass = '5' and parent_gchart_id = '0'") or die (mysql_error());
		while($tax_r = mysql_fetch_assoc($sql_taxation)){ 
		$tax = 0;
		$tax_gchart_id = $tax_r['gchart_id'];
		$tax_sub_mclass = $tax_r['sub_mclass'];
		
		$tax = getBalanceTax($tax_gchart_id,$value,$startingdate,$endingdate,$tax_sub_mclass);
		$total_taxation += $tax;
		?>
		<tr>
			<?php if($tax != 0){ ?>
			<td class="amounts_detail"><?=number_format($tax,2)?></td>
			<?php }else{ ?>
			<td class="amounts_detail">-</td>
			<?php } ?>
		</tr>	
		<?php } 
		$net_income = $befor_tax - $total_taxation;
		?>
		<tr>
			<td class="per_factor" style="border-top: 1px solid black;"><?=number_format($total_taxation,2);?></td>
		</tr>		
		<!-- NET INCOME / LOSS-->
		<tr>
			<td><br /></td>
		</tr>
		<tr>
			<td style="font-weight: bold; text-decoration: underline; text-decoration-style:double; text-align: right; padding-right: 10px;"><?=number_format($net_income,2);?></td>
		</tr>
	</table>
	</div>
	</li>
</ul>
	 <?php 
	$total_net_income += $net_income;
	 } ?>	
	<!-- Start of TOTALS -->
	
	<!-- End of TOTALS -->
	 </div>
</body>
</html>