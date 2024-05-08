<?php
function new_sales_invoiceform(){
	$objResponse 	= new xajaxResponse();
	$options		= new options();	
	
	
	$content = "
		<div class='module_actions'>
			<div class='form-div' >
				Project : <br>
				<input type='text' class='textbox' id='project_name' >
				<input type='hidden' name='project_id' id='project_id'  >
			</div>
			
			<div class='form-div inline' >
				CONTRACT AMOUNT: <br>
				<input type='text' id='contract_amount' class='textbox_short' name='contract_amount' value='$contract_amount' readonly='readonly'  >
			</div>
			
			<div class='form-div inline'>
				PERCENTAGE ( % ): <br>
				<input type='text' id='percentage' name='percent' class='textbox_short'  >
			</div>
			
			<div class='form-div'>
				OR #: <br>
				<input type='text' class='textbox' name='or_no' >
			</div>
			
			<div class='form-div'>
				PAYMENT: <br>
				<input type='text' id='payment' class='textbox' name='amount'  >
			</div>
			
			<div class='form-div'>
				DATE : <br>
				<input type='text' class='textbox datepicker' name='date' autocomplete='off' value='".date("Y-m-d")."' >
			</div>

						
			<div class='form_div' style='text-align:left;' >
				<input type='button' value='SUBMIT' name='b' onmouseover=\"Tip('Submit if Details are Incomplete');\" onClick=\"xajax_new_sales_invoice(xajax.getFormValues('dialog_form'));\" >
				<input type='button' value='FINISH' name='b' onmouseover=\"Tip('Finish if Details are Complete');\" onClick=\"xajax_new_sales_invoice_finish(xajax.getFormValues('dialog_form'));\" >
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
				j(this).parent().parent().next().show(500);
				j(this).parent().parent().hide(500);
			});
			
			j(\".back\").click(function(){
				j(this).parent().parent().hide(500);
				j(this).parent().parent().prev().show(500);
			});
			
			j(\"input:button\").button();
			
			j('#percentage').keyup(function(){
				solvePayment();
			});
			
			function solvePayment(){
				var contract_amount = j('#contract_amount').val();	
				var percentage = j('#percentage').val();
				
				var payment = contract_amount * (percentage/100);
				
				if(payment > contract_amount){
					alert('Error : Payment is greater than Balance');	
				}else{
					j('#payment').val(payment);
				}
			}
			
			j('#project_name').autocomplete({
				source: 'dd_projects.php',
				minLength: 1,
				select: function(event, ui) {
					j('#project_name').val(ui.item.value);
					j('#project_id').val(ui.item.id);
					j('#contract_amount').val(ui.item.contract_amount);
				}
			});
		});		
		</script>
	";
			
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script('openDialog();');
	return $objResponse;
}
	
function new_sales_invoice($form_data) {
	$objResponse 	= new xajaxResponse();
	$options		= new options();

	$date					= $form_data['date'];
	$project_id				= $form_data['project_id'];
	$project				= $options->attr_Project($project_id,'project_name');
	$or_no					= $form_data['or_no'];
	$amount					= $form_data['amount'];	
	$percent				= $form_data['percent'];
	
	$bank					= $form_data['bank'];
	$checkno				= $form_data['checkno'];
	$checkdate				= $form_data['checkdate'];
	$checkamount			= $form_data['checkamount'];
	
	$gchart_id				= $form_data['gchart_id'];
	
	$user_id				= $_SESSION['userID'];

	
	$sql = 
		"insert into 
			sales_invoice 
		set
			date		= '$date',
			project_id	= '$project_id',
			or_no		= '$or_no',
			amount		= '$amount',
			percent		= '$percent',
			invoice_status = 'S',
			user_id		= '$user_id'
		";
	
	$query = mysql_query($sql) or $objResponse->alert(mysql_error());	

	$sales_invoice_id	= mysql_insert_id();	
	
	$options->insertAudit($sales_invoice_id,"sales_invoice_id","S");
	
	$objResponse->alert("Query Successful!");
	$objResponse->script("window.location.reload();");

	return $objResponse;  			   
}

