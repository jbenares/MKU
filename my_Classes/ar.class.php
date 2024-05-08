<?php
function ar_form($form_data,$account,$account_id){
	$objResponse 	= new xajaxResponse();
	$options		= new options();	
	
	$total_amount 	= $form_data['ap_total_amount'];
	$checkList		= $form_data["checkList"];
	
	$supplier		= $options->attr_Supplier($supplier_id,'account');
	
	$account = ($account=="p")?"project_id":"contractor_id";
	
	$label = ($account=="project_id")?"PROJECT":"CONTRACTOR";
	$account_id_display = ($account == "project_id")?$options->attr_Project($account_id,'project_name'):$options->attr_Contractor($account_id,'contractor');
		
	
	$content = "
		<div class='module_actions'>
			<div class='form-div' >
				$label : <br>
				<input type='text' class='textbox' value='$account_id_display' readonly='readonly' >
				<input type='hidden' name='account_id' value='$account_id' >
				<input type='hidden' name='account' value='$account' >
			</div>
			<div class='form-div inline' >
				BALANCE: <br>
				<input type='text' id='balance' class='textbox_short' name='total_amount' value='$total_amount' readonly >
			</div>
			
			<div class='form-div inline'>
				PERCENTAGE ( % ): <br>
				<input type='text' id='percentage' class='textbox_short'  >
			</div>
			
			<div class='form-div'>
				TOTAL PAYMENT: <br>
				<input type='text' id='payment' class='textbox' name='total_amount' readonly=\"readonly\" >
			</div>
			
			<div class='form-div'>
				DATE : <br>
				<input type='text' class='textbox datepicker' name='date' >
			</div>
			
			<div class=\"module_title\"><img src='images/money.png'>PAYMENT DETAILS : </div>
			
			<div class='form-div'>
				BANK : <br>
				<input type='text' class='textbox' name='bank'>
			</div>
			
			<div class='form-div'>
				CHECK NO : <br>
				<input type='text' class='textbox' name='checkno' autocomplete='off' >
			</div>
			
			<div class='form-div'>
				CHECK DATE : <br>
				<input type='text' class='textbox datepicker' name='checkdate' autocomplete=\"off\">
			</div>
			
			<div class='form-div'>
				CHECK AMOUNT : <br>
				<input type='text' class='textbox' name='amount' readonly id='check_amount' >
			</div>
			
			<div class='form_div' style='text-align:right;'>
				<input type='button' value='NEXT' class='next'>
			</div>
		</div>
		
		<div class='module_actions' style='display:none;'>
			<div class=\"module_title\"><img src='images/money.png'>CASH ACCOUNT : </div>
			<div class='form-div'>
				CASH ACCOUNT : <br>
				".$options->option_chart_of_accounts()."
			</div>
						
			<div class='form_div' style='text-align:left;' >
				<input type='button' value='BACK' class='back'>
				
				<input type='button' value='FINISH' name='b' onClick=\"xajax_ar_pay(xajax.getFormValues('dialog_form'),xajax.getFormValues('_form'));\" >
			</div>
		</div>
		
		<script type='text/javascript'>
			j(\"#dialog\").dialog(\"option\",\"title\",\"PAYMENT\");
			j(\".datepicker\").each(function(){
				j(this).datepicker({ 
				dateFormat: 'yy-mm-dd',
				changeMonth: true,
				changeYear: true 
			});
			j(\".next\").click(function(){
				j(this).parent().parent().next().show();
				j(this).parent().parent().hide();
			});
			
			j(\".back\").click(function(){
				j(this).parent().parent().hide();
				j(this).parent().parent().prev().show();
			});
			
			j(\"input:button\").button();
			
			j('#percentage').keyup(function(){
				solvePayment();
			});
			
			function solvePayment(){
				var balance = j('#balance').val();	
				var percentage = j('#percentage').val();
				
				var payment = balance * (percentage/100);
				
				if(payment > balance){
					alert('Error : Payment is greater than Balance');	
				}else{
					j('#payment,#check_amount').val(payment);
				}
			}
		});		
		</script>
	";
			
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script('openDialog();');
	return $objResponse;
}
	
