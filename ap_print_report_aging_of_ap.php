<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$date=$_REQUEST['date']
		
	
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
	margin:0px auto;
	padding:0.1in;
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
	padding:10px;
}

.content table th{
	border-top:1px solid #000;
	border-bottom:1px solid #000;	
}

.content table tr:last-child td{
	border-top:3px double #000;	
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
	
        
        
        <?php
			require("form_heading.php");
        ?>
        
        <div style="text-align:center; font-size:14px; margin-bottom:20px;">
	        AGING OF ACCOUNTS PAYABLE<br />
			<span style="font-size:8px; font-style:italic;">As of <?=date("F j, Y",strtotime($date))?></span>
        </div>           
       
        <div class="content" style="">
        	<table style="width:100%;">
            	<tr>
                	<th>No.</th>
                    <th>Project</th>
                    <th>Current Balance</th>
                    <th>31 to 60 days</th>
                    <th>61 to 90 days</th>
                    <th>90+ days</th>
                    <th>Total</th>
                </tr>
         		<?php
					$x=1;
					$balance=0;
					$sumtotal=0;
					$query="
						SELECT
							*
						FROM
							supplier
						order by
							account asc
					";

					$result=mysql_query($query);
					
					$date30=date("Y-m-d", strtotime('- 30 days',strtotime($date)));
					$date31=date("Y-m-d", strtotime('- 31 days',strtotime($date)));
					$date60=date("Y-m-d", strtotime('- 60 days',strtotime($date)));
					$date61=date("Y-m-d", strtotime('- 61 days',strtotime($date)));
					$date90=date("Y-m-d", strtotime('- 90 days',strtotime($date)));
					$date91=date("Y-m-d", strtotime('- 91 days',strtotime($date)));
						
					while($r=mysql_fetch_assoc($result)):
					$supplier_id	= $r['account_id'];
					$supplier		= $r['account'];
					
					$date_30 		= $options->getAPBalanceBetweenDates($supplier_id,$date30,$date);
					$date_31_60		= $options->getAPBalanceBetweenDates($supplier_id,$date60,$date31);
					$date_61_90 	= $options->getAPBalanceBetweenDates($supplier_id,$date90,$date61);
					$date_91_above 	= $options->getAPBalanceOnAndOverDate($supplier_id,$date91);
					
					
					
					set_time_limit(30);
					
							
					$total 	= $date_30 + $date_31_60 + $date_61_90 + $date_91_above;
							
					
					if($total > 0){
						$sumtotal+=$total;
						$total1  += $date_30;
						$total2	 += $date_31_60;
						$total3  += $date_61_90;
						$total4	 += $date_91_above;
				?>	
                	<tr>
                		<td><?=$x++.'.';?></td>
                        <td><?=$supplier;?></td>
                        <td><div align="right"><?=number_format($date_30,2,'.',',');?></div></td>
                        <td><div align="right"><?=number_format($date_31_60,2,'.',',');?></div></td>
                        <td><div align="right"><?=number_format($date_61_90,2,'.',',');?></div></td>
                        <td><div align="right"><?=number_format($date_91_above,2,'.',',');?></div></td>
                        <td><div align="right"><?=number_format($total,2,'.',',');?></div></td>
					</tr>
				<?php	
					}
					endwhile;
				?>
                
                	<tr style="font-weight:bolder;">
                    	<td colspan="2" align="right">Total: </td>
                        <td style="text-align:right;"><?=number_format($total1,2,'.',',')?></td>
                        <td style="text-align:right;"><?=number_format($total2,2,'.',',')?></td>
                        <td style="text-align:right;"><?=number_format($total3,2,'.',',')?></td>
                        <td style="text-align:right;"><?=number_format($total4,2,'.',',')?></td>
                        <td><div align="right"><?=number_format($sumtotal,2,'.',',')?></div></td>
                    </tr>
            
            </table>
        </div><!--End of content-->
    

  


</div>
</body>
</html>