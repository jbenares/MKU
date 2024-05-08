<?php


function new_worksection(){
	$options = new bd_options();
	$objResponse = new xajaxResponse();
	$content = "
		<div class='ui-widget ui-widget-content' style='padding:5px;'>
				
				<div class='form-div'>
					Description : <br>	
					<input type='text' class='textbox' name='description' value=''>
				</div>
				
				<div class='form-div'>
					Work Code : <br>	
					<input type='text' class='textbox' name='work_code' value=''>
				</div>
				
				<div class='form-div'>
					Work Category : <br>
					".$options->option_workcategory($work_catetory_id,'work_cat_id','Select Work Category')."
				</div>

								
				<div class='form-div'>
					Unit : <br>
					<select name='unit'>
						<option value='Hour'>Hour</option>
						<option value='Day'>Day</option>
						<option value='Week'>Week</option>
						<option value='Month'>Month</option>
						<option value='Lot'>Lot</option>
					</select>
				</div>
					
				<div class='form-div'>
					Price Per Unit : <br>
					<input type='text' class='textbox' name='price_per_unit' value=''>
				</div>
				
				<div class='form-div'>
					<input type='button' class='button' name='submit' value='Submit' onclick=xajax_new_workform(xajax.getFormValues('dialog_form')); >
				</div>
		</div>
	";
	
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script("j( \"#dialog\" ).dialog( \"option\", \"title\", 'Work Type' );");
	$objResponse->script("j( \"input:submit, input:button, .buttons , input:reset\").button();");
	$objResponse->script('openDialog();');
	
	return $objResponse;
}

function new_workform($form_data){
	$objResponse 	= new xajaxResponse();
	$options		= new bd_options();
	
				$description		= $form_data['description'];
				$work_code	= $form_data['work_code'];
				$work_cat_id 	= $form_data['work_cat_id'];				
				$unit = $form_data['unit'];
				$price_per_unit = $form_data['price_per_unit'];
	
	mysql_query("
		insert into
			work_type
		set
			description	= '$description',
			company_code = '$work_code',
			work_cat_id		= '$work_cat_id',			
			unit		= '$unit',
			wt_price_per_unit		= '$price_per_unit',
			is_deleted = '0'
	") or die($objResponse->alert(mysql_error()));
	
	$objResponse->alert("Query Successful");
	$objResponse->script("window.location.reload();");
	
	return $objResponse;
}


function edit_workform($id){
	$options = new bd_options();
	$objResponse = new xajaxResponse();
	
	$result=mysql_query("
		select
					*
				from
					work_type wt, work_category w
				where
					wt.work_code_id ='$id' AND w.work_category_id = wt.work_cat_id
	") or die($objResponse->alert(mysql_error()));
	
	$r = mysql_fetch_assoc($result);

				$description		= $r['description'];
				$company_code		= $r['company_code'];
				$work_cat_id 	= $r['work_cat_id'];				
				$unit = $r['unit'];
				$price_per_unit = $r['wt_price_per_unit'];

	$content = "
		<div class='ui-widget ui-widget-content' style='padding:5px;'>
				
				<input type='hidden' name='work_code_id' value='$id' >
				
				<div class='form-div'>
					Description : <br>	
					<input type='text' class='textbox' name='description' value='$description'>
				</div>
				
				<div class='form-div'>
					Work Code : <br>	
					<input type='text' class='textbox' name='work_code' value='$company_code'>
				</div>
				
				<div class='form-div'>
					Work Category : <br>
					".$options->option_workcategory_list($r['work_cat_id'])."
				</div>				
				
				<div class='form-div'>
					Unit : <br>
					<select name='unit'>
						<option value='$unit'>$unit</option>
						<option value='Hour'>Hour</option>
						<option value='Day'>Day</option>
						<option value='Week'>Week</option>
						<option value='Month'>Month</option>
						<option value='Lot'>Lot</option>
					</select>
				</div>
					
				<div class='form-div'>
					Price Per Unit : <br>
					<input type='text' class='textbox' name='price_per_unit' value='$price_per_unit'>
				</div>
								
				<div class='form-div'>
					<input type='button' class='button' name='submit' value='Submit' onclick=xajax_edit_work(xajax.getFormValues('dialog_form')); >
				</div>
		</div>
	";
	
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script("j( \"#dialog\" ).dialog( \"option\", \"title\", 'Work Type' );");
	$objResponse->script("j( \"input:submit, input:button, .buttons , input:reset\").button();");
	$objResponse->script('openDialog();');
	
	return $objResponse;
}

function edit_work($form_data){
	$objResponse 	= new xajaxResponse();
	$options		= new bd_options();
	
	$work_code 	= $form_data['work_code'];
	$description		= $form_data['description'];
	$work_cat_id 	= $form_data['work_category_id'];	
	$unit = $form_data['unit'];
	$price_per_unit = $form_data['price_per_unit'];
	$work_code_id = $form_data['work_code_id'];
	
	mysql_query("
		update
			work_type
		set
			description 	= '$description',
			company_code = '$work_code',
			work_cat_id	= '$work_cat_id',			
			unit		= '$unit',
			wt_price_per_unit		= '$price_per_unit'
		where
			work_code_id		= '$work_code_id'
	") or die($objResponse->alert(mysql_error()));
	
	$objResponse->alert("Query Successful");
	$objResponse->script("window.location.reload();");
	
	return $objResponse;
}

?>