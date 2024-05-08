<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$fromdate=$_REQUEST['fromdate'];
	$todate=$_REQUEST['todate'];
	$locale_id=$_REQUEST['locale_id'];
	$category=$_REQUEST['category'];
	$type=$_REQUEST['type'];
	$option=$_REQUEST['option'];
	
	
	if($option=="Amount"){
		$amount_option = TRUE;	
		$summary_display = "Sales";
	}else{
		$amount_option = FALSE;	
		$summary_display = "Quantity";
	}

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


@media print and (width: 14in) and (height: 8.5in) {
  @page {
	  margin: 1in;
  }
}
	
body
{
	size: legal landscape;
	padding:0px;
	font-family:Arial, Helvetica, sans-serif;
	font-size:10pt;
}
.container{
	width:13.8in;
	/*height:10.8in;*/
	margin:0px auto;
	/*border:1px solid #000;*/
	padding:0.1in;
	/*overflow:auto;*/
}

.header
{	
	margin:20px 0px;
	
}

.header table, .content table
{
	width:100%;

}
.header table td, .content table td
{
	padding:1px;	
}

.content table{
	border-collapse:collapse;
}
.content table td,.content table th{
	border:1px solid #000;
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

.verticalText
{
text-align: center;
vertical-align: middle;
width: 20px;
margin: 0px;
padding: 0px;
padding-left: 3px;
padding-right: 3px;
padding-top: 10px;
white-space: nowrap;
-webkit-transform: rotate(-90deg); 
-moz-transform: rotate(-90deg);  	

}

th
{
text-align: center;
vertical-align: bottom;
height: 100px;
padding-bottom: 3px;
padding-left: 5px;
padding-right: 5px;
}

</style>
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
   
   
   		 <?php
			require("form_heading.php");
        ?>
    
        <div style="text-align:center; font-size:14px; margin-bottom:20px;">
	        PERIODIC SALES REPORT<br />
			<span style="font-size:8px; font-style:italic;">Date covered From <?=date("F j, Y",strtotime($fromdate))?> to <?=date("F j, Y",strtotime($todate))?></span>
        </div>    
      
        <?php
		$stocks = array();
		$query="
			select
				*
			from
				productmaster
			where
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
			$stock_id = $r[stock_id];		
			array_push($stocks,$stock_id);
		endwhile;
		
		$date=$fromdate;
		$sumtotal=0;
		$x=1;
		$dates=array();			
		while($date<=$todate){
			array_push($dates,$date);
			$date=date("Y-m-d",strtotime("+1 day",strtotime($date)));
		}
		?>
        
      	<div class="content" style="">
        	<table style="width:100%;">
            	<tr>
                    <th></th>
                    <?php	
                    foreach($dates as $date){
                    ?>
                        <th><div class="verticalText"><?=$date?></div></th>
                    <?php
                        }
                    ?>
                    <th>Total</th>
               	</tr>
				<?php
				$total_sum_sales=0;
                foreach($stocks as $stock_id){
                ?>
                	<tr>
                        <td><?=$options->attr_stock($stock_id,'stock')?></td>
                        <?php
						$total_sale=0;
                        foreach($dates as $date){
							$sale = $options->getTotalSalesOfItem($date,$stock_id,$amount_option);
							$total_sale+=$sale;
							$total_sum_sales+=$sale;
                        ?>
                            <td align="right"><?=($sale>0)?number_format($sale,2,'.',','):""?></td>
                        <?php
                        }
                        ?>
                        <td align="right"><?=number_format($total_sale,2,'.',',')?></td>
                   	</tr>
                <?php
                }
                ?>
                <tr>
                	<td colspan="<?=count($dates)+1?>" align="right">Total <?=$summary_display?></td>
                    <td align="right"><?=number_format($total_sum_sales,2,'.',',')?></td>
                </tr>
            </table>
        
        </div><!--End of content-->
        
        
    </div><!--End of Form-->
    

  


</div>
</body>
</html>