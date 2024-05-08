<?php
//ini_set('max_execution_time', 1000);

require_once('my_Classes/options.class.php');
include_once("conf/ucs.conf.php");
require_once(dirname(__FILE__).'/library/lib.php');
$options=new options();	

$from = $_REQUEST['from'];
$to   = $_REQUEST['to'];
$b   = $_REQUEST['b'];
$error_type   = $_REQUEST['error_type'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ERROR CHECKER</title>
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
	require("form_heading.php");
?>   
<div class="container">

<?php
if($error_type == 'acc'){
?>
<div style="text-align:left; font-size:12px; margin-bottom:20px; font-weight:bold;">
    ERROR CHECKER - NO ACCOUNTS <br />
	<em>From <?=date("F j, Y",strtotime($from));?> To <?=date("F j, Y",strtotime($to));?></em>
</div>   
<?php
$sql = mysql_query("Select * from gltran_header as h 
					INNER JOIN gltran_detail as d  
					where 
						h.date between '$from' and '$to' and
						h.gltran_header_id = d.gltran_header_id and 
						d.gchart_id = '0' and
						(d.debit != '0' OR d.credit != '0') and
						h.`status` != 'C'") or die (mysql_error());
?>
<table width="1200"><!-- Parent -->
	<thead>
	<tr>
		<td style="font-weight: bold;" class="top_total">#</td>
		<td style="font-weight: bold; text-align: center;" class="top_total">X Reference</td>
		<td style="font-weight: bold; text-align: center;" class="top_total" >Date</td>
		<td style="font-weight: bold; text-align: center;" class="top_total" >General Reference</td>
		<td style="font-weight: bold; text-align: center;" class="top_total" >Gchart ID</td>
		<td style="font-weight: bold; text-align: center;" class="top_total" >Debit</td>
		<td style="font-weight: bold; text-align: center;" class="top_total" >Credit</td>
	</tr>
	</thead>
<?php	
$credit = 0;
$debit = 0;
$count = 0;

$count = mysql_num_rows($sql);
if(mysql_num_rows($sql) > 0){
	while($r = mysql_fetch_assoc($sql)){
		$i++
?>
	<tr>
		<td><?=$i?></td>
		<td style="text-align: center;"><?=$r['xrefer']?></td>
		<td style="text-align: center;"><?=$r['date']?></td>
		<td style="text-align: center;"><?=$r['generalreference']?></td>
		<td style="text-align: center;"><?=$r['gchart_id']?></td>
		<td style="text-align: right;"><?=number_format($r['debit'],2)?></td>
		<td style="text-align: right;"><?=number_format($r['credit'],2)?></td>
	</tr>
<?php
	} 
} 
?>	
</table>
<?php }else if($error_type == 'imba'){ ?>
<div style="text-align:left; font-size:12px; margin-bottom:20px; font-weight:bold;">
    ERROR CHECKER - UNBALANCE <br />
	<em>From <?=date("F j, Y",strtotime($from));?> To <?=date("F j, Y",strtotime($to));?></em>
</div>   
<?php
$sql = mysql_query("Select sum(debit) as total_debit, sum(credit) as total_credit , h.gltran_header_id, h.date, h.xrefer, h.generalreference
				from gltran_header as h 
				INNER JOIN gltran_detail as d  
				where 
					h.date between '$from' and '$to' and
					h.gltran_header_id = d.gltran_header_id and 
					h.`status` != 'C' and debit != credit
					group by h.gltran_header_id") or die (mysql_error());
?>
<table width="1200"><!-- Parent -->
	<thead>
	<tr>
		<td style="font-weight: bold;" class="top_total">#</td>
		<td style="font-weight: bold; text-align: center;" class="top_total">X Reference</td>
		<td style="font-weight: bold; text-align: center;" class="top_total">Date</td>
		<td style="font-weight: bold; text-align: center;" class="top_total">General Reference</td>
		<td style="font-weight: bold; text-align: center;" class="top_total" >Debit</td>
		<td style="font-weight: bold; text-align: center;" class="top_total" >Credit</td>
		<td style="font-weight: bold; text-align: center;" class="top_total" >Difference</td>
	</tr>
	</thead>
<?php	
$count = mysql_num_rows($sql);
if(mysql_num_rows($sql) > 0){
	while($r = mysql_fetch_assoc($sql)){
		if($r['total_debit'] > $r['total_credit']){
		$total = $r['total_debit'] - $r['total_credit'];
		}else{
		$total = $r['total_credit'] - $r['total_debit'];	
		}
		if($total > 0 || $total < 0){
		$i++;
?>
	<tr>
		<td><?=$i?></td>
		<td style="text-align: center;"><?=$r['xrefer']?></td>
		<td style="text-align: center;"><?=$r['date']?></td>
		<td style="text-align: center;"><?=$r['generalreference']?></td>
		<td style="text-align: right;"><?=number_format($r['total_debit'],2)?></td>
		<td style="text-align: right;"><?=number_format($r['total_credit'],2)?></td>
		<td style="text-align: right;"><?=number_format($total,2)?></td>
	</tr>
<?php
		}
	} 
} 
?>	
</table>

<?php } ?>
</br>
</div>
</body>
</html>
