<?php
//ini_set('max_execution_time', 1000);

require_once('my_Classes/options.class.php');
include_once("conf/ucs.conf.php");
require_once(dirname(__FILE__).'/library/lib.php');


$from = $_REQUEST['from'];
$to   = $_REQUEST['to'];
$options_bb   = $_REQUEST['options_bb'];

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
<title>BEGINNING BALANCE</title>
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
<?php if($_REQUEST['options_bb'] == 'all'){ ?>		
<div style="text-align:left; font-size:12px; margin-bottom:20px; font-weight:bold;">
    BEGINNING BALANCE - DETAILED<br />
	<em>From <?=date("F j, Y",strtotime($from));?> To <?=date("F j, Y",strtotime($to));?></em>
</div>   
<div class="container">
<table>
	<thead>
	<tr>
		<td style="font-weight: bold; padding-right: 50px;" class="top_total">Accounts</td>
		<td style="font-weight: bold; text-align: right;" class="top_total">Balance</td>
		<td style="font-weight: bold; margin-left: 20px; text-align: right;" class="top_total">Class</td>
	</tr>
	</thead>
<?php	
$sql = mysql_query("Select sum(credit) as total_credit, sum(debit) as total_debit, g.gchart_id, g.gchart, g.mclass, g.parent_gchart_id
					from gltran_header as h,
					gltran_detail as d,
					gchart as g
					where h.gltran_header_id = d.gltran_header_id and
					h.date between '$from' and '$to' and 
					g.gchart_id = d.gchart_id and
					h.`status` != 'C'
					group by g.gchart
					") or die (mysql_error());

	$credit = 0;
	$debit = 0;
	$balance = 0;
	$total_balance = 0;
while($r = mysql_fetch_assoc($sql)){
	//$credit += $r['total_credit'];
	//$debit += $r['total_debit'];
	$balance = $r['total_debit'] - $r['total_credit'];
	$total_balance += $balance;
	if($balance != 0){
?>
	<tr>
		<td style="padding-right: 50px;"><?=$r['gchart']?></td>
		<td style="text-align: right;"><?=number_format($balance,2)?></td>
		<td style="text-align: right;"><?=$r['mclass']?></td>
	</tr>
<?php
	}
}
?>
	<tr>
		<td class="total">TOTAL</td>
		<td class="total" style="text-align: right;"><?=number_format($total_balance,2)?></td>
		<td class="total" style="margin-left: 20px;text-align: right;"></td>
	</tr>
</table>
</div>
<?php    
}
if($_REQUEST['options_bb'] == 'parent'){
	
$sql = mysql_query("Select sum(credit) as total_credit, sum(debit) as total_debit, g.gchart_id, g.gchart, g.mclass
					from gltran_header as h,
					gltran_detail as d,
					gchart as g
					where h.gltran_header_id = d.gltran_header_id and
					h.date between '$from' and '$to' and 
					g.gchart_id = d.gchart_id and
					h.`status` != 'C' and
					g.parent_gchart_id = '0'
					group by g.gchart_id
					order by g.mclass
					") or die (mysql_error());
					
$sql2 = mysql_query("Select sum(credit) as total_credit, sum(debit) as total_debit, g.gchart_id, g.gchart, g.mclass, g.parent_gchart_id as g_parent
					from gltran_header as h,
					gltran_detail as d,
					gchart as g
					where h.gltran_header_id = d.gltran_header_id and
					h.date between '$from' and '$to' and 
					g.gchart_id = d.gchart_id and
					h.`status` != 'C' and
					g.parent_gchart_id != '0'
					group by g.parent_gchart_id
					order by g.mclass
					") or die (mysql_error());

?>
        <div style="text-align:left; font-size:12px; margin-bottom:20px; font-weight:bold;">
           	BEGINNING BALANCE - PARENT<br />
			<em>From <?=date("F j, Y",strtotime($from));?> To <?=date("F j, Y",strtotime($to));?></em>
        </div>   
<div class="container">
<table>
	<thead>
	<tr>
		<td style="font-weight: bold; padding-right: 50px;" class="top_total">Accounts</td>
		<td style="font-weight: bold; text-align: right;" class="top_total">Balance</td>
		<td style="font-weight: bold; margin-left: 20px; text-align: right;" class="top_total">Class</td>
	</tr>
	</thead>
<!-- Parents -->
<?php	
	$credit = 0;
	$debit = 0;
	$balance1 = 0;
while($r = mysql_fetch_assoc($sql)){
	$credit_p += $r['total_credit'];
	$debit_p += $r['total_debit'];
	$balance1 = ($r['total_debit'] - $r['total_credit']) + ($r['beg_debit'] - $r['beg_credit']);
	if($balance1 != 0){
?>
	<tr>
		<td style="padding-right: 50px;"><?=$r['gchart']?></td>
		<td style="text-align: right;"><?=number_format($balance1,2)?></td>
		<td style="text-align: right;"><?=$r['mclass']?></td>
	</tr>
	
<?php }
} 
	
	$credit = 0;
	$debit = 0;
	$balance2 = 0;
while($r2 = mysql_fetch_assoc($sql2)){
	
	$parent = $r2[g_parent];
	$sql_p = mysql_query("Select gchart from gchart where gchart_id = '$parent'") or die (mysql_error());
	$rp = mysql_fetch_assoc($sql_p);
	$balance2 = $r2['total_debit'] - $r2['total_credit'];
	$credit_c += $r2['total_credit'];
	$debit_c += $r2['total_debit'];
	if($balance2 != 0){
?>
	<tr>
		<td style="padding-right: 50px;"><?=$rp['gchart']?></td>
		<td style="text-align: right;"><?=number_format($balance2,2)?></td>
		<td style="text-align: right;"><?=$r2['mclass']?></td>
	</tr>
<?php
	}
}
	$total_balance = 0;
	$grand_total_debit = $debit_p + $debit_c;
	$grand_total_credit = $credit_p + $credit_c;
	$total_balance = $grand_total_debit - $grand_total_credit;
?>
	<tr>
		<td class="total">TOTAL</td>
		<td class="total" style="text-align: right;"><?=number_format($total_balance,2)?></td>
		<td class="total" style="margin-left: 20px;text-align: right;"></td>
	</tr>
</table>
</div>
<?php } ?>
</body>
</html>
