<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$startdate=$_REQUEST[startdate];
	$enddate=$_REQUEST[enddate];
	$supplier_id=$_REQUEST[supplier_id];
		
	
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
           	ACCOUNTS PAYABLE LEDGER<br />
            Date covered <?=date("F j, Y",strtotime($startdate))?> to <?=date("F j, Y",strtotime($enddate))?>
        </div>   
        
           
        <div class="header" style="">
        	<table style="width:100%;">
                <tr>
                	<td width="25%"><strong>Supplier:</strong></td>
                    <td width="75%"><?=$options->getSupplierName($supplier_id);?></td>
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
				$balanceforwarded=$options->getBalancePayableForwarded($startdate,$supplier_id);
				
				?>
                <tr>
                    <td colspan="5"><div align="left">Balance Forwarded</div></td>
                    <td><div align="right"><?=number_format($balanceforwarded,2,'.',',');?></div></td>
                </tr>
                    
         		<?php
					
					$query="
						select
							*
						from
							accounts_payable
						where
							due_date between '$startdate' and '$enddate'
						AND	
							supplier_id = '$supplier_id'
						and
							status!='C'
					";

					$result=mysql_query($query) or die(mysql_error());
					
					while($r=mysql_fetch_assoc($result)):
					$balance+=$r[netamount];
					
					$data[]= array(
						'date' => $r['due_date'],
						'reference' => $r['header_id'],
						'remarks' => 'Materials Receiving Report',
						'charges' => $r['total_amount'],
						'payments' => 0
					);
				?>	
				<?php	
					endwhile;
				?>
                
                <?php
					
					
					$query="
						select
							*
						FROM	
							ap_payment
						where
							status!='C'
						and
							date between '$startdate' and '$enddate'
						and
							supplier_id = '$supplier_id'		
					";	

					$result=mysql_query($query) or die(mysql_error());
					
					while($r=mysql_fetch_assoc($result)):
					$balance-=$r[debit];
					
					$data[]= array(
						'date' => $r['date'],
						'reference' => $r['checkno'],
						'remarks' => 'Disbursement',
						'charges' => 0,
						'payments' => $r['amount']
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
    </div><!--End of Form-->
    
</div>
</body>
</html>