<?php
//ini_set('max_execution_time', 1000);

require_once('my_Classes/options.class.php');
include_once("conf/ucs.conf.php");
require_once(dirname(__FILE__).'/library/lib.php');


$from = $_REQUEST['from'];
$to   = $_REQUEST['to'];

$options=new options();	
$total_credit = 0;
$total_debit = 0;
$parent = 0;
$g_parent = 0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>BALANCE SHEET</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<style type="text/css">

body
{
	size: legal portrait;		
	padding:0px;
	font-family:Arial, Helvetica, sans-serif;
	font-size:11px;
}

.header
{
	text-align:center;	
	margin-top:20px;
}

.header table, .content table
{
	text-align:left;	
}
.header table td, .content table td
{
	padding:3px;
	
}

.content table{
	border-collapse:collapse;
}
.content table td,.content table th{
	/*border:1px solid #000;*/
	padding:3px;
}
.withborder td,.withborder th{
	border:1px solid #000;
}
hr
{
	margin:40px 0px;	
	border:1px dashed #999;

}

.clearfix:after {
	content: ".";
	display: block;
	clear: both;
	visibility: hidden;
	line-height: 0;
	height: 0;
}
 
.clearfix {
	display: inline-block;
}

html[xmlns] .clearfix {
	display: block;
}
 
* html .clearfix {
	height: 1%;
}

.noborder{
	border:none;	
}

.alignRight{
	text-align:right;	
}
.total{
	border-top: 1px solid black;
	font-weight: bold;
	font-size: 12px;
}
.total2{
	border-top: 1px solid black;
	border-bottom: 1px solid black;
	font-weight: bold;
}
.total3{
	border-top: 1px solid black;
	border-bottom: 1px solid black;
	font-size: 14px;
	padding-top: 5px;
	padding-bottom: 5px;
	font-weight: bold;
}

.total4{
	font-weight: bold;
	font-size: 12px;	
}

.top_total{
	border-bottom: 1px solid black;
	font-weight: bold;
}
#numbers_format{
	text-align: right;
	padding-left: 20px;
}
#totalities{
	font-weight: bold; 
	font-size: 14px;
	padding-left: 20px;
}

#totalities_main{
	font-weight: bold; 
	font-size: 14px;
	padding-left: 0px;
}
a:link{
	color: black;
}
a:hover{
	color: red;
}

a:visited{
	color: black;
}
</style>
</head>
<body>

	<?php
		$tmpStartingDate=explode("-",$startingdate);
	?>
    
    
     <div style="margin-bottom:100px;"><!--Start of Form-->
    
    	<?php
			require("form_heading.php");
        ?>

        <div style="text-align:left; font-size:12px; margin-bottom:20px; font-weight:bold;">
           	INCOME STATEMENT<br />
			<em>From <?=date("F j, Y",strtotime($from));?> To <?=date("F j, Y",strtotime($to));?></em>
        </div>      
<div class="container">
<table width="700">
	<thead>
	<tr>
		<td width="300" id="totalities" style="padding-left: 20px;">SALES</td>
		<td width="170" style="font-weight: bold; text-align: right;"></td>
	</tr>
	</thead>
