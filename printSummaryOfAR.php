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
        	<h2 style="margin:0px;">SUMMARY OF ACCOUNT RECEIVABLES</h2>
			As of <?=$date?> 
        </div>
        
       
        <div class="content" style="">
        	<table style="width:100%;">
            	<tr>
                	<th>No.</th>
                    <th>Customer Name</th>
                    <th>Account Balance</th>
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
					
					while($r=mysql_fetch_assoc($result)):
					$sumtotal+=$options->getAccountBalanceForAR($r[account_id],$date);
				?>	
                	<tr>
                		<td><?=$x++.'.';?></td>
                        <td><?=$r[account];?></td>
                        <td><div align="right"><?=number_format($options->getAccountBalanceForAR($r[account_id],$date),2,'.',',');?></div></td>
					</tr>
				<?php	
					endwhile;
				?>
                
                <tr style="font-weight:bolder;">
                	<td colspan="2" align="right">Total: </td>
                    <td><div align="right"><?=number_format($sumtotal,2,'.',',');?></div></td>
                </tr>
                
            
            </table>
        
        </div><!--End of content-->
    </div><!--End of Form-->
    

  


</div>
</body>
</html>