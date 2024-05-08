<?php


function new_sectionform(){
	$options = new bd_options();
	$objResponse = new xajaxResponse();
	
	
	$content = "
		<div class='ui-widget ui-widget-content' style='padding:5px;'>
				
				<div class='form-div'>
					Project Name : <br>	
					".$options->option_project_list($project_id,'project_id','Select Project')."
				</div>
				
				<div class='form-div'>
					Work Category : <br>
					".$options->option_workcategory($work_category_id,'work_category_id','Select Work Category')."
				</div>
				
				<div class='form-div'>
					Section Code : <br>
					<input type='text' class='textbox' name='section_code' value=''>
				</div>
				
				<div class='form-div'>
					Section Name : <br>
					<input type='text' class='textbox' name='section_name' value=''>
				</div>
				
				<div class='form-div'>
					Section Description : <br>
					<textarea name='section_description'></textarea>
				</div>
								
				
				<div class='form-div'>
					<input type='button' class='button' name='submit' value='Submit' onclick=xajax_new_section(xajax.getFormValues('dialog_form')); >
				</div>
		</div>
	";
	
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script("j( \"#dialog\" ).dialog( \"option\", \"title\", 'SECTION' );");
	$objResponse->script("j( \"input:submit, input:button, .buttons , input:reset\").button();");
	$objResponse->script('openDialog();');
	
	return $objResponse;
}

function new_section($form_data){
	$objResponse 	= new xajaxResponse();
	$options		= new bd_options();
	
	$project_id 	= $form_data['project_id'];
	$work_subcategory_id = $form_data['work_category_id'];
	$section_code		= $form_data['section_code'];
	$section_name		= $form_data['section_name'];
	$section_description		= $form_data['section_description'];
	
	mysql_query("
		insert into
			sections
		set
			project_id 	= '$project_id',
			work_category_id	= '$work_subcategory_id',
			section_name		= '$section_name',
			section_code		= '$section_code',
			section_description		= '$section_description'
	") or die($objResponse->alert(mysql_error()));
	
	$objResponse->alert("Query Successful");
	$objResponse->script("window.location.reload();");
	
	return $objResponse;
}


function edit_sectionform($id){
	$options = new bd_options();
	$objResponse = new xajaxResponse();
	
	$result=mysql_query("
		select
			*
		from
			sections s, work_category w, projects p
		where
			s.section_id = '$id' AND w.work_category_id = s.work_category_id AND p.project_id = s.project_id
	") or die($objResponse->alert(mysql_error()));
	
	$r = mysql_fetch_assoc($result);

	$project_name = $r['project_name'];
	$work = $r['work'];
	$section_code = $r['section_code'];
	$section_name = $r['section_name'];
	$section_description = $r['section_description'];

	$content = "
		<div class='ui-widget ui-widget-content' style='padding:5px;'>
				
				<input type='hidden' name='section_id' value='$id' >
			
				<div class='form-div'>
					Project Name : <br>
					".$options->option_project_list($r['project_id'])."
				</div>
				
				<div class='form-div'>
					Work Category : <br>
					".$options->option_workcategory_list($r['work_category_id'])."
				</div>
				
				<div class='form-div'>
					Section Code : <br>
					<input type='text' class='textbox' name='section_code' value='$section_code'>
				</div>
								
				<div class='form-div'>
					Section Name : <br>
					<input type='text' class='textbox' name='section_name' value='$section_name'>
				</div>
				
				<div class='form-div'>
					Section Description : <br>
					<textarea name='section_description'>$section_description</textarea>
				</div>
								
				<div class='form-div'>
					<input type='button' class='button' name='submit' value='Submit' onclick=xajax_edit_section(xajax.getFormValues('dialog_form')); >
				</div>
		</div>
	";
	
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script("j( \"#dialog\" ).dialog( \"option\", \"title\", 'SECTION' );");
	$objResponse->script("j( \"input:submit, input:button, .buttons , input:reset\").button();");
	$objResponse->script('openDialog();');
	
	return $objResponse;
}

function edit_section($form_data){
	$objResponse 	= new xajaxResponse();
	$options		= new bd_options();
	
	$section_id 	= $form_data['section_id'];
	$project_id 	= $form_data['project_id'];
	$work_subcategory_id = $form_data['work_category_id'];
	$section_code		= $form_data['section_code'];
	$section_name		= $form_data['section_name'];
	$section_description		= $form_data['section_description'];
	mysql_query("
		update
			sections
		set
			project_id 	= '$project_id',
			work_category_id	= '$work_subcategory_id',
			section_name		= '$section_name',
			section_code		= '$section_code',
			section_description		= '$section_description'
		where
			section_id		= '$section_id'
	") or die($objResponse->alert(mysql_error()));
	
	$objResponse->alert("Query Successful");
	$objResponse->script("window.location.reload();");
	
	return $objResponse;
}

?>