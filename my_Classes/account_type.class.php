<?php
function new_account_typeform(){
	$objResponse 	= new xajaxResponse();
	$options		= new options();	


	$content = "
		<div class='ui-widget-content' style='padding:5px;'>
			<div class='form-div'>
				Account Type : <br>
				<input type='text' class='textbox' name='account_type'>
			</div>
		
			<div class='form-div'>
				<input type='submit' id='submit' name=b value='Submit' class=buttons >
				<input type='reset' value='Clear Form' class=buttons>
			</div>
		</div>
		
		<script type='text/javascript'>
			j(\"input:submit,input:reset\").button();
			
			j(\"#dialog\").submit(function(){
				xajax_new_account_type(xajax.getFormValues('dialog_form'));
			});
		</script>
	";
	$objResponse->script("j(\"#dialog\" ).dialog( \"option\", \"title\", \"<img src=images/user_orange.png> Account Type \");");		
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script('openDialog();');
	return $objResponse;
}
	
function new_account_type($form_data) {
	$objResponse = new xajaxResponse();
	
	if(!empty($form_data[account_type])) {
	
		$sql = "insert into account_type set
					account_type=\"$form_data[account_type]\"
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
	
function edit_account_typeform($id) {
	$objResponse = new xajaxResponse();
	$options = new options();
	
	$sql = mysql_query("select
							*
						from
							account_type
						where
							account_type_id='$id'");
					  
	$r = mysql_fetch_assoc($sql);
	
	$account_type	= $r['account_type'];


	$content = "
		<div class='ui-widget-content' style='padding:5px;'>
			<div class='form-div'>
				Account Type : <br>
				<input type='text' class='textbox' name='account_type' value='$account_type'>
				<input type='hidden' name='account_type_id' value='$id'>
			</div>
			
			
			<div class='form-div'>
				<input type='submit' id='submit' name=b value='Submit' class=buttons >
				<input type='reset' value='Clear Form' class=buttons>
			</div>
		</div>
		
		<script type='text/javascript'>
			j(\"input:submit,input:reset\").button();
			
			j(\"#dialog\").submit(function(){
				xajax_edit_account_type(xajax.getFormValues('dialog_form'));
			});
		</script>
	";
	$objResponse->script("j(\"#dialog\" ).dialog( \"option\", \"title\", \"<img src=images/user_orange.png> account_type \");");		
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script('openDialog();');
		
	return $objResponse;
}
	
function edit_account_type($form_data) {
	$objResponse = new xajaxResponse();
	
	if(!empty($form_data[account_type])){
	   
		$sql = "update account_type set
					account_type=\"$form_data[account_type]\"
				where
					account_type_id=\"$form_data[account_type_id]\"
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