function ar_pay($form_data,$form_data2){
	
	$objResponse 	= new xajaxResponse();
	$options		= new options();
	
	$date			= $form_data['date'];
	$total_amount	= $form_data['total_amount'];
	$bank			= $form_data['bank'];
	$checkno		= $form_data['checkno'];
	$checkdate		= $form_data['checkdate'];
	$amount			= $form_data['amount'];
	$account_id		= $form_data['gchart_id'];
	
	$a_id			= $form_data['account_id'];
	$a				= $form_data['account'];
	
	$account		= ($a == "p")?"project_id":"contractor_id";
	
	$view			= $form_data2['view'];
	$checkList		= $form_data2['checkList'];
	
	
	
	mysql_query("
		insert into
			ar_header
		set
			status = 'S',
			account = '$a',
			account_id = '$a_id'
	") or die(mysql_error());
	
	$ar_header_id = mysql_insert_id();
	
	foreach($checkList as $ar_id){
		mysql_query("
			insert into
				ar_detail
			set
				ar_header_id 	= '$ar_header_id',
				ar_id			= '$ar_id'
		") or $objResponse->alert(mysql_error());
	}
	
	mysql_query("
		insert into
			ar_payment
		set
			bank			= '$bank',
			checkno			= '$checkno',
			checkdate		= '$checkdate',
			amount			= '$amount',
			type			= 'CH',
			date			= '$date',
			ar_header_id	= '$ar_header_id',
			account			= '$a',
			account_id		= '$a_id'
	") or $objResponse->alert(mysql_error());
	
	$ar_payment_id = mysql_insert_id();
			
	$options->postAR($ar_payment_id,$account_id);
		
	$objResponse->alert("Transaction Successful.");	
	$objResponse->script("window.location.reload();");
	
	return $objResponse;
}

function ar_form_add($ar_header_id,$account, $account_id){
	$objResponse 	= new xajaxResponse();
	$options		= new options();	
	
	
	$label = ($account=="project_id")?"PROJECT":"CONTRACTOR";
	$account_id_display = ($account == "project_id")?$options->attr_Project($account_id,'project_name'):$options->attr_Contractor($account_id,'contractor');
	
	$balance 		= $options->getARBalance($ar_header_id);	
	$total_amount	= $options->getARTotalAmount($ar_header_id);
	
	$content = "
		<div class='module_actions'>
			<div class='form-div' >
				$label : <br>
				<input type='text' class='textbox' value='$account_id_display' readonly='readonly' >
				<input type='hidden' name='account_id' value='$account_id' >
				<input type='hidden' name='account' value='$account' >
				<input type='hidden' name='ar_header_id' value='$ar_header_id' >
			</div>
			
			<div class='form-div' >
				TOTAL AMOUNT: <br>
				<input type='text' id='total_amount' class='textbox' value='$total_amount' readonly >
			</div>
			<div class='form-div inline' >
				BALANCE: <br>
				<input type='text' id='balance' class='textbox_short' name='total_amount' value='$balance' readonly >
			</div>
			
			<div class='form-div inline'>
				PERCENTAGE ( % ): <br>
				<input type='text' id='percentage' class='textbox_short'  >
			</div>
			
			<div class='form-div'>
				TOTAL PAYMENT: <br>
				<input type='text' id='payment' class='textbox' name='total_amount' readonly=\"readonly\" >
			</div>
			
			<div class='form-div'>
				DATE : <br>
				<input type='text' class='textbox datepicker' name='date' >
			</div>
			
			<div class=\"module_title\"><img src='images/money.png'>PAYMENT DETAILS : </div>
			
			<div class='form-div'>
				BANK : <br>
				<input type='text' class='textbox' name='bank'>
			</div>
			
			<div class='form-div'>
				CHECK NO : <br>
				<input type='text' class='textbox' name='checkno' autocomplete='off' >
			</div>
			
			<div class='form-div'>
				CHECK DATE : <br>
				<input type='text' class='textbox datepicker' name='checkdate' autocomplete=\"off\">
			</div>
			
			<div class='form-div'>
				CHECK AMOUNT : <br>
				<input type='text' class='textbox' name='amount' readonly id='check_amount' >
			</div>
			
			<div class='form_div' style='text-align:right;'>
				<input type='button' value='NEXT' class='next'>
			</div>
		</div>
		
		<div class='module_actions' style='display:none;'>
			<div class=\"module_title\"><img src='images/money.png'>CASH ACCOUNT : </div>
			<div class='form-div'>
				CASH ACCOUNT : <br>
				".$options->option_chart_of_accounts()."
			</div>
						
			<div class='form_div' style='text-align:left;' >
				<input type='button' value='BACK' class='back'>
				
				<input type='button' value='FINISH' name='b' onClick=\"xajax_ar_pay_add(xajax.getFormValues('dialog_form'),xajax.getFormValues('_form'));\" >
			</div>
		</div>
		
		<script type='text/javascript'>
			j(\"#dialog\").dialog(\"option\",\"title\",\"PAYMENT\");
			j(\".datepicker\").each(function(){
				j(this).datepicker({ 
				dateFormat: 'yy-mm-dd',
				changeMonth: true,
				changeYear: true 
			});
			j(\".next\").click(function(){
				j(this).parent().parent().next().show();
				j(this).parent().parent().hide();
			});
			
			j(\".back\").click(function(){
				j(this).parent().parent().hide();
				j(this).parent().parent().prev().show();
			});
			
			j(\"input:button\").button();
			
			j('#percentage').keyup(function(){
				solvePayment();
			});
			
			function solvePayment(){
				var total_amount = j('#total_amount').val();
				var balance = j('#balance').val();	
				var percentage = j('#percentage').val();
				
				var payment = total_amount * (percentage/100);
				
				if(payment > balance){
					alert('Error : Payment is greater than Balance');	
				}else{
					j('#payment,#check_amount').val(payment);
				}
			}
		});		
		</script>
	";
			
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script('openDialog();');
	return $objResponse;
}

function ar_pay_add($form_data,$form_data2){
	
	$objResponse 	= new xajaxResponse();
	$options		= new options();
	
	$date			= $form_data['date'];
	$total_amount	= $form_data['total_amount'];
	$bank			= $form_data['bank'];
	$checkno		= $form_data['checkno'];
	$checkdate		= $form_data['checkdate'];
	$amount			= $form_data['amount'];
	$account_id		= $form_data['gchart_id'];
	$ar_header_id	= $form_data['ar_header_id'];
	
	$a_id		= $form_data['account_id'];
	$a			= $form_data['account'];
	
	$view			= $form_data2['view'];
	
	mysql_query("
		insert into
			ar_payment
		set
			bank			= '$bank',
			checkno			= '$checkno',
			checkdate		= '$checkdate',
			amount			= '$amount',
			type			= 'CH',
			date			= '$date',
			ar_header_id	= '$ar_header_id',
			account			= '$a',
			account_id		= '$a_id'
	") or $objResponse->alert(mysql_error());
	
	$ap_payment_id = mysql_insert_id();
			
	$options->postAR($ar_payment_id,$account_id);
		
	$objResponse->alert("Transaction Successful.");	
	$objResponse->script("window.location.reload();");
	
	return $objResponse;
}

?>