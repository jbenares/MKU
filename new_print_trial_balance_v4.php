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

function getChildBeginning($gchart_id){
	$sql = mysql_query("select
					sum(g.beg_debit) as bdebit, sum(g.beg_credit) as bcredit
					from
					gchart as g
					where g.gchart_void = '0' and
					g.parent_gchart_id = '$gchart_id'") or die (mysql_error());
					
	$r = mysql_fetch_assoc($sql);
	
	$value['debit'] = $r['bdebit'];
	$value['credit'] = $r['bcredit'];
	
	set_time_limit(1000);
	return $value;
	
	
}	

function getTransactions($ngchart,$from,$to){
						

	$value2 = $r2['bcredit'];
	
	set_time_limit(1000);
	return $value2;	
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
<?php	
	
	// Parent and child
	$sqlp = mysql_query("select
							*
							from gchart as g
							where
							g.parent_gchart_id = '0' and
							g.gchart_void = '0'
							order by
							g.acode asc
						") or die (mysql_error());	
						
	while($rp = mysql_fetch_assoc($sqlp)){
		
	$ngchart = $rp['gchart_id'];
	
	
	$value1 = getChildBeginning($ngchart);	
	
	//trial 
	$sql2 = mysql_query("select
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
						(g.parent_gchart_id = '$ngchart' or g.gchart_id = '$ngchart')") or die (mysql_error());
					
	$r2 = mysql_fetch_assoc($sql2);
	set_time_limit(1000);
	
		
	$debit = $rp['beg_debit'] + $value1['debit'] + $r2['debit'];
	$credit = $rp['beg_credit'] + $value1['credit'] + $r2['credit'];
	
	$ndebit += $debit;
	$ncredit += $credit;
	
	if($debit != 0 or $credit != 0){
?>
	<tr>
		<td style="padding-right: 50px;"><a href="admin.php?view=ee7e0ef4c4cece4b9009&gchart_id=<?=$rp['gchart_id']?>&startingdate=<?=$_REQUEST['from']?>&endingdate=<?=$_REQUEST['to']?>" target="_blank" class="link_tb" /><?=$rp['gchart']?></a></td>
		<td id="numbers_format">
		<?php if($debit != 0){ ?>
			<?=number_format($debit,2)?>
		<?php } ?>
		</td>
		<td id="numbers_format">
		<?php if($credit != 0){ ?>
			<?=number_format($credit,2)?>
		<?php } ?>	
		</td>
		<td style="text-align: right;"><?=$rp['mclass']?></td>
	</tr>
<?php
	}
}
?>
	<tr>
		<td class="total">TOTAL</td>
		<td class="total" id="numbers_format"><?=number_format($ndebit,2)?></td>
		<td class="total" id="numbers_format"><?=number_format($ncredit,2)?></td>
		<td class="total"></td>
	</tr>
</table>
</div>
</body>
</html>
