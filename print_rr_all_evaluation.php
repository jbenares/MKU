<?php
//ini_set('max_execution_time', 1000);

require_once('my_Classes/options.class.php');
include_once("conf/ucs.conf.php");
require_once(dirname(__FILE__).'/library/lib.php');
$options=new options();	

$from 		= $_REQUEST['from'];
$to   		= $_REQUEST['to'];
	
$i = 0;

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
	font-size:14px;
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
	border-top: 1px solid black;
	text-align: center;
	font-family: arial;
	font-size: 11px;
}

.td_detail{
	text-align: left;
	font-family: arial;
	font-size: 11px;
}

.td_detail2{
	text-align: center;
	font-family: arial;
	font-size: 11px;
	border-top: 1px solid black;
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

a:link{
	color: black;
}

a:visited{
	color: black;
}

a:hover{
	color: red;
}
li{
	list-style-type: none;
}
.total_eva{
	font-weight: bold;
	text-align: center;
}
.total_eva2{
	font-weight: bold;
	
}
.total_eva3{
	text-align: center;
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
           	MRR EVALUATION REPORT - ALL SUPPLIERS<br />
			<em>From <?=date("F j, Y",strtotime($from));?> To <?=date("F j, Y",strtotime($to));?></em>
        </div>      
<div class="container">
<table width="100%">
	<thead>
	<tr>
		<td style="font-weight: bold; width: 20px;" class="top_total">#</td>
		<td style="font-weight: bold; width: 400px; text-align: left; "  class="top_total">SUPPLIER</td>
		<td style="font-weight: bold; width: 200px; text-align: center;" class="top_total">PRODUCTS/SERVICES(25%)</td>
		<td style="font-weight: bold; width: 200px; text-align: center;" class="top_total">DELIVERY(25%)</td>
		<td style="font-weight: bold; width: 200px; text-align: center;" class="top_total">CUSTOMER RELATIONS(20%)</td>
		<td style="font-weight: bold; width: 200px; text-align: center;" class="top_total">SUPPORT FUNCTIONS(20%)</td>
		<td style="font-weight: bold; width: 200px; text-align: center;" class="top_total">PRICE(10%)</td>
		<td style="font-weight: bold; width: 100px; text-align: center;" class="top_total">%</td>
		<td style="font-weight: bold; width: 100px; text-align: center;" class="top_total">AVERAGE(5)</td>
	</tr>
	</thead>
	<?php
	$sql = mysql_query("Select 
					po.supplier_id, s.account, 
					sum(e.eva_ps) as eva_ps, 
					sum(e.eva_d) as eva_d,
					sum(e.eva_cr) as eva_cr,
					sum(e.eva_sf) as eva_sf,
					sum(e.eva_p) as eva_p,
					count(h.rr_header_id) as rr_count
					from 
					po_header as po,
					rr_header as h,
					rr_evaluation as e,
					supplier as s
					where 
					po.po_header_id = h.po_header_id and
					h.rr_header_id = e.rr_header_id and
					po.supplier_id = s.account_id and
					po.date between '$from' and '$to' and 
					po.`status` != 'C'
					group by po.supplier_id ") or die (mysql_error());
					
	while($r = mysql_fetch_assoc($sql)){
	$prod_serv = ($r['eva_ps'] /($r['rr_count'] * 5) * .25) * 100;
	$delivery = ($r['eva_d'] /($r['rr_count'] * 5) * .25) * 100;
	$cust_rel = ($r['eva_cr'] /($r['rr_count'] * 5) * .20) * 100;
	$supp_func = ($r['eva_sf'] /($r['rr_count'] * 5) * .20) * 100;
	$price = ($r['eva_p'] /($r['rr_count'] * 5) * .10) * 100;
		
	$total_perc = $prod_serv + $delivery + $cust_rel + $supp_func + $price;	
	$ave = ($total_perc/100) * 5; 
	$i++;
	?>
	<tr>
		<td class="td_detail" style="border-top: 1px solid black;"><?=$i?></td>
		<td class="td_detail" style="border-top: 1px solid black;"><?=$r['account']?></td>
		<td class="td_detail2"><?=number_format($prod_serv,2)?>%</td>
		<td class="td_detail2"><?=number_format($delivery,2)?>%</td>
		<td class="td_detail2"><?=number_format($cust_rel,2)?>%</td>
		<td class="td_detail2"><?=number_format($supp_func,2)?>%</td>
		<td class="td_detail2"><?=number_format($price,2)?>%</td>
		<td class="td_detail2" style="font-weight: bold;"><?=number_format($total_perc,2)?>%</td>
		<td class="td_detail2" style="font-weight: bold;"><?=number_format($ave,2)?></td>
	</tr>
	<?php } ?>
	<tr>
		<td colspan="9" class="top_total">&nbsp;</td>
	</tr>	
</table>
</div>
</body>
</html>
