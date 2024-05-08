<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	$joborder_id=$_REQUEST[id];
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
	letter-spacing:2px;
	padding:0px;
	margin:0px;
	font-family:Arial, Helvetica, sans-serif;
	font-size:14pt;
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
	float:left; width:40%;
}
.header h2
{
	font-weight:100;
	padding:0px;
	margin-bottom:5px;
}

.header table, .content table
{
	width:100%;
	text-align:left;	
}
.header table td, .content table td
{
	padding:1px;	
}

.content table{
	border-collapse:collapse;	
	float:right; width:60%;
}
.content table td,.content table th{
	border:1px solid #000;
	padding:2px;
}
hr
{
	margin:40px 0px;	
	border:1px dashed #999;

}

.red{
	color:#F00;	
}
</style>
</head>
<body>
<div class="container">
	
	<?php
    
        $query="SELECT
                    *
                FROM
                    joborder_header
				where
					joborder_id='$joborder_id'
            ";
                    
        $result=mysql_query($query) or die(mysql_error());
    
		while($r=mysql_fetch_assoc($result))
		{
			$jobnum=$r[jobnum];
			$jobdate=$r[jobdate];
			$finishedproduct=$r[finishedproduct];
			$customername=$r[customername];
			$formulation_id=$r[formulation_id];
			$numberofbatches=round($r[numberofbatches],0);
			$inventorybalance=$r[inventorybalance];
			$excessused=$r[excessused];
			
			$excess=$excessused+($inventorybalance*$options->getPackageQty($r[typeofpackage]))
	?>
     <div><!--Start of Form-->
        <div class="header" style="">
        	<table style="width:100%;">
            	<tr>
                	<td><strong>J.O. #:</strong></td>
               	</tr>
                <tr>
                    <td class="red"><?=$jobnum;?></td>
				</tr>
                
                <tr>
                	<td><strong>J.O. Date:</strong></td>
               	</tr>
                <tr>
                	 <td class="red"><?php echo $jobdate;?></td>
                </tr>
                <tr>
                	<td><strong>Finished Product:</strong></td>
               	</tr>
                <tr>
                	<td class="red"><?php echo $options->getMaterialName($finishedproduct);?></td>
                </tr>
                <tr>
                	<td><strong>Customer:</strong></td>
              	</tr>
                <tr>
                	<td class="red"><?php echo $options->getAccountName($customername);?></td>
                </tr>
                <tr>
                	<td><strong>Excess from Inventory Balance:</strong></td>
                </tr>
                <tr>
                	<td class="red"><?php echo $excess?></td>
                </tr>
                <tr>
                	<td><strong>Number of Batches:</strong></td>
                </tr>
               	<tr>
                	<td class="red"><?php echo $numberofbatches; ?></td>
                </tr>
                <tr>
                	<td><strong>Type:</strong></td>
               	</tr>
                <tr>
                	<td>Micro</td>
                </tr>	
            </table>
     	</div><!--End of header-->
        <?php
	
			
			$query="
				SELECT
					*
				FROM
					joborder_details
				where 
					joborder_id='$joborder_id'
					and type='micro'
			";
			
			$result2=mysql_query($query) or die(mysql_error());		
		?>
        <div class="content" style="">
        	<table cellspacing="0">
            	<tr>
                	<th>Material</th>
                    <th>Quantity</th>
               	</tr>
                <?php
					$totalQuantity=0;
					$microQuantity=0;
					while($r2=mysql_fetch_assoc($result2))
					{
						
						echo "	<tr>
									<td>".$options->getMaterialName($r2[material])."</td>
									<td><div align='right'>$r2[quantity]</div></td>
								</tr>";
						$totalQuantity+=$r2[quantity];
						$microQuantity+=$r2[quantity];
					}
            	?>
                <tr>
                	<td colspan="2"><div align="right";><strong>Total Quantity: <?php echo $microQuantity;?></strong></div></td>
                </tr>
            </table>
        </div><!--End of content-->
       <div style="clear:both;"></div>
       <hr  width='98%' />
       
       <div class="header">
        	<table style="width:100%;">
            	<tr>
                	<td><strong>J.O. #:</strong></td>
               	</tr>
                <tr>
                    <td class="red"><?=$jobnum;?></td>
				</tr>
                
                <tr>
                	<td><strong>J.O. Date:</strong></td>
               	</tr>
                <tr>
                	 <td class="red"><?php echo $jobdate;?></td>
                </tr>
                <tr>
                	<td><strong>Finished Product:</strong></td>
               	</tr>
                <tr>
                	<td class="red"><?php echo $options->getMaterialName($finishedproduct);?></td>
                </tr>
                <tr>
                	<td><strong>Customer:</strong></td>
              	</tr>
                <tr>
                	<td class="red"><?php echo $options->getAccountName($customername);?></td>
                </tr>
                <tr>
                	<td><strong>Excess from Inventory Balance:</strong></td>
                </tr>
                <tr>
                	<td class="red"><?php echo $excess?></td>
                </tr>
                <tr>
                	<td><strong>Number of Batches:</strong></td>
                </tr>
               	<tr>
                	<td class="red"><?php echo $numberofbatches; ?></td>
                </tr>
                <tr>
                	<td class="red"><strong>Type:</strong></td>
               	</tr>
                <tr>
                	<td>Micro</td>
                </tr>	
            </table>
        </div><!--End of header-->
        <?php
			//query formulation details
			//$query="select * from formulation_details where formulation_id='$formulation_id' and type='macro'";
			
			$query="
				SELECT
					*
				FROM
					joborder_details
				where 
					joborder_id='$joborder_id'
					and type='macro'
			";
			
			$result2=mysql_query($query) or die(mysql_error());		
		?>
        <div class="content">
        	<table border="1" cellspacing="0">
            	<tr>
                	<th>Material</th>
                    <th>Quantity</th>
               	</tr>
                <?php
				
					$macroQuantity=0;
					while($r2=mysql_fetch_assoc($result2))
					{
						
						echo "	<tr>
									<td>".$options->getMaterialName($r2[material])."</td>
									<td><div align='right'>$r2[quantity]</div></td>
								</tr>";
								
						$totalQuantity+=$r2[quantity];
						$macroQuantity+=$r2[quantity];
					}
            	?>
                <tr>
                	<td colspan="2"><div align="right";><strong>Total Quantity: <?php echo $macroQuantity;?></strong></div></td>
                </tr>
            </table>
        </div><!--End of content-->
        <div style="clear:both;"></div>
    </div><!--End of Form-->
    

    <?php }?>


</div>
</body>
</html>