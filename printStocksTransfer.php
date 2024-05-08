<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$transfer_hdr_id=$_REQUEST[id];
	
	$query="
		select
			*
		from 
			transfer_header
		where
			transfer_hdr_id='$transfer_hdr_id'
	";
	$result=mysql_query($query);
	$r=mysql_fetch_assoc($result);
	$user_id=$r[user_id];

	
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


@media print and (width: 8.5in) and (height: 14in) {
  @page {
	  margin: 1in;
  }
}
	
body
{
	size: legal portrait;
		
	padding:0px;
	/*margin:0px;*/
	font-family:Arial, Helvetica, sans-serif;
	font-size:10pt;
}
.container{
	width:8.3in;
	/*height:10.8in;*/
	margin:0px auto;
	/*border:1px solid #000;*/
	padding:0.1in;
	/*overflow:auto;*/
}

.header
{
	text-align:center;	
	margin-top:20px;
}

.header table, .content table
{
	width:100%;
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
	border:1px solid #000;
	padding:10px;
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


</style>
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
     	
     	<div align="right" style="font-size:18pt; font-weight:bolder;">
        	STOCKS TRANSFER
        </div>
        <?php	
			$account=$options->getSupplierDetails($r[account_id]);
		?>          
        <div class="header" style="">
        	<table style="width:100%;">
                <tr>
                	<td width="20%">Source Location:</td>
                    <td width="49%"><?=$options->getLocationName($r[from_locale])?></td>
                    <td width="9%">Date: </td>
                    <td width="22%"><?=$r['date']?></td>
               	</tr>
                <tr>
					<td>Destination Location:</td>
                    <td><?=$options->getLocationName($r[to_locale])?></td>
                    <td></td>
                    <td></td>
               	</tr>
               
            </table>
     	</div><!--End of header-->
        <?php
	
			
			$query="
				select
					*
				from
					transfer_detail
				where
					transfer_hdr_id='$transfer_hdr_id'
			";
			
			$result=mysql_query($query) or die(mysql_error());		
		?>
        <div class="content" >
        	<table cellspacing="0">
            	<tr>
                	<th>Qty</th>
                    <th>Unit</th>
                    <th>Stock #</th>
                    <th>Description</th>
                </tr>
           		<?php
				$totalqty=0;
				while($r=mysql_fetch_assoc($result)):
					$totalqty+=$r[qty];
				?>
                    <tr>
                        <td><div align="right"><?=$r[qty]?></div></td>
                        <td>Kilos</td>
                        <td><?=$options->getStockCode($r[stock_id])?></td>
                        <td><?=$options->getMaterialName($r[stock_id])?></td>
                    </tr>
                <?php
				endwhile;
				?>
                <tr>
                	<td colspan="3"><div align="right">Total Quantity</div></td>
                    <td><div align="right"><?=number_format($totalqty,3,'.',',')?></div></td>
                </tr>
            </table>
            <table>
            	<tr>
                	<td width="25%" rowspan="2"></td>
                    <td width="32%" rowspan="2" valign="top">RECIEVED IN GOOD CONDITION BY:</td>
                    <td width="43%">ENCODED BY: <?php echo $options->getUserName($user_id);?></td>
                </tr>
                <tr>
	                <td>RECEIVED BY:</td>
                </tr>
            </table>
        </div><!--End of content-->
    </div><!--End of Form-->
    

  


</div>
</body>
</html>