function new_sales_invoice_finish($form_data) {
	$objResponse 	= new xajaxResponse();
	$options		= new options();

	$date					= $form_data['date'];
	$project_id				= $form_data['project_id'];
	$project				= $options->attr_Project($project_id,'project_name');
	$or_no					= $form_data['or_no'];
	$amount					= $form_data['amount'];	
	$percent				= $form_data['percent'];
	
	$bank					= $form_data['bank'];
	$checkno				= $form_data['checkno'];
	$checkdate				= $form_data['checkdate'];
	$checkamount			= $form_data['checkamount'];
	
	$gchart_id				= $form_data['gchart_id'];
	
	$user_id				= $_SESSION['userID'];

	
	$sql = 
		"insert into 
			sales_invoice 
		set
			date		= '$date',
			project_id	= '$project_id',
			or_no		= '$or_no',
			amount		= '$amount',
			percent		= '$percent',
			invoice_status = 'F',
			user_id		= '$user_id'
		";
	
	$query = mysql_query($sql) or $objResponse->alert(mysql_error());	

	$sales_invoice_id	= mysql_insert_id();	
	
	$options->insertAudit($sales_invoice_id,"sales_invoice_id","F");
	
	$options->postAR($sales_invoice_id);
	
	
	$objResponse->alert("Sales Invoice Finished and Posted!");
	$objResponse->script("window.location.reload();");

	return $objResponse;  			   
}


function edit_sales_invoiceform($id) {
	$objResponse = new xajaxResponse();
	$options = new options();
	
	$result=mysql_query("
		select 
			*
		from
			sales_invoice
		where
			sales_invoice_id = '$id'
	") or $objResponse->alert(mysql_error());		
	
	$r=mysql_fetch_assoc($result);
	
	$sales_invoice_id		= $id;
	$sales_invoice_id_pad	= str_pad($sales_invoice_id,7,0,STR_PAD_LEFT);
	
	$date					= $r['date'];
	$project_id				= $r['project_id'];
	$project				= $options->attr_Project($project_id,'project_name');
	$contract_amount		= $options->attr_Project($project_id,'contract_amount');
	$or_no					= $r['or_no'];
	$amount					= $r['amount'];	
	$percent				= $r['percent'];
	
	$bank					= $r['bank'];
	$checkno				= $r['checkno'];
	$checkdate				= $r['checkdate'];
	$checkdate_display		= ($checkdate == "0000-00-00")?"":$checkdate;
	
	$checkamount			= $r['checkamount'];
	
	$gchart_id				= $r['gchart_id'];
	
	$content = "
		<div class='module_actions'>
			<div class='form-div' >
				Project : <br>
				<input type='text' class='textbox' id='project_name' value='$project' >
				<input type='hidden' name='project_id' id='project_id'  value='$project_id' >
			</div>
			
			<div class='form-div inline' >
				CONTRACT AMOUNT: <br>
				<input type='text' id='contract_amount' class='textbox_short' name='contract_amount' value='$contract_amount' readonly='readonly'  >
			</div>
			
			<div class='form-div inline'>
				PERCENTAGE ( % ): <br>
				<input type='text' id='percentage' name='percent' class='textbox_short' value='$percent'  >
			</div>
			
			<div class='form-div' >
				INVOICE NO : <br>
				<input type='text' class='textbox'  value='$sales_invoice_id_pad' readonly='readonly' >
				<input type='hidden' name='sales_invoice_id' value='$sales_invoice_id' >
			</div>
			
			<div class='form-div'>
				OR #: <br>
				<input type='text' class='textbox' name='or_no' value='$or_no' >
			</div>
			
			<div class='form-div'>
				PAYMENT: <br>
				<input type='text' id='payment' class='textbox' name='amount' value='$amount' >
			</div>
			
			<div class='form-div'>
				DATE : <br>
				<input type='text' class='textbox datepicker' name='date' autocomplete='off' value='$date' >
			</div>
			
			
			<div class='form_div' style='text-align:left;' >
				<input type='button' value='BACK' class='back'>
				<input type='button' value='UPDATE' name='b' onmouseover=\"Tip('Submit if Details are Incomplete');\" onClick=\"xajax_edit_sales_invoice(xajax.getFormValues('dialog_form'));\" >
				<input type='button' value='FINISH' name='b' onmouseover=\"Tip('Finish if Details are Complete');\" onClick=\"xajax_edit_sales_invoice_finish(xajax.getFormValues('dialog_form'));\" >
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
				j(this).parent().parent().next().show(500);
				j(this).parent().parent().hide(500);
			});
			
			j(\".back\").click(function(){
				j(this).parent().parent().hide(500);
				j(this).parent().parent().prev().show(500);
			});
			
			j(\"input:button\").button();
			
			j('#percentage').keyup(function(){
				solvePayment();
			});
			
			function solvePayment(){
				var contract_amount = j('#contract_amount').val();	
				var percentage = j('#percentage').val();
				
				var payment = contract_amount * (percentage/100);
				
				if(payment > contract_amount){
					alert('Error : Payment is greater than Balance');	
				}else{
					j('#payment').val(payment);
				}
			}
			
			j('#project_name').autocomplete({
				source: 'dd_projects.php',
				minLength: 1,
				select: function(event, ui) {
					j('#project_name').val(ui.item.value);
					j('#project_id').val(ui.item.id);
					j('#contract_amount').val(ui.item.contract_amount);
				}
			});
		});		
		</script>
	";
			
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script('openDialog();');
		
	return $objResponse;
}
	
