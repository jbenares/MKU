<?php
//ini_set('max_execution_time', 1000);

require_once('my_Classes/options.class.php');
include_once("conf/ucs.conf.php");
require_once(dirname(__FILE__).'/library/lib.php');
$options=new options();	

$from 		= $_REQUEST['from'];
$to   		= $_REQUEST['to'];
$account_id  = $_REQUEST['account_id'];
	
$sql = mysql_query("Select * from supplier where account_id = '$account_id'") or die (mysql_error());
$r = mysql_fetch_assoc($sql);
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
           	CA ADJUSTMENTS - <?=$r['account']?><br />
			<em>From <?=date("F j, Y",strtotime($from));?> To <?=date("F j, Y",strtotime($to));?></em>
        </div>      
<div class="container">
<?php
$sql2 = mysql_query("Select * 
					from 
					po_header as po,
					projects as pr,
					supplier as su
					where po.date between '$from' and '$to' and
					po.project_id = pr.project_id and
					po.supplier_id = su.account_id and
					po.`status` != 'C' and
					su.account_id = '$account_id' and
					po.po_type = 'M'
					order by po.date ASC
					") or die (mysql_error());
?>
<table width="1100"><!-- Parent -->
	<thead>
	<tr>
		<td style="font-weight: bold;" width="100" class="top_total">P.O. #</td>
		<td style="font-weight: bold;" width="100" class="top_total">P.O. Date</td>
		<td colspan="2" style="font-weight: bold; text-align: left; width: 800px;" class="top_total">Project</td>
		<td style="font-weight: bold;" width="200" class="top_total"></td>
	</tr>
	</thead>
	<?php	while($r2 = mysql_fetch_assoc($sql2)){ 
		$po_header_id = $r2['po_header_id'];
	?>
	<tr>
		<td style="border-top: 1px solid black;"><?=sprintf("%07d", $r2['po_header_id']);?></td>
		<td style="border-top: 1px solid black;"><?=$r2['date'];?></td>
		<td style="border-top: 1px solid black;" colspan="3"><?=$r2['project_name'];?></td>
	</tr>	
		<?php 
		$sql3 = mysql_query("Select * from
							rr_header as rr,
							supplier as su,
							gltran_header as gl
							where
							rr.po_header_id = '$po_header_id' and
							su.account_id = rr.supplier_id and
							gl.header = 'rr_header_id' and
							gl.header_id = rr.rr_header_id and
							gl.status != 'C' and
							rr.`status` != 'C'") or die (mysql_error());
	?>
		<tr>
			<td >&nbsp;</td>
			<td >&nbsp;</td>
			<td style="border-top: 1px dotted black; border-bottom: 1px dotted black;font-weight: bold; width: 200px;">MRR</td>
			<td style="border-top: 1px dotted black; border-bottom: 1px dotted black;font-weight: bold;">MRR Date</td>
			<td style="border-top: 1px dotted black; border-bottom: 1px dotted black;font-weight: bold;" align="right">Gross Amount</td>
		</tr>
	<?php
		while($r3 = mysql_fetch_assoc($sql3)){
		$total_gross += $r3['grossamount'];	
		?>
		<tr>
			<td></td>
			<td></td>
			<td><a href="admin.php?view=1da21dd42f2e46c2d13e&gltran_header_id=<?=$r3['gltran_header_id']?>" target="_blank">M.R.R # : <?=sprintf("%07d", $r3['rr_header_id'])?></a></td>
			<td><?=$r3['date']?></td>
			<td align="right"><?=number_format($r3['grossamount'],2)?></td>
		</tr>
	<?php 	} 
	}
	?>
		<tr>
			<td align="left;" style="font-weight: bold;" width="100" class="top_total">TOTAL:</td>
			<td style="font-weight: bold;" width="100" class="top_total"></td>
			<td style="font-weight: bold;" width="100" class="top_total"></td>
			<td align="right" colspan="2" style="font-weight: bold;" width="100" class="top_total"><?=number_format($total_gross,2)?></td>
		</tr>
</table>
</div>
</body>
</html>
