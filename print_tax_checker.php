<?php
//ini_set('max_execution_time', 1000);

require_once('my_Classes/options.class.php');
include_once("conf/ucs.conf.php");
require_once(dirname(__FILE__).'/library/lib.php');
$options=new options();	

$from 			= $_REQUEST['from'];
$to   			= $_REQUEST['to'];
$percent 	 	= $_REQUEST['percent'];
	
	
function getTaxChart($gchart_id){
	$sql = mysql_query("Select * from gchart where gchart_id = '$gchart_id'") or die (mysql_error());
	$r = mysql_fetch_assoc($sql);
	if(mysql_num_rows($sql) > 0 ){
		return $r['gchart'];
	}else{
		return 0;
	}
}	
function getCashChart($gchart_id){
	$sql = mysql_query("Select * from gchart where gchart_id = '$gchart_id'") or die (mysql_error());
	$r = mysql_fetch_assoc($sql);
	if(mysql_num_rows($sql) > 0 ){
		return $r['gchart'];
	}else{
		return 0;
	}
}	
function getVatChart($gchart_id){
	$sql = mysql_query("Select * from gchart where gchart_id = '$gchart_id'") or die (mysql_error());
	$r = mysql_fetch_assoc($sql);
	if(mysql_num_rows($sql) > 0 ){
		return $r['gchart'];
	}else{
		return 0;
	}
}	
function getChart($gchart_id){
	$sql = mysql_query("Select * from gchart where gchart_id = '$gchart_id'") or die (mysql_error());
	$r = mysql_fetch_assoc($sql);
	if(mysql_num_rows($sql) > 0 ){
		return $r['gchart'];
		echo 'aaaaa';
	}else{
		exit;
	}
}	

function getGL($cv_header_id){
	$sql = mysql_query("select * from gltran_header as h,
						gltran_detail as d
						where
						h.gltran_header_id = d.gltran_header_id and
						h.`status` != 'C' and
						h.header_id = '$cv_header_id' and
						h.header = 'cv_header_id'") or die (mysql_error());

	while($r = mysql_fetch_assoc($sql)){
		$arr['d'] = $r['debit'];
		$arr['c'] = $r['credit'];
		$arr['g'] = $r['gchart_id'];
	}
	
	return $arr;
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
           	CVs - <?=$percent?>%<br />
			<em>From <?=date("F j, Y",strtotime($from));?> To <?=date("F j, Y",strtotime($to));?></em>
        </div>      
<div class="container">
<?php
$sql = mysql_query("select * from cv_header
as cv 
where 
cv.`status` = 'F' and
cv.wtax = '$percent' and
cv.cv_date between '$from' and '$to'") or die (mysql_error());
set_time_limit(1000)
?>
<table width="600"><!-- Parent -->
	<thead>
	<tr>
		<td style="font-weight: bold;" width="400" class="top_total">CV Header</td>
		<td style="font-weight: bold; text-align: center; width: 117px;" class="top_total">CV Date</td>
		<td style="font-weight: bold; text-align: center; width: 117px;" class="top_total">Wtax</td>
		<td style="font-weight: bold; text-align: center; width: 117px;" class="top_total">Wtax Gchart</td>
		<td style="font-weight: bold; text-align: center; width: 117px;" class="top_total">Vat</td>
		<td style="font-weight: bold; text-align: center; width: 117px;" class="top_total">Vat Gchart</td>
		<td style="font-weight: bold; text-align: center; width: 117px;" class="top_total">Cash Amount</td>
		<td style="font-weight: bold; text-align: center; width: 117px;" class="top_total">Cash Gchart</td>
	</tr>
	</thead>
	<?php
	while($r = mysql_fetch_assoc($sql)){
		
		$tax_gchart = getTaxChart($r['wtax_gchart_id']);
		$vat_gchart = getVatChart($r['vat_gchart_id']);
		$cash_gchart = getCashChart($r['cash_gchart_id']);
	?>
	<tr>
		<td><?=$r['cv_header_id']?></td>
		<td><?=$r['cv_date']?></td>
		<td><?=$r['wtax']?></td>
		<td><?=$tax_gchart?></td>
		<td><?=$r['vat']?></td>
		<td><?=$vat_gchart?></td>
		<td><?=$r['cash_amount']?></td>
		<td><?=$cash_gchart?></td>
	</tr>
	<?php 
		$sql1 = mysql_query("select * from gltran_header as h,
						gltran_detail as d
						where
						h.gltran_header_id = d.gltran_header_id and
						h.`status` != 'C' and
						h.header_id = '".$r['cv_header_id']."' and
						h.header = 'cv_header_id'") or die (mysql_error());
		while($r1 = mysql_fetch_assoc($sql1)){
			
			
		?>	
	<tr>
		<td><?php
		if($r1['gchart_id'] == 0){
			echo '<div style="color: red;">RED!</div>';
		} ?>	=getChart($r1['gchart_id'])?></td>
		<td><?=$r1['debit']?></td>
		<td><?=$r1['credit']?></td>
		<td><?=$r1['credit']?></td>
	</tr>	
		<?php
		}
		?>
	<tr>
		<td colspan="8"><hr /></td>
	</tr>	
	<?php } ?>
</table>
</div>
</body>
</html>
