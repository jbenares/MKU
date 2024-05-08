<script type="text/javascript" src="scripts/jquery.keyz.js"></script>
<?php include('library/acctg.php'); ?>


<?php

function getOutbal($application_id){
	$result = mysql_query("
		select 
			ifnull(min(outbal),0) as outbal
		from
			dprc_payment as p, dprc_ledger as l
		where
			p.dprc_payment_id = l.dprc_payment_id
		and p.application_id = '$application_id'
	") or die(mysql_error());
	$r = mysql_fetch_assoc($result);
	return $r['outbal'];
}

function wtaxPaid($application_id){
	$result = mysql_query("	
		select 	
			*
		from
			dprc_app_wtax
		where
			application_id = '$application_id'
	") or die(mysql_error());
	
	return (mysql_num_rows($result) > 0) ? true : false;
}

function getAppPosted(){
	$result = mysql_query("
		select DISTINCT(h_id) as h_id from gltran_detail where h = 'application_id'
	") or die(mysql_error());
	$a  = array();
	while( $r = mysql_fetch_assoc($result) ){
		$a[] = $r['h_id'];
	}
	return $a;
}


#echo date("m/d/Y",strtotime("-38 days",strtotime("2013-01-07")));
#echo "<br>";
#echo date("m/d/Y",strtotime("-3 days",strtotime("2012-12-03")));
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
<style type="text/css">
input[type="text"]{
	font-size: 11px;
	width: 200px;
	border: 1px solid #C0C0C0;
	padding: 3px;	
}
select{
	font-size: 11px;
	width: 210px;
	border: 1px solid #C0C0C0;
	padding: 3px;	
}

.align-right{
	text-align:right;
}
.cp_table{
	width:50%;
	border-collapse:collapse;
}
.cp_table tr th {
	border-top:1px solid #000;
	border-bottom:1px solid #000;
}

.table-form tr td:nth-child(odd){
	text-align:right;
	font-weight:bold;
}
.table-form td{
	padding:3px;	
}
.table-form{
	display:inline-table;	
	border-collapse:collapse;
}

.table-content{
	margin-top:5px;
	width:100%;
	border-collapse:collapse;
}


.table-content tr:first-child td{
	font-weight:bold;
	border-top:3px solid #000;
	border-bottom:1px solid #000;
	
}
.table-content td{
	padding:4px;
}
.table-content tr:last-child td{
	border-top:1px solid #000;	
}

</style>
<?php
	#COMMENT THIS
	require_once("dprc_options.class.php");
	

	$b					= $_REQUEST['b'];
	$user_id			= $_SESSION['userID'];

	#ID
	$application_id			= $_REQUEST['application_id'];
	
	#SEARCH
	$search_application		= $_REQUEST['search_application'];
	$search_customer		= $_REQUEST['search_customer'];
	
	#HEADER
	
	$customer_id			= $_REQUEST['customer_id'];
	$reservation_no			= $_REQUEST['reservation_no'];
	$reservation_amount		= $_REQUEST['reservation_amount'];
	$package_type_id			= $_REQUEST['package_type_id'];
	$subd_id				= $_REQUEST['subd_id'];
	$model_id				= $_REQUEST['model_id'];
	$phase					= $_REQUEST['phase'];
	$block					= $_REQUEST['block'];
	$lot					= $_REQUEST['lot'];
	$lot_area				= $_REQUEST['lot_area'];
	$floor_area				= $_REQUEST['floor_area'];
	$payment_code			= $_REQUEST['payment_code'];
	$dp_code				= $_REQUEST['dp_code'];
	$loan_value			 	= $_REQUEST['loan_value'];
	$dp_percent				= $_REQUEST['dp_percent'];
	
	$with_disc				= $_REQUEST['with_disc'];
	$loan_term				= $_REQUEST['loan_term'];
	$dp_disc_fixed			= $_REQUEST['dp_disc_fixed'];
	$disc_rate				= $_REQUEST['disc_rate'];
	$interest_rate			= $_REQUEST['interest_rate'];
	$dp_amount				= $_REQUEST['dp_amount'];
	$dp_period				= $_REQUEST['dp_period'];
	$outstanding_balance	= $_REQUEST['outstanding_balance'];
	$dp_balance				= $_REQUEST['dp_balance'];
	
	$application_date		= $_REQUEST['application_date'];
	$net_loan				= $_REQUEST['net_loan'];
	$amortization			= $_REQUEST['amortization'];
	$date_due				= $_REQUEST['date_due'];
	$penalized				= $_REQUEST['penalized'];
	$penalty_per_day		= $_REQUEST['penalty_per_day'];
	$grace_period			= $_REQUEST['grace_period'];
	$date_approved			= $_REQUEST['date_approved'];
	$date_cancelled			= $_REQUEST['date_cancelled'];
	$datecut				= $_REQUEST['datecut'];
	
	
	
	#PAYMENTS
	$dprc_payment_id		= $_REQUEST['dprc_payment_id'];
	$or_date				= $_REQUEST['or_date'];
	$postcode				= $_REQUEST['postcode'];
	$pay_mode				= $_REQUEST['pay_mode'];
	$date_encoded			= $_REQUEST['date_encoded'];
	$payment_amount			= $_REQUEST['payment_amount'];
	$or_no					= $_REQUEST['or_no'];
	$penalize				= $_REQUEST['penalize'];
	$check_no				= $_REQUEST['check_no'];
	$remarks				= $_REQUEST['remarks'];
	
	$discount_amount		= $_REQUEST['discount_amount'];
	$discount_remarks		= $_REQUEST['discount_remarks'];
	
	
	if( $b == "Post" ){
		$aAppID 			= $_REQUEST['aAppID'];
		$post_date			= $_REQUEST['post_date'];
		$post_remarks		= $_REQUEST['post_remarks'];
		$post_project_id	= $_REQUEST['post_project_id'];
		
		//INSERT GL HERE
		$gltran_header_id = Accounting::postApplication($aAppID,$post_date,$post_remarks,$post_project_id);
		$msg = "Click <a style='color:#F00; font-weight:bold; font-size:13px;' href='admin.php?view=1da21dd42f2e46c2d13e&gltran_header_id=$gltran_header_id'>here</a> to see GL postings.";
	}
	
	if($b == 'D'){ 
		mysql_query("
			update application set application_void = '1' where application_id = '$application_id'
		") or die(mysql_error());	
		$msg = "Application Voided.";
	
	}
	
	if($b == "delete_payment"){
		
		$_post_code = $options->getAttribute('dprc_payment','dprc_payment_id',$dprc_payment_id,'postcode');
		
		mysql_query("
			delete from dprc_payment where dprc_payment_id = '$dprc_payment_id'
		") or die(mysql_error());	
		
		#echo "Post Code : ".$_post_code;
		if($_post_code == "V" || $_post_code == "D"){
			mysql_query("
				delete from dprc_dp where dprc_payment_id = '$dprc_payment_id' 
			") or die(mysql_error());	
		}else{
			mysql_query("
				delete from dprc_ledger where dprc_payment_id = '$dprc_payment_id'
			") or die(mysql_error());
		}
	}
	
	if($b=="Submit"){
		$query="
			insert into 
				application
			set
				customer_id				= '$customer_id',
				reservation_no			= '$reservation_no',
				reservation_amount		= '$reservation_amount',
				package_type_id			= '$package_type_id',
				subd_id					= '$subd_id',
				model_id				= '$model_id',
				phase					= '$phase',
				block					= '$block',
				lot						= '$lot',
				lot_area				= '$lot_area',
				floor_area				= '$floor_area',
				payment_code			= '$payment_code',
				dp_code					= '$dp_code',
				loan_value			 	= '$loan_value',
				dp_percent				= '$dp_percent',
				
				with_disc				= '$with_disc',
				loan_term				= '$loan_term',
				dp_disc_fixed			= '$dp_disc_fixed',
				disc_rate				= '$disc_rate',
				interest_rate			= '$interest_rate',
				dp_amount				= '$dp_amount',
				dp_period				= '$dp_period',
				outstanding_balance		= '$outstanding_balance',
				dp_balance				= '$dp_balance',
				
				application_date		= '$application_date',
				net_loan				= '$net_loan',
				amortization			= '$amortization',
				date_due				= '$date_due',
				penalized				= '$penalized',
				penalty_per_day			= '$penalty_per_day',
				grace_period			= '$grace_period',
				date_approved			= '$date_approved',
				date_cancelled			= '$date_cancelled',
				datecut					= '$datecut'
		";	
		
		mysql_query($query) or die(mysql_error());
		$application_id = mysql_insert_id();
		$msg = "Transaction Added";
		
	}else if($b=="Update"){
		#CANNOT UPDATE DATE CUTTOF
		
		$query="
			update
				application
			set
				customer_id				= '$customer_id',
				reservation_no			= '$reservation_no',
				reservation_amount		= '$reservation_amount',
				package_type_id			= '$package_type_id',
				subd_id					= '$subd_id',
				model_id				= '$model_id',
				phase					= '$phase',
				block					= '$block',
				lot						= '$lot',
				lot_area				= '$lot_area',
				floor_area				= '$floor_area',
				payment_code			= '$payment_code',
				dp_code					= '$dp_code',
				loan_value			 	= '$loan_value',
				dp_percent				= '$dp_percent',
				
				with_disc				= '$with_disc',
				loan_term				= '$loan_term',
				dp_disc_fixed			= '$dp_disc_fixed',
				disc_rate				= '$disc_rate',
				interest_rate			= '$interest_rate',
				dp_amount				= '$dp_amount',
				dp_period				= '$dp_period',
				outstanding_balance		= '$outstanding_balance',
				dp_balance				= '$dp_balance',
				
				application_date		= '$application_date',
				net_loan				= '$net_loan',
				amortization			= '$amortization',
				date_due				= '$date_due',
				penalized				= '$penalized',
				penalty_per_day			= '$penalty_per_day',
				grace_period			= '$grace_period',
				date_approved			= '$date_approved',
				date_cancelled			= '$date_cancelled'
			where
				application_id = '$application_id'
		";	
		
		mysql_query($query) or die(mysql_error());
		
		$msg = "Transaction Updated";
	}
	
	#PAYMENT DB TRANSACTIONS
	
	if($b=="Submit Payment"){

		$do_not_apply_interest = ( $_REQUEST['do_not_apply_interest'] ) ? 1 : 0;

		$query="
			insert into 
				dprc_payment
			set
				application_id        = '$application_id',
				or_date               = '$or_date',
				postcode              = '$postcode',
				pay_mode              = '$pay_mode',
				date_encoded          = '$date_encoded',
				payment_amount        = '$payment_amount',
				or_no                 = '$or_no',
				penalize              = '$penalize',
				check_no              = '$check_no',
				user_id               = '$user_id',
				remarks               = '$remarks',
				discount_amount       = '$discount_amount',
				discount_remarks      = '$discount_remarks',
				do_not_apply_interest = '$do_not_apply_interest'
		";	
		
		mysql_query($query) or die(mysql_error());
		$dprc_payment_id = mysql_insert_id();
		$msg = "Payment Submited";
		
		#BREAK DOWN PAYMENT
		#get latest ledger of application to get outstanding balance. if no rows returned get net_loan
		$outbal = dprc::getOutstandingBalance($application_id);
		$outbal = ( $outbal > 0 ) ? $outbal : $net_loan;
		
		#echo "($loan_term,$interest_rate,$net_loan,$application_date,$date_due,$dp_period,$outbal,$or_date,$payment_amount,$grace_period) <br>";
		
		#$aBreakDown = dprc::computeNewBalance($loan_term,$interest_rate,$net_loan,$application_date,$date_due,$dp_period,$outbal,$or_date,$payment_amount,$grace_period);
		
		
		#echo "<pre>";
		#print_r($aBreakDown);
		#echo "</pre>";
		
		#SPOT CASH LOT ONLY
		if($payment_code == "1"){ #1 - payment_code spot cash
			dprc::dprcSpotCash($application_id,$dprc_payment_id,$discount_amount);
		#DOWNPAYMENT RESERVATION
		} else if($postcode == "D" || $postcode == "V"){			
			dprc::dprcDPLedger($application_id,$dprc_payment_id,$discount_amount,$discount_remarks);
		}else{

			$aBreakDown = dprc::getLatestLedger($application_id);			
			#ADD MANUAL DISCOUNT | PLACE ALL AMOUNT TO PRINCIPAL 
			if( $discount_amount > 0 ){
				
				$new_outbal = $aBreakDown['outbal'] - $discount_amount;
				
				mysql_query("
						insert into
							dprc_ledger
						set	
							dprc_payment_id = '$dprc_payment_id',
							period			= '$aBreakDown[period]',
							amount  		= '$discount_amount',
							principal		= '$discount_amount',
							interest		= '0',
							penalty			= '0',
							late_days		= '0',
							outbal			= '$new_outbal',
							due_date		= '$aBreakDown[due_date]'
					") or die(mysql_error());
			}
			
			dprc::dprcLedger($application_id,$or_date,$payment_amount,$dprc_payment_id,$postcode,$do_not_apply_interest);
			
			
			/*mysql_query("
				insert into
					dprc_ledger
				set	
					dprc_payment_id = '$dprc_payment_id',
					period			= '$aBreakDown[period]',
					amount  		= '$payment_amount',
					principal		= '$aBreakDown[principal]',
					interest		= '$aBreakDown[interest]',
					penalty			= '$aBreakDown[penalty]',
					late_days		= '$aBreakDown[late_days]',
					outbal			= '$aBreakDown[new_balance]',
					due_date		= '$aBreakDown[due_date]'
			") or die(mysql_error());*/
		}
	}
	
	$query="
		select
			*
		from
			application 
		where
			application_id = '$application_id'
	";
	
	$result=mysql_query($query) or die(mysql_error());
	$r=mysql_fetch_assoc($result);
	
	$customer_id			= $r['customer_id'];
	$reservation_no			= $r['reservation_no'];
	$reservation_amount		= $r['reservation_amount'];
	$package_type_id			= $r['package_type_id'];
	$subd_id				= $r['subd_id'];
	$model_id				= $r['model_id'];
	$phase					= $r['phase'];
	$block					= $r['block'];
	$lot					= $r['lot'];
	$lot_area				= $r['lot_area'];
	$floor_area				= $r['floor_area'];
	$payment_code			= $r['payment_code'];
	$dp_code				= $r['dp_code'];
	$loan_value			 	= $r['loan_value'];
	$dp_percent				= $r['dp_percent'];
	
	$with_disc				= $r['with_disc'];
	$loan_term				= $r['loan_term'];
	$dp_disc_fixed			= $r['dp_disc_fixed'];
	$disc_rate				= $r['disc_rate'];
	$interest_rate			= $r['interest_rate'];
	$dp_amount				= $r['dp_amount'];
	$dp_period				= $r['dp_period'];
	$outstanding_balance	= $r['outstanding_balance'];
	$dp_balance				= $r['dp_balance'];
	
	$application_date		= $r['application_date'];
	$net_loan				= $r['net_loan'];
	$amortization			= $r['amortization'];
	$date_due				= $r['date_due'];
	$penalized				= $r['penalized'];
	$penalty_per_day		= $r['penalty_per_day'];
	$grace_period			= $r['grace_period'];
	$date_approved			= $r['date_approved'];
	$date_cancelled			= $r['date_cancelled'];
	$datecut				= $r['datecut'];
?>

<form name="header_form" id="header_form" action="" method="post">
<div class="module_actions">	
    <div class='inline'>
        Application : <br />  
        <input type="text" class="textbox"  name="search_application" value="<?=$search_application?>"  onclick="this.select();"  autocomplete="off" />
    </div>  
    <div class='inline'>
        Customer: <br />  
        <input type="text" class="textbox"  name="search_customer" value="<?=$search_customer?>"  onclick="this.select();"  autocomplete="off" />
    </div>   

    <input type="submit" name="b" value="Search" />
    <a href="admin.php?view=<?=$view?>"><input type="button" value="New" /></a>
</div>

<?php
if($b == "Search"){
?>
	<?php
	
	$aAppPosted = getAppPosted();
	#print_r($aAppPosted);
	
	
	
    $page = $_REQUEST['page'];
    if(empty($page)) $page = 1;
     
    $limitvalue = $page * $limit - ($limit);
    
    $sql = "
		select
        	*
        from
			application as a, customer as c, subd as s
		where
			a.customer_id = c.customer_id
		and a.subd_id = s.subd_id
		and a.application_void = '0'
    ";

    if(!empty($search_customer)){
	$sql.="
		and
			(customer_last_name like '$search_customer%' or customer_first_name like '$search_customer%') 
	";
    }
        
    if(!empty($search_application)){
    $sql.="
		and
			application_id like '$search_application%'
    ";
    }
	
	$sql.="
		order by application_id asc
	";
  
    $pager = new PS_Pagination($conn,$sql,$limit,$link_limit);
            
    $i=$limitvalue;
    $rs = $pager->paginate();
	
	$pagination	= $pager->renderFullNav("$view&b=Search&search_application=$search_application");
    ?>
    <div class="module_actions">
    	<input type="button" class="buttons" onclick="jQuery('#_dialog_gl').dialog('open');" value="POST to GL"  />
    </div>
    <div class="pagination">
        <?=$pagination?>
    </div>
    <table id="my_table" cellspacing="2" cellpadding="5" width="100%" align="center" class="display_table">
    <tr>				
    
        <th width="20">#</th>
        <th width="20"></th>
        <th width="20"></th>
        <th>APPLICATION #</th>
        <th>CUSTOMER</th>
        <th>SUBD.</th>
        <th>PHASE.</th>
        <th>BLOCK</th>
        <th>LOT</th>
        <th>MODEL</th>
        <th>DATE CANCELLED</th>
        <th>WTAX STATUS</th>


    </tr>  
    <?php								
    while($r=mysql_fetch_assoc($rs)) {
        
		$check_box_disabled = (in_array($r['application_id'],$aAppPosted));
		
        echo '<tr>';
        echo '<td width="20">'.++$i.'</td>';
		echo "<td><input type='checkbox' name='aAppID[]' value='$r[application_id]' ".( ( $check_box_disabled ) ? "disabled='disabled'" : "" )."  ></td>";
        echo '<td width="15"><a href="admin.php?view='.$view.'&application_id='.$r['application_id'].'" title="Show Details"><img src="images/edit.gif" border="0"></a></td>';
		echo '<td>'.str_pad($r['application_id'],7,0,STR_PAD_LEFT).'</td>';	
		echo '<td>'."$r[customer_last_name], $r[customer_first_name] $r[customer_middle_name] $r[customer_appel]".'</td>';	
		echo '<td>'.$r['subd'].'</td>';	
		echo '<td>'.$r['phase'].'</td>';	
		echo '<td>'.$r['block'].'</td>';	
		echo '<td>'.$r['lot'].'</td>';	
		echo '<td>'.$options->getAttribute('model','model_id',$r['model_id'],'model').'</td>';	
		echo '<td style="text-align:center;">'.( ($r['date_cancelled'] != "0000-00-00") ? $r['date_cancelled'] : '' ).'</td>';	
		echo '<td>'.( ( wtaxPaid($r['application_id']) ) ? "PAID" : "UNPAID" ).'</td>';	
	


        echo '</tr>';
    }
    ?>
    </table>
    <div class="pagination">
	   	 <?=$pagination?>
    </div>
<?php
}else{
?>
<div class=form_layout>
    <?php if(!empty($msg)) echo '<div id="status_update" class="ui-state-highlight ui-corner-all" style="padding: 0px 0.7em; text-align:left;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right:.3em;"></span> '.$msg.'</p></div>'; ?>
    <div class="module_title"><img src='images/user_orange.png'>APPLICATION</div>
    <div class="module_actions">
        <input type="hidden" name="application_id" value="<?=$application_id?>" />
        <div id="messageError">
            <ul>
            </ul>
        </div>
        
        <table class="table-form">
            
            <tr>
                <td>Account:</td>
                <td colspan="5">
                    <input type="text" style="width:50px;" id="customer_id" name="customer_id" value="<?=$customer_id?>" readonly="readonly" />
                    <strong>                    Accout Name:
                    </strong>                    <input type="text" name="customer_name" class="customer" value="<?=($customer_id) ? $options->getAttribute('customer','customer_id',$customer_id,'customer_first_name') : "" ?><?=($customer_id) ? " ".$options->getAttribute('customer','customer_id',$customer_id,'customer_last_name') : ""?>"/>
                </td>
            </tr>
            <tr>
                <td>Reservation No.:</td>
                <td colspan="5">
                    <input type="text" style="width:50px;" name="reservation_no" value="<?=$reservation_no?>" />
                    <input type="text" style="width:50px;" name="reservation_amount" value="<?=$reservation_amount?>" />
                    <strong>Package Type:</strong>
                    <?=$options->getTableAssoc($package_type_id,'package_type_id','Select Package Type',"select * from dprc_package_types order by package_type asc",'package_type_id','package_type')?>
              	</td>
            </tr>
            
            
            <tr>
                <td>Subdivision:</td>
                <td colspan="5"><?=$options->getTableAssoc($subd_id,'subd_id','Select Subdivision',"select * from subd order by subd asc",'subd_id','subd')?></td>
            </tr>
            
            <tr>
                <td>Model No.:</td>
                <td>
                    <?=$options->getTableAssoc($model_id,'model_id','Select Model',"select * from model order by model asc",'model_id','model')?>
                </td>
                <td>Block:</td>
                <td><input type="text" name="block" value="<?=$block?>" style="width:50px;"  /></td>
                <td><b>Lot:</b></td>
                <td><input type="text" name="lot" value="<?=$lot?>" style="width:50px;"  /></td>
            </tr>
            <tr>
                <td>Phase:</td>
                <td><input type="text" name="phase" value="<?=$phase?>"  /></td>
                <td>Lot Area:</td>
                <td><input type="text" name="lot_area" value="<?=$lot_area?>" style="width:50px;" /></td>
                <td><strong>Floor Area:</strong></td>
                <td><input type="text" name="floor_area" value="<?=$floor_area?>" style="width:50px;"  /></td>
            </tr>
            <tr>
                <td>Payment Code:</td>
                <td>
                    <!--<select name="payment_code">
                        <option>Select Payment Code:</option>
                        <option value="AMORTIZED" <?=($payment_code == "AMORTIZED") ? "selected = 'selected'" : "" ?>>AMORTIZED</option>
                    </select> -->
                    <?=$options->getTableAssoc($r['payment_code'],'payment_code','Select Payment Code',"select * from dprc_payment_codes order by payment_type asc",'payment_code','payment_type')?>
                </td>
                <td>DP Code:</td>
                <td colspan="3">
                	<!--<input type="text" name="dp_code" value="<?=$dp_code?>" /> -->
                    <?=$options->getTableAssoc($dp_code,'dp_code','Select DP Code',"select * from dprc_dp_code order by dp_type asc",'dp_code','dp_type')?>
                </td>
            </tr>
                            
            <tr>
                <td>Loan Value:</td>
                <td><input type="text" id="loan_value" name="loan_value" value="<?=$loan_value?>" /></td>
                <td>DP Percent:</td>
                <td><input type="text" name="dp_percent" value="<?=$dp_percent?>" id="dp_percent" style="width:50px;" />%</td>
                <td><strong>With Disc:</strong></td>
                <td>
                    <select name="with_disc" style="width:50px;">
                        <option value="">With Disc:</option>
                        <option value="YES" <?=($with_disc == "YES") ? "selected = 'selected'" : "" ?>>YES</option>
                        <option value="NO" <?=($with_disc == "NO") ? "selected = 'selected'" : "" ?>>NO</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Loan Term:</td>
                <td>
                    <select name="loan_term" id="loan_term">
                        <option value="">Select Loan Term:</option>
                        <option value="5" <?=($loan_term == "5") ? "selected = 'selected'" : "" ?>>5 YEARS</option>
                        <option value="7" <?=($loan_term == "7") ? "selected = 'selected'" : "" ?>>7 YEARS</option>
                        <option value="10" <?=($loan_term == "10") ? "selected = 'selected'" : "" ?>>10 YEARS</option>
                    </select>
                </td>
                <td>DP Disc Fixed:</td>
                <td>
                    <select name="dp_disc_fixed" style="width:50px;">
                        <option value=""></option>
                        <option value="YES" <?=($dp_disc_fixed == "YES") ? "selected = 'selected'" : "" ?>>YES</option>
                        <option value="NO" <?=($dp_disc_fixed == "NO") ? "selected = 'selected'" : "" ?>>NO</option>
                    </select>
                </td>
                <td>
                    Disc Rate:
                </td>
                <td>
                <input type="text" name="disc_rate" style="width:50px;" /></td>
            </tr>
            <tr>
                <td>Int Rate/Yr:</td>
                <td>
                    <select name="interest_rate" id="interest_rate">
                        <option value="">Select Interest Rate:</option>
                        <option value="16" <?=($interest_rate == "16") ? "selected = 'selected'" : "" ?>>16%</option>
                        <option value="16.25" <?=($interest_rate == "16.25") ? "selected = 'selected'" : "" ?>>16.25%</option>
                        <option value="16.50" <?=($interest_rate == "16.50") ? "selected = 'selected'" : "" ?>>16.50%</option>
                    </select>
                </td>
                <td>DP Amount:</td>
                <td><input type="text" id="dp_amount" value="<?=$dp_amount?>" name="dp_amount" style="width:100px;" /></td>
                <td>DP Period:</td>
                <td>
                <input type="text" value="<?=$dp_period?>" name="dp_period" id="dp_period" style="width:50px;" /></td>
            </tr>
            <tr>
                <td>Outstanding Balance:</td>
                <?php 
				$outstanding_balance = getOutbal($application_id);
				?>
                <td><input type="text" value="<?=$outstanding_balance?>" /></td>
                <td>DP Balance:</td>
                <?php 
				$dp_balance = dprc::getDownpaymentBalance($application_id);
				?>
                <td colspan="3"><input type="text" value="<?=$dp_balance?>" /></td>
            </tr>
        </table>
        
        <table class="table-form" style="border:2px dashed #666; padding:10px; margin:10px;">
            <?php if(!empty($application_id)) { ?>
            <tr>
                <td>Application No:</td>
                <td><input type="text" value="<?=str_pad($application_id,7,0,STR_PAD_LEFT)?>" /></td>
            </tr>
            <?php } ?>
            <tr>
                <td>Application Date:</td>
                <td><input type="text" class="datepicker" name="application_date" value="<?=$application_date?>"  /></td>
            </tr>
            <tr>
                <td>Net Loan:</td>
                <td><input type="text" name="net_loan" id="net_loan" value="<?=$net_loan?>" /></td>
            </tr>
            <tr>
                <td>Amortization:</td>
                <td><input type="text" name="amortization" id="amortization" value="<?=$amortization?>"  /></td>
            </tr>
            <tr>
                <td>
                    Date Due:
                </td>
                <td>
                    <input type="text" name="date_due" style="width:50px;" value="<?=$date_due?>" />
                    th day of moth
                </td>
            </tr>
            <tr>
                <td>Penalized:</td>
                <td>
                    <select name="penalized" style="width:100px;">
                        <option value="">Penalized ?:</option>
                        <option value="1" <?=($penalized == "1") ? "selected = 'selected'" : "" ?>>YES</option>
                        <option value="0" <?=($penalized == "0") ? "selected = 'selected'" : "" ?>>NO</option>
                    </select>
                <input type="text" name="penalty_per_day" id="penalty_per_day" style="width:50px;" value="<?=$penalty_per_day?>" />%</td>
            </tr>
            <tr>
                <td>Grace Period:</td>
                <td><input type="text" name="grace_period" value="<?=$grace_period?>" /></td>
            </tr>
            <tr>
                <td>Date Cut off:</td>
                <td><input type="text" class="datepicker" name="datecut" value="<?=$datecut?>" /></td>
            </tr>
            <tr>
                <td>Date Approved:</td>
                <td><input type="text" class="datepicker" name="date_approved" value="<?=$date_approved?>" /></td>
            </tr>
            <tr>
                <td>Date Cancelled:</td>
                <td><input type="text" class="datepicker" name="date_cancelled" value="<?=($date_cancelled == "0000-00-00") ? "" : $date_cancelled?>" /></td>
            </tr>
            <tr>
                <td>Encoded by:</td>
                <td><input type="text" value="<?=$options->getUserName($user_id)?>" /></td>
            </tr>
        </table>
        
    </div>
    <div class="module_actions">
        <?php if(!empty($application_id)){ ?>
        <input type="submit" name="b" id="b" value="Update" />
        <input type="button" value="Payment" onclick="j('#_dialog_payment').dialog('open');"  />        
        <?php }else{ ?>
        <input type="submit" name="b" id="b" value="Submit" />
        <?php } ?>
        <a href="admin.php?view=<?=$view?>"><input type="button" value="New" /></a>
        <?php if(!empty($application_id)){ ?>
        <a href="admin.php?view=<?=$view?>&application_id=<?=$application_id?>&b=D" onclick="return approve_confirm();"><input type="button" value="Delete" /></a>
        <?php } ?>
    </div>
    <?php if(!empty($application_id)){ ?>
    <div class="module_actions">
    	<a href="dprc/print_dprc_statementOfPayments.php?application_id=<?=$application_id?>" target="new"><input type="button" value="Statement of Payments"  /></a>
        <a href="dprc/print_dprc_loanLedger.php?application_id=<?=$application_id?>" target="new"><input type="button" value="Loan Ledger"  /></a>
        <!--<input type="button" value="Loan Ledger" onclick="xajax_getDateForLoanLedger('<?=$application_id?>');"  /> -->
        <a href="dprc/print_dprc_downPaymentLedger.php?application_id=<?=$application_id?>" target="new"><input type="button" value="Downpayment Ledger"  /></a>
        <input type="button" value="SOA" onclick="xajax_getDateForStatementOfAccount('<?=$application_id?>');"  />
        <a href="dprc/print_dprc_amortizationSchedule.php?application_id=<?=$application_id?>" target="new"><input type="button" value="Amortization Schedule"  /></a>
    </div>
    <?php } ?>
</div>
<div style="background:#000; padding:5px; color:#FFF; font-weight:bold;">APPLICATION PAYMENTS</div>
<table class="table-content">
<tr>
	<td width="20"></td>
    <td>OR Date</td>
    <td>OR No.</td>
    <td>Post Code</td>
    <td style="text-align:right;">Amount</td>
    <td style="text-align:center;">Date Encoded</td>
    <td>Penalize</td>
    <td>Remarks</td>
</tr>
<?php
$result = mysql_query("
	select 
		*
	from
		application as a, dprc_payment as p
	where
		a.application_id = p.application_id
	and
		a.application_id = '$application_id'	
") or die(mysql_error());
$rows = mysql_num_rows($result);
$i = 1;
while($r = mysql_fetch_assoc($result)){
?>
<tr>
	<td><?php if($rows == $i) { ?><a onclick="return approve_confirm();" href="admin.php?view=<?=$view?>&application_id=<?=$application_id?>&dprc_payment_id=<?=$r['dprc_payment_id']?>&b=delete_payment"><img src="images/trash.gif" /></a><?php } ?></td>
    <td><?=$r['or_date']?></td>
    <td><?=$r['or_no']?></td>
    <td><?=$options->getAttribute('dprc_post_codes','postcode',$r['postcode'],'postcode_desc')?></td>
    <td class="align-right"><?=number_format($r['payment_amount'],2)?></td>
    <td style="text-align:center;"><?=dprc::mdy($r['date_encoded'])?></td>
    <td><?=($r['penalize']) ? "Yes" : "No" ?></td>
    <td><?=$r['remarks']?></td>
</tr>
<?php
$i++; 
} 
?>
<tr>
	<td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
</tr>
</table>

<div style="background:#000; padding:5px; color:#FFF; font-weight:bold;">APPLICATION LEDGER</div>
<?php
$result = mysql_query("
	select 
		*
	from
		application as a, dprc_payment as p, dprc_ledger as l
	where
		a.application_id = p.application_id
	and
		p.dprc_payment_id = l.dprc_payment_id
	and
		a.application_id = '$application_id'
	order by
		period asc, or_date asc
") or die(mysql_error());
?>
<table class="table-content">
	<tr>
    	<td>OR DATE</td>
        <td>OR NO</td>
    	<td>PERIOD</td>
        <td style="text-align:right;">AMOUNT</td>
        <td style="text-align:right;">PRINCIPAL</td>
        <td style="text-align:right;">INTEREST</td>
        <td style="text-align:right;">PENALTY</td>
        <td style="text-align:right;">LATE DAYS</td>
        <td style="text-align:right;">OUTBAL</td>
    </tr>
<?php
$total_amount = $total_principal = $total_interest = $total_penalty = $records = 0;
while($r = mysql_fetch_assoc($result)){
	$records ++;
	$total_amount 		+= $r['principal'] + $r['interest'] + $r['penalty'];
	$total_principal 	+= $r['principal'];
	$total_interest  	+= $r['interest'];
	$total_penalty 		+= $r['penalty'];
	
?>
	<tr>
    	
    	<td><?=dprc::mdy($r['or_date'])?></td>
        <td><?=$r['or_no']?></td>
        <td><?=dprc::displayPeriod($r['period'])?></td>
        <td style="text-align:right;"><?=dprc::numform($r['principal'] + $r['interest'] + $r['penalty'])?></td>
        <td style="text-align:right;"><?=dprc::numform($r['principal'])?></td>
        <td style="text-align:right;"><?=dprc::numform($r['interest'])?></td>
        <td style="text-align:right;"><?=dprc::numform($r['penalty'])?></td>
        <td style="text-align:right;" ><?=$r['late_days']?></td>
        <td style="text-align:right;"><?=dprc::numform($r['outbal'])?></td>
    </tr>
<?php } ?>
	<tr>
    	<td>Records : <?=$records?></td>
        <td>&nbsp;</td>
        <td>Summary:</td>
        <td style="text-align:right;"><?=dprc::numform($total_amount)?></td>
        <td style="text-align:right;"><?=dprc::numform($total_principal)?></td>
        <td style="text-align:right;"><?=dprc::numform($total_interest)?></td>
        <td style="text-align:right;"><?=dprc::numform($total_penalty)?></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
</table>


    
<?php } #END OF ELSE  ?>
<?php
/*if($b == "Print Preview" && $application){

	echo "	<iframe id='JOframe' name='JOframe' frameborder='0' src='printIssuance.php?id=$application' width='100%' height='500'>
			</iframe>";
}
*/		
?>
</form>

<div id="_dialog_payment" style="padding:10px;">
	<table>
    	<tr>
        	<td>OR Date:</td>
            <td><input type="text" class="datepicker" name="or_date" readonly="readonly" /></td>
            
            <td>OR #:</td>
            <td><input type="text" name="or_no" /></td>
        </tr>
        <tr>
        	<td>Post Code:</td>
            <td>
            	<!--<select name="postcode">
                    <option value="">Select Post Code:</option>
                    <option value="Downpayment" <?=($postcode == "Downpayment") ? "selected = 'selected'" : "" ?>>Downpayment</option>
                    <option value="Regular" <?=($postcode == "Regular") ? "selected = 'selected'" : "" ?>>Regular</option>                    
                </select> -->
                <?=$options->getTableAssoc($r['postcode'],'postcode','Select Post Code',"select * from dprc_post_codes order by postcode_desc asc",'postcode','postcode_desc')?>
            </td>
            
            <td>Penalize:</td>
            <td>
            	<select name="penalize">
                    <option value="1" <?=($penalize == "1") ? "selected = 'selected'" : "" ?>>Yes</option>
                    <option value="0" <?=($penalize == "0") ? "selected = 'selected'" : "" ?>>No</option>                    
                </select>
            </td>
        </tr>
        <tr>
        	<td>Pay Mode:</td>
            <td>
            	<select name="pay_mode">
                    <option value="CASH" <?=($pay_mode == "CASH") ? "selected = 'selected'" : "" ?>>CASH</option>
                    <option value="CHECK" <?=($pay_mode == "CHECK") ? "selected = 'selected'" : "" ?>>CHECK</option>                    
                </select>
            </td>
            
            <td>Check No.:</td>
            <td><input type="text" name="check_no" /></td>
        </tr>
        <tr>
        	<td>Date Encoded:</td>
            <td><input type="text" class="datepicker" name="date_encoded" value="<?=date("Y-m-d")?>" /></td>
            
            <td>Encoded by:</td>
            <td><input type="text" value="<?=$options->getUserName($user_id)?>" /></td>
        </tr>
        <tr>
        	<td>Amount:</td>
            <td><input type="text" name="payment_amount" /></td>

            <td>Do not Apply Interest</td>
            <td><input type="checkbox" name='do_not_apply_interest' value='1' ></td>
        </tr>
        <tr>
        	<td>Remarks:</td>
            <td colspan="3"><textarea style="border:1px solid #c0c0c0; width:100%;" name="remarks" ></textarea></td>
        </tr>
    </table>
    
    <fieldset style="border:none; border-top:1px solid #c0c0c0;">
    	<legend>Discount Details</legend>
        <table style="width:100%;">
        	<tr>
                <td style="width:20%;">Disc. Amount:</td>
                <td><input type="text" name="discount_amount" /></td>
            </tr>
            <tr>
                <td>Remarks:</td>
                <td colspan="3"><textarea style="border:1px solid #c0c0c0; width:100%;" name="discount_remarks" ></textarea></td>
            </tr>
        </table>
    </fieldset>
    <input type="submit" name="b" value="Submit Payment" />
</div>

<div id="_dialog_gl" style="padding:10px;">
	<table>
    	<tr>
        	<td>Post Date:</td>
            <td><input type="text" class="datepicker" name="post_date" readonly="readonly" /></td>
       	</tr>
        <tr>
        	<td>Project:</td>
            <td>
            	<input type="text" class="textbox project" />
                <input type="hidden" name="post_project_id" />
           	</td>
        </tr>
        <tr>
        	<td>Remarks:</td>
            <td colspan="3"><textarea style="border:1px solid #c0c0c0; width:100%;" name="post_remarks" ></textarea></td>
        </tr>
    </table>
    <input type="submit" name="b" value="Post" onclick="return approve_confirm();" />
</div>
<script type="text/javascript">
j(function(){	

	jQuery(this).keyz({
		"f5": function(ctl,sft,alt,event) {
			return false;
		}
	});
	

	j("#loan_term,#loan_value,#dp_amount,#interest_rate,#dp_percent").change(function(){
		xajax_computeAmortization(xajax.getFormValues('header_form'));
	})
	
	var dlg_payment = j("#_dialog_payment").dialog({autoOpen: false , modal:true , show: 'slide' , hide : 'slide' , width : 'auto', resizable : false, height : 'auto', title : "NEW PAYMENT ENTRY"});
	dlg_payment.parent().appendTo(jQuery("form:first"));;
	
	var dialog_gl = j("#_dialog_gl").dialog({autoOpen: false , modal:true , show: 'slide' , hide : 'slide' , width : 'auto', resizable : false, height : 'auto', title : "POST TO GENERAL LEDGER"});
	dialog_gl.parent().appendTo(jQuery("form:first"));;
	
	j(".customer").autocomplete({
		source: "dprc/list_dprc_customers.php",
		minLength: 1,
		select: function(event, ui) {
			j(this).val(ui.item.value);
			j("#customer_id").val(ui.item.id);
		}
	});
	
	jQuery("#dprc_dp_code").change(function(){
		xajax_getDownpaymentPeriodOnChange(xajax.getFormValues('header_form'));
	});
});
</script>
	