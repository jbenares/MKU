<?php
include_once("../conf/ucs.conf.php");
include_once("../library/lib.php");
include_once("../my_Classes/options.class.php");


define('TITLE', "APV PAYMENT HISTORY");

$from_date      = $_REQUEST['from_date'];
$to_date        = $_REQUEST['to_date'];
$payment_status = $_REQUEST['payment_status'];
$options = new options();


function hasPayment($apv_header_id){
	$options = new options();
	#apv
	#check sum amount of apv
	$result = mysql_query("
		select 
			sum(amount) as amount
		from
			apv_detail 
		where
			apv_header_id = '$apv_header_id'
	") or die(mysql_error());
	$r = mysql_fetch_assoc($result);
	$sum_amount = $r['amount'];
	
	#discount amount
	$discount_amount = $options->getAttribute('apv_header','apv_header_id',$apv_header_id,'discount_amount');
	
	if($discount_amount >= $sum_amount){
		return true;	
	}
		
	$result = mysql_query("
		select
			*
		from
			cv_header as h, cv_detail as d
		where
			h.cv_header_id = d.cv_header_id
		and
			h.status != 'C'
		and
			d.apv_header_id = '$apv_header_id'
	") or die(mysql_error());
	
	if( mysql_num_rows($result) > 0 ){
		return true;	
	} else { 
		return false;
	}
}

function getCVRef($apv_header_id,$po_header_id){
	$sql = mysql_query("select
						* 
						from
						cv_header as h,
						cv_detail as d
						where
						h.cv_header_id = d.cv_header_id and
						d.apv_header_id = '$apv_header_id' and
						h.`status` != 'C'") or die (mysql_error());
						
	$count = mysql_num_rows($sql);
	if($count > 0){
		$r = mysql_fetch_assoc($sql);
		$ref = 'C.V#: '.str_pad($r['cv_no'],7,0,STR_PAD_LEFT);
	}else{
		$sqlp = mysql_query("select
								*
								from
								ev_header as e,
								ev_detail as d
								where
								e.ev_header_id = d.ev_header_id and
								e.`status` != 'C' and
								e.po_header_id = '$po_header_id'") or die (mysql_error());
	$count2 = mysql_num_rows($sqlp);
		if($count2 > 0){
		$rp = mysql_fetch_assoc($sqlp);
		
		$ref = 'D.V#: '.str_pad($rp['ev_header_id'],7,0,STR_PAD_LEFT);
		
		}else{
			
		$ref = '';	
		}
	}
	
	
	return $ref;
}

function getCheckDate($apv_header_id){
	//$options = new options();
		
	//check apv to cv	
	$result = mysql_query("
		select
			*
		from
			cv_header as h, cv_detail as d
		where
			h.cv_header_id = d.cv_header_id
		and
			h.status != 'C'
		and
			d.apv_header_id = '$apv_header_id'
	") or die(mysql_error());
	
	$r = mysql_fetch_assoc($result);
	$count1 = mysql_num_rows($result);
	$check_date = $r['check_date'];
	
	
	//check disbursement
	$result2 = mysql_query("
	Select 
	*, ev.date as ev_date 
	from
	apv_header as apv,
	po_header as po,
	ev_header as ev
	where 
	apv.apv_header_id = '$apv_header_id' and
	apv.po_header_id = po.po_header_id and
	ev.po_header_id = po.po_header_id and
	po.`status` != 'C' and
	ev.`status` != 'C' and
	apv.`status` != 'C'
	") or die (mysql_error());
	
	$count2 = mysql_num_rows($result2);
	if($count2 > 0){
		$r2 = mysql_fetch_assoc($result2);
		$ev_header = $r2['ev_header_id'];
		$ev_date = $r2['ev_date'];
		
		$sql = mysql_query("Select  sum(amount) as total_amount from ev_detail where ev_header_id = '$ev_header'") or die (mysql_error());
		$r3 = mysql_fetch_assoc($sql);
		$ev_total_amount = $r3['total_amount'];
	}
	
	if($count1 > 0){
		return $check_date;
	}else if($count2 > 0){
		return $ev_date;
	}
	
}

function getReport($from_date,$to_date){

	$sql = "
        select
			apv_header_id,
			po_header_id,
			date,
			po_date,
			project_id,
			work_category_id,
			sub_work_category_id,
			supplier_id,
			terms,
			status,
			user_id
			/*date_format(getDueDateFromAPV(apv_header_id),'%m/%d/%Y') as due_date */
        from
            apv_header as h, supplier as s
        where
            h.supplier_id = s.account_id
        and date between '$from_date' and '$to_date'        
        order by apv_header_id desc
    ";

	$a = array();
	while( $r = mysql_fetch_assoc( $result ) ){
		$a[] = $r;
	}

	return $a;
	
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
        	<table cellspacing="0" border="0">
            	<thead>
                    <tr>
                        <th>APV #</th>
		                <th>PO #</th>
		                <th>DATE</th>
		                <th>SUPPLIER</th>  
		                <th>TERMS</th>       
		                <!--<th>DUE DATE</th>-->
		                <th>PROJECT</th>
		                <th>SCOPE OF WORK</th>
		                <th width="100">Reference</th>
				        <th align="right">AMOUNT</th>
		                <th>STATUS</th>
		                <th>PREPARED BY</th>
		                <th width="20">PAYMENT STATUS</th>
		                <th width="20">CHECK DATE</th>
		                <!--<th width="20">NO. OF DAYS</th>-->
                    </tr>
               	</thead>
                <tbody>                  
                    <?php                               
                    
                    $sql = "
				        select
							apv_header_id,
							po_header_id,
							date,
							po_date,
							h.project_id,
							work_category_id,
							sub_work_category_id,
							supplier_id,
							terms,
							status,
							user_id
							/*date_format(getDueDateFromAPV(apv_header_id),'%m/%d/%Y') as due_date*/
				        from
				            apv_header as h, supplier as s, projects as p
				        where
				            h.supplier_id = s.account_id and
							 h.project_id = p.project_id	
				        and date between '$from_date' and '$to_date'    
						and
							h.status !='C'
				        order by  s.account asc, p.project_name asc
				    ";
					
				    $aReport = lib::getArrayDetails($sql);
					//echo count($aReport);
					#echo mysql_num_rows(mysql_query($sql));
					if(count($aReport))
						$var = '';
						$subAmount=0;
						foreach( $aReport as $r ){
							
							$apv_header_id			= $r['apv_header_id'];
	                        $apv_header_id_pad		= str_pad($apv_header_id,7,0,STR_PAD_LEFT);
	                        $po_header_id			= $r['po_header_id'];
	                        $po_header_id_pad		= str_pad($po_header_id,7,0,STR_PAD_LEFT);
	                        $date 					= $r['date'];
	                        $po_date				= $r['po_date'];
	                        $project_id 			= $r['project_id'];
							$due_date				= $r['due_date'];
							
							$hasPayment = hasPayment($apv_header_id);
	                        if( $_REQUEST['payment_status'] == "P" ){
	                        	if( !$hasPayment ) continue;													
	                        } else if(  $_REQUEST['payment_status'] == "U"  ) {
	                        	if( $hasPayment ) continue;
							}
							
							$cv_no = getCVRef($apv_header_id,$po_header_id);
							
							$check_date = getCheckDate($apv_header_id);
							
							$startTimeStamp = strtotime($due_date);
							$endTimeStamp = strtotime($check_date);
							
							$timeDiff = abs($endTimeStamp - $startTimeStamp);
							
							$numberDays = $timeDiff/86400;

							//convert to integer
							$numberDays = intval($numberDays);
							
							$getAmount = mysql_query("select sum(amount) as total_sum from apv_detail where apv_header_id='$apv_header_id'");         
							$rAmount = mysql_fetch_array($getAmount);
							
							$supplier_id = $r['supplier_id'];
							//echo $var." - ".$supplier_id;
							
							if(empty($var)){
								$var = $supplier_id;
							}
							
							#echo "<br/>";
							
						  if(($var != $supplier_id)){
								$var = $supplier_id;
								?>
								<tr>
									<td></td>
									<td></td>
									<td></td>		
									<td></td>	
									<!--<td></td>-->
									<td></td>
									<td></td>
									<td></td>
									<td><u></u></td>
									<td style="text-align:right;border-top:1px solid #000;"><b><?=number_format($subAmount,2); ?></b></td> 
									<td></td>	
									<td></td>
									<td></td>
									<td></td>
									<!--<td></td>-->
								</tr>
								<?php
								$subAmount=0;
							}
							$subAmount += $rAmount[total_sum];
							
							
	                        $project				= lib::getAttribute('projects','project_id',$project_id,'project_name');
	                        $work_category_id 		= $r['work_category_id'];
	                        $work_category			= lib::getAttribute('work_category','work_category_id',$work_category_id,'work');
	                        $sub_work_category_id 	= $r['sub_work_category_id'];
	                        $sub_work_category		= lib::getAttribute('work_category','work_category_id',$sub_work_category_id,'work');
	                        $supplier				= lib::getAttribute('supplier','account_id',$supplier_id,'account');
	                        $terms 					= $r['terms'];
	                        $status					= $r['status'];
	                        $user_id				= $r['user_id'];
	                        $due_date				= $r['due_date'];
							
							 ?>
									<tr>
										<td><?=$apv_header_id_pad?></td>
										<td><?=$po_header_id_pad?></td>
										<td><?=date("m/d/Y", strtotime($date))?></td>		
										<td><?=$supplier?></td>	
										<td><?=$terms?></td>
										<!--<td><?=$due_date?></td>-->
										<td><?=$project?></td>
										<td><?=$work_category?> <?=$sub_work_category?></td>
										<td><?=$cv_no?></td>
										<td align="right"><?=number_format($rAmount[total_sum],2); ?></td> 
										<td><?=$options->getTransactionStatusName($status)?></td>	
										<td><?=$options->getUserName($user_id)?></td>
										<td style="text-align:center;" ><?=($hasPayment) ? "PAID" : "UNPAID" ?></td>
										<td><?=$check_date?></td>
										<?php/* if($endTimeStamp < $startTimeStamp){ ?>
										<td style="color: red;">
											<?php if($check_date){ ?>
											-<?=$numberDays?>
											<?php } ?>
										</td>
										<?php }else{ ?>
										<td>
											<?php if($check_date){ ?>
											<?=$numberDays?>
											<?php } ?>
										</td>										
										<?php } */?>
									</tr>
									<?php
		                  
						$total_all += $rAmount[total_sum];
		             }        
                    ?>
								
					<?php echo '<tr>
								<td colspan=14 style="border-top: 1px solid black;">&nbsp;</td>
							</tr>
							<tr>
								<td colspan=8 align=right ><b>Total : </b></td><td><b>'.number_format($total_all,2).'</b></td>
							</tr>'; ?>
           		</tbody>
            </table>            
        </div><!--End of content-->
    </div><!--End of Form-->

</div>
</body>
</html>

