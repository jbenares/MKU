<?php
//ini_set('max_execution_time', 1000);

require_once('my_Classes/options.class.php');
include_once("conf/ucs.conf.php");
require_once(dirname(__FILE__).'/library/lib.php');
$options=new options();	

$from 		= $_REQUEST['from'];
$to   		= $_REQUEST['to'];
$account_id  = $_REQUEST['account_id'];
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
table{
	padding: 20px;
}
td{
	padding: 10px;
}
.headers{
	font-weight: bold;
	border-top: 1px solid black;
	border-bottom: 1px solid black;
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
           	NO EVALUATION<br />
			<em>From <?=date("F j, Y",strtotime($from));?> To <?=date("F j, Y",strtotime($to));?></em>
        </div>      
<div class="container">
<?php
$sql2 = mysql_query("select 
p.po_header_id, p.date as po_date, rr.rr_header_id, rr.date as rr_date, rr.supplier_id, s.account
from 
rr_header as rr,
supplier as s,
po_header as p
where rr.rr_header_id NOT IN (Select rr_header_id from rr_evaluation) 
and s.account_id = rr.supplier_id
and p.po_header_id = rr.po_header_id
and p.`status` != 'C'
and rr.`status` != 'C'
and rr.date between '$from' and '$to'
order by rr.po_header_id") or die (mysql_error());

$i = 0;
?>
<table width="100%" border="0"><!-- Parent -->
<tr>
	<td class="headers">#</td>
	<td class="headers">SUPPLIER</td>
	<td class="headers">PO #</td>
	<td class="headers">PO DATE</td>
	<td class="headers">MRR #</td>
	<td class="headers">RR DATE</td>
</tr>
<?php
while($r2 = mysql_fetch_assoc($sql2)){
$i++;	
?>
<tr>
	<td><?=$i?></td>
	<td style="width: 400px;"><?=$r2['account']?></td>
	<td style="width: 200px;">P.O. # : <?=str_pad($r2['po_header_id'],7,0,STR_PAD_LEFT)?></td>
	<td style="width: 200px;"><?=$r2['po_date']?></td>
	<td style="width: 200px;">M.R.R # : <?=str_pad($r2['rr_header_id'],7,0,STR_PAD_LEFT)?></td>
	<td style="width: 200px;"><?=$r2['rr_date']?></td>
</tr>
<?php
}
?>
<tr>
	<td class="headers">&nbsp;</td>
	<td class="headers">&nbsp;</td>
	<td class="headers">&nbsp;</td>
	<td class="headers">&nbsp;</td>
	<td class="headers">&nbsp;</td>
	<td class="headers">&nbsp;</td>
</tr>
</table>
</div>
</body>
</html>
