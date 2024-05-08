<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$type=$_REQUEST[type];
	$date=$_REQUEST[reportdate];
	$project_id = $_REQUEST['project_id'];
	$query="
		select
			*
		from 
			productmaster
		order by stock asc
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
<link rel="stylesheet" type="text/css" href="css/print.css"/>
<style type="text/css">
	.table-content{
		border-collapse:collapse;	
	}
	.table-content td{
		border:1px solid #000;	
	}
</style>
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
     	
     	<div align="right" style="font-size:18pt; font-weight:bolder;">
        	INVENTORY BALANCE REPORT
        </div>
        
        <div align="right" style="margin:20px 0px;">	
        	Date: <div align="center" style="display:inline-block; border-bottom:1px dashed #000; width:150px; height:1em;"><?php echo date("m/d/Y")?></div>
        </div>
        
           
        <div class="header" style="">
        	<table style="width:100%;">
                <tr>
                	<td width="25%"><strong>Report Date:</strong></td>
                    <td width="75%"><?=date("F j, Y",strtotime($date))?></td>
               	</tr>
            </table>
     	</div><!--End of header-->
        
        <div class="content" style="">
        	<table class="table-content">
            	<tr>
                	<th width="45%">INVENTORY ITEM</th>
                    
                    <?php
					if($_REQUEST[priceoption]=="y"){
						echo "<th>COST</th>";
					}
					?>
                    <th>INVENTORY WAREHOUSE</th>
                </tr>	
             	<?php
					while($r=mysql_fetch_assoc($result)):
						$stock = $r['stock'];
						$stock_id = $r['stock_id'];
						$cost = $r['cost'];

						
						$warehouse_qty  = $options->inventory_warehouse(NULL,$stock_id);	
						
						if($warehouse_qty > 0){
				?>	
                        <tr>
                            <td><?=$r[stock]?></td>                            
                            <?php
							if($_REQUEST[priceoption]=="y"){
								echo "<td>P ".number_format($cost,2,'.',',')."</td>";
							}
							?>
                            <td style="text-align:right;"><?=number_format($warehouse_qty,2,'.',',')?></td>
                        </tr>
				<?php
						}
					endwhile;
				?>
            </table>
        
        </div><!--End of content-->
    </div><!--End of Form-->
</div>
</body>
</html>