<?php
	require_once('my_Classes/options.class.php');
	require_once("my_Classes/depreciation.class.php");
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	$fdate			= $_REQUEST['fdate'];
	$tdate			= $_REQUEST['tdate'];
	$rr_detail_id	= $_REQUEST['rr_detail_id'];
	
	$result = mysql_query("
				select 
					rr_detail_id,stock,asset_code,details,date_acquired,d.cost,estimated_life,quantity
				from 
					rr_detail as d, productmaster as p 
				where
					d.stock_id = p.stock_id
				and
					rr_detail_id = '$rr_detail_id'
			") or die(mysql_error());
			
	$r = mysql_fetch_assoc($result);
	
	$item 				= $r['stock'];
    $asset_code 		= $r['asset_code'];
    $asset_description 	= $r['details'];
    $date_acquired 		= $r['date_acquired'];
    $acquisition_cost 	= $r['cost'];
    $estimated_life 	= $r['estimated_life'];
	$quantity			= $r['quantity'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>JOB ORDER</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<style type="text/css">
body
{
	size: legal portrait;
	padding:0px;
	font-family:Arial, Helvetica, sans-serif;
	font-size:14px
}
.container{
	margin:0px auto;
}

.header
{
	margin:20px 0px;
}

.header table td
{
	border:none;	
}

hr
{
	margin:40px 0px;	
	border:1px dashed #999;

}

table{
	border-collapse:collapse;	
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

.table-print{
	/*width:100%;	*/
}

.table-print tr:nth-child(1) td{
	border-top:1px solid #000;
	border-bottom:1px solid #000;
	font-weight:bold;
}
.table-print tr td:nth-child(n+2){
	text-align:right;
}

.tr-summary{
	border-top:1px solid #000;	
}
.summary-highlight{
	border-bottom:3px double #000;	
	font-weight:bold;
}
.line_bottom {
	border-bottom-width: 1px;
	border-bottom-style: solid;
	border-bottom-color: #000000;
	border-left: 0px;
	border-right: 0px;
	border-top: 0px;
	width:120px;
}

.table-form tr td:nth-child(odd){
	text-align:right;
	font-weight:bold;
}
.table-form td{
	padding:3px;	
}
.table-form{
	display:inline-table;	
	border-collapse:collapse;
}

.summary-table td{
	padding:3px;
}
.summary-table td:nth-child(2){
	text-align:right;	
}
</style>
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
     	
     	<div style="font-weight:bolder; margin-bottom:10px;">
        	PROPERTY, PLANT & EQUIPMENT LAPSING SCHEDULE REPORT<br />
           	As of <?=date("m/d/Y",strtotime(Depreciation::getAsOfDate($tdate)))?>
        </div>           
        <div class="content" style="">
        	<table>
            	<tr>
                	<td>ASSET :</td>
                    <td><?=$item?></td>
                </tr>
                <tr>
                	<td>ASSET CODE :</td>
                    <td><?=$asset_code?></td>
                </tr>
                <tr>
                	<td>ASSET DESC. :</td>
                    <td><?=$asset_description?></td>
                </tr>
                <tr>
                    <td>UNIT :</td>
                    <td><?=$quantity?></td>
              	</tr>
                <tr>
                    <td>DATE ACQUIRED :</td>
                    <td><?=$date_acquired?></td>
               	</tr>
                <tr>
                    <td>ACQUISITION COST :</td>
                    <td><?=number_format($acquisition_cost,2,'.',',')?></td>
               	</tr>
                <tr>
                    <td>ESTIMATED USEFUL LIFE IN MONTHS :</td>
                    <td><?=$estimated_life?> MONTHS</td>
               	</tr>
                <tr>
                    <td>MONTHLY DEPRECIATION :</td>
                    <td><?=number_format(Depreciation::getMonthlyDepreciation($acquisition_cost,$estimated_life),2,'.',',')?></td>
                </tr>
            </table>
        	<table cellpadding="3" class="table-print">
            	<tr>
                	<td>DATE</th>
                    <td>MONTHLY DEPRECIATION</th>
                    <td>NET BOOK VALUE</th>
                </tr>	
                
                <?php
				$s = Depreciation::computeMonthlyLapsingSchedule($date_acquired,$tdate,$acquisition_cost,$estimated_life);
				#echo "<pre>";
				#print_r($s);
				#echo "</pre>";
				
				$total_depreciation = 0;
				foreach($s as $r){
					$total_depreciation += $r['monthly_dep'];
                ?>
                <tr>
                    <td><?=date("Y-m",strtotime($r['date']))?></td>
                    <td><?=number_format($r['monthly_dep'],2,'.',',')?></td>
                    <td style="font-weight:bold;"><?=number_format($r['net_book_value'],2,'.',',')?></td>
               	</tr>
                <?php } ?>                
                <tr class="tr-summary">
                	<td></td>
                    <td><span class="summary-highlight"><?=number_format($total_depreciation,2,'.',',')?></span></td>
                    <td></td>
                </tr>
            </table>
            
            <table class="summary-table">
            	<tr>
                    <td>ACQUISITION COST</td>
                    <td style="text-align:right;"><?=number_format($acquisition_cost,2,'.',',')?></td>
               	</tr>
                <tr>
                	<td style="padding-left:10px;">LESS :ACCUMULATED DEPRECIATION</td>
                    <td><?=number_format($total_depreciation,2,'.',',')?></td>
                </tr>
                <tr>
                	<td>NET BOOK VALUE</td>
                    <td style="border-top:1px solid #000;"><span class="summary-highlight"><?=number_format($acquisition_cost - $total_depreciation,2,'.',',')?></span></td>
                </tr>
            </table>
            
            <table cellspacing="0" cellpadding="5" align="center" width="98%" style="border:none; text-align:center; margin-top:10px;" class="summary">
                <tr>
                    <td>Prepared by:<p>
                        <input type="text" class="line_bottom" /><br>Bookkeeper</p></td>
                    <td>Noted by:<p>
                        <input type="text" class="line_bottom" /><br>Chief Financial Officer</p></td>
                </tr>
           	</table>
        
        </div><!--End of content-->
    </div><!--End of Form-->
</div>
</body>
</html>