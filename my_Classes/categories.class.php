<?php

	function new_categoriesform() {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		$newContent = "<br><img src='images/user_orange.png'> NEW CATEGORY<p>
					<form id=newareaform action=javascript:void(null); onsubmit=xajax_new_categories(xajax.getFormValues('newareaform'));toggleBox('demodiv',1);>
						<table class=form_table>	
						  <tr>
						  	<td>Level: </td>
						  	<td>".$options->getLevelOptions()."</td>
						  </tr>
						  <tr>
						  	<td>Sub Category: </td>
						  	<td>".$options->getSubCategoryOptions()."</td>
						  </tr>
						  <tr>
						  	<td>Category: </td>
						  	<td><input type=text name=category class=textbox></td>
						  </tr>
						  <tr>
						  	<td>Category Code: </td>
						  	<td><input type=text name='category_code' class=textbox></td>
						  </tr>
						  <tr>
						  	<td>Category Type: </td>
							<td>".$options->option_category_type()."</td>
						  </tr>
						  <tr>
						  	<td>Remark: </td>
						  	<td><textarea name='remark' class='textarea_small' style='overflow:hidden;width:350px;height:50px;font-size:11px;font-family:Arial;'></textarea></td>
						  </tr>
						  <tr>
						  	<td>Income Account</td>
							<td>".$options->option_chart_of_accounts('','income_id')."</td>
						  </tr>
						  <tr>
						  	<td>Expense Account</td>
							<td>".$options->option_chart_of_accounts('','expense_id')."</td>
						  </tr>
						  <tr>
						  	<td></td>
						  	<td>						  	  
						  	  <input type=submit name=b value='Submit' class=buttons>
						  	  <input type=reset value='Clear Form' class=buttons>
						  	</td>
						  </tr>
						</table>
					   </form>";							   
		
		$objResponse->assign("Rdiv","innerHTML", $newContent);
		$objResponse->script("toggleBox('demodiv',0)");
		$objResponse->script("showBox()");
		
		return $objResponse;
	}
	
	function new_categories($form_data) {
		$objResponse = new xajaxResponse();
		//$objResponse->alert("Level: $form_data[level] \n SubCateg: $form_data[subcateg_id] \n Categ: $form_data[category] \n Remark: $form_data[remark]");
		
		if(!empty($form_data[category]))
		{
		
				
			$sql = "insert into categories set
						level='$form_data[level]',
						subcateg_id='$form_data[subcateg_id]',
						category='$form_data[category]',
						remark='$form_data[remark]',
						income_id='$form_data[income_id]',
						expense_id='$form_data[expense_id]',
						category_code = '$form_data[category_code]',
						category_type = '$form_data[category_type]'
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
	
	function edit_categoriesform($id) {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		//$objResponse->alert($id);
		
		$sql = mysql_query("select
								*
							from
								categories
							where
								categ_id='$id'");
						  
		$r = mysql_fetch_assoc($sql);
						  
		$newContent = "<br><img src='images/user_orange.png'> EDIT CATEGORY<p>
					<form id=newareaform action=javascript:void(null); onsubmit=xajax_edit_categories(xajax.getFormValues('newareaform'));toggleBox('demodiv',1);>
						<table class=form_table>	
						  <tr>
						  	<td>Level: </td>
						  	<td>".$options->getLevelOptionEdit($r[level])."</td>
							<input type=hidden name=categ_id value='$r[categ_id]' >
						  </tr>
						  <tr>
						  	<td>Sub Category: </td>
						  	<td>".$options->getSubCategoryOptionsEdit($r[subcateg_id])."</td>
						  </tr>
						  <tr>
						  	<td>Category: </td>
						  	<td><input type=text name=category class=textbox value='$r[category]'  ></td>
						  </tr>
						  <tr>
						  	<td>Category Code: </td>
						  	<td><input type=text name='category_code' class=textbox value='$r[category_code]' ></td>
						  </tr>
						  <tr>
						  	<td>Category Type: </td>
							<td>".$options->option_category_type($r['category_type'])."</td>
						  </tr>
						  <tr>
						  	<td>Income Account</td>
							<td>".$options->option_chart_of_accounts($r['income_id'],'income_id')."</td>
						  </tr>
						  <tr>
						  	<td>Expense Account</td>
							<td>".$options->option_chart_of_accounts($r['expense_id'],'expense_id')."</td>
						  </tr>
						  <tr>
						  	<td>Remark: </td>
						  	<td><textarea class='textarea_small' name='remark' style='overflow:hidden;width:350px;height:50px;font-size:11px;font-family:Arial;'>$r[remark]</textarea></td>
						  </tr>
						  <tr>
						  	<td></td>
						  	<td>						  	  
						  	  <input type=submit name=b value='Submit' class=buttons>
						  	  <input type=reset value='Clear Form' class=buttons>
						  	</td>
						  </tr>
						</table>
					   </form>";					   
		
		$objResponse->assign("Rdiv","innerHTML", $newContent);
		$objResponse->script("toggleBox('demodiv',0)");
		$objResponse->script("showBox()");
		
		return $objResponse;}
	
	function edit_categories($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[category]))
		{
		
				
			$sql = "update categories set
						level='$form_data[level]',
						subcateg_id='$form_data[subcateg_id]',
						category='$form_data[category]',
						remark='$form_data[remark]',
						income_id='$form_data[income_id]',
						expense_id='$form_data[expense_id]',
						category_code = '$form_data[category_code]',
						category_type = '$form_data[category_type]'
					where
						categ_id='$form_data[categ_id]'
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