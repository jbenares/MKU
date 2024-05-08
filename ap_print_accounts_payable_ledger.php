<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	
	$options=new options();	
	
	$from_date		= $_REQUEST[startdate];
	$to_date		= $_REQUEST[enddate];
	$supplier_id	= $_REQUEST[supplier_id];
	
	function getDueDate($rr_header_id){
		$options = new options();
		$rr_date		= $options->getAttribute('rr_header','rr_header_id',$rr_header_id,'date');
		$po_header_id 	= $options->getAttribute('rr_header','rr_header_id',$rr_header_id,'po_header_id');
		$terms			= $options->getAttribute('po_header','po_header_id',$po_header_id,'terms');
		
		if(is_numeric($terms)){
			return date("m/d/Y",strtotime("+$terms days",strtotime($rr_date)));	
		}else{
			return "<em>(TERMS IS NOT NUMERIC)</em>";	
		}
		
	}
	
	function getInvoices($rr_header_id){
		$result = mysql_query("
			select 
				invoice
			from	
				rr_detail
			where
				rr_header_id = '$rr_header_id'
		") or die(mysql_error());
		$a = array();
		while($r = mysql_fetch_assoc($result)){
			$a[] = $r['invoice'];	
		}
		$invoices = implode(',',$a);
		
		return $invoices;
	}
		
	
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


body
{
	size: legal portrait;		
	padding:0px;
	font-family:Arial, Helvetica, sans-serif;
	font-size:11px;
}
.container{
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
	
     <div><!--Start of Form-->
        <?php
			require("form_heading.php");
        ?>

		<div style="text-align:center; font-size:14px; margin-bottom:20px;">
	        ACCOUNTS PAYABLE LEDGER<br />
			<span style="font-size:8px; font-style:italic;">Date covered From <?=date("F j, Y",strtotime($startdate))?> to <?=date("F j, Y",strtotime($enddate))?></span>
        </div>
           
        <div class="header" style="">
        	<table style="width:100%;">
                <tr>
                	<td width="10%"><strong>Supplier:</strong></td>
                    <td width="90%"><?=$options->getSupplierName($supplier_id);?></td>
               	</tr>
            </table>
     	</div><!--End of header-->
        
        <div class="content" style="">
        	<table style="width:100%;">
            	<tr>
                	<th>Date</th>
                    <th nowrap="nowrap">Reference</th>
                    <th nowrap="nowrap">Due Date</th>
                    <th>Remarks</th>
                    <th>Particulars</th>
                    <th style="text-align:right;">Charges</th>
                    <th style="text-align:right;">Payments</th>
                    <th style="text-align:right;">Balance</th>
                </tr>
                
                <?php
				$balance=0;
				$_date = date("Y-m-d",strtotime("-1 day",strtotime($from_date)));
				$balanceforwarded=$options->getAPBalanceOnAndOverDate($supplier_id,$_date);
				
				?>
                <tr>
                    <td colspan="5"><div align="left">Balance Forwarded</div></td>
                    <td><div align="right"><?=number_format($balanceforwarded,2,'.',',');?></div></td>
                </tr>
                    
         		<?php
                #MRR
				$query="
					select
						*
					from
						rr_header
					where
						date between '$from_date' and '$to_date'
					AND	
						supplier_id = '$supplier_id'
					and
						status!='C'
				";

				$result=mysql_query($query) or die(mysql_error());
				
				while($r=mysql_fetch_assoc($result)):
				
				$data[]= array(
					'date' => $r['date'],
					'reference' => "M.R.R # :".str_pad($r['rr_header_id'],7,0,STR_PAD_LEFT),
					'remarks' => 'Materials Receiving Report',
					'charges' => $r['netamount'],
					'payments' => 0,
					'invoices' => getInvoices($r['rr_header_id']),
					'due_date' => getDueDate($r['rr_header_id'])
				);
				
				endwhile;
				?>
                
                <?php
				#EV
				/*$query="
					select
						cv_date,
						sum(amount) as amount,
						h.cv_header_id,
						h.cv_no
					from
						cv_header as h, cv_detail as d, gchart as g
					where
						type = 'E'
					and
						h.cv_header_id = d.cv_header_id
					and
						d.gchart_id = g.gchart_id
					and
						mclass != 'A'
					and
						status != 'C'
					and
						cv_date between '$from_date' and '$to_date'
					and
						supplier_id = '$supplier_id'		
					group by h.cv_no
				";	

				$result=mysql_query($query) or die(mysql_error());
				
				while($r=mysql_fetch_assoc($result)):
				
				$data[]= array(
					'date' => $r['cv_date'],
					'reference' => "C.V # :".$r['cv_no'],
					'remarks' => 'Check Voucher',
					'charges' => $r['amount'],
					'payments' => 0
				);

				endwhile;*/
				?>
                
                 <?php
				 #CV
				$query="
					select
						cv_date,
						h.cv_header_id,
						sum(amount) as amount,
						cv_no,
						check_date,
						particulars
					FROM	
						cv_header as h, cv_detail as d
					where
						h.cv_header_id = d.cv_header_id
					and
						status!='C'
					and
						cv_date between '$from_date' and '$to_date'
					and
						supplier_id = '$supplier_id'	
					group by h.cv_header_id
				";	

				$result=mysql_query($query) or die(mysql_error());
				
				while($r=mysql_fetch_assoc($result)):
				
				$data[]= array(
					'date' => $r['cv_date'],
					'reference' => "C.V # :".$r['cv_no']." (".date("m/d/Y",strtotime($r['check_date'])).")",
					'remarks' => 'Disbursement Voucher',
					'charges' => 0,
					'payments' => $r['amount'],
					'particulars' => $r['particulars'],
				);

				endwhile;
				?>
          	  <?php
					
					$date=array();
					if($data):
						foreach ($data as $key => $row) {
							$date[]  = $row['date'];
						}
						array_multisort($date, SORT_ASC, $data);
						$balance=0;
						$balance+=$balanceforwarded;
						
						$total_charges = 0;
						$total_payments = 0;
						foreach ($data as $key => $row):
						$balance+=$row['charges'];
						$balance-=$row['payments'];
						
						$total_charges += $row['charges'];
						$total_payments += $row['payments'];
						
						$date_display = date("m/d/Y",strtotime($row['date']));
						?>
							<tr>
								<td ><?=$date_display?></td>
								<td nowrap="nowrap"><?=$row['reference']?></td>
                                <td nowrap="nowrap"><?=$row['due_date']?></td>
								<td><?=$row['remarks']?> <?php if(!empty($row['invoices'])) echo "(".$row['invoices'].")" ?></td>
                                <td><?=$row['particulars']?></td>
								<td style="text-align:right;"><?=number_format($row['charges'],2,'.',',')?></td>
								<td style="text-align:right;"><?=number_format($row['payments'],2,'.',',')?></td>
								 <td style="text-align:right;"><?=number_format($balance,2,'.',',')?></td>
							</tr>
						<?php
						endforeach;
						echo "
							<tr>
								<td style='border-top:1px solid #000;'></td>
								<td style='border-top:1px solid #000;' nowrap='nowrap'></td>
								<td style='border-top:1px solid #000;' nowrap='nowrap'></td>
								<td style='border-top:1px solid #000;'></td>
								<td style='border-top:1px solid #000;'></td>
								<td style='text-align:right; border-top:1px solid #000; font-weight:bold;'>".number_format($total_charges,2,'.',',')."</td>
								<td style='text-align:right; border-top:1px solid #000; font-weight:bold;'>".number_format($total_payments,2,'.',',')."</td>
								<td style='border-top:1px solid #000;'></td>
							</tr>
						";
					endif;
				?>
               
            
            </table>
        
        </div><!--End of content-->
    </div><!--End of Form-->
    
</div>
</body>
</html>