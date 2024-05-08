<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$date			= $_REQUEST['date'];
	$account_id		= $_REQUEST['account_id'];
	
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>JOB ORDER</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<link rel="stylesheet" type="text/css" href="css/print.css"/>
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
     	
     	<div align="right" style="font-size:18pt; font-weight:bolder;">
        	OVERALL ORDERS
        </div>
        
        <div align="right" style="margin:20px 0px;">	
        	Date: <div align="center" style="display:inline-block; border-bottom:1px dashed #000; width:150px; height:1em;"><?php echo date("m/d/Y")?></div>
        </div>
        
        
        <div class="content" style="">
        	<table>
            	<tr>
                	<th width="20">#</th>
                    <th>Item Description</th>
                    <th>Quantity</th>
                </tr>	
                <?php
				$result=mysql_query("
					select
						stock_id,
						sum(quantity) as quantity,
						price
					from
						order_header as h, order_details as d
					where
						h.order_header_id = d.order_header_id
					and
						status != 'C'						
					and
						date = '$date'
					group by
						stock_id
				") or die(mysql_error());
				$i = 1;
				while($r=mysql_fetch_assoc($result)){
					$stock_id		= $r['stock_id'];
					$stock_name		= $options->stockAttr($stock_id,'stock');
					$quantity		= $r['quantity'];
					$price			= $r['price'];
					$amount			= $price * $quantity;
				?>
                	<tr>
                    	<td><?=$i++?></td>
                        <td><?=$stock_name?></td>
                        <td class="align-right"><?=number_format($quantity,2,'.',',')?></td>
                    </tr>
                <?php
				}
                ?>
            </table>        
            <table style="margin-top:10px;">
            	<tr>
                	<td>Checked By:</td>
                	<td>Approved By:</td>
                	<td>Released By:</td>
                </tr>
            	
            </table>
        </div><!--End of content-->
    </div><!--End of Form-->


</div>
</body>
</html>