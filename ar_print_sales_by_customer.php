<?php
require_once('my_Classes/options.class.php');
include_once("conf/ucs.conf.php");

$options=new options();	

$fromdate	= $_REQUEST['fromdate'];
$todate		= $_REQUEST['todate'];

$project_name		= $_REQUEST['project_name'];
$project_id			= $_REQUEST['project_id'];

$contractor_name	= $_REQUEST['contractor_name'];
$contractor_id		= $_REQUEST['contractor_id'];

$account			= $_REQUEST['account'];

$account_id = $id	= ($account=="p")?$project_id:$contractor_id;
$header				= ($account=="p")?"project_id":"contractor_id";


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
	
     <div><!--Start of Form-->
        
        
        <?php
			require("form_heading.php");
        ?>
    
        <div style="text-align:center; font-size:14px; margin-bottom:20px;">
	        SALES BY CUSTOMER<br />
			<span style="font-size:8px; font-style:italic;">Date covered From <?=date("F j, Y",strtotime($fromdate))?> to <?=date("F j, Y",strtotime($todate))?></span>
        </div>
      
      
      	
        <div class="content" style="">
        	<table style="width:100%;">
            	<tr>
                	<th width="9%"># </th>
                    <th width="65%">Customer Name</th>
                    <th width="26%">Sales</th>
                </tr>
                
                <?php
					$query="
						select
							sum(total_amount) as total_amount,
							account,
							account_id
						from
							accounts_receivable			
						where
							status != 'C'
						and
							date between '$fromdate' and '$todate'
						group by
							account, account_id
					";
					
				$result=mysql_query($query) or die(mysql_query());				
				
				$sumtotal=0;
				$x=1;
				while($r=mysql_fetch_assoc($result)):
					$total_amount	= $r['total_amount'];	
					$account		= $r['account'];
					$account_id		= $r['account_id'];
					
					$account_id_display = ($account == "project_id")?$options->attr_Project($account_id,'project_name'):$options->attr_Contractor($account_id,'contractor');
					$sumtotal += $total_amount;
				?>	
                	<tr>
                		<td><?=$x++?></td>
                        <td><?=$account_id_display?></td>
                        <td width="26%"><div align="right"><?=number_format($total_amount,2,'.',',');?></div></td>
					</tr>
				<?php	
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