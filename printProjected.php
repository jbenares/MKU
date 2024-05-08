<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$inventorydate=$idate=$_REQUEST[inventorydate];
	$joborderdate=$jdate=$_REQUEST[joborderdate];
	$locale_id =$_REQUEST[locale_id];
	
	$query="
		select
			*
		from 
			productmaster
		where
			type='RM'
	";
	$result=mysql_query($query);
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
	margin:20px 0px;
	
	
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
        	PROJECTED
        </div>
        
        <div align="right" style="margin:20px 0px;">	
        	Date: <div align="center" style="display:inline-block; border-bottom:1px dashed #000; width:150px; height:1em;"><?php echo date("m/d/Y")?></div>
        </div>
        
           
        <div class="header" style="">
        	<table style="width:100%;">
                <tr>
                	<td width="25%"><strong>Inventory Date:</strong></td>
                    <td width="75%"><?=$inventorydate?></td>
               	</tr>
                <tr>
                	<td width="25%"><strong>Job Order Date:</strong></td>
                    <td width="75%"><?=$joborderdate?></td>
               	</tr>
                <tr>
                	<td width="25%"><strong>Location      :</strong></td>
                    <td width="75%"><?=$options->getLocationName($_REQUEST[locale_id])?></td>
               	</tr>
            </table>
     	</div><!--End of header-->
        
        <div class="content" style="">
        	<table>
            	<tr>
                	<th>INVENTORY ITEM</th>
                    <?php
					if($_REQUEST[priceoption]=="y"){
						echo "<th>Price</th>";
					}
					?>
                    <th>CURRENT BALANCE</th>
                    <th>JOB ORDER REQUIREMENT</th>
                    <th>BALANCE</th>
                    <th>REORDER LEVEL</th>
                    <th>ORDER QTY</th>
                    <th>PO QTY</th>
                </tr>	
             	<?php
					while($r=mysql_fetch_assoc($result)):
					//$current = $options->getCurrentBalanceOfStockForProjected($r[stock_id],$inventorydate,$locale_id) + $options->getTotalStocksReceived($r[stock_id],$inventorydate,$joborderdate,$locale_id);
					
					
					$joborderdate = date('Y-m-d',strtotime ('+1 day' , strtotime ( $joborderdate)));
					$inventorydate = date('Y-m-d',strtotime ('+1 day' , strtotime ( $inventorydate)));
					
					$current = $options->getCurrentBalanceOfStock($r[stock_id],$joborderdate,$locale_id);
					
					/*
						$balance = $options->getCurrentBalanceOfStockForProjected($r[stock_id],$inventorydate,$locale_id) - $options->getJobOrderRequirementOfStockForProjected($r[stock_id],$inventorydate,$joborderdate,$locale_id);
					*/				
					
					$jorequirements=$options->getJobOrderRequirementOfStockForProjected($r[stock_id],$idate,$jdate,$locale_id);
					$balance = $current - $jorequirements;
					$orderqty=0;
					$reorderlevel=$r[reorderlevel];
					if($balance < $reorderlevel){
						$orderqty  = $reorderlevel - $balance;
					}
					
					/*
					if($balance<0){
						$orderqty=$r[reorderlevel] + $balance;	
					}
					*/
					
				?>	
                        <tr <?php if($balance<0){echo 'style="color:#F00;"';}?>>
                            <td><?=$r[stock]?></td>
                            <?php
							if($_REQUEST[priceoption]=="y"){
								echo "<td>P ".number_format($options->getCostOfStock($r[stock_id]),2,'.',',')."</td>";
							}
							?>
                            <td><div align="right"><?=number_format($current,3,'.',',')?></div></td>
                            <td><div align="right"><?=number_format($jorequirements,3,'.',',')?></div></td>
                            <td><div align="right"><?=number_format($balance,3,'.',',')?></div></td>
                            <td><div align="right"><?=number_format($r[reorderlevel],3,'.',',')?></div></td>
                            <td><div align="right"><?=number_format($orderqty,3,'.',',')?></div></td>
                            <td><div align="right"><?=number_format($options->getPOQuantity($jdate,$r[stock_id],$locale_id),3,'.',',')?></div></td>
                        </tr>
				<?php
					endwhile;
				?>
            
            </table>
        	<?php
				//echo $options->getInventoryBalance($finishedproduct,NULL,$datefinished);
			?>
        
        </div><!--End of content-->
    </div><!--End of Form-->
    

  


</div>
</body>
</html>