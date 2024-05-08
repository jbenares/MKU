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
	text-align: center;
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
           	MRR EVALUATION REPORT - <?=$r['account']?><br />
			<em>From <?=date("F j, Y",strtotime($from));?> To <?=date("F j, Y",strtotime($to));?></em>
        </div>      
<div class="container">
<?php
$count_po = 0;
$sql2 = mysql_query("Select * 
					from 
					po_header as po,
					projects as pr,
					supplier as su
					where po.date between '$from' and '$to' and
					po.project_id = pr.project_id and
					po.supplier_id = su.account_id and
					po.`status` != 'C' and
					su.account_id = '$account_id'
					order by po.date ASC
					") or die (mysql_error());
	
$i = 0;
?>
<table width="1100">
	<thead>
	<tr>
		<td style="font-weight: bold;" width="100" class="top_total">#</td>
		<td style="font-weight: bold;" width="100" class="top_total">P.O. #</td>
		<td style="font-weight: bold; width: 20px; text-align: center;" class="top_total">Transactions</td>
		<td style="font-weight: bold;" width="200" class="top_total">P.O. Date</td>
		<td style="font-weight: bold; text-align: left; width: 800px; text-align: center;" class="top_total">Criteria</td>
		<td style="font-weight: bold; text-align: left; width: 800px; text-align: center;" class="top_total">Total Raw Score</td>
		<td style="font-weight: bold; text-align: left; width: 800px; text-align: center;" class="top_total">Average Raw Score</td>
		<td style="font-weight: bold; text-align: left; width: 800px; text-align: center;" class="top_total">Average Weighted Points</td>
	</tr>
	</thead>
	<?php	while($r2 = mysql_fetch_assoc($sql2)){ 
		$po_header_id = $r2['po_header_id'];
		
		
		//MRR
		$rr_count = 0;
		$eva_ps = 0;
		$eva_d = 0;
		$eva_cr = 0;
		$eva_sf = 0;
		$eva_p = 0;
		//$total_rr = 0;
		
		$sql_count_rr = mysql_query("Select 
			h.rr_header_id, e.eva_ps, e.eva_d, e.eva_cr, e.eva_sf, e.eva_p
			from 
			rr_header as h,
			rr_evaluation as e
			where 
			h.po_header_id = '$po_header_id' and
			e.rr_header_id = h.rr_header_id and
			status != 'C'") or die (mysql_error());
		$rr_count = mysql_num_rows($sql_count_rr);
		if($rr_count > 0){
		$i++;
			while($rr_r = mysql_fetch_assoc($sql_count_rr)){
		$j++;
			//total accum raw score
			$accum_ps += $rr_r['eva_ps'];
			$accum_d += $rr_r['eva_d'];
			$accum_cr += $rr_r['eva_cr'];
			$accum_sf += $rr_r['eva_sf'];
			$accum_p += $rr_r['eva_p'];
			
			//per rr
			$eva_ps += $rr_r['eva_ps'];
			$eva_d += $rr_r['eva_d'];
			$eva_cr += $rr_r['eva_cr'];
			$eva_sf += $rr_r['eva_sf'];
			$eva_p += $rr_r['eva_p'];
			
			$total_rr += $rr_count;
			}
			
			//total per PO
			$total_eva_ps = ($eva_ps/$rr_count) * .25;
			$total_eva_d = ($eva_d/$rr_count) * .25;
			$total_eva_cr = ($eva_cr/$rr_count) * .20;
			$total_eva_sf = ($eva_sf/$rr_count) * .20;
			$total_eva_p = ($eva_p/$rr_count) * .10;
			
			//last total
			$grand_eva_ps += $total_eva_ps;
			$grand_eva_d += $total_eva_d;
			$grand_eva_cr += $total_eva_cr;
			$grand_eva_sf += $total_eva_sf;
			$grand_eva_p += $total_eva_p;
			

			
			//$p_eva_ps	 = (($grand_eva_ps/$i) /5 ) * 100;
			//$p_eva_d	 = (($grand_eva_d/$i) /5 ) * 100;
			//$p_eva_cr	 = (($grand_eva_cr/$i) /5 ) * 100;
			//$p_eva_sf	 = (($grand_eva_sf/$i) /5 ) * 100;
			//$p_eva_p	 = (($grand_eva_p/$i) /5 ) * 100;

		}
		
		if($rr_count > 0){

		$av_rr_po_ps 	= $grand_eva_ps / $i;
		$av_rr_po_d 	= $grand_eva_d / $i;
		$av_rr_po_cr 	= $grand_eva_cr / $i;
		$av_rr_po_sf 	= $grand_eva_sf / $i;
		$av_rr_po_p 	= $grand_eva_p / $i;
	?>
	<tr>
		<td style="border-top: 1px solid black; text-align: center; vertical-align: top; padding-top: 15px;"><?=$i;?></td>
		<td style="border-top: 1px solid black; text-align: center; vertical-align: top; padding-top: 15px;"><?=sprintf("%07d", $r2['po_header_id']);?></td>
		<td style="border-top: 1px solid black; text-align: center; vertical-align: top; padding-top: 15px;"><?=$rr_count?></td>
		<td style="border-top: 1px solid black; text-align: center; vertical-align: top; padding-top: 15px;"><?=$r2['date'];?></td>
		<td style="border-top: 1px solid black;">
			<ul>
				<li>Product/Services</li>
				<li>Delivery</li>
				<li>Customer Relations</li>
				<li>Support Functions</li>
				<li>Price</li>
			</ul>
		</td>
		<td style="border-top: 1px solid black;">
			<ul>
				<li><?=$eva_ps?></li>
				<li><?=$eva_d?></li>
				<li><?=$eva_cr?></li>
				<li><?=$eva_sf?></li>
				<li><?=$eva_p?></li>
			</ul>
		</td>
		<td style="border-top: 1px solid black;">
			<ul>
				<li><?=number_format($eva_ps/$rr_count,2)?></li>
				<li><?=number_format($eva_d/$rr_count,2)?></li>
				<li><?=number_format($eva_cr/$rr_count,2)?></li>
				<li><?=number_format($eva_sf/$rr_count,2)?></li>
				<li><?=number_format($eva_p/$rr_count,2)?></li>
			</ul>
		</td>
		<td style="border-top: 1px solid black;">
			<ul>
				<li><?=number_format($total_eva_ps,2)?></li>
				<li><?=number_format($total_eva_d,2)?></li>
				<li><?=number_format($total_eva_cr,2)?></li>
				<li><?=number_format($total_eva_sf,2)?></li>
				<li><?=number_format($total_eva_p,2)?></li>
			</ul>		
		</td>
	</tr>
	<?php 	}
	}
			//Ave Raw score
			$raw_ps  	= $accum_ps / ($j * 5);
			$raw_d 	 	= $accum_d / ($j * 5);
			$raw_cr 	= $accum_cr / ($j * 5);
			$raw_sf 	= $accum_sf / ($j * 5);
			$raw_p		= $accum_p / ($j * 5);
			
			//compute percentage
			$p_eva_ps	 = (number_format(($accum_ps / ($j * 5)),2) * .25) * 100;
			$p_eva_d	 = (number_format(($accum_d / ($j * 5)),2) * .25) * 100;
			$p_eva_cr	 = (number_format(($accum_cr / ($j * 5)),2) * .20) * 100;
			$p_eva_sf	 = (number_format(($accum_sf / ($j * 5)),2) * .20) * 100;
			$p_eva_p	 = (number_format(($accum_p / ($j * 5)),2) * .10) * 100;
			
			//total RAW
			$total_raw = $raw_ps + $raw_d + $raw_cr + $raw_sf + $raw_p;
			
			//total percentage
			$total_percent = $p_eva_ps + $p_eva_d + $p_eva_cr + $p_eva_sf + $p_eva_p;
	
	?>
</table>
<br />
<span style="font-weight: bold;">COMPUTATIONS:</span>
<table width="1100">
	<tr>
		<td style="border-top: 1px solid black;" class="total_eva" width="200">CRITERIA</td>
		<td style="border-top: 1px solid black;" class="total_eva">%</td>
		<td style="border-top: 1px solid black;" class="total_eva" width="285">TOTAL ACCUMULATED RAW SCORE</td>
		<td style="border-top: 1px solid black;" class="total_eva" width="285">AVERAGE RAW SCORE</td>
		<td style="border-top: 1px solid black;" class="total_eva" width="285">PERCENTAGE</td>
	</tr>
	<tr>
		<td style="border-top: 1px solid black;" class="total_eva2">Products/Services</td>
		<td style="border-top: 1px solid black;" class="total_eva2">25%</td>
		<td style="border-top: 1px solid black;" class="total_eva3"><?=$accum_ps?> / <?=$j*5?></td>
		<td style="border-top: 1px solid black;" class="total_eva3"><?=number_format($raw_ps,2)?></td>
		<td style="border-top: 1px solid black;" class="total_eva3"><?=number_format($p_eva_ps,2)?>%</td>
		
	</tr>
	<tr>
		<td class="total_eva2">Delivery</td>
		<td class="total_eva2">25%</td>
		<td class="total_eva3"><?=$accum_d?> / <?=$j*5?></td>
		<td class="total_eva3"><?=number_format($raw_d,2)?></td>
		<td class="total_eva3"><?=number_format($p_eva_d,2)?>%</td>
		
	</tr>
	<tr>
		<td class="total_eva2">Customer Relations</td>
		<td class="total_eva2">20%</td>
		<td class="total_eva3"><?=$accum_cr?> / <?=$j*5?></td>
		<td class="total_eva3"><?=number_format($raw_cr,2)?></td>
		<td class="total_eva3"><?=number_format($p_eva_cr,2)?>%</td>
		
	</tr>
	<tr>
		<td class="total_eva2">Support Functions</td>
		<td class="total_eva2">20%</td>
		<td class="total_eva3"><?=$accum_sf?> / <?=$j*5?></td>
		<td class="total_eva3"><?=number_format($raw_sf,2)?></td>
		<td class="total_eva3"><?=number_format($p_eva_sf,2)?>%</td>
		
	</tr>
	<tr>
		<td class="total_eva2">Price</td>
		<td class="total_eva2">10%</td>
		<td class="total_eva3"><?=$accum_p?> / <?=$j*5?></td>
		<td class="total_eva3"><?=number_format($raw_p,2)?></td>
		<td class="total_eva3"><?=number_format($p_eva_p,2)?>%</td>
	</tr>
	<tr>
		<td style="border-top: 1px solid black;" class="total_eva2">TOTAL</td>
		<td style="border-top: 1px solid black;" class="total_eva3"></td>
		<td style="border-top: 1px solid black;" class="total_eva3"></td>
		<td style="border-top: 2px solid black;" class="total_eva3"><?=$total_raw?></td>
		<td style="border-top: 1px solid black;" class="total_eva3"><?=number_format($total_percent,2)?>%</td>
	</tr>
</table>
</div>
</body>
</html>
