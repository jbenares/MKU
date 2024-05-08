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

        <div style="text-align:left; font-size:12px; margin-bottom:20px; font-weight:bold;">
           	TRIAL BALANCE<br />
			<em>From <?=date("F j, Y",strtotime($from));?> To <?=date("F j, Y",strtotime($to));?></em>
        </div>      
<div class="container">

	<table>
	<thead>
	<tr>
		<td style="font-weight: bold; padding-right: 50px;" class="top_total">Account</td>
		<td style="font-weight: bold; padding-right: 100px" class="top_total">Debit</td>
		<td style="font-weight: bold; padding-right: 50px" class="top_total">Credit</td>
		<td style="font-weight: bold; padding-right: 20px" class="top_total">Class</td>
		<td style="font-weight: bold; padding-right: 20px" class="top_total">Parent</td>
	</tr>
	</thead>
<?php	
	$credit = 0;
	$debit = 0;
while($r = mysql_fetch_assoc($sql)){
	$credit += $r['total_credit'];
	$debit += $r['total_debit'];
?>
	<tr>
		<td style="padding-right: 50px;"><?=$r['gchart']?></td>
		<td><?=number_format($r['total_debit'],2)?></td>
		<td><?=number_format($r['total_credit'],2)?></td>
		<td><?=$r['mclass']?></td>
		<td><?=$r['parent_gchart_id']?></td>
	</tr>
<?php
}
?>
	
	<tr>
		<td class="total">TOTAL</td>
		<td class="total"><?=number_format($debit,2)?></td>
		<td class="total"><?=number_format($credit,2)?></td>
		<td class="total"></td>
	</tr>	
</table>
</div>
</body>
</html>
