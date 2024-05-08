<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$startdate=$_REQUEST[startdate];
	$enddate=$_REQUEST[enddate];
	$account_id=$_REQUEST[account_id];
		
	
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
        	<h2 style="margin:0px;">SALES SUMMARY FOR SUBSIDIARY</h2>
			Date covered <?=$startdate?> to <?=$enddate?>
        </div>
        
           
        <div class="header" style="">
        	<table style="width:100%;">
                <tr>
                	<td width="25%"><strong>Customer Name:</strong></td>
                    <td width="75%"><?=$options->getAccountName($account_id);?></td>
               	</tr>
                <tr>
                	<td><strong>Address      :</strong></td>
                    <td><?=$options->getAccountAddress($account_id)?></td>
               	</tr>
            </table>
     	</div><!--End of header-->
        
        <div class="content" style="">
        	<table style="width:100%;">
            	<tr>
                	<th>Date</th>
                    <th>Reference</th>
                    <th>Bank</th>
                    <th>Check No.</th>
                    <th>Date Check</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
         		<?php
					
					$query="
						SELECT
							date,
							reference,
							bank,
							checkno,
							datecheck,
							checkamount,
							checkstatus
						FROM
							pay_header,pay_checks
						WHERE 
							pay_header.pay_header_id=pay_checks.pay_header_id
						and
							date between '$startdate' and '$enddate'
						and
							account_id='$account_id'
						and
							status!='C'
					";

					$result=mysql_query($query);
					
					while($r=mysql_fetch_assoc($result)):
				
				?>	
                	<tr>
                		<td><?=$r['date'];?></td>
                        <td><?=$r[reference];?></td>
                        <td><?=$r[bank];?></td>
                        <td><?=$r[checkno];?></td>
                        <td><?=$r[datecheck];?></td>
                        <td><div align="right"><?=number_format($r[checkamount],2,'.',',');?></div></td>
						<td><?=$r[checkstatus];?></td>
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