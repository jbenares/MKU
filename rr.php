<?php
function poIsClosed($po_header_id){
	$result = mysql_query("
		select
			closed
		from
			po_header
		where
			po_header_id = '$po_header_id'
	") or die(mysql_error());
	$r = mysql_fetch_assoc($result);
	return $r['closed'];
}
function hasBudgetDeduction($rr_detail_id){
	$result = mysql_query("
		select * from budget_deduction where rr_detail_id = '$rr_detail_id'
	") or die(mysql_error());

	return (mysql_num_rows($result) > 0) ? 1 : 0;
}
?>
<style type="text/css">
.table-contents tr td:nth-child(odd) {
	text-align:right;
	font-weight:bold;
}
.eva_td{
	padding: 10px;
	width: 100px;
	border: 1px dotted grey;
}
.eva_wt{
	width: 50px;
	text-align: center;
	border: 1px dotted grey;
}
.eva_total{
	border: 1px dotted grey;
	padding: 10px;
	text-align: center;
}
#raw{
	text-align: center;
}

</style>
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
	$b					= $_REQUEST['b'];
	$rr_header_id		= $_REQUEST['rr_header_id'];
	$po_header_id		= $_REQUEST['po_header_id'];
	$po_header_id_pad 	= (!empty($po_header_id))?str_pad($po_header_id,7,0,STR_PAD_LEFT):"";
	$date				= $_REQUEST['date'];
	$paytype			= $_REQUEST['paytype'];
	$rr_in				= $_REQUEST['rr_in'];
	$project_id			= $_REQUEST['project_id'];
	$supplier_id		= $_REQUEST['supplier_id'];
	$discount_amount  	= $_REQUEST['discount_amount'];
	$advance_payment_amount  	= $_REQUEST['advance_payment_amount'];
	$rr_type			= $_REQUEST['rr_type'];
	$ppe_gchart_id		= $_REQUEST['ppe_gchart_id'];
	$ap_account_id		= $_REQUEST['ap_account_id'];

	$user_id			= $_SESSION['userID'];

	$stock_id 	= $_REQUEST['stock_id'];
	$stock_name	= $_REQUEST['stock_name'];

	$checkList	= $_REQUEST['checkList'];

	#for update
	$_cost			= $_REQUEST['_cost'];
	$_quantity		= $_REQUEST['_quantity'];
	$_discount		= $_REQUEST['_discount'];
	$_rr_detail_id	= $_REQUEST['_rr_detail_id'];
	
	//evaluation
	$eva_ps 	 = $_REQUEST['eva_ps'];
	$eva_d		 = $_REQUEST['eva_d'];
	$eva_cr		 = $_REQUEST['eva_cr'];
	$eva_sf		 = $_REQUEST['eva_sf'];
	$eva_p		 = $_REQUEST['eva_p'];
	
	if($_SESSION[userID] == '20160719-110150' || $_SESSION[userID] == '20200311-050946' || $_SESSION[userID] == '20170830-120801' || $_SESSION[userID] == '20200319-055723'){
		$old_id_rr = $_REQUEST['old_id_rr'];
	}
	
	
	function checkExistAPV($po_header_id){
		$c = 0;
		
		$q =  mysql_query("select *
							from
							rr_header as h,
							apv_header as d,
							po_header as p
							where
							p.po_header_id = h.po_header_id and
							p.po_header_id = d.po_header_id and
							d.`status` != 'C' and
							p.po_header_id = '$po_header_id'") or die (mysql_error());
		$c = mysql_num_rows($q);

		if($c > 0){
			return true;
		}else{
			return false;
		}
	}

	function getAdvancePaymentFromPO($rr_header_id){
		$options = new options();
		$po_header_id = $options->getAttribute('rr_header','rr_header_id',$rr_header_id,'po_header_id');
		$result = mysql_query("
			select sum(amount) as amount from ev_header as h, ev_detail as d where h.ev_header_id = d.ev_header_id and h.po_header_id = '$po_header_id' and h.status != 'C'
		") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		return $r['amount'];
	}

	function getAdvancePaymentsFromRR($rr_header_id){
		$options = new options();
		$po_header_id = $options->getAttribute('rr_header','rr_header_id',$rr_header_id,'po_header_id');
		$result = mysql_query("
			select sum(advance_payment_amount) as amount from rr_header where status != 'C' and rr_header_id != '$rr_header_id' and po_header_id ='$po_header_id'
		") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		return $r['amount'];
	}

	function getSumAmountOfRR($rr_header_id){
		$result = mysql_query("
			select sum(amount) as amount from rr_detail where rr_header_id = '$rr_header_id'
		") or die(mysql_error());
		$r = mysql_fetch_assoc($result);
		return $r['amount'];
	}


	if($b == "Unfinish"){
		mysql_query("
			update rr_header set status = 'S' where rr_header_id = '$rr_header_id'
		") or die(mysql_error());

		mysql_query("
			update gltran_header set status = 'C' where header_id = '$rr_header_id' and header = 'rr_header_id'
		") or die(mysql_error()); 
		
		$msg = "MRR Unfinished";
	}


	if($b=="Submit"){
		
		if($old_id_rr > 0){	
		$sql = mysql_query("select * from rr_header where rr_header_id = '$old_id_rr'") or die(mysql_error());	
			$count = mysql_num_rows($sql);
			if($count > 0){
				$msg = "Error, RR already exist";
			}else{
				$query="
				insert into
					rr_header
				set
					rr_header_id='$old_id_rr',
					date='$date',
					user_id='$user_id',
					status='S',
					po_header_id='$po_header_id',
					paytype = '$paytype',
					rr_in = '$rr_in',
					project_id = '$project_id',
					supplier_id = '$supplier_id',
					discount_amount = '$discount_amount',
					rr_type = '$rr_type',
					ppe_gchart_id = '$ppe_gchart_id',
					ap_account_id = '$ap_account_id',
					encoded_datetime = now()
				";

				mysql_query($query) or die(mysql_error());
		
				$options->insertAudit($old_id_rr,'rr_header_id','I');

				$msg="Transaction Saved";
				header("Location: admin.php?view=$view&rr_header_id=".$old_id_rr);
				
			}
		
		}else{
			
			$query="
			insert into
				rr_header
			set
				date='$date',
				user_id='$user_id',
				status='S',
				po_header_id='$po_header_id',
				paytype = '$paytype',
				rr_in = '$rr_in',
				project_id = '$project_id',
				supplier_id = '$supplier_id',
				discount_amount = '$discount_amount',
				rr_type = '$rr_type',
				ppe_gchart_id = '$ppe_gchart_id',
				ap_account_id = '$ap_account_id',
				encoded_datetime = now()
			";

			mysql_query($query) or die(mysql_error());

			$rr_header_id = mysql_insert_id();
			$options->insertAudit($rr_header_id,'rr_header_id','I');

			$msg="Transaction Saved";
			
		
		}

	}else if($b=="Update"){
		$query="
			update
				rr_header
			set
				date='$date',
				user_id='$user_id',
				status='S',
				po_header_id='$po_header_id',
				paytype = '$paytype',
				rr_in = '$rr_in',
				project_id = '$project_id',
				supplier_id = '$supplier_id',
				discount_amount = '$discount_amount',
				rr_type = '$rr_type',
				ppe_gchart_id = '$ppe_gchart_id',
				ap_account_id = '$ap_account_id'
			where
				rr_header_id='$rr_header_id'
		";

		mysql_query($query) or die(mysql_error());
		$options->insertAudit($rr_header_id,'rr_header_id','U');

		$msg = "Transaction Updated";

		if(!empty($_rr_detail_id)){
			$x = 0;
			foreach($_rr_detail_id as $id){
				$amount = $_quantity[$x] * ($_cost[$x] - $_discount[$x]);
				mysql_query("
					update
						rr_detail
					set
						quantity = '$_quantity[$x]',
						cost = '$_cost[$x]',
						discount = '$_discount[$x]',
						amount ='$amount'
					where
						rr_detail_id = '$id'
				") or die(mysql_error());
				$x++;
			}
		}

	}else if($b=="Cancel"){
		
		$check_exist = checkExistAPV($po_header_id);
		
		if($check_exist == true){
			$msg = "Invalid Request, Accounts Payable Voucher exist under this MRR Reference!";
			
		}else{
		
		$query="
			update
				rr_header
			set
				status='C'
			where
				rr_header_id='$rr_header_id'
		";
		mysql_query($query) or die(mysql_error());
		$options->insertAudit($rr_header_id,'rr_header_id','C');

		$msg = "Transaction Cancelled";
		}
			
	}else if($b=="Finish"){
		$query="
			update
				rr_header
			set
				status='F'
			where
				rr_header_id='$rr_header_id'
		";
		mysql_query($query) or die(mysql_error());
		$options->insertAudit($rr_header_id,'rr_header_id','F');

		//$options->postRR($rr_header_id);
		$msg = "Transaction Finished";

		/*
			UPDATE COST
		*/
		$result=mysql_query("
			select
				*
			from
				rr_detail
			where
				rr_header_id = '$rr_header_id'
		") or die(mysql_error());

		while($r=mysql_fetch_assoc($result)){
			$stock_id		= $r['stock_id'];
			$cost			= $r['cost'];

			mysql_query("
				update
					productmaster
				set
					cost = '$cost'
				where
					stock_id = '$stock_id'
			") or die(mysql_error());
		}

		/****************
		AUTO ADD TO ACCOUNTABLES IF ASSET
		****************/

		if($rr_type == "A"){
			$result = mysql_query("
				select
					*
				from
					rr_header as h, rr_detail as d
				where
					h.rr_header_id = d.rr_header_id
				and
					h.rr_header_id = '$rr_header_id'
			") or die(mysql_error());
			while($r = mysql_fetch_assoc($result)){
				mysql_query("
					insert into
						accountables
					set
						rr_detail_id = '$r[rr_detail_id]',
						date = '$r[date]',
						time = '".date("H:i:s")."',
						project_id = '$r[project_id]'
				") or die(mysql_error());
			}
		}
	}else if($b=="Update Details"){
		$rr_detail_id=$_REQUEST[rr_detail_id];

		$quantity		= $_REQUEST[quantity];
		$cost			= $_REQUEST[cost];
		$package_id		= $_REQUEST[package_id];

		$x=0;

		foreach($rr_detail_id as $id):
			$packageqty=$options->getPackageQty($package_id[$x]);

			if($package_id[$x]){
				$amount=$quantity[$x] * $cost[$x] * $packageqty;
			}else{
				$amount=$quantity[$x] * $cost[$x];
			}

			mysql_query("
				update
					rr_detail
				set
					quantity='$quantity[$x]',
					amount='$amount'
				where
					rr_detail_id='$id'
			") or die(mysql_error());
			$x++;
		endforeach;

		$msg = "Transaction Details Updated";
	}else if($b=="Delete Details"){
		//print_r($checkList);

		if(!empty($checkList)){
			foreach($checkList as $rr_detail_id){

				#INSERT AUDIT TRAIL
				$stock_id = $options->getAttribute("rr_detail","rr_detail_id",$rr_detail_id,"stock_id"); #CHANGE
				$_header_id = $options->getAttribute("rr_detail","rr_detail_id",$rr_detail_id,"rr_header_id"); #CHANGE
				$stock		= $options->getAttribute("productmaster","stock_id",$stock_id,"stock");
				$_desc = $options->getUserName($user_id)." deleted $stock in RR# $_header_id on ".date("m/d/Y h:i:s A");
				$options->insertAuditTrail($_desc,$user_id,$_header_id,"RR");

				mysql_query("
					delete from
						rr_detail
					where
						rr_detail_id = '$rr_detail_id'
				") or die(mysql_error());

				mysql_query("
					delete from
						budget_deduction
					where
						rr_detail_id = '$rr_detail_id'
				") or die(mysql_error());
			}
		}
	}else if($b=="New"){
		header("Location: admin.php?view=$view");
	}else if($b=="Submit Evaluation"){
		$count_e = 0;
		$curr_date = Date("Y-m-d");
		//check if exists
		$sql_e = mysql_query("Select * from rr_evaluation where rr_header_id = '$rr_header_id'") or die (mysql_error());
		$count_e = mysql_num_rows($sql_e);
		
		if($count_e == 0){
			
			mysql_query("
			Insert into rr_evaluation set 
			rr_header_id = '$rr_header_id',
			eva_ps = '$eva_ps',
			eva_d = '$eva_d',
			eva_cr = '$eva_cr',
			eva_sf = '$eva_sf',
			eva_p = '$eva_p',
			date = '$curr_date',
			user_id = '$user_id'
			") or die (mysql_error());
			
		}else if($count_e == 1){
			
			mysql_query("
			Update rr_evaluation set 
			eva_ps = '$eva_ps', 
			eva_d = '$eva_d', 
			eva_cr = '$eva_cr',
			eva_sf = '$eva_sf', 
			eva_p = '$eva_p' 
			where rr_header_id = '$rr_header_id'"
			) or die (mysql_error());
		}
		

	}

	/*if( $rr_header_id ){
		//$advance_payment_from_po = getAdvancePaymentFromPO($rr_header_id);
		//$total_advance_payment_from_rr = getAdvancePaymentsFromRR($rr_header_id);


		if( $advance_payment_from_po == 0 ){
			$_advance_payment = 0;
			mysql_query("update rr_header set advance_payment_amount = '$_advance_payment' where rr_header_id = '$rr_header_id'") or die(mysql_error());
		} else {
				
		//11-06-15 FOR INVESTIGATION - negative values
			$remaining = $advance_payment_from_po - $total_advance_payment_from_rr;
			#echo "$advance_payment_from_po - $total_advance_payment_from_rr;";
			
			//net - discount
			$total_rr_amount =  getSumAmountOfRR($rr_header_id) - $discount_amount;
			#echo "<br> $total_rr_amount;";
			//if($remaining >= $total_rr_amount ){
			//	$_advance_payment = $total_rr_amount;
			//}else{
				$_advance_payment = $remaining;
			//}

			mysql_query("update rr_header set advance_payment_amount = '$_advance_payment' where rr_header_id = '$rr_header_id'") or die(mysql_error());
		}

		$sql_eva = mysql_query("Select * from rr_evaluation where rr_header_id = '$rr_header_id'") or die (mysql_error());
		$r_eva = mysql_fetch_assoc($sql_eva);
		
		$eva_ps = $r_eva['eva_ps'];
		$eva_d = $r_eva['eva_d'];
		$eva_cr = $r_eva['eva_cr'];
		$eva_sf = $r_eva['eva_sf'];
		$eva_p = $r_eva['eva_p'];
		
		$wt_eva_ps = $eva_ps * (25/100);
		$wt_eva_d = $eva_d * (25/100);
		$wt_eva_cr = $eva_cr * (20/100);
		$wt_eva_sf = $eva_sf * (20/100);
		$wt_eva_p = $eva_p * (10/100);
		
		$total_eva = $wt_eva_ps + $wt_eva_d + $wt_eva_cr + $wt_eva_sf + $wt_eva_p;
	}*/



	$query="
		select
			*
		from
			rr_header as h, projects as p
		where
			h.project_id = p.project_id
		and
			rr_header_id ='$rr_header_id'
	";

	$result=mysql_query($query);
	$r = $aVal = mysql_fetch_assoc($result);


	$rr_header_id		= $r['rr_header_id'];
	$rr_header_id_pad	= (!empty($rr_header_id))?str_pad($rr_header_id,7,0,STR_PAD_LEFT):"";
	$po_header_id		= $r['po_header_id'];
	$po_header_id_pad 	= (!empty($po_header_id))?str_pad($po_header_id,7,0,STR_PAD_LEFT):"";
	$date				= $r['date'];
	$paytype			= $r['paytype'];
	$project_id 	 	= $r['project_id'];
	$rr_in				= $r['rr_in'];
	$discount_amount 	= $r['discount_amount'];
	$rr_type			= $r['rr_type'];

	$project_name		= $options->attr_Project($project_id,'project_name');
	$project_code		= $options->attr_Project($project_id,'project_code');
	$project_display	= ($project_id)?"$project_name - $project_code":"";

	$supplier_id		= $r['supplier_id'];
	$supplier			= $options->attr_Supplier($supplier_id,'account');

	$user_id			= $r['user_id'];
	$status				= $r['status'];

	$advance_payment_amount = $r['advance_payment_amount'];
	
	/*if($advance_payment_amount > 0){
		$advances = $advance_payment_amount;
	}else if($total_advance_payment_from_rr > 0){
		$advances = $total_advance_payment_from_rr;
	}else if($advance_payment_from_po > 0){
		$advances = $advance_payment_from_po;
	}*/
?>
<form name="header_form" id="header_form" action="" method="post">
<div class=form_layout>
	<?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
	<div class="module_title"><img src='images/user_orange.png'>STOCKS RECEIVING</div>

    <div style="width:50%; float:left;">
        <div class="module_actions">
            <input type="hidden" name="rr_header_id" id="rr_header_id" value="<?=$rr_header_id?>" />
            <input type="hidden" name="view" value="<?=$view?>" />
            <div id="messageError">
                <ul>
                </ul>
            </div>

            <table style="display:inline-table;" class="table-contents" >
            	<tr>
                	<td style="text-align: left;">DATE : <input type="text" class="datepicker required textbox3" title="Please enter date"  name="date" readonly='readonly'  value="<?=$date?>"></td>
                </tr>
                <tr>
                	<td style="text-align: left;">PO # : <input type="text" class="textbox3" id="po_name" value="<?=$po_header_id_pad?>"/>
                        <input type="hidden" name="po_header_id" id="po_header_id" value="<?=$po_header_id?>"  />
                        <img src="images/folder.png" id="folder" style="cursor:pointer;" /></td>
                </tr>
                <tr>
                	<td style="text-align: left;">PROJECT : <input type="text" class="textbox" id="project_display" value="<?=$project_display?>" readonly="readonly" />
						<input type="hidden" name="project_id" id="project_id" value="<?=$project_id?>" class="required" title="Please Select a Project" /></td>                 
                </tr>
                <tr>
                	<td style="text-align: left;">SUPPLIER : 
						<input type="text" class="textbox" id="supplier_name" value="<?=$supplier?>" readonly="readonly" />
		                <input type="hidden" name="supplier_id" id="supplier_id" value="<?=$supplier_id?>" />
					</td>
                </tr>
                <tr>
                	<td style="text-align: left;">MRR TYPE: <select name='rr_type'>
                        	<option value='M' <?=($rr_type == "M") ? "selected='selected'" : '' ?> >MERCHANDISE INVENTORY</option>
                        	<option value='A' <?=($rr_type == "A") ? "selected='selected'" : '' ?>>PROPERTY AND EQUIPMENT</option>
                        </select>
					</td>
                </tr>
				<?php if($_SESSION[userID] == '20160719-110150' || $_SESSION[userID] == '20200311-050946' || $_SESSION[userID] == '20170830-120801' || $_SESSION[userID] == '20200319-055723'){ ?>
				<tr>
					<td style="text-align: left;">RR HEADER ID: 
						<input type="text" class="textbox" name="old_id_rr" />
						
					</td>
				</tr>
				<?php } ?>
       		</table>
            <table style="display:inline-table;" class="table-contents" >
                <!--<tr>
					<td style="text-align: left;"><span style="color: red;">ADVANCE PAYMENT <br /> AMOUNT : </span><input type="text" class="textbox" name="advance_payment_amount" value="<?=$advance_payment_amount?>"/></td>
                </tr>-->
                <tr>
                	<td style="text-align: left;">RECEIVE IN : <?=$options->option_rr_in($rr_in)?></td>
                </tr>

                <tr>
                	<td style="text-align: left;">PAY TYPE : <?=$options->getPayTypeOptions($paytype)?></td>
                </tr>

                <!--<tr>
                	<td style="text-align: left;">DISCOUNT AMOUNT : <input type="text" class="textbox" name="discount_amount" value="<?=$discount_amount?>" /></td>
                </tr>
				 <tr>
                    <td style="text-align: left;">
                    	<span style="color: red;">A/P ACCOUNT : </span> <?=$options->getTableAssoc($r['ap_account_id'],'ap_account_id','Select A/P Account',"select * from gchart order by gchart asc",'gchart_id','gchart');?>
                   	</td>
                </tr>
                <tr>
                    <td style="text-align: left;">
                    	<span style="color: red;">PROPERTY AND EQUIPMENT ACCOUNT: <em>(FOR ASSET ONLY)</em> : </span> <br /><?=$options->getTableAssoc($r['ppe_gchart_id'],'ppe_gchart_id','Select Property and Equipment Account',"select * from gchart where parent_gchart_id = '56' or gchart_id = '56' order by gchart asc",'gchart_id','gchart');?>
                   	</td>
                </tr>-->
            </table>
		<div id="_dialog">
		<!--<form name="_form2" id="_form2" action="" method="post"> -->
			<div id="_dialog_content">
				<table width="800">
				<thead>
				<tr>
					<td style="padding: 10px; text-align: center; width: 350px; background-color: lightblue;" colspan="2">CRITERIA</td>
					<td style="text-align: center; width: 50;background-color: lightblue;">WEIGHT</td>
					<td style="text-align: center;background-color: lightblue;">RAW SCORE (1-5)</td>
					<td style="text-align: center;background-color: lightblue;">WEIGHTED POINTS</td>
				</tr>
				</thead>
				<tr>
					<td class="eva_td">Product/Services</td>
					<td class="eva_td">No defective product or services performed that is not in accordance to agreement</td>
					<td class="eva_wt">25%</td>
					<td class="eva_wt"><input type="text" class="textbox3" id="raw" name="eva_ps" value="<?=$eva_ps?>" style="width: 50px;" min="0" max="5" /></td>
					<td class="eva_wt"><?=number_format($wt_eva_ps,2)?></td>
				</tr>
				<tr>
					<td class="eva_td">Delivery</td>
					<td class="eva_td">Delivery/Pick-Up or Service made on date promised</td>
					<td class="eva_wt">25%</td>
					<td class="eva_wt"><input type="text" class="textbox3" id="raw" name="eva_d" value="<?=$eva_d?>" style="width: 50px;" min="0" max="5" /></td>
					<td class="eva_wt"><?=number_format($wt_eva_d,2)?></td>
				</tr>
				<tr>
					<td class="eva_td">Customer Relations</td>
					<td class="eva_td">Rejects/Issues are replaced/resolved</td>
					<td class="eva_wt">20%</td>
					<td class="eva_wt"><input type="text" class="textbox3" id="raw" name="eva_cr" value="<?=$eva_cr?>" style="width: 50px;" min="0" max="5" /></td>
					<td class="eva_wt"><?=number_format($wt_eva_cr,2)?></td>
				</tr>
				<tr>
					<td class="eva_td">Support Functions</td>
					<td class="eva_td">Invoices Correctly</td>
					<td class="eva_wt">20%</td>
					<td class="eva_wt"><input type="text" class="textbox3" id="raw" name="eva_sf" value="<?=$eva_sf?>" style="width: 50px;" min="0" max="5"/></td>
					<td class="eva_wt"><?=number_format($wt_eva_sf,2)?></td>
				</tr>
				<tr>
					<td class="eva_td">Price</td>
					<td class="eva_td">Consistent</td>
					<td class="eva_wt">10%</td>
					<td class="eva_wt"><input type="text" class="textbox3" id="raw" name="eva_p" value="<?=$eva_p?>" style="width: 50px;" min="0" max="5"/></td>
					<td class="eva_wt"><?=number_format($wt_eva_p,2)?></td>
				</tr>
				<tr>
					<td class="eva_total" colspan="2" style="text-align: left;">TOTAL</td>
					<td class="eva_total"></td>
					<td class="eva_total"></td>
					<td class="eva_wt" style="font-weight: bold;"><?=number_format($total_eva,2)?></td>
				</tr>
				</table>
				<br />
				<input type="submit" name="b" value="Submit Evaluation"  />
			</div>
		<!--</form> -->
		</div>

		
		<script type="text/javascript">
			j(function(){
				var dlg = j("#_dialog").dialog({autoOpen: false , modal:true , show: 'slide' , hide : 'slide' , width : 'auto', resizable : false, height : 'auto', maxHeight : 600, title : "Supplier Evaluation - MRR"});
				dlg.parent().appendTo(jQuery("form:first"));
			});
			

		</script>
		
     	</div>
        <div style="background-color:#CCC; padding:5PX;">
            <?php if(!empty($status)){ ?>
            <div class="inline" style="vertical-align:top;">
                MRR # : <br />
                <input type="text" class="textbox3" name="status" id="status" value="<?=$rr_header_id_pad?>" readonly="readonly"/>
            </div>

            <div class='inline' style="vertical-align:top;">
                <div>Status : </div>
                <div>
                    <input type="text" class="textbox3" name="status" id="status" value="<?=$options->getTransactionStatusName($status)?>" readonly="readonly"/>
                </div>
            </div>

            <div class='inline'>
                <div>Encoded by : </div>
                <div>
                    <input type='text' class="textbox" value="<?=$options->getUserName($user_id);?>" readonly="readonly" />
                    <?php
                    if( !empty($aVal['encoded_datetime']) ){
                    	echo "<br>".$aVal['encoded_datetime'];
                    }
                    ?>
                </div>
            </div>
            <?php
            }
            ?>
        </div>
        <div class="module_actions">
            <input type="submit" name="b" value="New" />
            <?php
            if($status=="S"){
            ?>
            <input type="submit" name="b" id="b" value="Update" />
            <input type="submit" name="b" id="b" value="Finish" />

            <?php
            }else if($status!="F" && $status!="C"){
            ?>
            <input type="submit" name="b" id="b" value="Submit" />
            <?php } ?>
			
			<!--<?php if($rr_header_id){ ?>
				<input type="button" value="Evaluation" onclick="j('#_dialog').dialog('open');" />  
			<?php } ?>-->
			
            <?php if($b!="Print Preview" && $status == 'F'){ ?>
                <input type="submit" name="b" id="b" value="Print Preview" />
            <?php } ?>

            <?php if($b=="Print Preview" && !empty($status)){ ?>
                <input type="button" value="Print" onclick="printIframe('JOframe');" />
            <?php } ?>

           <!-- <?php if($b!="Print Preview Aggr" && !empty($status)){ ?>
                <input type="submit" name="b" id="b" value="Print Preview Aggr" />
            <?php } ?>-->

            <?php if($b=="Print Preview Aggr" && $status == "F"){ ?>
                <input type="button" value="Print Aggr" onclick="printIframe('JOframe');" />
            <?php } ?>

            <?php if($status == "F"){ 
					//if($registered_access == '' or $registered_access == ''){
			?>
            <input type="submit" name="b" value="Unfinish" />
            <?php 
					//}
			} 
			?>

            <?php if($status!="C" && !empty($status)){ ?>
            <input type="submit" name="b" id="b" value="Cancel" />
            <?php
            }
            ?>
        </div>
        <?php
        if($status=="S"){
        ?>
        <div class="module_title"><img src='images/database_table.png'>SEARCH RESULTS : </div>
        <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
            <tr bgcolor="#C0C0C0">
                <th width="20"><b>#</b></th>
                <th width="30"></th>
                <th>Item</th>
                <th width="60">PO Quantity</th>
                <th width="60">RR Quantity</th>
                <th width="60">Balance</th>
                <th width="60">Unit</th>
                <!--<th width="60">U.Price</th> -->
            </tr>
            <?php
            $result=mysql_query("
                select
                    d.stock_id,
                    stock,
                    sum(quantity) as quantity,
                    unit,
					details
                from
                    po_detail as d, productmaster as p,po_header as h
                where
                    h.po_header_id = d.po_header_id
                and
                    project_id = '$project_id'
                and
                    d.stock_id = p.stock_id
                and
                    d.po_header_id = '$po_header_id'
				and
					h.approval_status = 'A'
                group by
                    stock_id
            ") or die(mysql_error());
            $i=1;
            while($r=mysql_fetch_assoc($result)){
                $stock_id 		= $r['stock_id'];
                $stock			= $r['stock'];
                $po_quantity	= $r['quantity'];
                $unit			= $r['unit'];
                $cost			= $r['cost'];

                $total_stocks_received = $options->rr_totalStocksReceived($po_header_id,$stock_id,$cost);

                $balance = $po_quantity - $total_stocks_received;
            ?>
            <tr>
                <td><?=$i++?></td>
                <td><input type="button" value="Receive" onclick="xajax_receive_stock_id_form('<?=$stock_id?>','<?=$cost?>',xajax.getFormValues('header_form'));" <?php if(poIsClosed($po_header_id)) echo "disabled = 'disabled'";  ?> /></td>
                <td><?=$stock?> <?=($r['details']) ? "($r[details])" : ""?></td>
                <td class="align-right"><?=number_format($po_quantity,2,'.',',')?></td>
                <td class="align-right"><?=number_format($total_stocks_received,2,'.',',')?></td>
                <td class="align-right"><?=number_format($balance,2,'.',',')?></td>
                <td><?=$unit?></td>
                <!--<td class="align-right"><?=number_format($cost,2,'.',',')?></td> -->
            </tr>
            <?php
            }
            ?>
        </table>
        <?php
        }
        ?>
    </div>
    <div style="width:50%; float:right;">
        <div class="module_title"><img src='images/book_open.png'>RECEIVING DETAILS:  </div>
        <div class="module_actions">
            <?php
            if($status=="S"){
            ?>
            <input type="submit" name="b" value="Delete Details" onclick="return approve_confirm();"/>
            <?php
            }
            ?>
        </div>
        <table cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table" id="search_table">
            <tr bgcolor="#C0C0C0">
                <th width="20"><b>#</b></th>
                <th width="20"></th>
                <th width="20"></th>
                <?php if($rr_type == "A") { ?>
                <th width="20"></th>
                <?php } ?>
                <th>Item Description</th>
                <th width="60">Inv. / DR #</th>
                <th width="60">Quantity</th>
                <th width="60">Unit</th>
                <th width="100">U.Price</th>
                <th width="100">Discount</th>
                <th width="100">Amount</th>
                <th width="100">Installed by</th>
                <th width="100">Date Installed</th>
                <th width="100">Withdrawal</th>
            </tr>
            <?php
            $result=mysql_query("
                select
                    d.stock_id,
                    stock,
                    quantity,
                    unit,
                    d.cost,
                    rr_detail_id,
                    invoice,
                    amount,
					account_id,
					details,
					d.discount,
					d.installed_by,
					d.date_installed,
					d.withdrawal
                from
                    rr_detail as d, productmaster as p
                where
                    d.stock_id = p.stock_id
                and
                    rr_header_id = '$rr_header_id'
            ") or die(mysql_error());

            $i=1;
            $netamount = 0;
            while($r=mysql_fetch_assoc($result)){
                $stock_id 		= $r['stock_id'];
                $stock			= $r['stock'];
                $quantity		= $r['quantity'];
                $unit			= $r['unit'];
                $cost			= $r['cost'];
                $rr_detail_id	= $r['rr_detail_id'];
                $invoice		= $r['invoice'];
                $amount			= $r['amount'];
                $discount		= $r['discount'];
                $installed_by	= $r['installed_by'];
                $date_installed	= $r['date_installed'];
                 $withdrawal			= $r['withdrawal'];

                $netamount += $amount;

            ?>
            <tr>
                <td><?=$i++?></td>
                <td><input type="checkbox" name="checkList[]" value="<?=$rr_detail_id?>" onclick="document.header_form.checkAll.checked=false"></td>
                <td>
                	<!--<img src="images/<?php//=(hasBudgetDeduction($rr_detail_id)) ? "flag_green.gif" : "flag_red.gif" ?>" style="cursor:pointer;" onclick="xajax_displayBudgetDeductionForm('<?=$rr_detail_id?>');" />-->
               	</td>
                <?php if($rr_type == "A") { ?>
                <td>
                	<img src="images/page_tick.gif" style="cursor:pointer;" onclick="xajax_asset_details_form('<?=$rr_detail_id?>');" />
               	</td>
                <?php } ?>
                <td><?=$stock?><?=(!empty($r['details'])) ? "<br>(".$r['details'].")" : ""?></td>
                <td><?=$invoice?></td>
                 <td class="align-right"><?=number_format($quantity,2,'.',',')?></td>
                <td><?=$unit?></td>
                <!--<td class="align-right"><?=number_format($cost,2,'.',',')?></td> -->
                <td class="align-right"><input type="textbox" class="textbox3" style="text-align:right;" name="_cost[]" value="<?=$cost?>" autocomplete="off" /></td>
                <td class="align-right"><?=number_format($discount*$quantity,2,'.',',')?></td>
                <td class="align-right"><?=number_format($amount,2,'.',',')?></td>
                <td class="align-right"><?=$installed_by?></td>
                <td class="align-right"><?=$date_installed?></td>
                <td class="align-right"><?=$withdrawal?></td>
                <input type="hidden" name="_rr_detail_id[]" value="<?=$rr_detail_id?>" />
                <input type="hidden" name="_quantity[]" value="<?=$quantity?>" />
                <input type="hidden" name="_discount[]" value="<?=$discount?>" />
            </tr>
            <?php
            }
            ?>
        </table>
        <?php
        if($status=="S"){
            mysql_query("
                update
                    rr_header
                set
                    grossamount = '$netamount',
                    netamount 	= '$netamount'
                where
                    rr_header_id = '$rr_header_id'
            ") or die(mysql_error());

        }
        ?>
   	</div>
    <div style="clear:both;">
		<?php
        if($b == "Print Preview" && $rr_header_id){

            echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_rr.php?id=$rr_header_id' width='100%' height='500'>
                    </iframe>";
        }else if( $b == "Print Preview Aggr"  && $rr_header_id){
			echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='print_report_rr_aggr.php?id=$rr_header_id' width='100%' height='500'>
                    </iframe>";
		}
        ?>


   	</div>


</div>
</form>
<script type="text/javascript">
j(function(){

	j("#cost,#quantity").keyup(function(){
		var price = document.getElementById("cost").value;
		var quantity = document.getElementById("quantity").value;

		var amount = price * quantity;
		var amountFormatted = Number(amount);
		document.getElementById("amount").value=amountFormatted.toFixed(2);
	});

	j("#folder").dblclick(function(){
		xajax_show_po();
	});
	
});

</script>
