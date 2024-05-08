<?php
//ini_set('max_execution_time', 1000);

require_once('my_Classes/options.class.php');
include_once("conf/ucs.conf.php");
require_once(dirname(__FILE__).'/library/lib.php');
$options=new options();	

$from 		= $_REQUEST['from'];
$to   		= $_REQUEST['to'];
$gchart_id  = $_REQUEST['gchart_id'];
	
$sqlg = mysql_query("Select * from gchart where gchart_id = '$gchart_id'") or die (mysql_error());
$rg = mysql_fetch_assoc($sqlg);
$gchart_name = $rg['gchart'];
$total_credit = 0;
$total_debit = 0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>BEGINNING BALANCE BREAKDOWN</title>
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
}

.top_total{
	border-bottom: 1px solid black;
	font-weight: bold;
}

.parent_total{
	border-bottom: 1px solid black;
	border-top: 1px solid black;
	font-weight: bold;
}
#numbers_format{
	text-align: right;
	padding-left: 20px;
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
           	BEGINNING BALANCE BREAKDOWN - <?=$rg['gchart']?><br />
			<em>From <?=date("F j, Y",strtotime($from));?> To <?=date("F j, Y",strtotime($to));?></em>
        </div>      
<div class="container">

<?php
$sql = mysql_query("Select sum(credit) as total_credit, sum(debit) as total_debit, g.gchart_id, g.gchart, g.mclass, g.parent_gchart_id
					from gltran_header as h,
					gltran_detail as d,
					gchart as g
					where h.gltran_header_id = d.gltran_header_id and
					h.date between '$from' and '$to' and 
					g.gchart_id = d.gchart_id and
					h.`status` != 'C' and
					g.gchart_id = '$gchart_id'
					group by g.gchart
					") or die (mysql_error());
?>
<table width="600"><!-- Parent -->
	<thead>
	<tr>
		<td style="font-weight: bold;" width="300" class="top_total">Parent Account</td>
		<td style="font-weight: bold; text-align: center; width: 117px;" class="top_total">Balance</td>
		<td style="font-weight: bold; text-align: center;" class="top_total" width="50">Class</td>
	</tr>
	</thead>
<?php	
$credit = 0;
$debit = 0;
$count = 0;

$count = mysql_num_rows($sql);
if(mysql_num_rows($sql) > 0){
	while($r = mysql_fetch_assoc($sql)){
	$credit1 += $r['total_credit'];
	$debit1 += $r['total_debit'];
?>
	<tr>
		<td class="parent_total"style="padding-right: 50px;"><?=$gchart_name?></td>
		<td id="numbers_format" style="border-top: 1px solid black;border-bottom: 1px solid black;"><?=number_format(($r['total_debit']-$r['total_credit'])+($r['beg_debit']-$r['beg_credit']),2)?></td>
		<td class="parent_total" style="text-align: center;"><?=$r['mclass']?></td>
	</tr>
<?php
	}
}else{
?>
	<tr>
		<td style="padding-right: 50px;"><?=$rg['gchart']?></td>
		<td></td>
		<td></td>
	</tr>
<?php } ?>
		
</table>
</br>
<?php

$sql = mysql_query("Select sum(credit) as total_credit, sum(debit) as total_debit, g.gchart_id, g.gchart, g.mclass, g.parent_gchart_id
					from gltran_header as h,
					gltran_detail as d,
					gchart as g
					where h.gltran_header_id = d.gltran_header_id and
					h.date between '$from' and '$to' and 
					g.gchart_id = d.gchart_id and
					h.`status` != 'C' and
					g.parent_gchart_id != '0' and
					g.parent_gchart_id = '$gchart_id'
					group by g.gchart
					") or die (mysql_error());
?>
<table width="600"><!-- Child -->
	<thead>
	<tr>
		<td style="font-weight: bold;" width="300" class="top_total">Child Accounts</td>
		<td style="font-weight: bold; width: 117px; text-align: center;" class="top_total">Balance</td>
		<td style="font-weight: bold; text-align: center;" class="top_total" width="50">Class</td>
	</tr>
	</thead>
<?php	
	$credit = 0;
	$debit = 0;
while($r = mysql_fetch_assoc($sql)){
	$credit += $r['total_credit'];
	$debit += $r['total_debit'];
	if(($r['total_debit']-$r['total_credit']) != 0){
?>
	<tr>
		<td style="padding-right: 50px;"><?=$r['gchart']?></td>
		<td id="numbers_format"><?=number_format($r['total_debit']-$r['total_credit'],2)?></td>
		<td style="text-align: center;"><?=$r['mclass']?></td>
	</tr>
<?php
	}
}
	$total_debit = $debit + $debit1;
	$total_credit = $credit + $credit1;
	$balance2 = $total_debit - $total_credit;
?>
	<tr>
		<td class="total">TOTAL</td>
		<td id="numbers_format" class="total"><?=number_format($balance2,2)?></td>
		<td id="numbers_format" class="total"></td>
	</tr>	
</table>
</div>
</body>
</html>
