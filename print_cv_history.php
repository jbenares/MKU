<?php
//ini_set('max_execution_time', 1000);

require_once('my_Classes/options.class.php');
include_once("conf/ucs.conf.php");
require_once(dirname(__FILE__).'/library/lib.php');
$options=new options();	

$from 		= $_REQUEST['from'];
$to   		= $_REQUEST['to'];
$gchart_id  = $_REQUEST['gchart_id'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ACCOUNT BREAKDOWN</title>
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
	
if($gchart_id){
	$sql = mysql_query("Select ch.cv_header_id, sum(cd.amount) as cv_amount, ch.supplier_id, ch.`status`, ch.cv_date, ch.check_date
	from
	cv_header as ch,
	cv_detail as cd
	where
	ch.cv_header_id = cd.cv_header_id and
	ch.`status` != 'C' and
	cd.gchart_id = '$gchart_id' and
	ch.cv_date between '$from' and '$to'
	group by ch.cv_header_id") or die (mysql_error());
}else{
	$sql = mysql_query("Select ch.cv_header_id, sum(cd.amount) as cv_amount, ch.supplier_id, ch.`status`, ch.cv_date, ch.check_date
	from
	cv_header as ch,
	cv_detail as cd
	where
	ch.cv_header_id = cd.cv_header_id and
	ch.`status` != 'C' and
	ch.cv_date between '$from' and '$to'
	group by ch.cv_header_id") or die (mysql_error());	
}

 ?>

        <div style="text-align:left; font-size:12px; margin-bottom:20px; font-weight:bold;">
           	CV HISTORY
			<em>From <?=date("F j, Y",strtotime($from));?> To <?=date("F j, Y",strtotime($to));?></em>
        </div>   
		
<div class="container">
<table width="1000" style="font-size: 16px;"><!-- Parent -->
	<thead>
	<tr>
		<td style="font-weight: bold;" width="10" class="top_total">#</td>
		<td style="font-weight: bold; text-align: left; width: 117px;" class="top_total">REFERENCE</td>
		<td style="font-weight: bold; text-align: center; width: 117px;" class="top_total">CV DATE</td>
		<td style="font-weight: bold; text-align: center; width: 117px;" class="top_total">CHECK DATE</td>
		<td style="font-weight: bold; text-align: right; width: 117px;" class="top_total">AMOUNT</td>
	</tr>
	</thead>
<?php 
$total_amount = 0;
$partial = 0;
$i = 0;
while($r = mysql_fetch_assoc($sql)){ 
$partial = $r['cv_amount'];
$total_amount += $partial;
$i++;
?>
	<tr>
		<td><?=$i?></td>
		<td style="text-align: left;">CV # : <?=sprintf("%07d", $r['cv_header_id'])?></td>
		<td style="text-align: center;"><?=date("m/d/y",strtotime($r['cv_date']))?></td>
		<td style="text-align: center;"><?=date("m/d/y",strtotime($r['check_date']))?></td>
		<td style="text-align: right;"><?=number_format($partial,2)?></td>
	</tr>
<?php } ?>
		
	<tr>
		<td class="total">TOTAL</td>
		<td class="total"></td>
		<td class="total"></td>
		<td class="total"></td>
		<td class="total" style="text-align: right;"><?=number_format($total_amount,2)?></td>
	</tr>	
</table>
</div>
</body>
</html>
