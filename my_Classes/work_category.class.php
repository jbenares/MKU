<?php
function new_work_categoryform(){
	$objResponse 	= new xajaxResponse();
	$options		= new options();	


	$content = "
		<div class='ui-widget-content' style='padding:5px;'>
			
			<div class='form-div'>
				Work Sub Category : <br>
				".$options->option_workcategory()."
			</div>
			
			<div class='form-div'>
				Work : <br>
				<input type='text' name='work' class='textbox'>
			</div>
			
			<div class='form-div'>
				<input type='submit' id='submit' name=b value='Submit' class=buttons >
				<input type='reset' value='Clear Form' class=buttons>
			</div>
		</div>
		
		<script type='text/javascript'>
			j(\"input:submit,input:reset\").button();
			
			j(\"#dialog\").submit(function(){
				xajax_new_work_category(xajax.getFormValues('dialog_form'));
			});
		</script>
	";
	$objResponse->script("j(\"#dialog\" ).dialog( \"option\", \"title\", \"<img src=images/user_orange.png> Work Category \");");		
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script('openDialog();');
	return $objResponse;
}
	
function new_work_category($form_data) {
	$objResponse 	= new xajaxResponse();
	$options		= new options();
	
	$work					= $form_data['work'];
	$work_subcategory_id	= $form_data['work_subcategory_id'];
	$level	= (empty($work_subcategory_id))?1:2;
	
	if(!empty($work)) {

	
		$sql = "insert into work_category set
					level =\"$level\",
					work=\"$work\",
					work_subcategory_id=\"$work_subcategory_id\"
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
	
function edit_work_categoryform($id) {
	$objResponse = new xajaxResponse();
	$options = new options();
	
	$sql = mysql_query("select
							*
						from
							work_category
						where
							work_category_id = '$id'");
					  
	$r = mysql_fetch_assoc($sql);

	$content = "
		<div class='ui-widget-content' style='padding:5px;'>
			
			<input type='hidden' name='work_category_id' value='$id' >
			
			<div class='form-div'>
				Work Sub Category : <br>
				".$options->option_workcategory($r['work_subcategory_id'])."
			</div>
			
			<div class='form-div'>
				Work : <br>
				<input type='text' name='work' class='textbox' value='$r[work]'>
			</div>
			
			<div class='form-div'>
				<input type='submit' id='submit' name=b value='Submit' class=buttons >
				<input type='reset' value='Clear Form' class=buttons>
			</div>
		</div>
		
		<script type='text/javascript'>
			j(\"input:submit,input:reset\").button();
			
			j(\"#dialog\").submit(function(){
				xajax_edit_work_category(xajax.getFormValues('dialog_form'));
			});
		</script>
	";
	
	$objResponse->script("j(\"#dialog\" ).dialog( \"option\", \"title\", \"<img src=images/user_orange.png> Work Category \");");		
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script('openDialog();');
		
	return $objResponse;
}
	
function edit_work_category($form_data) {
	$objResponse 	= new xajaxResponse();
	$options		= new options();
	
	$work_category_id 	  	= $form_data['work_category_id'];
	$work					= $form_data['work'];
	$work_subcategory_id	= $form_data['work_subcategory_id'];
	$level	= (empty($work_subcategory_id))?1:2;
	
	if(!empty($work)) {

	
		$sql = "
			update 
				work_category 
			set
				level =\"$level\",
				work=\"$work\",
				work_subcategory_id=\"$work_subcategory_id\"
			where
				work_category_id = '$work_category_id'
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

?>