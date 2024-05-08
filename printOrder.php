<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$order_header_id=$_REQUEST[id];

	
	$query="
		select
			*
		from 
			order_header
		where
			order_header_id='$order_header_id'
	";
	$result=mysql_query($query);
	$r=mysql_fetch_assoc($result);
	$user_id		= $r['user_id'];
	$account_id		= $r['account_id'];
	$account_name	= $options->getAccountName($account_id);
	$time			= $r['time'];
	$date			= $r['date'];
	$netamount		= $r['netamount'];
	$status			= $r['status'];

	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ORDER SHEET</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<style type="text/css">

@media print and (width: 8.5in) and (height: 14in) {
  @page {
	  margin: 1in;
  }
  
  .page-break{
		display:block;
		page-break-before:always;  
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

.footer td{
	border:none;
}


</style>
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
	
    <div style="text-align:center"><strong>CERES PASALUBONG, INC <br /><?=$company_tel_no?></strong></div>
    <div style="text-align:right; font-weight:bolder;">
        ORDER NO. : <?=$order_header_id?><br />
        DATE : <?=$date?>
    </div>       
        <div class="header" style="">
        	<table style="width:100%;">
                <tr>
                	<td width="14%">BRANCH NAME:</td>
                    <td width="55%" style="border-bottom:1px solid #000;"><?=$account_name?></td>
               	</tr>
                <tr>
				  <td>TIME TO BE DELIVERED:</td>
                  <td style="border-bottom:1px solid #000;"><?=$time?></td>
               	</tr>
               
            </table>
     	</div><!--End of header--><br />

        <?php
	
			
			$query="
				select
					*
				from
					order_details
				where
					order_header_id='$order_header_id'
			";
			
			$result=mysql_query($query) or die(mysql_error());		
		?>
        <div class="content" >
        	<table cellspacing="0">
            	<tr>
                	<th>QTY</th>
                    <th>ITEMS</th>
                    <th>PRICE</th>
                    <th>DISC</th>
                    <th>AMOUNT</th>
                </tr>
           		<?php
				$totalamount=0;
				while($r=mysql_fetch_assoc($result)):
					$quantity 		= $r[quantity];
					$stock_id		= $r[stock_id];
					
					$srp = $options->attr_stock($stock_id,'price1');
					$stock			= $options->getMaterialName($stock_id);
					$price			= $r[price];
					$amount			= $r[amount];
					
					$discount = $srp - $price;
				
				
					$totalamount+=$amount;
				?>
                    <tr>
                        <td><div align="right"><?=$quantity?></div></td>
                        <td><?=$stock?></td>
                        <td><div align="right">P <?=number_format($price,2,'.',',')?></div></td>
                        <td><div align="right"><?=number_format($discount,2,'.',',')?></div></td>
                        <td><div align="right">P <?=number_format($amount,2,'.',',')?></div></td>
                    </tr>
                <?php
				endwhile;
				?>
                <tr>
                	<td colspan="4"><div align="right">Total</div></td>
                    <td><div align="right">P <?=number_format($totalamount,2,'.',',')?></div></td>
                </tr>
            </table>
            <div style="margin-top:20px;">
            	<span>PREPARED BY :</span>
                <div style="width:50%; display:inline-block; border-bottom:1px solid #000;"></div><br /><br />
					
                <span>PREPARED BY :</span>
                <div style="width:50%; display:inline-block; border-bottom:1px solid #000;"><?=$options->getUserName($user_id);?></div> <br /><br />
                
                <span>RECEIVED BY :</span>
                <div style="width:50%; display:inline-block; border-bottom:1px solid #000;"></div><br /><br />

            </div>
        </div><!--End of content-->
    </div><!--End of Form-->
</div>
<div class="page-break"></div>
</body>
</html>
