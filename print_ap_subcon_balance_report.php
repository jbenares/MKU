<?php
	require_once('my_Classes/options.class.php');
	include_once("conf/ucs.conf.php");
	require_once(dirname(__FILE__).'/library/lib.php');

	//$date			= $_REQUEST['date'];
	$from			= $_REQUEST['from'];
	$to				= $_REQUEST['to'];
	$supplier_id	= $_REQUEST['supplier_id'];
	$project_id		= $_REQUEST['project_id'];
    $work_category_id		= $_REQUEST['work_category_id'];
    $sub_word_category_id		= $_REQUEST['sub_word_category_id'];
	
	$options=new options();	
	
	function getPayments($po_header_id){
		$result = mysql_query("
			select
				sum(d.amount) as amount
			from
				cv_header as h, cv_detail as d, sub_apv_header as s
			where
				h.cv_header_id = d.cv_header_id
			and
				h.sub_apv_header_id = s.sub_apv_header_id
			and
				s.po_header_id = '$po_header_id'
			and
				h.status != 'C'
			and
				s.status != 'C'
		") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		
		return $r['amount'];
	}
	
	function getAccomplishmentsAmount($po_header_id){
		$result = mysql_query("
			select
				sum(amount) as amount
			from
				sub_apv_header as h, sub_apv_detail as d
			where
				h.sub_apv_header_id = d.sub_apv_header_id
			and po_header_id = '$po_header_id'
			and h.status != 'C'
		") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		
		return $r['amount'];
	}
	
	function getPOAmount($po_header_id){
		$result = mysql_query("
			select 
				sum(amount) as amount
			from
				po_header as h, spo_detail as d, sub_spo_detail as s
			where
				h.po_header_id = d.po_header_id
			and
				d.spo_detail_id = s.spo_detail_id
			and
				h.po_header_id = '$po_header_id'
		") or die(mysql_error());	
		$r = mysql_fetch_assoc($result);
		
		return $r['amount'];
	}
	
	function getCharges($date,$supplier_id){
		$supplier_id = "s-$supplier_id";
		#SUBCONTRACTORS - 18
		$result = mysql_query("
			select 	
				sum(debit) as debit
			from
				gltran_header as h, gltran_detail as d
			where
				h.gltran_header_id = d.gltran_header_id
			and
				h.status != 'C'
			and
				gchart_id = '18'
			and
				date between '$from' and '$to'
			and	
				account_id = '$supplier_id'
		") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		$debit = $r['debit'];
		
		return $debit;
	}
	
	function getDisbursement($date,$supplier_id){	
		#SUBCONTRACTORS - 18
		
		$result = mysql_query("
			select
				sum(amount) as amount
			from
				ev_header as h, ev_detail as d
			where
				h.ev_header_id = d.ev_header_id
			and
				status != 'C'
			and
				date between '$from' and '$to'
			and
				supplier_id = '$supplier_id'
			and
				gchart_id = '18'
		") or die(mysql_error());
		
		$r = mysql_fetch_assoc($result);
		$amount = $r['amount'];
		
		return $amount;
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
	letter-spacing:1px;
}
.container{
}

.header
{
	text-align:center;	
	margin-top:20px;
}

.header table, .content table
{
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
           	A/P SUBCONTRACTOR<br />
			<span style="font-size:10px; font-style:italic;">From <?=date("F j, Y",strtotime($from))?> To <?=date("F j, Y",strtotime($to))?></span>
			<span></span>
        </div>   
             
        <div class="content" >
        	<table cellspacing="0" >
                <tr>
                	<th>SUPPLIER</th>
                    <th>PO#</th>
					<th>STATUS</th>
					<th>DATE CLOSED</th>
                    <th>PROJECT</th>
                    <th>PO AMOUNT</th>
                    <th>ACCOMPLISHMENTS</th>
                    <th>PAYMENTS</th>
                    <th>BALANCE</th>
					
                </tr>
                <?php
                $query="
                    select 
                        *
                    FROM	
                       	po_header as h, supplier as s
					where
						h.supplier_id = s.account_id
					and status != 'C'
					and po_type = 'S'
					and date between '$from' and '$to'
					and s.subcon = '1'
                ";
				
				if( $supplier_id ) $query .= " and h.supplier_id = '$supplier_id' ";
				if( $work_category_id ) $query .= " and h.work_category_id = '$work_category_id' ";
                if( $sub_work_category_id ) $query .= " and h.sub_work_category_id = '$sub_work_category_id' ";
				#if( $project_id ) 
				#echo $query;
				$t_po_amount = $t_accomplishments_amount = $t_payments = $t_balance = 0;
				$tt_po_amount = $tt_accomplishments_amount = $tt_payments = $tt_balance = 0;
				if( $project_id ){
				
					$query .=" and h.project_id = '$project_id'";
					$query .= "order by s.account asc,h.project_id asc";
				
					$result=mysql_query($query) or die(mysql_error());						
						while($r=mysql_fetch_assoc($result)):
							#echo $supplier=$r[project_id];
							#$supplier_id=$r[account_id];
							
							#$charge=getCharges($date,$supplier_id);
							#$disbursement=getDisbursement($date,$supplier_id);
							#$balance=$charge-$disbursement;
							$po_amount = getPOAmount($r['po_header_id']);
							$accomplishments_amount = getAccomplishmentsAmount($r['po_header_id']);
							$payments = getPayments($r['po_header_id']);
							$date_closed = $r['date_closed'];
							
							$balance = $po_amount - $payments;
							
							#if($balance > 0){
								
							$tt_po_amount += $po_amount;
							$tt_accomplishments_amount += $accomplishments_amount;
							$tt_payments += $payments;
							$tt_balance += $balance;
						?>	
							<tr>
								<td><?=$r['account']?></td>
								<td><?=str_pad($r['po_header_id'],7,0,STR_PAD_LEFT)?></td>
								<td><?=(($r['closed']) ? "<span>CLOSED</span>" : "")?></td>
								<td>
									
								<?php
								if ($date_closed != "0000-00-00"){
									echo date("F j,Y",strtotime($date_closed));
								}else{
									"";
								}
								?>
								</td>
								<td><?=lib::getAttribute('projects','project_id',$r['project_id'],'project_name')?></td>
								<td style="text-align:right;"><?=number_format($po_amount,2,'.',',')?></td>
								<td style="text-align:right;"><?=number_format($accomplishments_amount,2,'.',',')?></td>
								<td style="text-align:right;"><?=number_format($payments,2,'.',',')?></td>
								<td style="text-align:right;"><?=number_format($balance,2,'.',',')?></td> 
								
							</tr>	
						<?php	
							#}
						endwhile;
				}else{
					#$subpo=$subacom=$subpay=$subbal=0;
					$query .= "order by s.account asc,h.project_id asc";
				
					$result=mysql_query($query) or die(mysql_error());
					$pro_id = "";
					$sql=mysql_query("select *
										FROM	
											po_header as h, supplier as s
										where
											h.supplier_id = s.account_id
										and status != 'C'
										and po_type = 'S'
										and date between '$from' and '$to'
										and s.subcon = '1'
										and h.supplier_id = '$supplier_id'
										");
					$row=mysql_num_rows($sql);
					$i=1;
					while($r=mysql_fetch_assoc($result)):
							#$r[project_id];
							
							if(empty($pro_id)) {
								$pro_id = $r[project_id];
								#$subpo=$subacom=$subpay=$subbal=0;
								$po_amount = getPOAmount($r['po_header_id']);
								$accomplishments_amount = getAccomplishmentsAmount($r['po_header_id']);
								$payments = getPayments($r['po_header_id']);
								$date_closed =$r['date_closed'];
							
								
								$balance = $po_amount - $payments;
								
								#if($balance > 0){
									
								$t_po_amount += $po_amount;
								$t_accomplishments_amount += $accomplishments_amount;
								$t_payments += $payments;
								$t_balance += $balance;
							}else{
								if($pro_id != $r[project_id]){
									?>
										<tr>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td style="text-align:right;font-weight:bolder;"><?=number_format($t_po_amount,2,'.',',')?></td>
											<td style="text-align:right;font-weight:bolder;"><?=number_format($t_accomplishments_amount,2,'.',',')?></td>
											<td style="text-align:right;font-weight:bolder;"><?=number_format($t_payments,2,'.',',')?></td>
											<td style="text-align:right;font-weight:bolder;"><?=number_format($t_balance,2,'.',',')?></td>   
										</tr>	
									<?php
									
									$tt_po_amount +=$t_po_amount;
									$tt_accomplishments_amount +=$t_accomplishments_amount;
									$tt_payments +=$t_payments;
									$tt_balance +=$t_balance;	
									
									$t_po_amount = $t_accomplishments_amount = $t_payments = $t_balance = 0;
									
									$pro_id = $r[project_id];
									//continue;
									$po_amount = getPOAmount($r['po_header_id']);
									$accomplishments_amount = getAccomplishmentsAmount($r['po_header_id']);
									$payments = getPayments($r['po_header_id']);
									$date_closed =$r['date_closed'];
							
									
									$balance = $po_amount - $payments;
									
									#if($balance > 0){
										
									$t_po_amount += $po_amount;
									$t_accomplishments_amount += $accomplishments_amount;
									$t_payments += $payments;
									$t_balance += $balance;
									
								}else{
									$po_amount = getPOAmount($r['po_header_id']);
									$accomplishments_amount = getAccomplishmentsAmount($r['po_header_id']);
									$payments = getPayments($r['po_header_id']);
									$date_closed =$r['date_closed'];
							
									
									$balance = $po_amount - $payments;
									
									#if($balance > 0){
										
									$t_po_amount += $po_amount;
									$t_accomplishments_amount += $accomplishments_amount;
									$t_payments += $payments;
									$t_balance += $balance;
									
								}	
							}
							
						?>	
							<tr>
								<td><?=$r['account']?></td>
								<td><?=str_pad($r['po_header_id'],7,0,STR_PAD_LEFT).'-'.$i?></td>
								<td><?=(($r['closed']) ? "<span>CLOSED</span>" : "")?></td>
								<td>
									
								<?php
								if ($date_closed != "0000-00-00"){
									echo date("F j,Y",strtotime($date_closed));
								}else{
									"";
								}
								?>
								</td>
								<td><?=lib::getAttribute('projects','project_id',$r['project_id'],'project_name')?></td>
								<td style="text-align:right;"><?=number_format($po_amount,2,'.',',')?></td>
								<td style="text-align:right;"><?=number_format($accomplishments_amount,2,'.',',')?></td>
								<td style="text-align:right;"><?=number_format($payments,2,'.',',')?></td>
								<td style="text-align:right;"><?=number_format($balance,2,'.',',')?></td> 
												
							</tr>	
						<?php
							if($i==$row){
										//echo $row;
										?>
											<tr>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td style="text-align:right;font-weight:bolder;"><?=number_format($t_po_amount,2,'.',',')?></td>
												<td style="text-align:right;font-weight:bolder;"><?=number_format($t_accomplishments_amount,2,'.',',')?></td>
												<td style="text-align:right;font-weight:bolder;"><?=number_format($t_payments,2,'.',',')?></td>
												<td style="text-align:right;font-weight:bolder;"><?=number_format($t_balance,2,'.',',')?></td>
												
											</tr>	
										<?php
										$tt_po_amount +=$t_po_amount;
										$tt_accomplishments_amount +=$t_accomplishments_amount;
										$tt_payments +=$t_payments;
										$tt_balance +=$t_balance;	
							}
							$i++;
					endwhile;
					
				}
				
                ?>  
                <tr>
                    <td style="border-top:2px solid #000;"></td>
                    <td style="border-top:2px solid #000;"></td>
                    <td style="border-top:2px solid #000;"></td>
					<td style="border-top:2px solid #000;"></td>
					<td style="border-top:2px solid #000;"></td>		
                    <td style="text-align:right; border-top:2px solid #000;"><?=number_format($tt_po_amount,2,'.',',')?></td>
                    <td style="text-align:right; border-top:2px solid #000;"><?=number_format($tt_accomplishments_amount,2,'.',',')?></td>
                    <td style="text-align:right; border-top:2px solid #000;"><?=number_format($tt_payments,2,'.',',')?></td>
                    <td style="text-align:right; border-top:2px solid #000;"><?=number_format($tt_balance,2,'.',',')?></td> 
								
                </tr>	

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