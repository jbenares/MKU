<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");

	$date=$_REQUEST['date'];
	
	$options=new options();	
	
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
	margin-top:20px;
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
	/*border:1px solid #000;*/
	padding:10px;
}
.withborder td,.withborder th{
	border:1px solid #000;
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

.noborder{
	border:none;	
}

.alignRight{
	text-align:right;	
}

</style>
</head>
<body>
<div class="container">
    
     <div style="margin-bottom:100px;"><!--Start of Form-->
     
     	<?php
			require("form_heading.php");
        ?>

        <div style="text-align:center; font-size:14px; margin-bottom:20px;">
           	AP Report<br />
			As of <?php echo date("F j, Y",strtotime($date))?>
        </div>   
             
        <div class="content" >
        	<table cellspacing="0" class="withborder">
                <tr>
                	<th>SUPPLIER</th>
                    <th>CHARGES</th>
                    <th>PAYMENTS</th>
                    <th>BALANCE</th>
                </tr>
                <?php
                $query="
                    select 
                        *
                    FROM	
                       supplier
                ";
                
                $result=mysql_query($query) or die(mysql_error());		
				
                ?>  
                <?php
                while($r=mysql_fetch_assoc($result)):
					$supplier=$r[account];
					$supplier_id=$r[account_id];
					
					$charge=$options->getBalanceFromSupplier($date,$supplier_id);
					$disbursement=$options->getDisbursementForSupplier($date,$supplier_id);
					$balance=$charge-$disbursement;
				?>	
				<tr>
                	<td><?=$supplier?></td>
                    <td style="text-align:right;"><?=number_format($charge,2,'.',',')?></td>
                    <td style="text-align:right;"><?=number_format($disbursement,2,'.',',')?></td>
                    <td style="text-align:right;"><?=number_format($balance,2,'.',',')?></td>
                	
                </tr>	
				<?php	
				endwhile;
                ?>
                

            </table>
            <table  class="noborder" style="border:none; margin-top:20px;">
            	<tr>
                	<td>Prepared by:</td>
                    <td>Checked by:</td>
                    <td>Approved by:</td>
                    <td>Released by:</td>
                    <td>Received by:</td>
                </tr>
            </table>
        </div><!--End of content-->
    </div><!--End of Form-->
   
</div>
</body>
</html>