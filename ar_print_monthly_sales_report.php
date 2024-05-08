<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$year=$_REQUEST['year'];
	$locale_id=$_REQUEST[locale_id];

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
	font-size:10px;
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
     	
        <?php
			require("form_heading.php");
        ?>
    
        <div style="text-align:center; font-size:14px; margin-bottom:20px;">
	        MONTHLY SALES REPORT<br />
			<span style="font-size:8px; font-style:italic;">Year <?=$year?></span>
        </div>    

        <div class="content" style="">
        	<table style="width:100%;">
            	<tr>
                	<th width="9%"># </th>
                    <th width="65%">Month</th>
                    <th width="26%">Sales</th>
                </tr>
                
         		<?php
					$startmonth=$month=strtotime($year."-01-01");
					$endmonth=strtotime($year."-12-01");
					$sumtotal=0;
					$x=1;
					while($month<=$endmonth):
					$date=date("F",$month);
					$uptomonth=$m= strtotime("+1 month -1 day", $month);
					
					$fromdate=date("Y-m-d",$month);
					$todate=date("Y-m-d",$uptomonth);
					$sales=$options->getTotalSalesBetweenDate($fromdate,$todate);
					$sumtotal+=$sales;
				?>	
                	<tr>
                		<td><?=$x++?></td>
                        <td><?=$date?></td>
                        <td width="26%"><div align="right"><?=number_format($sales,2,'.',',');?></div></td>
					</tr>
                    
				<?php	
					$month = strtotime("+1 month", $month);

					endwhile;
				?>
                	<tr style="font-weight:bolder;">	
                    	<td colspan="2" align="right">Total: </td>
                        <td><div align="right"><?=number_format($sumtotal,2,'.',',')?></div></td>
                    </tr>
                
            
            </table>
        
        </div><!--End of content-->
    </div><!--End of Form-->
    

  


</div>
</body>
</html>