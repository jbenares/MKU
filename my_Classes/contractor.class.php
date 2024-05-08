<?php
function new_contractorform(){
	$objResponse 	= new xajaxResponse();
	$options		= new options();	


	$content = "
		<div class='ui-widget-content' style='padding:5px;'>
			<div class='form-div'>
				Contractor Code : <br>
				<input type='text' class='textbox' name='contractor_code'>
			</div>
			
			<div class='form-div'>
				Contractor : <br>
				<input type='text' name='contractor' class='textbox'>
			</div>
			
			<div class='form-div'>
				Address : <br>
				<textarea name='address' class='textarea_small'></textarea>
			</div>
			
			<div class='form-div'>
				Contact # : <br>
				<input type=text name=contactno class=textbox>
			</div>
			<div class='form-div'>
				Contact Person : <br>
				<input type=text name=contactperson class=textbox>
			</div>
			<div class='form-div'>
				<input type='submit' id='submit' name=b value='Submit' class=buttons >
				<input type='reset' value='Clear Form' class=buttons>
			</div>
		</div>
		
		<script type='text/javascript'>
			j(\"input:submit,input:reset\").button();
			
			j(\"#dialog\").submit(function(){
				xajax_new_contractor(xajax.getFormValues('dialog_form'));
			});
		</script>
	";
	$objResponse->script("j(\"#dialog\" ).dialog( \"option\", \"title\", \"<img src=images/user_orange.png> contractor \");");		
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script('openDialog();');
	return $objResponse;
}
	
function new_contractor($form_data) {
	$objResponse = new xajaxResponse();
	
	if(!empty($form_data[contractor])) {
	
		$sql = "insert into contractor set
					contractor_code =\"$form_data[contractor_code]\",
					contractor=\"$form_data[contractor]\",
					address=\"$form_data[address]\",
					contactno=\"$form_data[contactno]\",
					contactperson=\"$form_data[contactperson]\"
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
	
function edit_contractorform($id) {
	$objResponse = new xajaxResponse();
	$options = new options();
	
	$sql = mysql_query("select
							*
						from
							contractor
						where
							contractor_id='$id'");
					  
	$r = mysql_fetch_assoc($sql);
					  
	$objResponse 	= new xajaxResponse();
	$options		= new options();	


	$content = "
		<div class='ui-widget-content' style='padding:5px;'>
			<div class='form-div'>
				Contractor Code : <br>
				<input type='text' class='textbox' name='contractor_code' value='$r[contractor_code]'>
				<input type='hidden' name='contractor_id' value='$r[contractor_id]' >
			</div>
			
			<div class='form-div'>
				Contractor : <br>
				<input type='text' name='contractor' class='textbox' value=\"$r[contractor]\">
			</div>
			
			<div class='form-div'>
				Address : <br>
				<textarea name='address' class='textarea_small'>$r[address]</textarea>
			</div>
			
			<div class='form-div'>
				Contact # : <br>
				<input type=text name=contactno class=textbox value='$r[contactno]'>
			</div>
			
			<div class='form-div'>
				Contact Person : <br>
				<input type=text name=contactperson class=textbox value='$r[contactperson]'>
			</div>
			<div class='form-div'>
				<input type='submit' id='submit' name=b value='Submit' class=buttons >
				<input type='reset' value='Clear Form' class=buttons>
			</div>
		</div>
		
		<script type='text/javascript'>
			j(\"input:submit,input:reset\").button();
			
			j(\"#dialog\").submit(function(){
				xajax_edit_contractor(xajax.getFormValues('dialog_form'));
			});
		</script>
	";
	$objResponse->script("j(\"#dialog\" ).dialog( \"option\", \"title\", \"<img src=images/user_orange.png> Contractor \");");		
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script('openDialog();');
		
	return $objResponse;
}
	
	function edit_contractor($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[contractor])){
		   
			$sql = "update contractor set
						contractor_code = \"$form_data[contractor_code]\",
						contractor=\"$form_data[contractor]\",
						address=\"$form_data[address]\",
						contactno=\"$form_data[contactno]\",
						contactperson=\"$form_data[contactperson]\"
					where
						contractor_id=\"$form_data[contractor_id]\"
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