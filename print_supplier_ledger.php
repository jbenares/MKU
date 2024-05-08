<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	
	$date			= $_REQUEST['date'];
	$supplier_id	= $_REQUEST['supplier_id'];
		
	$options=new options();	
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
	font-size:11px;
}
.container{
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

.table-content{
}
.table-content th{
	border-top:1px solid #000;
	border-bottom:1px solid #000;	
}
.table-content th,.table-content tr td{
	padding:5px;
}
.table-content th:nth-child(n+4),.table-content tr td:nth-child(n+4){
	text-align:right;
}

.table-content tr:last-child td{
	border-top:1px solid #000;
	font-weight:bold;
}

</style>
</head>
<body>
<div class="container">
	
     <div style="margin-bottom:100px;"><!--Start of Form-->
     
     	 <?php
			require("form_heading.php");
        ?>

        <div style="text-align:left; font-size:12px; margin-bottom:20px; font-weight:bold;">
           Supplier Ledger<br />
			As of <?php echo date("F j, Y",strtotime($date))?>
        </div>           
        
        <div class="content" >
        	<table cellspacing="0" class="table-content">
            	<tr>
                	<th>DATE</th>
                    <th>ACCOUNT</th>
                    <th>REFERENCE</th>
                    <th>DEBIT </th>
                    <th>CREDIT</th>
                </tr>
                                   
                <?php
				$result = mysql_query("
					select 
						gchart,date,debit,credit,xrefer
					from
						gltran_header as h, gltran_detail as d, gchart as g
					where
						h.gltran_header_id = d.gltran_header_id
					and
						d.gchart_id = g.gchart_id
					and
						account_id = 's-$supplier_id'
					and
						date <= '$date'
					and
						h.status != 'C'
				") or die(mysql_error());     
				$total_debit = $total_credit = 0;
				while($r = mysql_fetch_assoc($result)){
					$total_debit += $r['debit'];
					$total_credit += $r['credit'];
				?>
				<tr>
                	<td><?=$r['date']?></td>
                    <td><?=$r['xrefer']?></td>
                    <td><?=$r['gchart']?></td>
                    <td><?=number_format($r['debit'],2,'.',',')?></td>
                    <td><?=number_format($r['credit'],2,'.',',')?></td>
                </tr>
				<?php } ?>
                <tr>
                	<td></td>
                    <td></td>
                    <td></td>
                    <td><?=number_format($total_debit,2,'.',',')?></td>
                    <td><?=number_format($total_credit,2,'.',',')?></td>
                </tr>
            </table>
            <table  class="noborder" style="border:none; margin-top:20px;">
            	<tr>
                	<td>Prepared by:</td>
                    <td>Checked by:</td>
                    <td>Approved by:</td>
                    <td>Released by:</td>
                    <td>Received by:</td>
                </tr>
            </table>
        </div><!--End of content-->
    </div><!--End of Form-->
   
</div>
</body>
</html>