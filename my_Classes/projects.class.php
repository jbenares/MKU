<?php
function update_scope_of_work($project_id,$id=NULL){
	$objResponse		= new xajaxResponse();
	$options			= new options();	
	
	$content = 	$options->option_scopeofwork($id,$project_id);
	
	$objResponse->assign('div_scope_of_work','innerHTML',$content);
	
	return $objResponse;
}



function new_projectform(){
	$options = new options();
	$objResponse = new xajaxResponse();

	
	$content = "
		<div class='ui-widget ui-widget-content' style='padding:5px;'>
				<div class='form-div'>
					Project Code : <br>
					<input type='text' class='textbox' name='project_code' value=''>
				</div>
				
				<div class='form-div'>
					Project Name : <br>
					<input type='text' class='textbox' name='project_name'  value='' >
				</div>
				
				<div class='form-div'>
					Contact : <br>
					<input type='text' class='textbox' name='contact'  value='' >
				</div>
				
				<div class='form-div'>
					Location : <br>
					<textarea name='location' class='textarea_small' ></textarea>
				</div>
				
				<div class='form-div'>
					Project Type : <br>
					".$options->getTableAssoc(NULL,"project_type_id","Select Type","SELECT * FROM project_type ORDER BY project_type ASC","project_type_id","project_type")."
				</div>
				
				<div class='form-div'>
					Owner : <br>
					<input type='text' class='textbox' name='owner' value=''>
				</div>
				
				<div class='form-div'>
					Contract Amount: <br>
					<input type='text' class='textbox' name='contract_amount' value=''>
				</div>
				
				<div class='form-div'>
					Status : <br>
					".$options->getProjectStatus(NULL,"pstatus")."
				</div>
				
				<div class='form-div'>
					Client Project: <br>
					<input type='checkbox' name='client_project'  value='1' >
				</div>
				
				<div class='form-div'>
					<input type='button' class='button' name='submit' value='Submit' onclick=xajax_new_project(xajax.getFormValues('dialog_form')); >
				</div>
		</div>
	";
	
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script("j( \"#dialog\" ).dialog( \"option\", \"title\", 'PROJECT' );");
	$objResponse->script("j( \"input:submit, input:button, .buttons , input:reset\").button();");
	$objResponse->script('openDialog();');
	
	return $objResponse;
}

function new_project($form_data){
	$objResponse 	= new xajaxResponse();
	$options		= new options();
	
	$project_name 	= $form_data['project_name'];
	$project_code	= $form_data['project_code'];
	$location		= $form_data['location'];
	$contact		= $form_data['contact'];
	$owner			= $form_data['owner'];
	$contract_amount	= $form_data['contract_amount'];
	$client_project	= ($form_data['client_project']) ? 1 : 0;
	
	mysql_query("
		insert into
			projects
		set
			project_name 	= '$project_name',
			project_code	= '$project_code',
			location		= '$location',
			contact			= '$contact',
			owner			= '$owner',
			contract_amount	= '$contract_amount',
			client_project	= '$client_project',
			project_type_id	= '$form_data[project_type_id]',
			pstatus			= '$form_data[pstatus]'
	") or die($objResponse->alert(mysql_error()));
	
	$objResponse->alert("Query Successful");
	$objResponse->script("window.location.reload();");
	
	return $objResponse;
}


function edit_projectform($id){
	$options = new options();
	$objResponse = new xajaxResponse();
	
	$result=mysql_query("
		select
			*
		from	
			projects
		where
			project_id = '$id'
	") or die($objResponse->alert(mysql_error()));
	
	$r = mysql_fetch_assoc($result);

	$project_name 	= $r['project_name'];
	$project_code	= $r['project_code'];
	$location		= $r['location'];
	$contact		= $r['contact'];
	$owner			= $r['owner'];
	$contract_amount	= $r['contract_amount'];
	
	$content = "
		<div class='ui-widget ui-widget-content' style='padding:5px;'>
				
				<input type='hidden' name='project_id' value='$id' >
			
				<div class='form-div'>
					Project Code : <br>
					<input type='text' class='textbox' name='project_code' value='$project_code'>
				</div>
				
				<div class='form-div'>
					Project Name : <br>
					<input type='text' class='textbox' name='project_name'  value='$project_name' >
				</div>
				
				<div class='form-div'>
					Contact : <br>
					<input type='text' class='textbox' name='contact'  value='$contact' >
				</div>
				
				<div class='form-div'>
					Location : <br>
					<textarea name='location' class='textarea_small' >$location</textarea>
				</div>
				
				<div class='form-div'>
					Project Type : <br>
					".$options->getTableAssoc($r['project_type_id'],"project_type_id","Select Type","SELECT * FROM project_type ORDER BY project_type ASC","project_type_id","project_type")."
				</div>
				
				<div class='form-div'>
					Owner : <br>
					<input type='text' class='textbox' name='owner' value='$owner'>
				</div>
				
				<div class='form-div'>
					Contract Amount: <br>
					<input type='text' class='textbox' name='contract_amount' value='$contract_amount'>
				</div>
				
				<div class='form-div'>
					Status : <br>
					".$options->getProjectStatus($r['pstatus'],"pstatus")."
				</div>
				
				<div class='form-div'>
					Client Project: <br>
					<input type='checkbox' name='client_project' ".(($r['client_project']) ? "checked = 'checked'" : "")."  value='1' >
				</div>
				
				<div class='form-div'>
					<input type='button' class='button' name='submit' value='Submit' onclick=xajax_edit_project(xajax.getFormValues('dialog_form')); >
				</div>
		</div>
	";
	
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script("j( \"#dialog\" ).dialog( \"option\", \"title\", 'PROJECT' );");
	$objResponse->script("j( \"input:submit, input:button, .buttons , input:reset\").button();");
	$objResponse->script('openDialog();');
	
	return $objResponse;
}

function edit_project($form_data){
	$objResponse 	= new xajaxResponse();
	$options		= new options();
	
	$project_id 	= $form_data['project_id'];
	$project_name 	= $form_data['project_name'];
	$project_code	= $form_data['project_code'];
	$location		= $form_data['location'];
	$contact		= $form_data['contact'];
	$owner			= $form_data['owner'];
	$contract_amount	= $form_data['contract_amount'];
	$client_project	= ($form_data['client_project']) ? 1 : 0;
	mysql_query("
		update
			projects
		set
			project_name 	= '$project_name',
			project_code	= '$project_code',
			location		= '$location',
			contact			= '$contact',
			owner			= '$owner',
			contract_amount	= '$contract_amount',
			client_project	= '$client_project',
			project_type_id	= '$form_data[project_type_id]',
			pstatus			= '$form_data[pstatus]'
		where
			project_id		= '$project_id'
	") or die($objResponse->alert(mysql_error()));
	
	$objResponse->alert("Query Successful");
	$objResponse->script("window.location.reload();");
	
	return $objResponse;
}

?>