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
        	<h2 style="margin:0px;">AGING OF ACCOUNTS RECEIVABLES</h2>
			As of <?=$date?>
        </div>
        
       
        <div class="content" style="">
        	<table style="width:100%;">
            	<tr>
                	<th>No.</th>
                    <th>Customer Name</th>
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
							account
					";

					$result=mysql_query($query);
					
					$date30=date("Y-m-d", strtotime('- 30 days',strtotime($date)));
					$date31=date("Y-m-d", strtotime('- 31 days',strtotime($date)));
					$date60=date("Y-m-d", strtotime('- 60 days',strtotime($date)));
					$date61=date("Y-m-d", strtotime('- 61 days',strtotime($date)));
					$date90=date("Y-m-d", strtotime('- 90 days',strtotime($date)));
					$date91=date("Y-m-d", strtotime('- 91 days',strtotime($date)));
					
					echo "<br>$date30 - $date<br>";
						
					while($r=mysql_fetch_assoc($result)):
					$total=	$options->getAccountBalanceBetweenDates($r[account_id],$date30,$date) + 
							$options->getAccountBalanceBetweenDates($r[account_id],$date60,$date31) +
							$options->getAccountBalanceBetweenDates($r[account_id],$date90,$date61) + 
							$options->getAccountBalanceOnAndOverDate($r[account_id],$date91);
					$sumtotal+=$total;
					
				?>	
                	<tr>
                		<td><?=$x++.'.';?></td>
                        <td><?=$r[account];?></td>
                        <td><div align="right"><?=number_format($options->getAccountBalanceBetweenDates($r[account_id],$date30,$date),2,'.',',');?></div></td>
                        <td><div align="right"><?=number_format($options->getAccountBalanceBetweenDates($r[account_id],$date60,$date31),2,'.',',');?></div></td>
                        <td><div align="right"><?=number_format($options->getAccountBalanceBetweenDates($r[account_id],$date90,$date61),2,'.',',');?></div></td>
                        <td><div align="right"><?=number_format($options->getAccountBalanceOnAndOverDate($r[account_id],$date91),2,'.',',');?></div></td>
                        <td><div align="right"><?=number_format($total,2,'.',',');?></div></td>
					</tr>
				<?php	
					endwhile;
				?>
                
                	<tr style="font-weight:bolder;">
                    	<td colspan="6" align="right">Total: </td>
                        <td><div align="right"><?=number_format($sumtotal,2,'.',',')?></div></td>
                    </tr>
            
            </table>
            
            
        
        </div><!--End of content-->
    </div><!--End of Form-->
    

  


</div>
</body>
</html>