function edit_sales_invoice($form_data) {
	$objResponse = new xajaxResponse();
	$options	= new options();
	
	$sales_invoice_id		= $form_data['sales_invoice_id'];
	
	$date					= $form_data['date'];
	$project_id				= $form_data['project_id'];
	$project				= $options->attr_Project($project_id,'project_name');
	$or_no					= $form_data['or_no'];
	$amount					= $form_data['amount'];	
	$percent				= $form_data['percent'];
	
	$bank					= $form_data['bank'];
	$checkno				= $form_data['checkno'];
	$checkdate				= $form_data['checkdate'];
	$checkamount			= $form_data['checkamount'];
	$gchart_id				= $form_data['gchart_id'];
	
	$user_id				= $_SESSION['userID'];

	
	$sql = 
		"update
			sales_invoice 
		set
			date		= '$date',
			project_id	= '$project_id',
			or_no		= '$or_no',
			amount		= '$amount',
			percent		= '$percent',
			invoice_status	= 'S',
			user_id		= '$user_id'
		where
			sales_invoice_id = '$sales_invoice_id'
		";
	
	$query = mysql_query($sql) or $objResponse->alert(mysql_error());		
	
	$options->insertAudit($sales_invoice_id,"sales_invoice_id","U");
	
	$objResponse->alert("TRANSACTION UPDATED!");
	$objResponse->script("window.location.reload();");
	

	return $objResponse;  
}

function edit_sales_invoice_finish($form_data) {
	$objResponse 	= new xajaxResponse();
	$options		= new options();

	$sales_invoice_id		= $form_data['sales_invoice_id'];
	$date					= $form_data['date'];
	$project_id				= $form_data['project_id'];
	$project				= $options->attr_Project($project_id,'project_name');
	$or_no					= $form_data['or_no'];
	$amount					= $form_data['amount'];	
	$percent				= $form_data['percent'];
	
	$bank					= $form_data['bank'];
	$checkno				= $form_data['checkno'];
	$checkdate				= $form_data['checkdate'];
	$checkamount			= $form_data['checkamount'];
	
	$gchart_id				= $form_data['gchart_id'];
	
	$user_id				= $_SESSION['userID'];

	
	$sql = 
		"update
			sales_invoice 
		set
			date		= '$date',
			project_id	= '$project_id',
			or_no		= '$or_no',
			amount		= '$amount',
			percent		= '$percent',
			invoice_status = 'F',
			user_id		= '$user_id'
		where
			sales_invoice_id  = '$sales_invoice_id'
		";
	
	$query = mysql_query($sql) or $objResponse->alert(mysql_error());	
	
	$options->insertAudit($sales_invoice_id,"sales_invoice_id","F");
	
	$options->postAR($sales_invoice_id);
		
	$objResponse->alert("Sales Invoice Finished and Posted!");
	$objResponse->script("window.location.reload();");

	return $objResponse;  			   
}
	
