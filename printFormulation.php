<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	$formulation_id=$_REQUEST[id];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>FORMULATION</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
</script>
<style type="text/css">
body
{
	padding:0px;
	margin:0px;
	font-family:Arial, Helvetica, sans-serif;
	font-size:15pt;
}
.container{
	width:8.3in;
	/*height:10.8in;*/
	margin:0px auto;
	/*border:1px solid #000;*/
	padding:0.1in;
	overflow:auto;
}

.header
{
	text-align:center;	
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
	padding:3px;	
}

.content table{
	margin-top:20px;	
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

</style>
</head>
<body>
<div class="container">
	
	<?php
    
        $query="SELECT
                    *
                FROM
                    formulation_header
				where
					formulation_id='$formulation_id'
            ";
                    
        $result=mysql_query($query) or die(mysql_error());
    
		while($r=mysql_fetch_assoc($result))
		{
			$formulation_id=$r[formulation_id];
			$formulationcode=$r[formulationcode];
			$formulationdate=$r[formulationdate];
			$category=$r[category];
			$description=$r[description];
			$customername=$r[customername];
	?>
     <div><!--Start of Form-->
        <div class="header">
        	<table style="width:100%;">
            	<tr>
                	<td width="22%"><strong>Formulaton Code:</strong></td>
                    <td width="78%"><?=$formulationcode;?></td>
				</tr>
               	</tr>
                <tr>
                	<td><strong>Category:</strong></td>
                    <td><?php echo $options->getCategoryNameWithLevel($category);?></td>
               	</tr>
                <tr>
                	<td><strong>Description:</strong></td>
                    <td><?=$description;?></td>
               	</tr>
                <tr>
                	<td><strong>Customer Name:</strong></td>
                    <td><?php echo $options->getAccountName($customername);?></td>
              	</tr>
                <tr>
                	<td><strong>Total Quantity:</strong></td>
                    <td><?php echo $options->getTotalQtyFromFormulationId($formulation_id);?></td>
                </tr>
                <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
                <tr>
                	<td><strong>Type:</strong></td>
                    <td>Micro</td>
               	</tr>
            </table>
     	</div><!--End of header-->
        <?php
			//query formulation details
			//$query="select * from formulation_details where formulation_id='$formulation_id' and type='micro'";
			$query="
				SELECT
					formulation_details.formulationdetail_id,
					formulation_details.formulation_id,
					formulation_details.type,
					formulation_details.material,
					formulation_details.quantity,
					formulation_details.remarks,
					productmaster.cost
					FROM
					formulation_details
					INNER JOIN productmaster ON formulation_details.material = productmaster.stock_id
					where 
					formulation_details.formulation_id='$formulation_id'
					and formulation_details.type='micro'
			";
			
			$result2=mysql_query($query) or die(mysql_error());		
		?>
        <div class="content">
        	<table cellspacing="0">
            	<tr>
                	<th>Material</th>
                    <th>Quantity</th>
                    <th>Remarks</th>
               	</tr>
                <?php
					$totalPrice=0;
					$totalQuantity=0;
					$microQuantity=0;
					while($r2=mysql_fetch_assoc($result2))
					{
						$price=number_format($r2[cost]*$r2[quantity],3,'.','');
						
						echo "	<tr>
									<td>".$options->getMaterialName($r2[material])."</td>
									<td><div align='right'>$r2[quantity]</div></td>
									<td>$r2[remarks]</td>
								</tr>";
						$totalPrice+=$price;
						$totalQuantity+=$r2[quantity];
						$microQuantity+=$r2[quantity];
					}
            	?>
                <tr>
                	<td colspan="3"><div align="right";><strong>Total Quantity: <?php echo $microQuantity;?></strong></div></td>
                </tr>
            </table>
        </div><!--End of content-->
       <hr  width='98%' />
        <div class="header">
        	<table style="width:100%;">
            	
            	
                <tr>
                	<td width="22%"><strong>Type:</strong></td>
                    <td>Macro</td>
               	</tr>
            </table>
        </div><!--End of header-->
        <?php
			//query formulation details
			//$query="select * from formulation_details where formulation_id='$formulation_id' and type='macro'";
			
			$query="
				SELECT
					formulation_details.formulationdetail_id,
					formulation_details.formulation_id,
					formulation_details.type,
					formulation_details.material,
					formulation_details.quantity,
					formulation_details.remarks,
					productmaster.cost
					FROM
					formulation_details
					INNER JOIN productmaster ON formulation_details.material = productmaster.stock_id
					where 
					formulation_details.formulation_id='$formulation_id'
					and formulation_details.type='macro'
			";
			
			$result2=mysql_query($query) or die(mysql_error());		
		?>
        <div class="content">
        	<table border="1" cellspacing="0"  style="margin-top:20px;">
            	<tr>
                	<th>Material</th>
                    <th>Quantity</th>
                    <th>Remarks</th>
               	</tr>
                <?php
				
					$totalPrice=0;
					$macroQuantity=0;
					while($r2=mysql_fetch_assoc($result2))
					{
						$price=number_format($r2[cost]*$r2[quantity],3,'.','');
						
						echo "	<tr>
									<td>".$options->getMaterialName($r2[material])."</td>
									<td><div align='right'>$r2[quantity]</div></td>
									<td>$r2[remarks]</td>
								</tr>";
								
						$totalPrice+=$price;
						$totalQuantity+=$r2[quantity];
						$macroQuantity+=$r2[quantity];
					}
            	?>
                <tr>
                	<td colspan="3"><div align="right";><strong>Total Quantity: <?php echo $macroQuantity;?></strong></div></td>
                </tr>
            </table>
        </div><!--End of content-->
    </div><!--End of Form-->
    

    <?php }?>


</div>
</body>
</html>