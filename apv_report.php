<?php
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
		return false;	
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
?>

<?php

	$b 				= $_REQUEST['b'];
	$user_id		= $_SESSION['userID'];	
	$keyword 		= $_REQUEST['keyword'];
	$checkList 		= $_REQUEST['checkList'];
	
	$search_supplier	= $_REQUEST['search_supplier'];
	
	$apv			= $_REQUEST['apv'];
	$percent		= $_REQUEST['percent'];
	$cv_date		= $_REQUEST['cv_date'];
	$check_date		= $_REQUEST['check_date'];
	$check_no		= $_REQUEST['check_no'];
	$supplier_id	= $_REQUEST['supplier_id'];
	$cash_gchart_id	= $_REQUEST['cash_gchart_id'];
	$ap_gchart_id	= $_REQUEST['ap_gchart_id'];


	if($b == "Generate CV"){
		mysql_query("
			insert into
				cv_header
			set
				percent = '$percent',
				cv_date = '$cv_date',
				check_date = '$check_date',
				check_no = '$check_no',
				supplier_id = '$supplier_id',
				cash_gchart_id = '$cash_gchart_id',
				ap_gchart_id = '$ap_gchart_id',
				user_id = '$user_id'
		") or die(mysql_error());	
		
		$cv_header_id = mysql_insert_id();
		
		if($apv){
			foreach($apv as $apv_header_id){
				$amount = $options->computeAPV($apv_header_id,$percent);
				mysql_query("
					insert into
						cv_detail
					set
						cv_header_id = '$cv_header_id',
						apv_header_id = '$apv_header_id',
						amount = '$amount'
				") or die(mysql_error());
			}
		}
		
		header("Location:admin.php?view=9d825239df14c9830e3b&cv_header_id=$cv_header_id");
	}
	
	if($b == "Generate AP Voucher"){
		if(!empty($checkList)){
			mysql_query("
				insert into
					apv_header
				set
					date = '".date("Y-m-d")."',
					supplier_id = '$ap_supplier'
			") or die(mysql_error());
			
			$apv_header_id = mysql_insert_id();			
			
			foreach($checkList as $ap_id){
				mysql_query("
					insert into
						apv_detail
					set
						apv_header_id = '$apv_header_id',
						ap_id = '$ap_id'
				");
			}
		}
	}	
?>
<script type="text/javascript">
function printIframe(id)
{
    var iframe = document.frames ? document.frames[id] : document.getElementById(id);
    var ifWin = iframe.contentWindow || iframe;
    iframe.focus();
    ifWin.printPage();
    return false;
}
</script>
<?php
$payment_status = $_REQUEST['payment_status'];
?>
<form name="_form" id="_form" action="" method="post">
<div class=form_layout>
	<div class="module_title"><img src='images/user_orange.png'><?=$transac->getMname($view);?></div>
	<div style="padding:3px; font-weight:bold; color:#FFF; background-color:#000; font-size:14px;">
			APV Report
	</div>
	<div style="background-color:#FFFFCC; border:1px solid #000; margin-top:0px; padding:10px;">		
		<div class="inline">
			From Date <br>
			<input type="text" class="textbox3 datepicker" name="from_date" id="from_date" value="<?=$_REQUEST['from_date']?>">
		</div>

		<div class="inline">
			To Date <br>
			<input type="text" class="textbox3 datepicker" name="to_date" id="to_date" value="<?=$_REQUEST['to_date']?>">
		</div>

		<div class="inline">
			Payment Status <br>
			<select name="payment_status" >
				<?php if($payment_status == "A"){ ?>
				<option selected value="A">All</option>
				<option value="P">Paid</option>
				<option value="U">Unpaid</option>
				<?php }else if($payment_status == "P"){ ?>
				<option selected value="P">Paid</option>
				<option value="A">All</option>
				<option value="U">Unpaid</option>
				<?php }else if($payment_status == "U"){ ?>
				<option selected value="U">Unpaid</option>
				<option value="A">All</option>
				<option value="P">Paid</option>
				<?php }else{ ?>
				<option selected disabled value="">Select Payment Status</option>
				<option value="A">All</option>
				<option value="P">Paid</option>
				<option value="U">Unpaid</option>
				<?php } ?>
			</select>
		</div>
     	<input type="submit" value="Generate Report"  />
		<input type="button" value="Print" onclick="printIframe('JOframe');" />
	</div>
    <?php if(!empty($msg)) echo '<div class="msg_div">'.$msg.'</div>'; ?>
    <div style="padding:3px; text-align:center;" id="content">
     <?php
     if(!empty($_REQUEST['from_date']) && !empty($_REQUEST['to_date']) && !empty($_REQUEST['payment_status']))
		{
	?>
   		<iframe id="JOframe" name="JOframe" frameborder="0" src="transactions/print_apv_report2.php?from_date=<?=$_REQUEST['from_date'];?>&to_date=<?=$_REQUEST['to_date']?>&payment_status=<?=$payment_status?>" width="100%" height="500">
        </iframe>
    </div>
    <?php }?>	
	
</div>
