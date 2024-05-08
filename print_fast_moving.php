<?php
//ini_set('max_execution_time', 1000);

require_once('my_Classes/options.class.php');
include_once("conf/ucs.conf.php");
require_once(dirname(__FILE__).'/library/lib.php');


$from_date = $_REQUEST['from_date'];
$to_date   = $_REQUEST['to_date'];

$options=new options();	
$total_credit = 0;
$total_debit = 0;
$parent = 0;
$g_parent = 0;

function getRequestQty($stock_id,$from_date,$to_date){
	$sqlr = mysql_query("select
							sum(d.quantity) as qty
							from
							pr_header as h,
							pr_detail as d
							where
							h.pr_header_id = d.pr_header_id and
							h.`status` != 'C' and
							h.date between '$from_date' and '$to_date' and
							d.stock_id = '$stock_id'") or die (mysql_error());
	$pr = mysql_fetch_assoc($sqlr);

	return $pr['qty'];
}	
				
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
.link_tb{
	
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
           	Fast Moving Items<br />
			<em>From <?=date("F j, Y",strtotime($from_date));?> To <?=date("F j, Y",strtotime($to_date));?></em>
        </div>      
<div class="container">
<span style="color: red;">***UNDER CONSTRUCTION***</span>
<table width="100%" border="1" style="border-collapse: collapse;">
<thead>
	<th width="400px; "style="text-align: left;">Items</th>
	<th>UOM</th>
	<th>Request Quantity</th>
	<th>Inventory Quantity</th>
	<th>Issuance Quantity</th>
</thead>
<?php
$sqlm = mysql_query("Select * from productmaster as p
					where
					p.`status` != 'C' ") or die (mysql_error());
					
while($rm = mysql_fetch_assoc($sqlm)){
$rqty = 0;
$iqty = 0;
//requests
$rqty = getRequestQty($rm['stock_id'],$from_date,$to_date);
$iqty = inventory_warehouse($to_date,$stock_id,$quantity_type = "quantity")
?>
<tr>
	<td><?=$rm['stock']?></td>
	<td style="text-align: right;"><?=$rm['unit']?></td>
	<td style="text-align: right;"><?=number_format($rqty,2)?></td>
	<td style="text-align: right;"><?=number_format($iqty,2)?></td>
	<td style="text-align: right;"></td>
</tr>
<?php 
}
?>
</table>
</div>
</body>
</html>
