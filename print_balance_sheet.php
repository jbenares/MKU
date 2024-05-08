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

function getRunningBalance($g1,$from){
	$new_start = date('Y-m-d', strtotime('-1 day', strtotime($from)));
	
	$sql = mysql_query("select 
				sum(d.debit) as total_debit, sum(d.credit) as total_credit, g.mclass 
				from
				gltran_header as h,
				gltran_detail as d,
				gchart as g
				where
				h.gltran_header_id = d.gltran_header_id and
				d.gchart_id = g.gchart_id and
				h.`status` != 'C' and
				g.gchart_void = '0' and
				h.date between '2015-01-01' and '$new_start' and
				(g.parent_gchart_id = '$g1' or g.gchart_id = '$g1')");
	$r = mysql_fetch_assoc($sql);			
	
	$running['debit'] = $r['total_debit'];
	$running['credit'] = $r['total_credit'];
	
	return $running;
}

function getTransactions($g1,$from,$to){
	$sql = mysql_query("Select sum(debit) as total_debit, sum(credit) as total_credit
						from gltran_header as h,
						gltran_detail as d,
						gchart as g
						where h.gltran_header_id = d.gltran_header_id and
						h.date between '$from' and '$to' and 
						g.gchart_id = d.gchart_id and
						h.`status` != 'C' and
						(g.gchart_id = '$g1' or g.parent_gchart_id = '$g1')
						group by g.mclass
						order by g.acode") or die (mysql_error());
	$r = mysql_fetch_assoc($sql);
	
	$trans['debit'] = $r['total_debit'];
	$trans['credit'] = $r['total_credit'];
	
	return $trans;
}
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
           	BALANCE SHEET<br />
			<em>From <?=date("F j, Y",strtotime($from));?> To <?=date("F j, Y",strtotime($to));?></em>
        </div>      
<div class="container">
<!-- Current Assets-->
<table width="700">
	<thead>
	<tr>
		<td width="300" id="totalities" style="padding-left: 20px;">CURRENT ASSETS</td>
		<td width="170" style="font-weight: bold; text-align: right;"></td>
	</tr>
	</thead>
<?php
//sub class - Current Assset (6)	
$sqlg1 = mysql_query("Select * from gchart where gchart_void = '0' and parent_gchart_id = '0' and mclass = 'A' and sub_mclass = '6' order by acode ASC") or die (mysql_error());				 
while($rg1 = mysql_fetch_assoc($sqlg1)){
$g1 = $rg1['gchart_id'];
//parent and child

$trans = getTransactions($g1,$from,$to);
$running = getRunningBalance($g1,$from);

$current_asset_b = $rg1['beg_debit'] - $rg1['beg_credit'];
$current_asset_p = $trans['debit'] - $trans['credit'];	
$current_asset_c = $running['debit'] - $running['credit'];	
$total_current_asset += $current_asset_b + $current_asset_p + $current_asset_c;
$total_current_asset_check = $current_asset_b + $current_asset_p + $current_asset_c;
if($total_current_asset_check != 0){
	?>
		<tr>
			<td style="padding-left: 30px;"><a href="admin.php?view=ee7e0ef4c4cece4b9009&gchart_id=<?=$g1?>&startingdate=<?=$_REQUEST['from']?>&endingdate=<?=$_REQUEST['to']?>" target="_blank" /><?=$rg1['gchart']?></a></td>
			<td style="text-align: right;"><?=number_format(($current_asset_b + $current_asset_c + $current_asset_p),2)?></td>
		</tr>
	<?php 
	} 
}
?>
	<tr>
		<td id="totalities">TOTAL CURRENT ASSET</td>
		<td class="total" style="text-align: right;"><?=number_format($total_current_asset,2)?></td>
	</tr>
</table>
<br />
<!-- Fixed Assets-->
<table width="700">
	<thead>
	<tr>
		<td width="300" id="totalities" style="padding-left: 20px;">NON-CURRENT ASSET</td>
		<td width="170" style="font-weight: bold; text-align: right;"></td>
	</tr>
	</thead>
<?php
//sub class - Fixed Assset (7)	
$sqlg1 = mysql_query("Select * from gchart where gchart_void = '0' and parent_gchart_id = '0' and mclass = 'A' and sub_mclass = '7' order by acode ASC") or die (mysql_error());				 
while($rg1 = mysql_fetch_assoc($sqlg1)){
$g1 = $rg1['gchart_id'];

//parent and child
$trans = getTransactions($g1,$from,$to);
$running = getRunningBalance($g1,$from);

$fixed_asset_b = $rg1['beg_debit'] - $rg1['beg_credit'];
$fixed_asset_p = $trans['debit'] - $trans['credit'];	
$fixed_asset_c = $running['debit'] - $running['credit'];	
$total_fixed_asset += $fixed_asset_b + $fixed_asset_p + $fixed_asset_c;
$total_fixed_asset_check = $fixed_asset_b + $fixed_asset_p + $fixed_asset_c;
if($total_fixed_asset_check != 0){
?>
	<tr>
		<td style="padding-left: 30px;"><a href="admin.php?view=ee7e0ef4c4cece4b9009&gchart_id=<?=$g1?>&startingdate=<?=$_REQUEST['from']?>&endingdate=<?=$_REQUEST['to']?>" target="_blank" /><?=$rg1['gchart']?></a></td>
		<td style="text-align: right;"><?=number_format($fixed_asset_p + $fixed_asset_c + $fixed_asset_b,2)?></td>
	</tr>
<?php 
} 
}
?>
	<tr>
		<td id="totalities">TOTAL PPE</td>
		<td class="total" style="text-align: right;"><?=number_format($total_fixed_asset,2)?></td>
	</tr>
</table>
<br />
<!-- Fixed Assets-->
<table width="700">
	<thead>
	<tr>
		<td width="300" id="totalities" style="padding-left: 20px;">LONG TERM ASSETS</td>
		<td width="170" style="font-weight: bold; text-align: right;"></td>
	</tr>
	</thead>
<?php
//sub class - Logn Term Assset (8)	
$sqlg1 = mysql_query("Select * from gchart where gchart_void = '0' and parent_gchart_id = '0' and mclass = 'A' and sub_mclass = '8' order by acode ASC") or die (mysql_error());				 
while($rg1 = mysql_fetch_assoc($sqlg1)){
$g1 = $rg1['gchart_id'];

//parent and child
$trans = getTransactions($g1,$from,$to);
$running = getRunningBalance($g1,$from);

$longterm_asset_b = $rg1['beg_debit'] - $rg1['beg_credit'];
$longterm_asset_p = $trans['debit'] - $trans['credit'];	
$longterm_asset_c = $running['debit'] - $running['credit'];	
$total_longterm_asset += $longterm_asset_b + $longterm_asset_p + $longterm_asset_c;
$total_longterm_asset_check = $longterm_asset_b + $longterm_asset_p + $longterm_asset_c;
if($total_longterm_asset_check != 0){
?>
	<tr>
		<td style="padding-left: 30px;"><a href="admin.php?view=ee7e0ef4c4cece4b9009&gchart_id=<?=$g1?>&startingdate=<?=$_REQUEST['from']?>&endingdate=<?=$_REQUEST['to']?>" target="_blank" /><?=$rg1['gchart']?></a></td>
		<td style="text-align: right;"><?=number_format($longterm_asset_p + $longterm_asset_c + $longterm_asset_b,2)?></td>
	</tr>
<?php 
} 
}
?>
	<tr>
		<td id="totalities">TOTAL LONG TERM ASSET</td>
		<td class="total" style="text-align: right;"><?=number_format($total_longterm_asset,2)?></td>
	</tr>
</table>

<br />
<!-- Other Assets-->
<table width="700">
	<thead>
	<tr>
		<td width="300" id="totalities" style="padding-left: 20px;">OTHER ASSETS</td>
		<td width="170" style="font-weight: bold; text-align: right;"></td>
	</tr>
	</thead>
<?php
//sub class - Logn Term Assset (13)	
$sqlg1 = mysql_query("Select * from gchart where gchart_void = '0' and parent_gchart_id = '0' and mclass = 'A' and sub_mclass = '13' order by acode ASC") or die (mysql_error());				 
while($rg1 = mysql_fetch_assoc($sqlg1)){
$g1 = $rg1['gchart_id'];

//parent and child
$trans = getTransactions($g1,$from,$to);
$running = getRunningBalance($g1,$from);

$other_asset_b = $rg1['beg_debit'] - $rg1['beg_credit'];
$other_asset_p = $trans['debit'] - $trans['credit'];	
$other_asset_c = $running['debit'] - $running['credit'];	
$total_other_asset += $other_asset_b + $other_asset_p + $other_asset_c;
$total_other_asset_check = $other_asset_b + $other_asset_p + $other_asset_c;
if($total_other_asset_check != 0){
?>
	<tr>
		<td style="padding-left: 30px;"><a href="admin.php?view=ee7e0ef4c4cece4b9009&gchart_id=<?=$g1?>&startingdate=<?=$_REQUEST['from']?>&endingdate=<?=$_REQUEST['to']?>" target="_blank" /><?=$rg1['gchart']?></a></td>
		<td style="text-align: right;"><?=number_format($other_asset_c + $other_asset_p + $other_asset_b,2)?></td>
	</tr>
<?php 
} 
}
?>
	<tr>
		<td id="totalities">TOTAL OTHER ASSET</td>
		<td class="total" style="text-align: right;"><?=number_format($total_other_asset,2)?><br /></td>
	</tr>
<?php
$total_asset = $total_other_asset + $total_longterm_asset + $total_fixed_asset + $total_current_asset;
?>
	<tr>
		<td id="totalities_main">TOTAL ASSET</td>
		<td class="total" style="text-align: right;"><?=number_format($total_asset,2)?></td>
	</tr>
</table>


<br />
<!-- Current Liabilities-->
<table width="700">
	<thead>
	<tr>
		<td width="300" id="totalities" style="padding-left: 20px;">CURRENT LIABILITIES</td>
		<td width="170" style="font-weight: bold; text-align: right;"></td>
	</tr>
	</thead>
<?php
//sub class - Current Liabilities (9)	
$sqlg1 = mysql_query("Select * from gchart where gchart_void = '0' and parent_gchart_id = '0' and mclass = 'L' and sub_mclass = '9' order by acode ASC") or die (mysql_error());				 
while($rg1 = mysql_fetch_assoc($sqlg1)){
$g1 = $rg1['gchart_id'];

//parent and child
$trans = getTransactions($g1,$from,$to);
$running = getRunningBalance($g1,$from);

$current_liabilities_b = $rg1['beg_credit'] - $rg1['beg_debit'];
$current_liabilities_p = $trans['credit'] - $trans['debit'];	
$current_liabilities_c = $running['credit'] - $running['debit'];	
$total_current_liabilities += $current_liabilities_b + $current_liabilities_p + $current_liabilities_c;
$total_current_liabilities_check = $current_liabilities_b + $current_liabilities_p + $current_liabilities_c;
if($total_current_liabilities_check != 0){
?>
	<tr>
		<td style="padding-left: 30px;"><a href="admin.php?view=ee7e0ef4c4cece4b9009&gchart_id=<?=$g1?>&startingdate=<?=$_REQUEST['from']?>&endingdate=<?=$_REQUEST['to']?>" target="_blank" /><?=$rg1['gchart']?><a/></td>
		<td style="text-align: right;"><?=number_format($current_liabilities_c + $current_liabilities_p + $current_liabilities_b,2)?></td>
	</tr>
<?php 
} 
}
?>
	<tr>
		<td id="totalities">TOTAL CURRENT LIABILITIES</td>
		<td class="total" style="text-align: right;"><?=number_format($total_current_liabilities,2)?><br /></td>
	</tr>
</table>

<br />
<!-- Long Term Liabilities-->
<table width="700">
	<thead>
	<tr>
		<td width="300" id="totalities" style="padding-left: 20px;">LONG TERM LIABILITIES</td>
		<td width="170" style="font-weight: bold; text-align: right;"></td>
	</tr>
	</thead>
<?php
//sub class - Long Term Liabilities (10)	
$sqlg1 = mysql_query("Select * from gchart where gchart_void = '0' and parent_gchart_id = '0' and mclass = 'L' and sub_mclass = '10' order by acode ASC") or die (mysql_error());				 
while($rg1 = mysql_fetch_assoc($sqlg1)){
$g1 = $rg1['gchart_id'];

//parent and child
$trans = getTransactions($g1,$from,$to);
$running = getRunningBalance($g1,$from);

$longterm_liabilities_b = $rg1['beg_credit'] - $rg1['beg_debit'];
$longterm_liabilities_p = $trans['credit'] - $trans['debit'];	
$longterm_liabilities_c = $running['credit'] - $running['debit'];	
$total_longterm_liabilities += $longterm_liabilities_b + $longterm_liabilities_p + $longterm_liabilities_c;
$total_current_liabilities_check = $longterm_liabilities_b + $longterm_liabilities_p + $longterm_liabilities_c;
if($total_current_liabilities_check != 0){
?>
	<tr>
		<td style="padding-left: 30px;"><a href="admin.php?view=ee7e0ef4c4cece4b9009&gchart_id=<?=$g1?>&startingdate=<?=$_REQUEST['from']?>&endingdate=<?=$_REQUEST['to']?>" target="_blank" /><?=$rg1['gchart']?></a></td>
		<td style="text-align: right;"><?=number_format($longterm_liabilities_c + $longterm_liabilities_p + $longterm_liabilities_b,2)?></td>
	</tr>
<?php 
} 
}
?>
	<tr>
		<td id="totalities">TOTAL LONG TERM LIABILITIES</td>
		<td class="total" style="text-align: right;"><?=number_format($total_longterm_liabilities,2)?><br /></td>
	</tr>
</table>

<br />
<!-- Other Liabilities -->
<table width="700">
	<thead>
	<tr>
		<td width="300" id="totalities" style="padding-left: 20px;">OTHER LIABILITIES</td>
		<td width="170" style="font-weight: bold; text-align: right;"></td>
	</tr>
	</thead>
<?php
//sub class - Retained Earnings (11)	
$sqlg1 = mysql_query("Select * from gchart where gchart_void = '0' and parent_gchart_id = '0' and mclass = 'L' and sub_mclass = '11' order by acode ASC") or die (mysql_error());				 
while($rg1 = mysql_fetch_assoc($sqlg1)){
$g1 = $rg1['gchart_id'];

//parent and child
$trans = getTransactions($g1,$from,$to);
$running = getRunningBalance($g1,$from);

$other_liabilities_b = $rg1['beg_credit'] - $rg1['beg_debit'];
$other_liabilities_p = $trans['credit'] - $trans['debit'];	
$other_liabilities_c = $running['credit'] - $running['debit'];	
$total_other_liabilities += $other_liabilities_b + $other_liabilities_p + $other_liabilities_c;
$total_other_liabilities_check = $other_liabilities_b + $other_liabilities_p + $other_liabilities_c;
if($total_other_liabilities_check != 0){
?>
	<tr>
		<td style="padding-left: 30px;"><a href="admin.php?view=ee7e0ef4c4cece4b9009&gchart_id=<?=$g1?>&startingdate=<?=$_REQUEST['from']?>&endingdate=<?=$_REQUEST['to']?>" target="_blank" /><?=$rg1['gchart']?></a></td>
		<td style="text-align: right;"><?=number_format($other_liabilities_c + $other_liabilities_p + $other_liabilities_b,2)?></td>
	</tr>
<?php 
} 
}
$total_liabilities = $total_current_liabilities + $total_longterm_liabilities + $total_other_liabilities;
?>
	<tr>
		<td id="totalities">TOTAL OTHER LIABILITIES</td>
		<td class="total" style="text-align: right;"><?=number_format($total_other_liabilities,2)?><br /></td>
	</tr>
	<tr>
		<td id="totalities_main">TOTAL LIABILITIES</td>
		<td class="total" style="text-align: right;"><?=number_format($total_liabilities,2)?><br /></td>
	</tr>
</table>

<br />
<!-- Equity -->
<table width="700">
	<thead>
	<tr>
		<td width="300" id="totalities" style="padding-left: 20px;">EQUITY</td>
		<td width="170" style="font-weight: bold; text-align: right;"></td>
	</tr>
	</thead>
<?php
//sub class - Retained Earnings (12)	
$sqlg1 = mysql_query("Select * from gchart where gchart_void = '0' and parent_gchart_id = '0' and mclass = 'R' and sub_mclass = '12' order by acode ASC") or die (mysql_error());				 
while($rg1 = mysql_fetch_assoc($sqlg1)){
$g1 = $rg1['gchart_id'];

//parent and child
$trans = getTransactions($g1,$from,$to);
$running = getRunningBalance($g1,$from);

$equity_b = $rg1['beg_credit'] - $rg1['beg_debit'];
$equity_p = $trans['credit'] - $trans['debit'];	
$equity_c = $running['credit'] - $running['debit'];	
$total_equity += $equity_b + $equity_p + $equity_c;
$total_equity_check = $equity_b + $equity_p + $equity_c;
if($total_other_liabilities_check != 0){
?>
	<tr>
		<td style="padding-left: 30px;"><a href="admin.php?view=ee7e0ef4c4cece4b9009&gchart_id=<?=$g1?>&startingdate=<?=$_REQUEST['from']?>&endingdate=<?=$_REQUEST['to']?>" target="_blank" /><?=$rg1['gchart']?></a></td>
		<td style="text-align: right;"><?=number_format($equity_c + $equity_p + $equity_b,2)?></td>
	</tr>
<?php 
} 
}
?>
	<tr>
		<td id="totalities">TOTAL EQUITY</td>
		<td class="total" style="text-align: right;"><?=number_format($total_equity,2)?><br /></td>
	</tr>
</table>

<?php
//sub class - Sales (1)	
$sqlg1 = mysql_query("Select * from gchart where gchart_void = '0' and parent_gchart_id = '0' and mclass = 'I' and sub_mclass = '1' order by acode ASC") or die (mysql_error());				 
while($rg1 = mysql_fetch_assoc($sqlg1)){
$g1 = $rg1['gchart_id'];

//parent and child
$trans = getTransactions($g1,$from,$to);
$running = getRunningBalance($g1,$from);

$sales_b = $rg1['beg_credit'] - $rg1['beg_debit'];
$sales_p = $trans['credit'] - $trans['debit'];	
$sales_c = $running['credit'] - $running['debit'];	
$total_sales += $sales_b + $sales_p + $sales_c;
} 
?>

<?php
//sub class - Other Income (4)	
$sqlg1 = mysql_query("Select * from gchart where gchart_void = '0' and parent_gchart_id = '0' and mclass = 'I' and sub_mclass = '4' order by acode ASC") or die (mysql_error());				 
while($rg1 = mysql_fetch_assoc($sqlg1)){
$g1 = $rg1['gchart_id'];

//parent and child
$trans = getTransactions($g1,$from,$to);
$running = getRunningBalance($g1,$from);

$other_income_b = $rg1['beg_credit'] - $rg1['beg_debit'];
$other_income_p = $trans['credit'] - $trans['debit'];	
$other_income_c = $running['credit'] - $running['debit'];	
$total_other_income += $other_income_b + $other_income_p + $other_income_c;
} 
?>

<?php
//sub class - Cost of Sales (2)	
$sqlg1 = mysql_query("Select * from gchart where gchart_void = '0' and parent_gchart_id = '0' and mclass = 'E' and sub_mclass = '2' order by acode ASC") or die (mysql_error());				 
while($rg1 = mysql_fetch_assoc($sqlg1)){
$g1 = $rg1['gchart_id'];

//parent and child
$trans = getTransactions($g1,$from,$to);
$running = getRunningBalance($g1,$from);

$cost_b = $rg1['beg_debit'] - $rg1['beg_credit'];
$cost_p = $trans['debit'] - $trans['credit'];	
$cost_c = $running['debit'] - $running['credit'];	
$total_cost += $cost_b + $cost_p + $cost_c;
} 
?>

<?php
//sub class - Expenses (3)	
$sqlg1 = mysql_query("Select * from gchart where gchart_void = '0' and parent_gchart_id = '0' and mclass = 'E' and sub_mclass = '3' order by acode ASC") or die (mysql_error());				 
while($rg1 = mysql_fetch_assoc($sqlg1)){
$g1 = $rg1['gchart_id'];

//parent and child
$trans = getTransactions($g1,$from,$to);
$running = getRunningBalance($g1,$from);

$expense_b = $rg1['beg_debit'] - $rg1['beg_credit'];
$expense_p = $trans['debit'] - $trans['credit'];	
$expense_c = $running['debit'] - $running['credit'];	
$total_expense += $expense_b + $expense_p + $expense_c;
} 
?>

<?php
//sub class - Taxation (5)	
$sqlg1 = mysql_query("Select * from gchart where gchart_void = '0' and parent_gchart_id = '0' and mclass = 'E' and sub_mclass = '5' order by acode ASC") or die (mysql_error());				 
while($rg1 = mysql_fetch_assoc($sqlg1)){
$g1 = $rg1['gchart_id'];

//parent and child
$trans = getTransactions($g1,$from,$to);
$running = getRunningBalance($g1,$from);

$tax_b = $rg1['beg_debit'] - $rg1['beg_credit'];
$tax_p = $trans['debit'] - $trans['credit'];	
$tax_c = $running['debit'] - $running['credit'];	
$total_tax += $tax_b + $tax_p + $tax_c;
}


$net_income = ($total_sales + $total_other_income) - ($total_cost + $total_tax + $total_expense);
$tle = $net_income + $total_liabilities + $total_equity;
?>

<!-- Totalities -->
<br />
<table width="700">
	<tr>
		<td id="totalities_main">NET INCOME</td>
		<td class="total" style="text-align: right;"><?=number_format($net_income,2)?></td>
	</tr>
	<tr>
		<td id="totalities_main">TOTAL LIABILITIES & EQUITY</td>
		<td class="total" style="text-align: right;"><?=number_format($tle,2)?></td>
	</tr>
</table>
</div>
</body>
</html>
