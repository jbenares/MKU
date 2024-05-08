<?php

	function new_brandform() {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		$newContent = "<br><img src='images/user_orange.png'> NEW BRAND<p>
					<form id=newareaform action=javascript:void(null); onsubmit=xajax_new_brand(xajax.getFormValues('newareaform'));toggleBox('demodiv',1);>
						<table class=form_table>	
						  <tr>
						  	<td>Brand Code: </td>
						  	<td> <input type=text class=textbox name=brandcode ></td>
						  </tr>
						  <tr>
						  	<td>Brand Name: </td>
						  	<td><input type=text class=textbox name=brandname ></td>
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
	
	function new_brand($form_data) {
		$objResponse = new xajaxResponse();
				
		if(!empty($form_data[brandname]))
		{		
			$sql = "insert into brand set
						brandcode='$form_data[brandcode]',
						brandname='$form_data[brandname]'
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
	
	function edit_brandform($id) {
		$objResponse = new xajaxResponse();
		$options = new options();
		
		//$objResponse->alert($id);
		
		$sql = mysql_query("select
								*
							from
								brand
							where
								brand_id='$id'");
						  
		$r = mysql_fetch_assoc($sql);
						  
		$newContent = "<br><img src='images/user_orange.png'> EDIT BRAND<p>
					<form id=newareaform action=javascript:void(null); onsubmit=xajax_edit_brand(xajax.getFormValues('newareaform'));toggleBox('demodiv',1);>
						<table class=form_table>	
						  <tr>
						  	<td>Brand Code: </td>
						  	<td> <input type=text class=textbox name=brandcode value='$r[brandcode]' ></td>
							<input type=hidden name=brand_id value='$r[brand_id]' >
						  </tr>
						  <tr>
						  	<td>Brand Name: </td>
						  	<td><input type=text class=textbox name=brandname value='$r[brandname]' ></td>
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
	
	function edit_brand($form_data) {
		$objResponse = new xajaxResponse();
		
		if(!empty($form_data[brandname]))
		{
		
				
			$sql = "update brand set
						brandcode='$form_data[brandcode]',
						brandname='$form_data[brandname]'
					where
						brand_id='$form_data[brand_id]'
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