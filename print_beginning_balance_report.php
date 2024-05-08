<?php
//ini_set('max_execution_time', 1000);

require_once('my_Classes/options.class.php');
include_once("conf/ucs.conf.php");
require_once(dirname(__FILE__).'/library/lib.php');
$options=new options();	

$from 		= $_REQUEST['from'];
$to   		= $_REQUEST['to'];
$gchart_id  = $_REQUEST['gchart_id'];
	
$sqlg = mysql_query("Select * from gchart where gchart_id = '$gchart_id'") or die (mysql_error());
$rg = mysql_fetch_assoc($sqlg);
$gchart_name = $rg['gchart'];
$total_credit = 0;
$total_debit = 0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>BEGINNING BALANCE HISTORY</title>
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
           	ACCOUNT BEGINNING BALANCE HISTORY - <?=$rg['gchart']?><br />
        </div>      
<div class="container">

<?php
$sql = mysql_query("Select * from gchart_beginning where gchart_id = '$gchart_id' order by date desc") or die (mysql_error());
$sql2 = mysql_query("Select * from gchart_beginning where gchart_id = '$gchart_id' and year_bal = year(NOW()) order by date desc limit 1") or die (mysql_error());
$r2 = mysql_fetch_assoc($sql2);

$count2 = mysql_num_rows($sql2);
$count1 = mysql_num_rows($sql);

if($count2 > 0){
?>
<table width="600"><!-- Parent -->
	<thead>
	<tr>
		<td style="font-weight: bold;" >Date</td>
		<td style="font-weight: bold; text-align: center; width: 117px;">Beginning Balance</td>
	</tr>
	</thead>
	<tr>
		<td style="padding-right: 50px;" class="parent_total"><?=date("Y F j  - g:i A", strtotime($r2['date']))?> - <span style="color: red;">CURRENT BEGINNING BALANCE</span></td>
		<td id="numbers_format" class="parent_total" style="text-align: right;"><?=number_format($r2['beg_debit']-$r2['beg_credit'],2)?></td>
	</tr>
</table>
<?php } ?>
<br />
<?php if($count1 > 0){ ?>
<table width="600"><!-- Parent -->
	<thead>
	<tr>
		<td style="font-weight: bold;border-bottom: 1px solid black;">Date</td>
		<td style="font-weight: bold;border-bottom: 1px solid black;">Year</td>
		<td style="font-weight: bold; text-align: right;border-bottom: 1px solid black;width: 117px;">Beginning Balance</td>
	</tr>
	</thead>
<?php	
$count = mysql_num_rows($sql);
if(mysql_num_rows($sql) > 0){
	while($r = mysql_fetch_assoc($sql)){
?>
	<tr>
		<td><?=date("Y F j  - g:i A", strtotime($r['date']))?></td>
		<td><?=$r['year_bal']?></td>
		<td style="text-align: right;"><?=number_format($r['beg_debit']-$r['beg_credit'],2)?></td>
	</tr>
<?php
	}
}
?>
</table>
<?php } ?>
</div>
</body>
</html>
