<?php
function new_accountform(){
	$objResponse 	= new xajaxResponse();
	$options		= new options();	


	$content = "
		<div class='ui-widget-content' style='padding:5px;'>
			<div class='form-div'>
				Account Code : <br>
				<input type='text' class='textbox' name='account_code'>
			</div>
			
			<div class='form-div'>
				Account : <br>
				<input type='text' class='textbox' name='account'>
			</div>
			
			<div class='form-div'>
				Account Type : <br>
				".$options->option_account_type()."
			</div>
			
			<div class='form-div'>
				<input type='submit' id='submit' name=b value='Submit' class=buttons >
				<input type='reset' value='Clear Form' class=buttons>
			</div>
		</div>
		
		<script type='text/javascript'>
			j(\"input:submit,input:reset\").button();
			
			j(\"#dialog\").submit(function(){
				xajax_new_account(xajax.getFormValues('dialog_form'));
			});
		</script>
	";
	$objResponse->script("j(\"#dialog\" ).dialog( \"option\", \"title\", \"<img src=images/user_orange.png> Account \");");		
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script('openDialog();');
	return $objResponse;
}
	
function new_account($form_data) {
	$objResponse = new xajaxResponse();
	
	if(!empty($form_data['account']) && $form_data['account_type_id']) {
	
		$sql = "insert into account set
					account =\"$form_data[account]\",
					account_type_id =\"$form_data[account_type_id]\",
					account_code =\"$form_data[account_code]\"
				";
		
		$query = mysql_query($sql);		
		
		if(!mysql_error()) {
			$objResponse->alert("Query Successful!");
			$objResponse->script("window.location.reload();");
		}					
		else
			$objResponse->alert(mysql_error());
	}
	else {
		$objResponse->alert("Fill in all fields!");
	}
	
	$objResponse->script("toggleBox('demodiv',0)");
	return $objResponse;  			   
}
	
function edit_accountform($id) {
	$objResponse = new xajaxResponse();
	$options = new options();
	
	$sql = mysql_query("select
							*
						from
							account
						where
							account_id='$id'");
					  
	$r = mysql_fetch_assoc($sql);

	$account			= $r['account'];
	$account_type_id	= $r['account_type_id'];
	$account_code		= $r['account_code'];
	

	$content = "
		<div class='ui-widget-content' style='padding:5px;'>
			<div class='form-div'>
				Account Code : <br>
				<input type='text' class='textbox' name='account_code' value='$account_code'>
			</div>
			
			<div class='form-div'>
				Account : <br>
				<input type='text' class='textbox' name='account' value='$account'>
				<input type='hidden' name='account_id' value='$id' >
			</div>
			
			<div class='form-div'>
				Account Type : <br>
				".$options->option_account_type($account_type_id)."
			</div>
			
			<div class='form-div'>
				<input type='submit' id='submit' name=b value='Submit' class=buttons >
				<input type='reset' value='Clear Form' class=buttons>
			</div>
		</div>
		
		<script type='text/javascript'>
			j(\"input:submit,input:reset\").button();
			
			j(\"#dialog\").submit(function(){
				xajax_edit_account(xajax.getFormValues('dialog_form'));
			});
		</script>
	";
	$objResponse->script("j(\"#dialog\" ).dialog( \"option\", \"title\", \"<img src=images/user_orange.png> Account \");");		
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script('openDialog();');
		
	return $objResponse;
}
	
function edit_account($form_data) {
	$objResponse = new xajaxResponse();
	
	if(!empty($form_data[account])){
	   
		$sql = "update account set
					account = \"$form_data[account]\",
					account_type_id=\"$form_data[account_type_id]\",
					account_code = \"$form_data[account_code]\"
				where
					account_id = \"$form_data[account_id]\"
				";
		
		$query = mysql_query($sql);
		
		
		if($query) {
			$objResponse->alert("Query Successful!");
			$objResponse->script("window.location.reload();");
		}					
		else
			$objResponse->alert(mysql_error());
	}
	else {
		$objResponse->alert("Fill in all fields!");
	}
	
	$objResponse->script("toggleBox('demodiv',0)");
	return $objResponse;  			   
}

?>