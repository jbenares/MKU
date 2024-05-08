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
	font-size:14pt;
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
.header table td{
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
		include_once("company_heading.php");
		?>
        
        <div align="center" style="margin:20px 0px;">	
        	<h2 style="margin:0px;">STATEMENT OF ACCOUNT</h2>
			Date covered <?=$startdate?> to <?=$enddate?>
        </div>
        
           
        <div class="header" style="clear:both;">
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
                	<th>Date Balance</th>
                    <th>Reference</th>
                    <th>Remarks</th>
                    <th>Charges</th>
                    <th>TOTAL</th>
                </tr>
         		<?php
					$balance=0;
					$query="
						SELECT
							*
						FROM
							dr_header
						WHERE 
							date between '$startdate' and '$enddate'
						and
							account_id='$account_id'
						and
							status!='C'
						and
							paytype='H'
					";

					$result=mysql_query($query);
					
					while($r=mysql_fetch_assoc($result)):
					$balance+=$r[netamount];
					
					$data[]= array(
						'date' => $r['date'],
						'reference' => str_pad($r[dr_header_id],7,0,STR_PAD_LEFT),
						'remarks' => 'Delivery',
						'charges' => $r[netamount],
						'payments' => 0
					);
				?>	
				<?php	
					endwhile;
				?>
                
                
                <?php
					/*
					$query="
						SELECT
							*
						FROM
							pay_header
						WHERE 
							date between '$startdate' and '$enddate'
						and
							account_id='$account_id'
						and
							status!='C'
					";

					$result=mysql_query($query);
					
					while($r=mysql_fetch_assoc($result)):
					$balance-=$r[total_amount];
				
					$data[]= array(
						'date' => $r['date'],
						'reference' => $r[reference],
						'remarks' => 'Checks or Cash Payments',
						'charges' => 0,
						'payments' => $r[total_amount]
					);
					

				?>	
				<?php	
					
					endwhile;
				*/
					
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
								<!--<td style="text-align:right;"><?=number_format($row['payments'],2,'.',',')?></td> -->
	   						    <td style="text-align:right;"><?=number_format($balance,2,'.',',')?></td>
							</tr>
						<?php
						endforeach;
					endif;
				?>
                
                <tr>
                	<td colspan="6">Remarks:</td>
                </tr>
            
            </table>
            
            <?php
			//$date = date("Y-m-d", strtotime('- 1 day',strtotime($enddate)));
			$date = $enddate;
            $date15=date("Y-m-d", strtotime('- 15 days',strtotime($date)));
			$date16=date("Y-m-d", strtotime('- 16 days',strtotime($date)));
			$date30=date("Y-m-d", strtotime('- 30 days',strtotime($date)));
			$date31=date("Y-m-d", strtotime('- 31 days',strtotime($date)));
			$date45=date("Y-m-d", strtotime('- 45 days',strtotime($date)));
			$date46=date("Y-m-d", strtotime('- 46 days',strtotime($date)));
			
			$balance_0_15 = $options->getAccountBalanceBetweenDates($account_id,$date15,$date);
			$balance_16_30 = $options->getAccountBalanceBetweenDates($account_id,$date30,$date16);
			$balance_31_45 = $options->getAccountBalanceBetweenDates($account_id,$date45,$date31);
			$balance_46 = $options->getAccountBalanceOnAndOverDate($account_id,$date46);
			
			$total_balance = $balance_0_15 + $balance_16_30 + $balance_31_45 + $balance_46;
			?>
            <table>
            	<tr>
                	<th>CURRENT</th>
                    <th>16 TO 30 DAYS</th>
                    <th>31 TO 45 DAYS</th>
                    <th>46 + DAYS</th>
                    <th>TOTAL</th>
                </tr>
                <tr>	
                	<td><?=number_format($balance_0_15,2,'.',',')?></td>
                    <td><?=number_format($balance_16_30,2,'.',',')?></td>
                    <td><?=number_format($balance_31_45,2,'.',',')?></td>
                    <td><?=number_format($balance_46,2,'.',',')?></td>
                    <td><?=number_format($total_balance,2,'.',',')?></td>
                </tr>
            </table>
        
        </div><!--End of content-->
    </div><!--End of Form-->
    

  


</div>
</body>
</html>