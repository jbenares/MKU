<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$fromdate=$_REQUEST['fromdate'];
	$todate=$_REQUEST['todate'];
	$locale_id=$_REQUEST[locale_id];
	$category=$_REQUEST[category];

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
   
        <div align="center" style="margin:20px 0px;">	
        	<h2 style="margin:0px;">PERIODIC SALES REPORT</h2>
			Date covered From <?=$fromdate?> to <?=$todate?>
        </div>
      	
        <?php
		
		$query="
			select
				*
			from
				productmaster
			where
				type='$_REQUEST[type]'
			and
			(
				categ_id1='$category'
			or
				categ_id2='$category'
			or	
				categ_id3='$category'
			or
				categ_id4='$category'
			)
				
		";
		$result=mysql_query($query);
		
		while($r=mysql_fetch_assoc($result)):
		if($options->getTotalSalesOfItemBetweenDate($fromdate,$todate,$r[stock_id],$locale_id)):
		
		?>
      
      	<div class="header">
        	<table>
            	<tr>
                	<td width="9%">
                    	Stock:
                    </td>
                    <td width="91%">	
                    	<?=$r[stock]?>
                    </td>
                </tr>
            </table>
        </div>

        <div class="content" style="">
        	<table style="width:100%;">
            	<tr>
                	<th width="9%"># </th>
                    <?php
					if($_REQUEST[reporttype]=="D"):
					?>
                    <th>Date</th>
                    <?php
					endif;
					?>
                    <th>Product Description</th>
                    <th>Total Sales</th>
                </tr>
                
         		<?php
					$date=$fromdate;
					$sumtotal=0;
					$x=1;
					
					if($_REQUEST[reporttype]=="D"):
				
					
					while($date<=$todate){
						$sales=$options->getTotalSalesOfItem($date,$r[stock_id],$locale_id);
						$sumtotal+=$sales;
						if($sales>0):
				?>	
                            <tr>
                                <td><?=$x++?></td>
                                <td><?=$date?></td>
                                <td><?=$options->getMaterialName($r[stock_id])?></td>
                                <td width="26%"><div align="right"><?=number_format($sales,2,'.',',');?></div></td>
                            </tr>
				<?php	
							
						endif;
						$date=date("Y-m-d",strtotime("+1 day",strtotime($date)));
					}
					else:
						$sales=$options->getTotalSalesOfItemBetweenDate($fromdate,$todate,$r[stock_id],$locale_id);
						$sumtotal+=$sales;
				?>
                	<tr>
                        <td><?=$x++?></td>
                       <!-- <td><?=$fromdate." to ".$todate?></td>-->
                        <td><?=$options->getMaterialName($r[stock_id])?></td>
                        <td width="26%"><div align="right"><?=number_format($sales,2,'.',',');?></div></td>
                    </tr>
                
                <?php
					endif;
				?>
                	<tr style="font-weight:bolder;">	
                    	<?php
						$colspan=($_REQUEST[reporttype]=="D")?3:2;
						?>
                    	<td colspan="<?=$colspan?>" align="right">Total Sales: </td>
                        <td><div align="right"><?=number_format($sumtotal,2,'.',',')?></div></td>
                    </tr>
                
            
            </table>
        
        </div><!--End of content-->
        
        <?php
		
		endif;
		endwhile;
		?>
    </div><!--End of Form-->
    

  


</div>
</body>
</html>