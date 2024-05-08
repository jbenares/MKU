<?php
function insert_deductions(){
	$objResponse 	= new xajaxResponse();
	$options 		= new options();	
	
	$content = $options->option_chart_of_accounts('','gchart_id[]')."<input type='text' class='textbox3' name='account_value'>";
	
	return $objResponse;
}

function apv_form($apv_header_id){
	$objResponse 	= new xajaxResponse();
	$options		= new options();	
	
	$total_amount 	= $form_data['ap_total_amount'];
	$checkList		= $form_data["checkList"];
	
	$result=mysql_query("
		select
			sum(total_amount) as total_amount
		from
			apv_header as h, apv_detail as d, accounts_payable as ap
		where
			h.apv_header_id = d.apv_header_id
		and
			d.ap_id = ap.ap_id
		and
			h.apv_header_id = '$apv_header_id'
	") or $objResponse->alert(mysql_error());
	
	$r=mysql_fetch_assoc($result);
	$total_amount = $r['total_amount'];
	
	$result=mysql_query("
		select
			*
		from
			apv_header
		where
			apv_header_id = '$apv_header_id'
	") or $objResponse->alert(mysql_error());
	
	$r=mysql_fetch_assoc($result);
	$supplier_id = $r['supplier_id'];
	
	$supplier		= $options->attr_Supplier($supplier_id,'account');
	
	$content = "
		<div class='module_actions'>
			<div class='form-div' >
				SUPPLIER : <br>
				<input type='text' class='textbox' value=\"$supplier\" readonly='readonly' >
				<input type='hidden' name='supplier_id' value='$supplier_id' >
				
				<input type='hidden' name='apv_header_id' value='$apv_header_id' >
			</div>
			
			<div class='form-div'>
				DATE of PAYMENT: <br>
				<input type='text' class='textbox datepicker' name='date' autocomplete='off' >
			</div>
			
			<div class=\"module_title\"><img src='images/money.png'>PAYMENT DETAILS : </div>
			
			<div class='form-div'>
				BANK : <br>
				<input type='text' class='textbox' name='bank' autocomplete='off'>
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
				<input type='text' class='textbox' name='amount' id='check_amount' value='$total_amount' >
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
				
				<input type='button' value='FINISH' name='b' onClick=\"xajax_ap_pay(xajax.getFormValues('dialog_form'),xajax.getFormValues('_form'));\" >
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


function ap_form($form_data,$supplier_id){
	$objResponse 	= new xajaxResponse();
	$options		= new options();	
	
	$total_amount 	= $form_data['ap_total_amount'];
	$checkList		= $form_data["checkList"];
	
	$supplier		= $options->attr_Supplier($supplier_id,'account');
	
	$content = "
		<div class='module_actions'>
			<div class='form-div' >
				SUPPLIER : <br>
				<input type='text' class='textbox' value='$supplier' readonly='readonly' >
				<input type='hidden' name='supplier_id' value='$supplier_id' >
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
				<input type='text' class='textbox datepicker' name='date' autocomplete='off' >
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
				
				<input type='button' value='FINISH' name='b' onClick=\"xajax_ap_pay(xajax.getFormValues('dialog_form'),xajax.getFormValues('_form'));\" >
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
	
function ap_pay($form_data,$form_data2){
	
	$objResponse 	= new xajaxResponse();
	$options		= new options();
	
	
	$date			= $form_data['date'];
	$supplier_id	= $form_data['supplier_id'];
	$apv_header_id	= $form_data['apv_header_id'];
	$bank			= $form_data['bank'];
	$checkno		= $form_data['checkno'];
	$checkdate		= $form_data['checkdate'];
	$account_id		= $form_data['gchart_id'];
	$amount			= $form_data['amount'];
	
	
	$view			= $form_data2['view'];
	
	mysql_query("
		insert into
			ap_payment
		set
			date			= '$date',
			bank			= '$bank',
			checkno			= '$checkno',
			checkdate		= '$checkdate',
			amount			= '$amount',
			type			= 'CH',
			supplier_id		= '$supplier_id',
			apv_header_id	= '$apv_header_id'
	") or $objResponse->alert(mysql_error());
	
	$ap_payment_id = mysql_insert_id();
			
	$options->postAP($ap_payment_id,$account_id);
		
	$objResponse->alert("Transaction Successful.");	
	$objResponse->redirect("admin.php?view=$view&supplier_id=$supplier_id");
	
	return $objResponse;
}

function ap_form_add($ap_header_id,$supplier_id){
	$objResponse 	= new xajaxResponse();
	$options		= new options();	
	
	
	$balance 		= $options->getAPBalance($ap_header_id);	
	$total_amount	= $options->getAPTotalAmount($ap_header_id);
	$supplier		= $options->attr_Supplier($supplier_id,'account');
	
	$content = "
		<div class='module_actions'>
			<div class='form-div' >
				SUPPLIER : <br>
				<input type='text' class='textbox' value='$supplier' readonly='readonly' >
				<input type='hidden' name='supplier_id' value='$supplier_id' >
				<input type='hidden' name='ap_header_id' value='$ap_header_id' >
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
				
				<input type='button' value='FINISH' name='b' onClick=\"xajax_ap_pay_add(xajax.getFormValues('dialog_form'),xajax.getFormValues('_form'));\" >
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

function ap_pay_add($form_data,$form_data2){
	
	$objResponse 	= new xajaxResponse();
	$options		= new options();
	
	$date			= $form_data['date'];
	$total_amount	= $form_data['total_amount'];
	$bank			= $form_data['bank'];
	$checkno		= $form_data['checkno'];
	$checkdate		= $form_data['checkdate'];
	$amount			= $form_data['amount'];
	$account_id		= $form_data['gchart_id'];
	$supplier_id	= $form_data['supplier_id'];
	$ap_header_id	= $form_data['ap_header_id'];
	
	$view			= $form_data2['view'];
	
	mysql_query("
		insert into
			ap_payment
		set
			bank			= '$bank',
			checkno			= '$checkno',
			checkdate		= '$checkdate',
			amount			= '$amount',
			type			= 'CH',
			supplier_id		= '$supplier_id',
			date			= '$date',
			ap_header_id	= '$ap_header_id'
	") or $objResponse->alert(mysql_error());
	
	$ap_payment_id = mysql_insert_id();
			
	$options->postAP($ap_payment_id,$account_id);
		
	$objResponse->alert("Transaction Successful.");	
	$objResponse->redirect("admin.php?view=$view&supplier_id=$supplier_id");
	
	return $objResponse;
}

?>