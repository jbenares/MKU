<?php
function new_supplierform(){
	$objResponse 	= new xajaxResponse();
	$options		= new options();	

	$content = "
		<div class='ui-widget-content' style='padding:5px;'>
			<div class='form-div'>
				Account Code : <br>
				<input type='text' class='textbox' name='account_code'>
			</div>
			<div class='form-div'>
				Advances Account: <br>
				".$options->getGcharts(NULL,"advances_gchart_id")."
			</div>
            <div class='form-div'>
				A/P Account: <br>
				".$options->getGcharts2(NULL,"payable_gchart_id")."
			</div>
			<div class='form-div'>
				Account : <br>
				<input type='text' name='account' class='textbox'>
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
				TIN : <br>
				<input type=text name=tin class=textbox>
			</div>
			
			<div class='form-div'>
				Contact Person : <br>
				<input type=text name=contactperson class=textbox>
			</div>
			<div class='form-div'>
					VAT Type : <br>
					".$options->getVattype(NULL,"vat_type")."
				</div>
			
			<div class='form-div'>
				Subcontractor <em>(Check if Subcon)</em> 
				<input type='checkbox' name='subcon' value='1'>
			</div>
			
			<div class='form-div'>
				<input type='submit' id='submit' name=b value='Submit' class=buttons >
				<input type='reset' value='Clear Form' class=buttons>
			</div>
		</div>
		
		<script type='text/javascript'>
			j(\"input:submit,input:reset\").button();
			
			j(\"#dialog\").submit(function(){
				xajax_new_supplier(xajax.getFormValues('dialog_form'));
			});
		</script>
	";
	$objResponse->script("j(\"#dialog\" ).dialog( \"option\", \"title\", \"<img src=images/user_orange.png> SUPPLIER \");");		
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script('openDialog();');
	return $objResponse;
}
	
function new_supplier($form_data) {
	$objResponse = new xajaxResponse();
	
	if(!empty($form_data[account])) {
		
		$subcon = ($form_data['subcon']) ? 1 : 0;
	
		$sql = "insert into supplier set
					account_code =\"$form_data[account_code]\",
					account=\"$form_data[account]\",
					address=\"$form_data[address]\",
					contactno=\"$form_data[contactno]\",
					contactperson=\"$form_data[contactperson]\",
					tin=\"$form_data[tin]\",
					vat_type=\"$form_data[vat_type]\",
					subcon = '$subcon',
					advances_gchart_id = '$form_data[advances_gchart_id]',
                    payable_gchart_id = '$form_data[payable_gchart_id]'
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
	
function edit_supplierform($id) {
	$objResponse = new xajaxResponse();
	$options = new options();
	
	$sql = mysql_query("select
							*
						from
							supplier
						where
							account_id='$id'");
					  
	$r = mysql_fetch_assoc($sql);
					  
	$objResponse 	= new xajaxResponse();
	$options		= new options();	


	$content = "
		<div class='ui-widget-content' style='padding:5px;'>
			<div class='form-div'>
				Account Code : <br>
				<input type='text' class='textbox' name='account_code' value='$r[account_code]'>
				<input type='hidden' name='account_id' value='$r[account_id]' >
			</div>
			
			<div class='form-div'>
				Advances Account: <br>
				".$options->getGcharts($r[advances_gchart_id],"advances_gchart_id")."
			</div>
			<div class='form-div'>
				A/P Account: <br>
				".$options->getGcharts2($r[payable_gchart_id],"payable_gchart_id")."
			</div>
			<div class='form-div'>
				Account : <br>
				<input type='text' name='account' class='textbox' value=\"$r[account]\">
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
				TIN : <br>
				<input type=text name=tin class=textbox value='$r[tin]'>
			</div>
				
			
			<div class='form-div'>
				Contact Person : <br>
				<input type=text name=contactperson class=textbox value='$r[contactperson]'>
			</div>
			<div class='form-div'>
					VAT Type : <br>
					".$options->getVattype($r['vat_type'],"vat_type")."
				</div>
			
			<div class='form-div'>
				Subcontractor <em>(Check if Subcon)</em> 
				<input type='checkbox' name='subcon' value='1' ".( ($r['subcon']) ? "checked='checked'" : "" ).">
			</div>
			
			<div class='form-div'>
				<input type='submit' id='submit' name=b value='Submit' class=buttons >
				<input type='reset' value='Clear Form' class=buttons>
			</div>
		</div>
		
		<script type='text/javascript'>
			j(\"input:submit,input:reset\").button();
			
			j(\"#dialog\").submit(function(){
				xajax_edit_supplier(xajax.getFormValues('dialog_form'));
			});
		</script>
	";
	$objResponse->script("j(\"#dialog\" ).dialog( \"option\", \"title\", \"<img src=images/user_orange.png> SUPPLIER \");");		
	$objResponse->assign('dialog_content','innerHTML',$content);
	$objResponse->script('openDialog();');
		
	return $objResponse;
}
	
	function edit_supplier($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[account])){
		   
		   $subcon = ($form_data['subcon']) ? 1 : 0;
		   
			$sql = "update supplier set
						account_code = \"$form_data[account_code]\",
						account=\"$form_data[account]\",
						address=\"$form_data[address]\",
						contactno=\"$form_data[contactno]\",
						contactperson=\"$form_data[contactperson]\",
						tin=\"$form_data[tin]\",
						vat_type=\"$form_data[vat_type]\",
						subcon = '$subcon',
						advances_gchart_id = '$form_data[advances_gchart_id]',
                        payable_gchart_id = '$form_data[payable_gchart_id]'
					where
						account_id=\"$form_data[account_id]\"
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