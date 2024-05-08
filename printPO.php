<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$po_header_id=$_REQUEST[id];
	
	$query="
		select
			*
		from 
			po_header
		where
			po_header_id='$po_header_id'
	";
	$result=mysql_query($query) or die(mysql_error());
	$r=mysql_fetch_assoc($result);

	$userID=$r[userID];
	
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
        	PURCHASE ORDER
        </div>
        <div class="header" style="">
        	<table style="width:100%;">
                <tr>
                	<td width="9%">Supplier:</td>
                    <td width="60%"><?=$options->getSupplierName($r[account_id])?></td>
                    <td width="9%">PO #: </td>
                    <td width="22%"><?=str_pad($r[po_header_id],7,"0",STR_PAD_LEFT)?></td>
               	</tr>
                <tr>
					<td>Location:</td>
                    <td><?=$options->getLocationName($r['locale_id'])?></td>
                    <td>Date:</td>
                    <td><?=date("F j, Y",strtotime($r['date']))?></td>
               	</tr>
               
            </table>
     	</div><!--End of header-->
        <?php
			$query="
				select
					pm.stock,
					pm.stockcode,
					pm.unit,
					po.qty,
					po.cost,
					po.amount
				from
					po_details as po,productmaster as pm
				where
					po.stock_id=pm.stock_id
				and
					po_header_id='$po_header_id'
				
			";
			$result=mysql_query($query) or die(mysql_error());		
		?>
        <div class="content" >
        	<table cellspacing="0">
            	<tr>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Stock Code</th>
                    <th>Description</th>
                    <th>Cost</th>
                    <th>Amount</th>
                </tr>
           		<?php
				$totalamount=0;
				while($r=mysql_fetch_assoc($result)):
					$totalamount+=$r[amount];
				?>
                    <tr>
	                    <td><?=number_format($r[qty],2,'.',',')?></td>
                        <td><?=$r[unit]?></td>
                        <td><?=$r[stockcode]?></td>
                        <td><?=$r[stock]?></td>
                        <td class="align-right">P <?=number_format($r[cost],2,'.',',')?></td>
                        <td class="align-right">P <?=number_format($r[amount],2,'.',',')?></td>
                    </tr>
                <?php
				endwhile;
				?>
                <tr style="border-top:3px double #000;">
                	<td colspan="5" class="align-right">Total</td>
                    <td class="align-right">P <?=number_format($totalamount,2,'.',',')?></td>
                </tr>
            </table>
        </div><!--End of content-->
    </div><!--End of Form-->
    

  


</div>
</body>
</html>