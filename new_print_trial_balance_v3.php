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
<title>TRIAL BALANCE</title>
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
		<td style="font-weight: bold; padding-right: 50px;" class="top_total">Accounts</td>
		<td style="font-weight: bold; text-align: center;" class="top_total">Debit</td>
		<td style="font-weight: bold; text-align: center;" class="top_total">Credit</td>
		<td style="font-weight: bold; padding-left: 20px;" class="top_total">Class</td>
	</tr>
	</thead>
<!-- Parents -->
<?php	
	//GL Transactions - Parent
	$sqlp = mysql_query("Select * from 
						gchart as g 
						inner join 
						gltran_header as h
						inner join 
						gltran_detail as d 
						where g.parent_gchart_id = '0' and g.gchart_void = '0' and
						h.gltran_header_id = d.gltran_header_id and
						h.date between '$from' and '$to' and 
						g.gchart_id = d.gchart_id and
						h.status != 'C' group by g.gchart_id
						order by g.acode ASC
					") or die (mysql_error());	
	$rp = mysql_fetch_assoc($sqlp);

	$sqlc = mysql_query("Select sum(credit) as total_credit, sum(debit) as total_debit, g.gchart_id, g.gchart, g.mclass, g.parent_gchart_id as g_parent
						from gltran_header as h,
						gltran_detail as d,
						gchart as g
						where h.gltran_header_id = d.gltran_header_id and
						h.date between '$from' and '$to' and 
						g.gchart_id = d.gchart_id and
						h.`status` != 'C' and
						g.parent_gchart_id != '0'
						group by g.parent_gchart_id
						order by g.acode
						") or die (mysql_error());
	
	
	//calculations	
	$debit_total = 0;
	$credit_total = 0;	
	while($rp = mysql_fetch_assoc($sqlp)){ 
	
	$final_debitp += $rp['beg_debit'] + $rp['debit'];
	$final_creditp += $rp['beg_credit'] + $rp['credit'];
?>
	<tr>
		<td style="padding-right: 50px;"><?=$rp['gchart']?></td>
		<td id="numbers_format"><?=number_format($rp['debit']+$rp['beg_debit'],2)?></td>
		<td id="numbers_format"><?=number_format($rp['credit']+$rp['beg_credit'],2)?></td>
		<td id="numbers_format"><?=number_format($rp['beg_debit'],2)?></td>
		<td id="numbers_format"><?=number_format($rp['beg_credit'],2)?></td>
		<td style="text-align: right;"><?=$rp['mclass']?></td>
	</tr>
<?php } 

	while($rc = mysql_fetch_assoc($sqlc)){ 
	
	$final_debitc += $rc['total_debit'];
	$final_creditc += $rc['total_credit'];
?>
	<tr>
		<td style="padding-right: 50px;"><?=$rc['gchart']?></td>
		<td id="numbers_format"><?=number_format($rc['total_debit'],2)?></td>
		<td id="numbers_format"><?=number_format($rc['total_credit'],2)?></td>
		<td style="text-align: right;"><?=$rc['mclass']?></td>
	</tr>
<?php } 


?>
	<tr>
		<td class="total">TOTAL</td>
		<td class="total" id="numbers_format"><?=number_format($final_debitp+$final_debitc,2)?></td>
		<td class="total" id="numbers_format"><?=number_format($final_creditp+$final_creditc,2)?></td>
		<td class="total"></td>
	</tr>
</table>
</div>
</body>
</html>