function sales_invoice_payment_finish($form_data) {
	$objResponse = new xajaxResponse();
	$options	= new options();
	
	$sales_invoice_id		= $form_data['sales_invoice_id'];
	
	$date					= $form_data['date'];
	$project_id				= $form_data['project_id'];
	$project				= $options->attr_Project($project_id,'project_name');
	$or_no					= $form_data['or_no'];
	$amount					= $form_data['amount'];	
	$percent				= $form_data['percent'];
	
	$payment_date			= $form_data['payment_date'];
	$bank					= $form_data['bank'];
	$checkno				= $form_data['checkno'];
	$checkdate				= $form_data['checkdate'];
	$checkamount			= $form_data['checkamount'];
	
	$user_id				= $_SESSION['userID'];
	
	$gchart_id				= $form_data['gchart_id'];

	$deduction_gchart_id	= $form_data['deduction_gchart_id'];
	$deduction_amount		= $form_data['deduction_amount'];
	
	#$objResponse->alert($deduction_gchart_id);
	#$objResponse->alert($deduction_amount);
	
	$sql = 
		"update
			sales_invoice 
		set
			payment_status	= 'F',
			user_id		= '$user_id' , 
			payment_date	= '$payment_date',
			bank		= '$bank',
			checkno		= '$checkno',
			checkdate	= '$checkdate',
			checkamount = '$checkamount'
		where
			sales_invoice_id = '$sales_invoice_id'
		";
	
	$query = mysql_query($sql) or $objResponse->alert(mysql_error());		
		
	$options->insertAudit($sales_invoice_id,"sales_invoice_id","F");
	
	$options->postAR_Payment($sales_invoice_id,$gchart_id,$deduction_gchart_id,$deduction_amount);
		
	$objResponse->alert("TRANSACTION FINISHED AND POSTED!");
	$objResponse->script("window.location.reload();");
	
	$objResponse->script("toggleBox('demodiv',0)");
	return $objResponse;  
}


