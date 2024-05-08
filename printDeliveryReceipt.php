<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$dr_header_id=$_REQUEST[id];
	
	$query="
		select
			*
		from 
			dr_header
		where
			dr_header_id='$dr_header_id'
	";
	$result=mysql_query($query);
	$r=mysql_fetch_assoc($result);
	$user_id= $r['user_id'];
	$date	= $r['date'];
	$username = $options->getUserName($user_id);
	$freight = $r['freight'];
	$order_header_id = $r['order_header_id'];
	$time_entered = $r['time_entered'];
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
	font-size:14pt;
	letter-spacing:2px;
}
.container{
	width:8.3in;
	/*height:10.8in;*/
	margin:0px auto;
	/*border:1px solid #000;*/
	
	position:relative;
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

.content table th, content table{
	border:1px solid #000;
}	
.content td{
	border-left:1px solid #000;
	border-right:1px solid #000;	
	border-collapse:collapse;
}

.content table td,.content table th{
	padding:3px;
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

.valignTop{
	vertical-align:top;	
}

.table-header, .table-header td{
	border:1px solid #000;
	border-collapse:collapse;	
	text-align:center;
	padding:5px;
}
.table-borders{
	border:1px solid #000;	
}


</style>
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
     
     	<?php
		include_once("company_heading.php");
		?>
        <div style="clear:both"></div>
     	<table style="width:200px; float:right;" class="table-header">
        	<tr>
            	<td style="font-size:24px; font-weight:bolder;" colspan="3">Delivery Receipt</td>
            </tr>
            <tr>
            	<td>Date</td>
                <td>DR #</td>
                <td>OS #</td>
            </tr>
            <tr>
            	<td><?=date('n/d/Y',strtotime($date))?></td>
                <td><?=str_pad($dr_header_id,7,'0',STR_PAD_LEFT)?></td>
                <td><?=str_pad($order_header_id,7,'0',STR_PAD_LEFT)?></td>
            </tr>
            <tr>
            	<td colspan="3">Time Encoded : <br /> <?=$time_entered?></td>
            </tr>
        </table>
        <?php	
			$account=$options->getAccountDetails($r[account_id]);
		?>          
        
        
        <table class="table-header" style="width:300px; margin-bottom:40px;">
        	<tr>
            	<td>Bill To</td>
            </tr>
        	<tr style="height:100px;">
            	<td><?=$options->getAccountName($r[account_id])?></td>
            </tr>
        </table>
        <?php
	
			
			$query="
				select
					*
				from
					dr_detail
				where
					dr_header_id='$dr_header_id'
			";
			
			$result=mysql_query($query) or die(mysql_error());		
		?>
        <div class="content" >
        	<table cellspacing="0">
            	<tr>
               		<th width="20" >Qty</th>
                    <th width="30">Item Code</th>
                    <th colspan="2">Description</th>
                    <th width="30" >Price <br> Each</th>
                    <th>Dis</th>
                    <th>Amount</th>
                </tr>
           		<?php
				$totalamount=0;
				$totalquantity=0;
				while($r=mysql_fetch_assoc($result)):
					$stock_id 		= $r['stock_id'];
					$stockcode		= $options->attr_stock($stock_id,'stockcode');
					$stock			= $options->attr_stock($stock_id,'stock');
					
					$quantity		= $r['quantity'];
					$srp			= $r['srp'];
					//$discount		= $r['discount'];
					$amount			= $r['amount'];
					$totalamount+=$amount;
					$totalquantity+=$quantity;
					
					$price1 = $options->attr_stock($stock_id,'price1');
					$discount = $price1 - $srp;
				?>
                    <tr>
                        <td><div align="right"><?=number_format($quantity,0)?></div></td>
                        <td><?=$stockcode?></td>
                        <td colspan="2"><?=$stock?></td>
                        <td><div align="right"><?=number_format($srp,2,'.',',')?></div></td>
                        <td><div align="right"><?=$discount?></div></td>
                        <td><div align="right"><?=number_format($amount,2,'.',',')?></div></td>
                    </tr>
                <?php
				endwhile;
				?>
                
                <?php
                $vatsales	= $totalamount / 1.12;
				$vat		= $totalamount - $vatsales;
				?>
		<tr class="table-borders">
			<td align="right"><?=$totalquantity?></td>
			<td colspan="5"></td>
		</tr>
                <tr class="table-borders">
                	<td colspan="2" rowspan="5" class="valignTop">Encoded By: <br /> <?=$username?></td>
                    <td rowspan="5" class="valignTop">Checked By:</td>
                    <td rowspan="5" class="valignTop">Received By:</td>
                
                	<td colspan="2"><div align="right">Vat Sales</div></td>
                    <td><div align="right">P <?=number_format($vatsales,2,'.',',')?></div></td>
                </tr>
                <tr class="table-borders">
                    <td colspan="2"><div align="right">Total Quantity</div></td>
    				<td><div align="right"><?=number_format($totalquantity,0,'.',',')?></div></td>
                </tr>
                <tr class="table-borders">
                    <td colspan="2"><div align="right">12 % VAT</div></td>
    				<td><div align="right">P <?=number_format($vat,2,'.',',')?></div></td>
                </tr>
                <tr class="table-borders">
	                <td colspan="2"><div align="right">Total Sales</div></td>
                    <td><div align="right">P <?=number_format($totalamount,2,'.',',')?></div></td>
                </tr>
                <tr class="table-borders">
	                <td colspan="2"><div align="right">Freight Cost</div></td>
                    <td><div align="right">P <?=number_format($freight,2,'.',',')?></div></td>
                </tr>
                <tr class="table-borders">
	                <td colspan="6"><div align="right">Total</div></td>
                    <td><div align="right">P <?=number_format($totalamount + $freight,2,'.',',')?></div></td>
                </tr>
                <tr class="table-borders">	
                	<td style="height:60px;" class="valignTop">No. of Crates</td>
                    <td colspan="6"></td>
                </tr>
            </table>
        </div><!--End of content-->
    </div><!--End of Form-->
    

  


</div>
</body>
</html>
