<?php
//ini_set('max_execution_time', 1000);

require_once('my_Classes/options.class.php');
include_once("conf/ucs.conf.php");
require_once(dirname(__FILE__).'/library/lib.php');
$options=new options();	

$from 			= $_REQUEST['from'];
$to   			= $_REQUEST['to'];
$gchart_id 	 	= $_REQUEST['gchart_id'];

function getChild($gchart_id,$from,$to){
	$sql = mysql_query("select
	sum(d.debit) as debit, sum(d.credit) as credit
	from
	gltran_header as h,
	gltran_detail as d
	where
	h.gltran_header_id = d.gltran_header_id and 
	h.`status` != 'C' and
	h.date between '$from' and '$to' and
	d.gchart_id = '$gchart_id'
") or die (mysql_error());
	
	$r = mysql_fetch_assoc($sql);
	
	$value['debit'] = $r['debit'];
	$value['credit'] = $r['credit'];
	
	return $value;
}	
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
        ?>

        <div style="text-align:left; font-size:12px; margin-bottom:20px; font-weight:bold;">
           	ACCOUNT BREAKDOWN - <?=$rg['gchart']?><br />
			<em>From <?=date("F j, Y",strtotime($from));?> To <?=date("F j, Y",strtotime($to));?></em>
        </div>      
<div class="container">
<?php
$sql_parent = mysql_query("select
						g.gchart_id,
						g.gchart,
						g.mclass,
						g.beg_debit,
						g.beg_credit,
						sum(d.debit) as debit,
						sum(d.credit) as credit
						from
						gchart as g,
						gltran_header as h,
						gltran_detail as d
						where
						h.gltran_header_id = d.gltran_header_id and
						d.gchart_id = g.gchart_id and
						g.gchart_void = '0' and
						h.`status` != 'C' and
						h.date between '$from' and '$to' and
						g.gchart_id = '$gchart_id'") or die (mysql_error());
						
$rp = mysql_fetch_assoc($sql_parent);	

$tp_debit = $rp['beg_debit'] + $rp['debit'];					
$tp_credit = $rp['beg_credit'] + $rp['credit'];		

if($rp['mclass'] == 'A' or $rp['mclass'] == 'E'){
	$tp_total = $tp_debit - $tp_credit;
}else{
	$tp_total = $tp_debit - $tp_debit;
}	
			

?>
<table width="600"><!-- Parent -->
	<thead>
	<tr>
		<td style="font-weight: bold;" width="400" class="top_total">Parent Account</td>
		<td style="font-weight: bold; text-align: center; width: 117px;" class="top_total">Debit</td>
		<td style="font-weight: bold; text-align: center; width: 117px;" class="top_total">Credit</td>
		<td style="font-weight: bold; text-align: center; width: 117px;" class="top_total">Balance</td>
		<td style="font-weight: bold; text-align: center;" class="top_total">Class</td>
	</tr>
	</thead>
	
	<tr>
		<td><?=$rp['gchart']?></td>
		<td style="text-align: right; width: 150px;"><?=number_format($tp_debit,2)?></td>
		<td style="text-align: right; width: 150px;"><?=number_format($tp_credit,2)?></td>
		<td style="text-align: right; width: 150px;"><?=number_format($tp_total,2)?></td>
		<td><?=$rp['mclass']?></td>
	</tr>
</table>
<br />
<?php

$parent = $rp['gchart_id'];
$sql_child = mysql_query("select
*
from
gchart as g
where
g.parent_gchart_id = '$parent' and
g.gchart_void = '0'") or die (mysql_error());

?>
<table width="600">
	<thead>
	<tr>
		<td style="font-weight: bold;" width="400" class="top_total">Child Accounts</td>
		<td style="font-weight: bold; text-align: center; width: 117px;" class="top_total">Debit</td>
		<td style="font-weight: bold; text-align: center; width: 117px;" class="top_total">Credit</td>
		<td style="font-weight: bold; text-align: center; width: 117px;" class="top_total">Balance</td>
		<td style="font-weight: bold; text-align: center;" class="top_total">Class</td>
	</tr>
	</thead>
	<?php while($rc = mysql_fetch_assoc($sql_child)){ 
	
		$val1 = getChild($rc['gchart_id'],$from,$to);
		$tc_debit = $tc_credit = $tc_total = 0;
	
		//each child 
		$tc_debit = $rc['beg_debit'] + $rc['debit'] + $val1['debit'];					
		$tc_credit = $rc['beg_credit'] + $rc['credit'] + $val1['credit'];		

		if($rc['mclass'] == 'A' or $rc['mclass'] == 'E'){
			$tc_total += ($tc_debit - $tc_credit);
		}else{
			$tc_total += ($tc_credit - $tc_debit);
		}	
		
		$tcc_debit += $tc_debit;
		$tcc_credit += $tc_credit;
	
	if($tc_debit != 0 or $tc_credit != 0){
	?>
	<tr>
		<td><?=$rc['gchart']?></td>
		<td style="text-align: right; width: 150px;"><?=number_format($tc_debit,2)?></td>
		<td style="text-align: right; width: 150px;"><?=number_format($tc_credit,2)?></td>
		<td style="text-align: right; width: 150px;"><?=number_format($tc_total,2)?></td>
		<td><?=$rc['mclass']?></td>
	</tr>
	<?php 
	}
		} 
		if($rp['mclass'] == 'A' or $rp['mclass'] == 'E'){
			$tcc_total += ($tcc_debit - $tcc_credit);
		}else{
			$tcc_total += ($tcc_credit - $tcc_debit);
		}	
		
	?>
	<tr>
		<td style="border-top: 1px solid black;"></td>
		<td style="text-align: right; width: 150px; border-top: 1px solid black;"><?=number_format($tcc_debit,2)?></td>
		<td style="text-align: right; width: 150px; border-top: 1px solid black;"><?=number_format($tcc_credit,2)?></td>
		<td style="text-align: right; width: 150px; border-top: 1px solid black;"><?=number_format($tcc_total,2)?></td>
		<td style="border-top: 1px solid black;"></td>
	</tr>
	
	<tr>
		<td colspan="5"><br /></td>
	</tr>
	<?php 
	//parent + child debit
	$tcp_debit = $tp_debit + $tcc_debit;
	//parent + child credit
	$tcp_credit = $tp_credit + $tcc_credit;
	//parent + child total
	
	if($rp['mclass'] == 'A' or $rp['mclass'] == 'E'){
		$tcp_total += ($tcp_debit - $tcp_credit);
	}else{
		$tcp_total += ($tcp_credit - $tcp_debit);
	}
	
	?>
	<tr>
		<td>TOTAL</td>
		<td style="border-bottom: 3px double black; text-align: right;"><?=number_format($tcp_debit,2)?></td>
		<td style="border-bottom: 3px double black; text-align: right;"><?=number_format($tcp_credit,2)?></td>
		<td style="border-bottom: 3px double black; text-align: right;"><?=number_format($tcp_total,2)?></td>
		<td></td>
	</tr>
</table>
</div>
</body>
</html>
