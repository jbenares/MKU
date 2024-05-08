<?php

	function new_packageform() {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		$newContent = "<br><img src='images/user_orange.png'> NEW PACKAGE<p>
					<form id=newareaform action=javascript:void(null); onsubmit=xajax_new_package(xajax.getFormValues('newareaform'));toggleBox('demodiv',1);>
						<table class=form_table>	
						  <tr>
						  	<td>Package Code: </td>
						  	<td><input type=text name=packagecode class=textbox></td>
						  </tr>
						  <tr>
						  	<td>Package Description: </td>
						  	<td><input type=text name=description class=textbox></td>
						  </tr>
						  <tr>
						  	<td>Quantity: </td>
						  	<td><input type=text name=qty class=textbox></td>
						  </tr>
						  <tr>
						  	<td>Unit: </td>
						  	<td><input type=text name=unit class=textbox></td>
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
	
	function new_package($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[packagecode]))
		{
			$sql = "insert into package set
						packagecode='$form_data[packagecode]',
						description='$form_data[description]',
						qty='$form_data[qty]',
						unit='$form_data[unit]'
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
	
	function edit_packageform($id) {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		//$objResponse->alert($id);
		
		$sql = mysql_query("select
								*
							from
								package
							where
								package_id='$id'");
						  
		$r = mysql_fetch_assoc($sql);
						  
		$newContent = "<br><img src='images/user_orange.png'> EDIT PACKAGE<p>
					<form id=newareaform action=javascript:void(null); onsubmit=xajax_edit_package(xajax.getFormValues('newareaform'));toggleBox('demodiv',1);>
						<table class=form_table>	
						  <tr>
						  	<td>Package Code: </td>
						  	<td>
								<input type=text name=packagecode class=textbox value='$r[packagecode]'>
								<input type=hidden name=package_id value='$r[package_id]'>
							</td>
						  </tr>
						  <tr>
						  	<td>Package Description: </td>
						  	<td><input type=text name=description class=textbox value='$r[description]'></td>
						  </tr>
						  <tr>
						  	<td>Quantity: </td>
						  	<td><input type=text name=qty class=textbox value='$r[qty]'></td>
						  </tr>
						  <tr>
						  	<td>Unit: </td>
						  	<td><input type=text name=unit class=textbox value='$r[unit]'></td>
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
	
	function edit_package($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[packagecode]))
		{
		
				
			$sql = "update 
						package
					set
						packagecode='$form_data[packagecode]',
						description='$form_data[description]',
						qty='$form_data[qty]',
						unit='$form_data[unit]'
					where
						package_id='$form_data[package_id]'
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