function sales_invoice_paymentform($id) {
	$objResponse = new xajaxResponse();
	$options = new options();
	
	$result=mysql_query("
		select 
			*
		from
			sales_invoice
		where
			sales_invoice_id = '$id'
	") or $objResponse->alert(mysql_error());		
	
	$r=mysql_fetch_assoc($result);
	
	$sales_invoice_id		= $id;
	$sales_invoice_id_pad	= str_pad($sales_invoice_id,7,0,STR_PAD_LEFT);
	
	$date					= $r['date'];
	$project_id				= $r['project_id'];
	$project				= $options->attr_Project($project_id,'project_name');
	$contract_amount		= $options->attr_Project($project_id,'contract_amount');
	$or_no					= $r['or_no'];
	$amount					= $r['amount'];	
	$percent				= $r['percent'];
	
	$payment_date			= $r['payment_date'];
	$bank					= $r['bank'];
	$checkno				= $r['checkno'];
	$checkdate				= $r['checkdate'];
	$checkdate_display		= ($checkdate == "0000-00-00")?"":$checkdate;
	
	$checkamount			= $r['checkamount'];
	$gchart_id				= $r['gchart_id'];
	
	$content = "
		<div class='module_actions'>
			<div class='form-div' >
				Project : <br>
				<input type='text' class='textbox' id='project_name' value='$project' >
				<input type='hidden' name='project_id' id='project_id'  value='$project_id' >
			</div>
			
			<div class='form-div inline' >
				CONTRACT AMOUNT: <br>
				<input type='text' id='contract_amount' class='textbox_short' name='contract_amount' value='$contract_amount' readonly='readonly'  >
			</div>
			
			<div class='form-div inline'>
				PERCENTAGE ( % ): <br>
				<input type='text' id='percentage' name='percent' class='textbox_short' value='$percent'  >
			</div>
			
			<div class='form-div' >
				INVOICE NO : <br>
				<input type='text' class='textbox'  value='$sales_invoice_id_pad' readonly='readonly' >
				<input type='hidden' name='sales_invoice_id' value='$sales_invoice_id' >
			</div>
			
			<div class='form-div'>
				OR #: <br>
				<input type='text' class='textbox' name='or_no' value='$or_no' >
			</div>
			
			<div class='form-div'>
				PAYMENT: <br>
				<input type='text' id='payment' class='textbox' name='amount' value='$amount' >
			</div>
			
			<div class='form-div'>
				DATE : <br>
				<input type='text' class='textbox datepicker' name='date' autocomplete='off' value='$date' >
			</div>
			
			<div class=\"module_title\"><img src='images/money.png'>DEDUCTION DETAILS : <img src='images/add.png' style='cursor:pointer;' onclick=\"xajax_add_deduction_form();\" > </div>
			
			
			<div id='deduction_div' >
				<div class='form-div'>
					<img src='images/trash.gif' style='cursor:pointer;' onclick=\"j(this).parent().remove();\">
					".$options->option_chart_of_accounts('','deduction_gchart_id[]')."
					<input type='text' class='textbox' name='deduction_amount[]'>
				</div>
			</div>
			
			
			<div class=\"module_title\"><img src='images/money.png'>PAYMENT DETAILS : </div>
			
			<div class='form-div'>
				PAYMENT DATE : <br>
				<input type='text' class='textbox datepicker' name='payment_date' autocomplete=\"off\" value='$payment_date'>
			</div>
			
			<div class='form-div'>
				BANK : <br>
				<input type='text' class='textbox' name='bank' value='$bank'>
			</div>
			
			<div class='form-div'>
				CHECK NO : <br>
				<input type='text' class='textbox' name='checkno' autocomplete='off'  value='$checkno' >
			</div>
			
			<div class='form-div'>
				CHECK DATE : <br>
				<input type='text' class='textbox datepicker' name='checkdate' autocomplete=\"off\" value='$checkdate_display'>
			</div>
			
			<div class='form-div'>
				CHECK AMOUNT : <br>
				<input type='text' class='textbox' name='checkamount' id='check_amount' value='$checkamount' >
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
				<input type='button' value='FINISH' name='b' onmouseover=\"Tip('Finish if Details are Complete');\" onClick=\"xajax_sales_invoice_payment_finish(xajax.getFormValues('dialog_form'));\" >
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
				j(this).parent().parent().next().show(500);
				j(this).parent().parent().hide(500);
			});
			
			j(\".back\").click(function(){
				j(this).parent().parent().hide(500);
				j(this).parent().parent().prev().show(500);
			});
			
			j(\"input:button\").button();
			
			j('#percentage').keyup(function(){
				solvePayment();
			});
			
			function solvePayment(){
				var contract_amount = j('#contract_amount').val();	
				var percentage = j('#percentage').val();
				
				var payment = contract_amount * (percentage/100);
				
				if(payment > contract_amount){
					alert('Error : Payment is greater than Balance');	
				}else{
					j('#payment').val(payment);
				}
			}
			
			j('#project_name').autocomplete({
				source: 'dd_projects.php',
				minLength: 1,
				select: function(event, ui) {
					j('#project_name').val(ui.item.value);
					j('#project_id').val(ui.item.id);
					j('#contract_amount').val(ui.item.contract_amount);
				}
			});
		});		
		</script>
	";
			
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script('openDialog();');
		
	return $objResponse;
}

function add_deduction_form(){
	$objResponse	= new xajaxResponse();
	$options		= new options();
	
	$content="
		<div class='form-div'>
			<img src='images/trash.gif' style='cursor:pointer;' onclick=\"j(this).parent().remove();\">
			".$options->option_chart_of_accounts('','deduction_gchart_id[]')."
			<input type='text' class='textbox' name='deduction_amount[]'>
		</div>
	";	
	
	$objResponse->append('deduction_div','innerHTML',$content);
	
	return $objResponse;
}


?>