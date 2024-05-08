<?php
include_once("../conf/ucs.conf.php");
include_once("../library/lib.php");
include_once("../my_Classes/options.class.php");


define('TITLE', "SUBCON APV PAYMENT HISTORY");

$from_date      = $_REQUEST['from_date'];
$to_date        = $_REQUEST['to_date'];
//$payment_status = $_REQUEST['payment_status'];
$options = new options();



$sql = mysql_query("
			Select 
			sub.sub_apv_header_id,
			sub.po_date,
			sub.po_header_id,
			sub.supplier_id,
			sup.account as supplier_name,
			sub.discount_amount,
			sub.date,
			sub.terms,
			sub.project_id,
			sub.work_category_id,
			sub.`status`,
			u.user_lname,
			u.user_fname,
			u.user_mname,
			sub.user_id,
			wrk.`work`,
			prj.project_name
			from
			sub_apv_header as sub,
			supplier as sup,
			work_category as wrk,
			projects as prj,
			admin_access as u
			where
			sub.supplier_id = sup.account_id and
			sub.`status` != 'C' and
			sub.work_category_id = wrk.work_category_id and
			sub.user_id = u.userID and
			prj.project_id = sub.project_id and
			sub.date between '$from_date' and '$to_date'
			order by sup.account
") or die (mysql_error());

function getAmount($sub_apv_header_id){
	
	$sql = mysql_query("Select sum(d.amount) as amount, h.sub_apv_header_id
					from
					sub_apv_detail as d,
					sub_apv_header as h
					where 
					h.sub_apv_header_id = d.sub_apv_header_id and
					h.`status` != 'C' and
					h.sub_apv_header_id = '$sub_apv_header_id'
			") or die (mysql_error());
	$r = mysql_fetch_assoc($sql);
	
	return $r['amount'];
}

function hasPayment($sub_apv_header_id){
	$total_amount2 = 0;
	$sql1 = mysql_query("
		Select h.cv_header_id, h.sub_apv_header_id, h.check_date, sum(d.amount) as amount
		from 
		cv_header as h,
		cv_detail as d
		where 
		sub_apv_header_id = '$sub_apv_header_id' and
		h.cv_header_id = d.cv_header_id and
		h.`status` != 'C'") or die (mysql_error());
		
	while($r1 = mysql_fetch_assoc($sql1)){
		$total_amount2 += $r1['amount'];
	}
	
	return $total_amount2;
}

function hasDisburse($po_header_id){
	$total_amount = 0;
	$sql1 = mysql_query("
			SELECT * 
		FROM 
		`ev_header` as h,
		ev_detail as d
		WHERE 
		h.po_header_id = '$po_header_id' and
		h.status != 'C' and
		h.ev_header_id = d.ev_header_id
	") or die (mysql_error());

	while($r = mysql_fetch_assoc($sql1)){
		$total_amount += $r['amount'];
	}
	
	return $total_amount;
}

function disburseDate($po_header_id){
	$sql = mysql_query("
			SELECT * 
		FROM 
		`ev_header` as h,
		ev_detail as d
		WHERE 
		h.po_header_id = '$po_header_id' and
		h.status != 'C' and
		h.ev_header_id = d.ev_header_id
	") or die (mysql_error());

	while($r = mysql_fetch_assoc($sql)){
	if(!empty($r['date'])){
			$disburse_date = $r['date'];
		}	
	}
	
	return $disburse_date;
}

function getCheckDate($sub_apv_header_id){
	$sql = mysql_query("
		Select h.cv_header_id, h.sub_apv_header_id, h.check_date, sum(d.amount) as amount, h.cv_no
		from 
		cv_header as h,
		cv_detail as d
		where 
		sub_apv_header_id = '$sub_apv_header_id' and
		h.cv_header_id = d.cv_header_id and
		h.`status` != 'C'
 ") or die (mysql_error());
	while($r = mysql_fetch_assoc($sql)){
		if(!empty($r['check_date'])){
			$check_date = $r['check_date'];
			
		}
		$cv_no = $r['cv_no'];
	}
	$arr_check['check_date'] = $check_date;
	$arr_check['cv_no'] = $cv_no;
	
	return $arr_check;
}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>REPORT</title>
<script>
function printPage() { print(); } //Must be present for Iframe printing
window.onload = print();
</script>
<style type="text/css">

body
{
	size: legal portrait;
	font-family:Arial, Helvetica, sans-serif;
	font-size:11px;
}
.container{
	margin:0px auto;
	padding:0.1in;
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

table{
	width:100%;
	border-collapse:collapse;	
}

table thead tr th{
	border-top:1px solid #000;
	border-bottom:1px solid #000;
	font-weight:bold;
}
/*table  tr td:nth-child(n+2){
	text-align:right;	
}*/
table td{
	padding:3px;	
}
</style>
</head>
<body>
<div class="container">
	
     <div><!--Start of Form-->
     	
     	<div style="text-align:center; font-weight:bolder; margin-bottom:5px;">
        	<?=$title?> <br />
            <u style="text-transform:uppercase;"><?=TITLE?></u>
            <p style="text-align:center;">            	            
            	<?php
            	if( !empty($from_date) && !empty($to_date) ){
		           echo "From ".lib::ymd2mdy($from_date)." to ".lib::ymd2mdy($to_date);
		       	} else {
		       		echo "As of ".lib::ymd2mdy($from_date);
		       	}
            	
            	?>
            </p>
        </div>
        <div class="content" >
        	<table cellspacing="0">
            	<thead>
                    <tr>
                        <th width="50">SUBCON APV #</th>
		                <th width="50">SUBCON PO #</th>
		                <th>PO DATE</th>
		                <th>SUPPLIER</th>  
		                <th>TERMS</th>       
		                <th>DUE DATE</th>
		                <th>PROJECT</th>
		                <th>SCOPE OF WORK</th>
		                <th>CV #</th>
				        <th align="right">AMOUNT</th>
						<th>APV DATE</th>
		                <th>STATUS</th>
		                <th>PREPARED BY</th>
		                <th width="20">PAYMENT STATUS</th>
		                <th width="20">CHECK DATE</th>
		                <th width="20">NO. OF DAYS</th>
                    </tr>
               	</thead>
                <tbody>      
				<?php while($r = mysql_fetch_assoc($sql)){
					$sub_apv_header_id = $r['sub_apv_header_id'];
					$cv_amount = 0;
					$check_amount = 0;
					$disbursement_amount = 0;
					if($r['status'] == 'S'){
						$status = 'Saved';
					}
					$terms = $r['terms'];
					$po_date = $r['po_date'];
					$po_header_id = $r['po_header_id'];
					$due_date = date('Y-m-d', strtotime($po_date. '+'.$terms.'days'));
					$date = $r['date'];
					
					$check_amount = hasPayment($sub_apv_header_id);
					$accomplishment_amount = getAmount($sub_apv_header_id);
					$disbursement_amount = hasDisburse($po_header_id);
					
					if($check_amount != 0 && $disbursement_amount != 0){
						$cv_amount = $check_amount +  $disbursement_amount;
					}else if($check_amount != 0){
						$cv_amount = $check_amount;
					}else if($disbursement_amount != 0){
						$cv_amount  = $disbursement_amount;
					}
					
					if(number_format($cv_amount) >= number_format($accomplishment_amount)){
						$payment_status = "<td style='color: green;'>PAID</td>";
					}else if($cv_amount < $accomplishment_amount){
						$payment_status = "<td style='color: red;'>UNPAID</td>";
					}else{
						$payment_status = "<td style='color: red;'>UNPAID</td>";
					}
					
					$final_date = '';
					$check_date = '';
					$disburse_date = '';
					
					$arr_check = getCheckDate($sub_apv_header_id);
					$check_date = $arr_check['check_date'];
					
					if($arr_check['cv_no']){
						$cv_no = str_pad($arr_check['cv_no'],7,0,STR_PAD_LEFT);
					}else{
						$cv_no = '';
					}
					$disburse_date = disburseDate($po_header_id);			
					
					if($disburse_date != ''){
						$final_date = $disburse_date;
					}else if($check_date != ''){
						$final_date = $check_date;
					}else{
						$final_date = '';
					}
					
					
							$startTimeStamp = strtotime($date);
							$endTimeStamp = strtotime($final_date);
							
							$timeDiff = abs($endTimeStamp - $startTimeStamp);
							
							$numberDays = $timeDiff/86400;

							//convert to integer
							$numberDays = intval($numberDays);
				?>
					<tr>	
						<td><?=$r['sub_apv_header_id']?></td>
						<td><?=$r['po_header_id']?></td>
						<td><?=$r['po_date']?></td>
						<td><?=$r['supplier_name']?></td>
						<td><?=$r['terms']?></td>
						<td><?=$due_date?></td>
						<td><?=$r['project_name']?></td>
						<td><?=$r['work']?></td>
						<td><?=$cv_no?></td>						
						<td align="right"><?=number_format(getAmount($sub_apv_header_id),2)?></td>
						<td><?=$r['date']?></td>
						<td><?=$status?></td>
						<td><?=$r['user_lname'],' ',$r['user_mname'],' ',$r['user_fname'] ?></td>
						<?=$payment_status?>
						<td>
						<?php if($cv_amount != 0){ ?>
						<?=$final_date?>
						<?php }?>
						</td>
						<?php	
							if($cv_amount != 0){
								if($final_date != ''){ ?>
								<td style="color: red;">-<?=$numberDays?></td>
						<?php 		}else{ ?>
								<td><?=$numberDays?></td>
						<?php		}
							}
						?>
							
					</tr>
				<?php 
					$total_amount += getAmount($sub_apv_header_id);
				} ?>
					<tr>
						<td colspan="8" style="border-top: 1px solid black;border-bottom: 1px solid black padding-top: 10px; padding-bottom: 10px;">TOTAL</td>
						<td style="border-top: 1px solid black;border-bottom: 1px solid black padding-top: 10px; padding-bottom: 10px; font-weight: bold;"><?=number_format($total_amount,2)?></td>
						<td style="border-top: 1px solid black;border-bottom: 1px solid black padding-top: 10px; padding-bottom: 10px;"colspan="6"></td>
					</tr>
           		</tbody>
            </table>            
        </div><!--End of content-->
    </div><!--End of Form-->

</div>
</body>
</html>