<?php
//sub class - Sales (1)	
$sqlg1 = mysql_query("Select * from gchart where gchart_void = '0' and parent_gchart_id = '0' and mclass = 'I' and sub_mclass = '1' order by acode ASC") or die (mysql_error());				 
while($rg1 = mysql_fetch_assoc($sqlg1)){
$g1 = $rg1['gchart_id'];
//parent
$sql1 = mysql_query("Select sum(credit) as total_credit, sum(debit) as total_debit, g.gchart_id, g.gchart, g.mclass, g.sub_mclass
					from gltran_header as h,
					gltran_detail as d,
					gchart as g
					where h.gltran_header_id = d.gltran_header_id and
					h.date between '$from' and '$to' and 
					g.gchart_id = d.gchart_id and
					h.`status` != 'C' and
					g.gchart_id = '$g1'
					group by g.gchart_id
					order by g.mclass
					") or die (mysql_error());

//child				
$sql2 = mysql_query("Select sum(credit) as total_credit, sum(debit) as total_debit, g.gchart_id, g.gchart, g.mclass, g.sub_mclass
					from gltran_header as h,
					gltran_detail as d,
					gchart as g
					where h.gltran_header_id = d.gltran_header_id and
					h.date between '$from' and '$to' and 
					g.gchart_id = d.gchart_id and
					h.`status` != 'C' and
					g.parent_gchart_id = '$g1'
					group by g.parent_gchart_id
					order by g.mclass
					") or die (mysql_error());	

$r1 = mysql_fetch_assoc($sql1);
$r2 = mysql_fetch_assoc($sql2);

$sales_b = $rg1['beg_credit'] - $rg1['beg_debit'];
$sales_p = $r1['total_credit'] - $r1['total_debit'];	
$sales_c = $r2['total_credit'] - $r2['total_debit'];	
$total_sales += $sales_b + $sales_p + $sales_c;
$total_sales_check = $sales_b + $sales_p + $sales_c;
if($total_sales_check != 0){
?>
	<tr>
		<td style="padding-left: 30px;"><a href="admin.php?view=ee7e0ef4c4cece4b9009&gchart_id=<?=$g1?>&startingdate=<?=$_REQUEST['from']?>&endingdate=<?=$_REQUEST['to']?>" target="_blank" /><?=$rg1['gchart']?></a></td>
		<td style="text-align: right;"><?=number_format($sales_b + $sales_p + $sales_c,2)?></td>
	</tr>
<?php 
} 
} 
?>
	<tr>
		<td id="totalities">TOTAL SALES</td>
		<td class="total" style="text-align: right;"><?=number_format($total_sales,2)?></td>
	</tr>
</table>
<table width="700">
	<thead>
	<tr>
		<td width="300" id="totalities" style="padding-left: 20px;">OTHER INCOME</td>
		<td width="170" style="font-weight: bold; text-align: right;"></td>
	</tr>
	</thead>
<?php
//sub class - Other Income (4)	
$sqlg1 = mysql_query("Select * from gchart where gchart_void = '0' and parent_gchart_id = '0' and mclass = 'I' and sub_mclass = '4' order by acode ASC") or die (mysql_error());				 
while($rg1 = mysql_fetch_assoc($sqlg1)){
$g1 = $rg1['gchart_id'];
//parent
$sql1 = mysql_query("Select sum(credit) as total_credit, sum(debit) as total_debit, g.gchart_id, g.gchart, g.mclass, g.sub_mclass
					from gltran_header as h,
					gltran_detail as d,
					gchart as g
					where h.gltran_header_id = d.gltran_header_id and
					h.date between '$from' and '$to' and 
					g.gchart_id = d.gchart_id and
					h.`status` != 'C' and
					g.gchart_id = '$g1'
					group by g.gchart_id
					order by g.mclass
					") or die (mysql_error());

//child				
$sql2 = mysql_query("Select sum(credit) as total_credit, sum(debit) as total_debit, g.gchart_id, g.gchart, g.mclass, g.sub_mclass
					from gltran_header as h,
					gltran_detail as d,
					gchart as g
					where h.gltran_header_id = d.gltran_header_id and
					h.date between '$from' and '$to' and 
					g.gchart_id = d.gchart_id and
					h.`status` != 'C' and
					g.parent_gchart_id = '$g1'
					group by g.parent_gchart_id
					order by g.mclass
					") or die (mysql_error());	

$r1 = mysql_fetch_assoc($sql1);
$r2 = mysql_fetch_assoc($sql2);

$other_income_b = $rg1['beg_credit'] - $rg1['beg_debit'];
$other_income_p = $r1['total_credit'] - $r1['total_debit'];	
$other_income_c = $r2['total_credit'] - $r2['total_debit'];	
$total_other_income += $other_income_b + $other_income_p + $other_income_c;
$total_sales_check = $other_income_b + $other_income_p + $other_income_c;
if($total_sales_check != 0){
?>
	<tr>
		<td style="padding-left: 30px;"><a href="admin.php?view=ee7e0ef4c4cece4b9009&gchart_id=<?=$g1?>&startingdate=<?=$_REQUEST['from']?>&endingdate=<?=$_REQUEST['to']?>" target="_blank" /><?=$rg1['gchart']?></a></td>
		<td style="text-align: right;"><?=number_format($other_income_b + $other_income_p + $other_income_c,2)?></td>
	</tr>
<?php 
} 
} 
?>
	<tr>
		<td id="totalities">TOTAL OTHER INCOME</td>
		<td class="total" style="text-align: right;"><?=number_format($total_other_income,2)?><br /></td>
	</tr>
</table>
<br />
<table width="700">
	<tr>
		<td id="totalities">TOTAL SALES & OTHER INCOME</td>
		<td class="total" style="text-align: right;"><?=number_format($soi=$total_other_income+$total_sales,2)?><br /></td>
	</tr>
</table>
<br />
<table width="700">
	<thead>
	<tr>
		<td width="300" id="totalities" style="padding-left: 20px;">COST OF SALES</td>
		<td width="170" style="font-weight: bold; text-align: right;"></td>
	</tr>
	</thead>
<?php
//sub class - Cost of Sales (2)	
$sqlg1 = mysql_query("Select * from gchart where gchart_void = '0' and parent_gchart_id = '0' and mclass = 'E' and sub_mclass = '2' order by acode ASC") or die (mysql_error());				 
while($rg1 = mysql_fetch_assoc($sqlg1)){
$g1 = $rg1['gchart_id'];
//parent
$sql1 = mysql_query("Select sum(credit) as total_credit, sum(debit) as total_debit, g.gchart_id, g.gchart, g.mclass, g.sub_mclass
					from gltran_header as h,
					gltran_detail as d,
					gchart as g
					where h.gltran_header_id = d.gltran_header_id and
					h.date between '$from' and '$to' and 
					g.gchart_id = d.gchart_id and
					h.`status` != 'C' and
					g.gchart_id = '$g1'
					group by g.gchart_id
					order by g.mclass
					") or die (mysql_error());

//child				
$sql2 = mysql_query("Select sum(credit) as total_credit, sum(debit) as total_debit, g.gchart_id, g.gchart, g.mclass, g.sub_mclass
					from gltran_header as h,
					gltran_detail as d,
					gchart as g
					where h.gltran_header_id = d.gltran_header_id and
					h.date between '$from' and '$to' and 
					g.gchart_id = d.gchart_id and
					h.`status` != 'C' and
					g.parent_gchart_id = '$g1'
					group by g.parent_gchart_id
					order by g.mclass
					") or die (mysql_error());	

$r1 = mysql_fetch_assoc($sql1);
$r2 = mysql_fetch_assoc($sql2);

$cost_b = $rg1['beg_debit'] - $rg1['beg_credit'];
$cost_p = $r1['total_debit'] - $r1['total_credit'];	
$cost_c = $r2['total_debit'] - $r2['total_credit'];	
$total_cost += $cost_b + $cost_p + $cost_c;
$total_cost_check = $cost_b + $cost_p + $other_income_c;
if($total_cost_check != 0){
?>
	<tr>
		<td style="padding-left: 30px;"><a href="admin.php?view=ee7e0ef4c4cece4b9009&gchart_id=<?=$g1?>&startingdate=<?=$_REQUEST['from']?>&endingdate=<?=$_REQUEST['to']?>" target="_blank" /><?=$rg1['gchart']?></a></td>
		<td style="text-align: right;"><?=number_format($cost_b + $cost_p + $cost_c,2)?></td>
	</tr>
<?php 
} 
} 
?>
	<tr>
		<td id="totalities">TOTAL COST OF SALES</td>
		<td class="total" style="text-align: right;"><?=number_format($total_cost,2)?></td>
	</tr>
</table>
<br />
<table width="700">
	<tr>
		<td id="totalities">GROSS PROFIT</td>
		<td class="total" style="text-align: right;"><?=number_format($gp=$soi-$total_cost,2)?></td>
	</tr>
</table>
<br />
<table width="700">
	<thead>
	<tr>
		<td width="300" id="totalities" style="padding-left: 20px;">EXPENSES</td>
		<td width="170" style="font-weight: bold; text-align: right;"></td>
	</tr>
	</thead>
<?php
//sub class - Expenses (3)	
$sqlg1 = mysql_query("Select * from gchart where gchart_void = '0' and parent_gchart_id = '0' and mclass = 'E' and sub_mclass = '3' order by acode ASC") or die (mysql_error());				 
while($rg1 = mysql_fetch_assoc($sqlg1)){
$g1 = $rg1['gchart_id'];
//parent
$sql1 = mysql_query("Select sum(credit) as total_credit, sum(debit) as total_debit, g.gchart_id, g.gchart, g.mclass, g.sub_mclass
					from gltran_header as h,
					gltran_detail as d,
					gchart as g
					where h.gltran_header_id = d.gltran_header_id and
					h.date between '$from' and '$to' and 
					g.gchart_id = d.gchart_id and
					h.`status` != 'C' and
					g.gchart_id = '$g1'
					group by g.gchart_id
					order by g.mclass
					") or die (mysql_error());

//child				
$sql2 = mysql_query("Select sum(credit) as total_credit, sum(debit) as total_debit, g.gchart_id, g.gchart, g.mclass, g.sub_mclass
					from gltran_header as h,
					gltran_detail as d,
					gchart as g
					where h.gltran_header_id = d.gltran_header_id and
					h.date between '$from' and '$to' and 
					g.gchart_id = d.gchart_id and
					h.`status` != 'C' and
					g.parent_gchart_id = '$g1'
					group by g.parent_gchart_id
					order by g.mclass
					") or die (mysql_error());	

$r1 = mysql_fetch_assoc($sql1);
$r2 = mysql_fetch_assoc($sql2);

$expense_b = $rg1['beg_debit'] - $rg1['beg_credit'];
$expense_p = $r1['total_debit'] - $r1['total_credit'];	
$expense_c = $r2['total_debit'] - $r2['total_credit'];	
$total_expense += $expense_b + $expense_p + $expense_c;
$total_expense_check = $expense_b + $expense_p + $expense_c;
if($total_expense_check != 0){
?>
	<tr>
		<td style="padding-left: 30px;"><a href="admin.php?view=ee7e0ef4c4cece4b9009&gchart_id=<?=$g1?>&startingdate=<?=$_REQUEST['from']?>&endingdate=<?=$_REQUEST['to']?>" target="_blank" /><?=$rg1['gchart']?></a></td>
		<td style="text-align: right;"><?=number_format($expense_b + $expense_p + $expense_c,2)?></td>
	</tr>
<?php 
} 
} 
?>
	<tr>
		<td id="totalities">TOTAL EXPENSE</td>
		<td class="total" style="text-align: right;"><?=number_format($total_expense,2)?></td>
	</tr>
</table>
<br />
<table width="700">
<tr>
	<td id="totalities_main">NET INCOME BEFORE TAX</td>
	<td class="total" style="text-align: right;"><?=number_format($nibt=$gp-$total_expense,2)?></td>
</tr>
</table>
<br />
<table width="700">
	<thead>
	<tr>
		<td width="300" id="totalities" style="padding-left: 20px;">TAXATION</td>
		<td width="170" style="font-weight: bold; text-align: right;"></td>
	</tr>
	</thead>
<?php
//sub class - Taxation (5)	
$sqlg1 = mysql_query("Select * from gchart where gchart_void = '0' and parent_gchart_id = '0' and mclass = 'E' and sub_mclass = '5' order by acode ASC") or die (mysql_error());				 
while($rg1 = mysql_fetch_assoc($sqlg1)){
$g1 = $rg1['gchart_id'];
//parent
$sql1 = mysql_query("Select sum(credit) as total_credit, sum(debit) as total_debit, g.gchart_id, g.gchart, g.mclass, g.sub_mclass
					from gltran_header as h,
					gltran_detail as d,
					gchart as g
					where h.gltran_header_id = d.gltran_header_id and
					h.date between '$from' and '$to' and 
					g.gchart_id = d.gchart_id and
					h.`status` != 'C' and
					g.gchart_id = '$g1'
					group by g.gchart_id
					order by g.mclass
					") or die (mysql_error());

//child				
$sql2 = mysql_query("Select sum(credit) as total_credit, sum(debit) as total_debit, g.gchart_id, g.gchart, g.mclass, g.sub_mclass
					from gltran_header as h,
					gltran_detail as d,
					gchart as g
					where h.gltran_header_id = d.gltran_header_id and
					h.date between '$from' and '$to' and 
					g.gchart_id = d.gchart_id and
					h.`status` != 'C' and
					g.parent_gchart_id = '$g1'
					group by g.parent_gchart_id
					order by g.mclass
					") or die (mysql_error());	

$r1 = mysql_fetch_assoc($sql1);
$r2 = mysql_fetch_assoc($sql2);

$tax_b = $rg1['beg_debit'] - $rg1['beg_credit'];
$tax_p = $r1['total_debit'] - $r1['total_credit'];	
$tax_c = $r2['total_debit'] - $r2['total_credit'];	
$total_tax += $tax_b + $tax_p + $tax_c;
$total_tax_check = $tax_b + $tax_p + $tax_c;
if($total_tax_check != 0){
?>
	<tr>
		<td style="padding-left: 30px;"><a href="admin.php?view=ee7e0ef4c4cece4b9009&gchart_id=<?=$g1?>&startingdate=<?=$_REQUEST['from']?>&endingdate=<?=$_REQUEST['to']?>" target="_blank" /><?=$rg1['gchart']?></a></td>
		<td style="text-align: right;"><?=number_format($tax_b + $tax_p + $tax_c,2)?></td>
	</tr>
<?php 
} 
} 
$net_income = ($total_sales + $total_other_income) - ($total_cost + $total_tax + $total_expense);
?>
	<tr>
		<td id="totalities">TOTAL TAXATION</td>
		<td class="total" style="text-align: right;"><?=number_format($total_tax,2)?></td>
	</tr>
</table>
<!-- Totalities -->
<br />
<table width="700">
<tr>
	<td id="totalities_main">NET INCOME</td>
	<td class="total" style="text-align: right;"><?=number_format($nibt-$total_tax,2)?></td>
</tr>
</table>
</div>
</body>
</html>