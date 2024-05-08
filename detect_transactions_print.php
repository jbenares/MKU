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
			

function getPO($from,$to){			
	$sql = mysql_query("Select 
		sum(d.amount) as amount , h.po_header_id
		from
		po_header as h,
		po_detail as d
		where
		h.po_header_id = d.po_header_id and
		h.po_type = 'M' and
		h.`status` != 'C' and
		h.date between '$from' and '$to'
		group by h.po_header_id") or die (mysql_error());
	while($r = mysql_fetch_assoc($sql)){
		$po[] = $r['po_header_id'];
	}
	
	return $po;
	
	set_time_limit(1000);
}	

function getAmt($from,$to,$po_header_id){
	$sql = mysql_query("Select 
		sum(d.amount) as amount , h.po_header_id
		from
		po_header as h,
		po_detail as d
		where
		h.po_header_id = d.po_header_id and
		h.`status` != 'C' and
		h.po_type = 'M' and
		h.date between '$from' and '$to' and
		h.po_header_id = '$po_header_id'
		group by h.po_header_id
		") or die (mysql_error());
	
	while($r = mysql_fetch_assoc($sql)){
		$amount = $r['amount'];
	}
	return $amount;
}

function getRR($from,$to,$po_header_id){
	$sql = mysql_query("select 
		rh.rr_header_id, sum(rd.amount) as rr_amount
		from
		rr_header as rh,
		rr_detail as rd 
		where 
		rh.rr_header_id = rd.rr_header_id and
		rh.`status` != 'C' and
		rh.po_header_id = '$po_header_id'
		group by rh.rr_header_id
		") or die (mysql_error());
	
	while($r = mysql_fetch_assoc($sql)){
		$rr_header[] = $r['rr_header_id'];
	}
	return $rr_header;
}


function getRRAmt($from,$to,$po_header_id){
	$sql = mysql_query("select 
		rh.rr_header_id, sum(rd.amount) as rr_amount
		from
		rr_header as rh,
		rr_detail as rd 
		where 
		rh.rr_header_id = rd.rr_header_id and
		rh.`status` != 'C' and
		rh.po_header_id = '$po_header_id'
		group by rh.rr_header_id
		") or die (mysql_error());
	
	while($r = mysql_fetch_assoc($sql)){
		$rr_amount[] = $r['rr_amount'];
	}
	return $rr_amount;
}

function getAPV($from,$to,$po_header_id){
	$sql = mysql_query("select 
		h.apv_header_id, sum(d.amount) as apv_amt
		from 
		apv_header h,
		apv_detail as d
		where 
		h.apv_header_id = d.apv_header_id and 
		h.po_header_id = '$po_header_id' and
		h.`status` != 'C'
		group by h.apv_header_id
		") or die (mysql_error());
	
	while($r = mysql_fetch_assoc($sql)){
		$apv_header_id[] = $r['apv_header_id'];
	}
	return $apv_header_id;
}

function getAPVAmt($from,$to,$po_header_id){
	$sql = mysql_query("select 
		h.apv_header_id, sum(d.amount) as apv_amt
		from 
		apv_header h,
		apv_detail as d
		where 
		h.apv_header_id = d.apv_header_id and 
		h.po_header_id = '$po_header_id' and
		h.`status` != 'C'
		group by h.apv_header_id
		") or die (mysql_error());
	
	while($r = mysql_fetch_assoc($sql)){
		$apv_amt[] = $r['apv_amt'];
	}
	return $apv_amt;
}

function getCVheader($from,$to,$po_header_id){
	$sql = mysql_query("select
		ch.cv_header_id, 
		sum(cd.amount) cd_amt
		from
		po_header as po,
		apv_header as apv,
		cv_header as ch,
		cv_detail as cd
		where
		po.po_header_id = apv.po_header_id and
		po.`status` != 'C' and
		ch.cv_header_id = cd.cv_header_id and
		cd.apv_header_id = apv.apv_header_id and
		apv.po_header_id = '$po_header_id'
		group by ch.cv_header_id
		") or die (mysql_error());
	
	while($r = mysql_fetch_assoc($sql)){
		$cv_header_id[] = $r['cv_header_id'];
	}
	return $cv_header_id;
}

function getCVamt($from,$to,$po_header_id){
	$sql = mysql_query("select
		ch.cv_header_id, 
		sum(cd.amount) cd_amt
		from
		po_header as po,
		apv_header as apv,
		cv_header as ch,
		cv_detail as cd
		where
		po.po_header_id = apv.po_header_id and
		po.`status` != 'C' and
		ch.cv_header_id = cd.cv_header_id and
		cd.apv_header_id = apv.apv_header_id and
		apv.po_header_id = '$po_header_id'
		group by ch.cv_header_id
		") or die (mysql_error());
	
	while($r = mysql_fetch_assoc($sql)){
		$cd_amt[] = $r['cd_amt'];
	}
	return $cd_amt;
}

function getDVid($from,$to,$po_header_id){
	$sql = mysql_query("select 
		ev.po_header_id, ev.ev_header_id, sum(ed.amount) as ev_amount
		from
		ev_header as ev,
		ev_detail as ed
		where
		ev.ev_header_id = ed.ev_header_id and
		ev.po_header_id = '$po_header_id' and
		ev.`status` != 'C'
		group by ev.ev_header_id
		") or die (mysql_error());
	
	while($r = mysql_fetch_assoc($sql)){
		$ev_header_id[] = $r['ev_header_id'];
	}
	return $ev_header_id;
}

function getDVamt($from,$to,$po_header_id){
	$sql = mysql_query("select 
		ev.po_header_id, ev.ev_header_id, sum(ed.amount) as ev_amount
		from
		ev_header as ev,
		ev_detail as ed
		where
		ev.ev_header_id = ed.ev_header_id and
		ev.po_header_id = '$po_header_id' and
		ev.`status` != 'C'
		group by ev.ev_header_id
		") or die (mysql_error());
	
	while($r = mysql_fetch_assoc($sql)){
		$ev_amount[] = $r['ev_amount'];
	}
	return $ev_amount;
}

function getMRRewt($from,$to,$po_header_id){
	$sql = mysql_query("select
		gh.gltran_header_id, gd.credit
		from
		gltran_header as gh,
		gltran_detail as gd,
		rr_header as rr
		where
		gh.gltran_header_id = gd.gltran_header_id and
		gh.header_id = rr.rr_header_id and
		gh.header = 'rr_header_id' and
		gh.`status` != 'C' and
		rr.`status` != 'C' and
		rr.po_header_id = '$po_header_id' and
		gd.gchart_id = '924'
		") or die (mysql_error());	
	while($r = mysql_fetch_assoc($sql)){
		$credit[] = $r['credit'];	
	}
	return $credit;
}

function getCVewt($from,$to,$po_header_id){
	$sql = mysql_query("select 
		h.apv_header_id, cd.amount, cd.cv_detail_id, ch.cv_header_id, gd.credit
		from 
		apv_header h,
		cv_detail as cd,
		cv_header as ch,
		gltran_header as gh,
		gltran_detail as gd
		where 
		h.po_header_id = '$po_header_id' and
		ch.cv_header_id = cd.cv_header_id and
		h.apv_header_id = cd.apv_header_id and
		gh.header_id = ch.cv_header_id and
		gh.header = 'cv_header_id' and
		h.`status` != 'C' and
		gh.gltran_header_id = gd.gltran_header_id and
		gd.gchart_id = '924'
		group by ch.cv_header_id
		") or die (mysql_error());	
	while($r = mysql_fetch_assoc($sql)){
		$credit[] = $r['credit'];	
	}
	return $credit;
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
.noborder{
	border:none;	
	border-collapse:collapse;
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

        <div style="text-align:center; font-size:12px; margin-bottom:20px; font-weight:bold;">
           TRANSACTIONS<br />
			<em>From <?=date("F j, Y",strtotime($from));?> To <?=date("F j, Y",strtotime($to));?></em>
        </div>      
<div class="container">
<?php
$po = getPO($from,$to);
?>
<table width="100%" border="1" class="noborder">
	<thead>
	<tr>
		<td style="font-weight: bold; text-align: center; padding: 10px;">PO</td>
		<td style="font-weight: bold; text-align: center;">PO AMOUNT</td>
		<td style="font-weight: bold; text-align: center;">MRR</td>
		<td style="font-weight: bold; text-align: center;">MRR AMOUNT</td>
		<td style="font-weight: bold; text-align: center;">MRR EWT(1%)</td>
		<td style="font-weight: bold; text-align: center;">APV</td>
		<td style="font-weight: bold; text-align: center;">APV AMOUNT</td>
		<td style="font-weight: bold; text-align: center;">CV</td>
		<td style="font-weight: bold; text-align: center;">CV AMOUNT</td>
		<td style="font-weight: bold; text-align: center;">CV EWT(1%)</td>
		<td style="font-weight: bold; text-align: center;">DV</td>
		<td style="font-weight: bold; text-align: center;">DV AMOUNT</td>
	</tr>
	</thead>
	<?php 
	foreach($po as $value){
	$amount = 0;
	$apv_header = 0;
	$cv_id = 0;
	$amount = getAmt($from,$to,$value);
	$rr = getRR($from,$to,$value);
	$rr_amount = getRRAmt($from,$to,$value);
	$apv_header = getAPV($from,$to,$value);
	$apv_amt = getAPVAmt($from,$to,$value);
	$cv_id = getCVheader($from,$to,$value);
	$cv_amt = getCVamt($from,$to,$value);
	$dv_id = getDVid($from,$to,$value);
	$dv_amt = getDVamt($from,$to,$value);
	$ewt_mrr = getMRRewt($from,$to,$value);
	$ewt_cv = getCVewt($from,$to,$value);
	?>
	<tr>
		<td style="text-align: right; padding: 5px;"><?=$value?></td>
		<td style="text-align: right; padding: 5px;"><?php echo "<span style='color: green;'>".$amount."</span>";	?></td>
		<td style="text-align: right; padding: 5px;">
		<?php foreach($rr as $rr_id){  
			echo $rr_id;
		?>
			<br />
		<?php } ?>
		</td>
		<td style="text-align: right; padding: 5px;">
		<?php 
		$total_ramt = 0;
		foreach($rr_amount as $ramt){
			
			$total_ramt += $ramt;
			echo "<span style='color: green; '>".number_format($ramt,2)."</span>";	
		?>		
			<br />
			
		<?php } 
			echo "<span style='color: green; border-top: 1px solid black;'>".number_format($total_ramt,2)."</span>";	
		?>
		</td>
		<td style="text-align: right; padding: 5px;">
		<?php foreach($ewt_mrr as $mrrewt){  
			echo $mrrewt;
		?>
			<br />
		<?php } ?>
		</td>		
		<td style="text-align: right; padding: 5px;">
		<?php foreach($apv_header as $apv){
			echo $apv;	
		?>		
			<br />
		<?php } ?>
		</td>
		<td style="text-align: right; padding: 5px;">
		<?php 
		$total_apv_amt = 0;
		foreach($apv_amt as $a_amt){
			
			$total_apv_amt += $a_amt;
			echo "<span style='color: green;'>".number_format($a_amt,2)."</span>";	
		?>		
			<br />
		<?php } 
			echo "<span style='color: green; border-top: 1px solid black;'>".number_format($total_apv_amt,2)."</span>";	
		?>
		</td>	
		<td style="text-align: right; padding: 5px;">
		<?php foreach($cv_id as $cv){
			echo $cv;
		?>		
			<br />
		<?php } ?>
		</td>	
		<td style="text-align: right; padding: 5px;">
		<?php $total_cv_amt = 0;
		foreach($cv_amt as $cv_am){
			$total_cv_amt += $cv_am;
			echo "<span style='color: green;'>".number_format($cv_am,2)."</span>";		
		?>		
			<br />
		<?php } 
			echo "<span style='color: green; border-top: 1px solid black;'>".number_format($total_cv_amt,2)."</span>";		
		?>
		</td>
		<td>
		<?php foreach($ewt_cv as $cv_tax){
			echo $cv_tax;
		?>		
			<br />
		<?php } ?>
		</td>
		<td style="text-align: right; padding: 5px;">
		<?php foreach($dv_id as $dv_header){
			echo $dv_header;
		?>		
			<br />
		<?php } ?>
		</td>	
		<td style="text-align: right; padding: 5px;">
		<?php foreach($dv_amt as $dv_am){
			echo "<span style='color: green;'>".$dv_am."</span>";	
		?>		
			<br />
		<?php } ?>
		</td>
	</tr>
	<?php } ?>
</table>
</div>
</body>
</html>
