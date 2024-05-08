<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$startdate		= $_REQUEST['startdate'];
	$enddate		= $_REQUEST['enddate'];
	$project_id		= $_REQUEST['project_id'];
		
	
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
	        ACCOUNT LEDGER<br />
			<span style="font-size:8px; font-style:italic;">Date covered From <?=date("F j, Y",strtotime($startdate))?> to <?=date("F j, Y",strtotime($enddate))?></span>
        </div>    

        <div class="header" style="">
        	<table style="width:100%;">
                <tr>
                	<td width="11%"><strong>Project :</strong></td>
                    <td width="89%"><?=$options->attr_Project($project_id,'project_name');?></td>
               	</tr>
            </table>
     	</div><!--End of header-->
        
        <div class="content" style="">
        	<table style="width:100%;">
            	<tr>
                	<th>Date</th>
                    <th>Reference</th>
                    <th>Remarks</th>
                    <th>Charges</th>
                    <th>Payments</th>
                    <th>Balance</th>
                </tr>
                
                <?php
				$balance=0;
				$balanceforwarded=$options->getBalanceForwarded($startdate,$project_id);
				
				?>
                <tr>
                    <td colspan="5"><div align="left">Balance Forwarded</div></td>
                    <td><div align="right"><?=number_format($balanceforwarded,2,'.',',');?></div></td>
                </tr>
                    
         		<?php
					
					$query="
						SELECT
							*
						FROM
							sales_invoice
						WHERE 
							date between '$startdate' and '$enddate'
						and
							project_id = '$project_id'
					";

					$result=mysql_query($query);
					
					while($r=mysql_fetch_assoc($result)):
					
					$sales_invoice_id		= $r['sales_invoice_id'];
					$sales_invoice_id_pad	= str_pad($sales_invoice_id,7,0,STR_PAD_LEFT);
					$or_no  				= $r['or_no'];
					$amount					= $r['amount'];
					
					
					$data[]= array(
						'date' => $r['date'],
						'reference' => "Invoice # : $sales_invoice_id_pad",
						'remarks' => 'Sales Invoice',
						'charges' => $amount,
						'payments' => 0
					);
				?>	
				<?php	
					endwhile;
				?>
                
                
                <?php
				
					$query="
						SELECT
							*
						FROM
							sales_invoice
						WHERE 
							payment_date between '$startdate' and '$enddate'
						and
							project_id = '$project_id'
					";

					$result=mysql_query($query);
					
					while($r=mysql_fetch_assoc($result)):
					
					$sales_invoice_id		= $r['sales_invoice_id'];
					$sales_invoice_id_pad	= str_pad($sales_invoice_id,7,0,STR_PAD_LEFT);
					$or_no  				= $r['or_no'];
					$checkamount			= $r['checkamount'];
					
					$data[]= array(
						'date' 		=> $r['payment_date'],
						'reference' =>  "Invoice # : $sales_invoice_id_pad",
						'remarks' 	=> 'Check Payments',
						'charges' 	=> 0,
						'payments' 	=> $checkamount
					);

				?>	
				<?php	
					endwhile;
					
					$date=array();
					if($data):
						foreach ($data as $key => $row) {
							$date[]  = $row['date'];
						}
						array_multisort($date, SORT_ASC, $data);
						$balance=0;
						$balance+=$balanceforwarded;
						foreach ($data as $key => $row):
						$balance+=$row['charges'];
						$balance-=$row['payments'];
						
						$date = date("F j, Y",strtotime($row['date']));
						?>
							<tr>
								<td ><?=$row['date']?></td>
								<td><?=$row['reference']?></td>
								<td><?=$row['remarks']?></td>
								<td style="text-align:right;"><?=number_format($row['charges'],2,'.',',')?></td>
								<td style="text-align:right;"><?=number_format($row['payments'],2,'.',',')?></td>
								 <td style="text-align:right;"><?=number_format($balance,2,'.',',')?></td>
							</tr>
						<?php
						endforeach;
					endif;
				?>
               
            
            </table>
        
        </div><!--End of content-->
</div>
</body>
</html>