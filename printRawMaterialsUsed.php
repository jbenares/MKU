<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$startdate=$_REQUEST[startdate];
	$enddate=$_REQUEST[enddate];
	$locale_id=$_REQUEST[locale_id];
	
	$query="
		SELECT
		joborder_details.material,
		sum(quantity * numberofbatches) AS quantity,
		productmaster.stock,
		joborder_header.jobdate,
		joborder_header.datefinished,
		joborder_details.material
		FROM
		joborder_details
		INNER JOIN productmaster ON productmaster.stock_id = joborder_details.material
		INNER JOIN joborder_header ON joborder_header.joborder_id = joborder_details.joborder_id
		WHERE 
			joborder_header.status!='C'
		and
			jobdate between '$startdate' and '$enddate'";
	if ($locale_id != 0)
		$query.= " AND locale_id = '$locale_id' ";	
	$query.="	
		group by
			material
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
        	SUMMARY OF RAW MATERIALS USED
        </div>
        
        <div align="right" style="margin:20px 0px;">	
        	Date: <div align="center" style="display:inline-block; border-bottom:1px dashed #000; width:150px; height:1em;"><?php echo date("m/d/Y")?></div>
        </div>
        
           
        <div class="header" style="">
        	<table style="width:100%;">
                <tr>
                	<td width="25%"><strong>Start Date:</strong></td>
                    <td width="75%"><?php echo $startdate;?></td>
               	</tr>
                <tr>
                	<td><strong>End Date:</strong></td>
                    <td><?php echo $enddate;?></td>
               	</tr>
                <tr>
                	<td width="25%"><strong>Location      :</strong></td>
                    <td width="75%"><?=$options->getLocationName($_REQUEST[locale_id])?></td>
               	</tr>
            </table>
     	</div><!--End of header-->
        
        <div class="content" style="">
        	<table style="width:100%;">
            	<tr>
                	<th>Inventory Item</th>
                    <?php
					if($_REQUEST[priceoption]=="y"){
						echo "<th>Price</th>";
					}
					?>
                    <th>Quantity</th>
                </tr>
                <?php
					while($r=mysql_fetch_assoc($result)):
				?>	
                	<tr>
                		<td><?=$r[stock];?></td>
                        <?php
						if($_REQUEST[priceoption]=="y"){
							echo "<td>P ".number_format($options->getCostOfStock($r[material]),2,'.',',')."</td>";
						}
						?>
                        <td><?=$r[quantity];?></td>
					</tr>
				<?php	
					endwhile;
				?>
            
            </table>
        
        </div><!--End of content-->
    </div><!--End of Form-->
    

  


</div>
</body>
</html>