<?php
function getDateForLoanLedger($application_id){
	$objResponse 	= new xajaxResponse();
	$options		= new options();
	
	$content="
		<div style='padding:10px;'>
			<table>
				<tr>
					<td>From Date</td>
					<td><input type='text' name='date' id='loan_ledger_from_date' class='textbox datepicker' readonly='readonly'></td>
				</tr>
				<tr>
					<td>To Date</td>
					<td><input type='text' name='date' id='loan_ledger_to_date' class='textbox datepicker' readonly='readonly'></td>
				</tr>
				<tr>
					<td colspan='2'><input type='button' value='Generate Report' onclick=\"openLoanLedger()\" ></td>
				</tr>
			</table>							
		</div>
	";
	
	$content.="
		<script type='text/javascript'>
		
			function openLoanLedger(){
				var from_date = j('#loan_ledger_from_date').val();
				var to_date = j('#loan_ledger_to_date').val();
				window.open('dprc/print_dprc_loanLedger.php?application_id=$application_id&from_date=' + from_date +'&to_date=' + to_date);	
				closeDialog();
			}
		
		
		
			j(\".datepicker\").each(function(){
					j(this).datepicker({ 
					dateFormat: 'yy-mm-dd',
					changeMonth: true,
					changeYear: true 
				});
			});	
			j('#dialog').dialog({title: 'Loan Ledger Report'});
		</script>
	";
	
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script("openDialog();");
	
	
	return $objResponse;
}

function getDateForStatementOfAccount($application_id){
	$objResponse 	= new xajaxResponse();
	$options		= new options();
	
	$content="
		<div style='padding:10px;'>
			<table>
				<tr>
					<td>Date</td>
					<td><input type='text' name='date' id='date' class='textbox datepicker' readonly='readonly'></td>
				</tr>
				<tr>
					<td colspan='2'><input type='button' value='Generate Report' onclick=\"openLoanLedger()\" ></td>
				</tr>
			</table>							
		</div>
	";
	
	$content.="
		<script type='text/javascript'>
		
			function openLoanLedger(){
				var date = j('#date').val();
				window.open('dprc/print_dprc_statementOfAccount.php?application_id=$application_id&date=' + date );	
				closeDialog();
			}
		
		
		
			j(\".datepicker\").each(function(){
					j(this).datepicker({ 
					dateFormat: 'yy-mm-dd',
					changeMonth: true,
					changeYear: true 
				});
			});	
			j('#dialog').dialog({title: 'Statement of Account Report'});
		</script>
	";
	
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script("openDialog();");
	
	
	return $objResponse;
}

function computeAmortization($form_data){
	$objResponse = new xajaxResponse();
	
	$loan_value 	= $form_data['loan_value'];
	$dp_amount		= $form_data['dp_amount'];
	$loan_term		= $form_data['loan_term'];
	$interest_rate	= $form_data['interest_rate'];
	$dp_percent		= $form_data['dp_percent'];
	
	if($interest_rate == 16 && $loan_term == 5) {
		$amortization_factor = 0.0243180571;	
	} else if($interest_rate == 16.25 && $loan_term == 7 ){
		$amortization_factor = 0.0200047141;
	} else if($interest_rate == 16.50 && $loan_term == 10 ){
		$amortization_factor = 0.0170642299;
	} else {
		return 0;	
	}
	
	if(empty($dp_percent)){
		$dp_percent = ($dp_amount / $loan_value ) * 100; 
		$objResponse->assign('dp_percent','value',$dp_percent);		
	}else{
		$dp_amount = ( $dp_percent / 100 ) * $loan_value ;
		$objResponse->assign('dp_amount','value',$dp_amount);		
	}
	
	
	$net_loan = $loan_value - $dp_amount;
	
	$amortization = ($loan_value - $dp_amount) * $amortization_factor;
	$amortization = round($amortization,2);
	#$objResponse->alert("$loan_value, $dp_amount,$loan_term,$interest_rate");
	
	#$penalty_per_day = ($amortization * 0.02) / 30;
	$penalty_per_day = (0.02) / 30;
	
	$objResponse->assign('amortization','value',$amortization);
	$objResponse->assign('net_loan','value',$net_loan);
	$objResponse->assign('penalty_per_day','value',$penalty_per_day);
	
	return $objResponse;
}

function getDownpaymentPeriodOnChange($form_data){
	$objResponse 	= new xajaxResponse();
	$options		= new options();
	
	$dp_code	= $form_data['dprc_dp_code'];
		
	$term = $options->getAttribute('dprc_dp_code','dp_code',$dp_code,'term');
		
	$objResponse->assign('dp_period','value',$term);
	return $objResponse;
}
?>